<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use App\Services\OrderStatusManager;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderStatusManager $statusManager,
    ) {}

    private function verifySignature(Request $request, $dataId)
    {
        $secret = config('services.mercadopago.webhook_secret');
        $xSignature = $request->header('x-signature');
        $xRequestId = $request->header('x-request-id');

        if(!$secret || !$xSignature) return false;

        $parts = preg_split('/,\s*/', $xSignature);
        $ts = '';
        $v1 = '';

        foreach ($parts as $part) {
            if (str_starts_with(trim($part), 'ts=')) {
                $ts = str_replace('ts=', '', trim($part));
            } elseif (str_starts_with(trim($part), 'v1=')) {
                $v1 = str_replace('v1=', '', trim($part));
            }
        }

        if (!$ts || !$v1) return false;

        $normalizedDataId = strtolower($dataId);

        $manifest = '';

        if($normalizedDataId !== "") $manifest .= "id:{$normalizedDataId};";
        if($xRequestId) $manifest .= "request-id:{$xRequestId};";
        $manifest .= "ts:{$ts};";

        $hash = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($hash, $v1);
    }

    public function getPayment($paymentId){
        
        $mpAccessToken = config('services.mercadopago.access_token');
        $response = Http::withToken($mpAccessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$response->successful()) {
            Log::error('Error fetching MercadoPago payment', ['id' => $paymentId, 'response' => $response->json()]);
            return null;
        }

        return $response->json();
    }

    public function mercadopago(Request $request)
    {
        
        $data = $request->all();
        $type = $data['type'] ?? '';
        $paymentId = (string) ($data['data']['id'] ?? '');
    
        if(!$this->verifySignature($request, $paymentId)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        if ($type !== 'payment' || !$paymentId) {
            return response()->json(['success' => true, 'message' => 'Ignored event'], 200);
        }

        $payment = $this->getPayment($paymentId);
        
        if(!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found in API'], 404);
        }

        $externalReference = (string) ($payment['external_reference'] ?? '');
        $order = Order::find($externalReference);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 200);
        }

        $paidAmount = (float) ($payment['transaction_amount'] ?? 0);
        $totalOrder = (float) ($order->total ?? 0);

        if($paidAmount !== $totalOrder) {
            Log::warning('Payment amount does not match order total', [
                'order_id' => $order->id,
                'paid_amount' => $paidAmount,
                'order_total' => $totalOrder,
            ]);
            return response()->json(['success' => false, 'message' => 'Payment amount does not match order total'], 400);
        }

        $paymentStatus = $payment['status'] ?? '';

        $estadoPago = $paymentStatus === 'approved' ? 'pagado' : ($paymentStatus === 'pending' ? 'pendiente' : 'rechazado');

        if ($estadoPago === 'pagado' && $order->payment_status !== 'pagado') {
            $order->update(['payment_status' => 'pagado']);

            //Crear payment
            $paymentRecord = Payment::updateOrCreate(
                ['reference' => $paymentId],
                [
                    'order_id' => $order->id,
                    'method'   => 'mercadopago',
                    'amount'   => $paidAmount,
                    'status'   => $estadoPago,
                    'paid_at'  => Carbon::parse($payment['date_approved'] ?? now()),
                ]
            );
            
            $this->statusManager->onPaymentRegistered($order);

            $order->statusHistory()->create([
                'status' => $order->status,
                'notes'  => 'Pago registrado a través de MercadoPago.',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Webhook processed successfully'], 200);
    }
}
