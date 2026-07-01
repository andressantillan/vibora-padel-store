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

        if (! $secret || ! $xSignature) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $xSignature) as $part) {
            $pair = array_pad(explode('=', trim($part), 2), 2, '');
            $parts[trim($pair[0])] = trim($pair[1]);
        }

        $ts = $parts['ts'] ?? '';
        $v1 = $parts['v1'] ?? '';

        if ($ts === '' || $v1 === '') {
            return false;
        }

        // MP normaliza data.id a minúsculas si es alfanumérico.
        $normalizedId = strtolower($dataId);
        $requestId = $request->header('x-request-id');

        $manifest = '';
        if ($normalizedId !== '') {
            $manifest .= 'id:'.$normalizedId.';';
        }
        if ($requestId) {
            $manifest .= 'request-id:'.$requestId.';';
        }
        $manifest .= 'ts:'.$ts.';';

        $hash = hash_hmac('sha256', $manifest, $secret);

        Log::info('MercadoPago Signature Debug', [
            'manifest' => $manifest,
            'generated_hash' => $hash,
            'received_v1' => $v1,
            'ts' => $ts,
            'data_id' => $dataId,
            'x_request_id' => $requestId,
            'secret_length' => strlen($secret),
        ]);

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
        Log::info('MercadoPago Webhook Received', $request->all());
        
        $data = $request->all();
        $type = $data['type'] ?? '';
        $paymentId = (string) ($data['data']['id'] ?? '');
    
        if(!$this->verifySignature($request, $paymentId)) {
            Log::warning('MercadoPago Webhook: Invalid signature', ['paymentId' => $paymentId]);
            // TODO: Descomentar esto cuando MercadoPago arregle el bug de la firma cruzada
            // return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        if ($type !== 'payment' || !$paymentId) {
            Log::info('MercadoPago Webhook: Ignored event', ['type' => $type, 'paymentId' => $paymentId]);
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
            
            $this->statusManager->onPaymentRegistered($order, 'Pago registrado a través de MercadoPago.');
        }

        return response()->json(['success' => true, 'message' => 'Webhook processed successfully'], 200);
    }
}
