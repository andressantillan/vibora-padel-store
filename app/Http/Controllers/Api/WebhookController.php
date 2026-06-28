<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class WebhookController extends Controller
{
    public function mercadopago(Request $request)
    {
        // 1. Verificar firma de seguridad
        $secret = config('services.mercadopago.webhook_secret');
        if ($secret) {
            $xSignature = $request->header('x-signature');
            $xRequestId = $request->header('x-request-id');
            
            if (!$xSignature) {
                return response()->json(['error' => 'Missing signature'], 400);
            }

            // Parse signature
            $parts = explode(',', $xSignature);
            $ts = '';
            $hash = '';
            foreach ($parts as $part) {
                if (str_starts_with(trim($part), 'ts=')) {
                    $ts = str_replace('ts=', '', trim($part));
                } elseif (str_starts_with(trim($part), 'v1=')) {
                    $hash = str_replace('v1=', '', trim($part));
                }
            }

            $dataId = $request->query('data_id', $request->query('id')); 
            
            if(!$dataId && $request->input('data.id')) {
                 $dataId = $request->input('data.id');
            }
            
            $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
            
            $hmac = hash_hmac('sha256', $manifest, $secret);

            if (!hash_equals($hmac, $hash)) {
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // 2. Procesar notificación
        $action = $request->input('action') ?? $request->input('topic') ?? $request->query('topic');
        
        if ($action === 'payment.created' || $action === 'payment.updated' || $action === 'payment') {
            $paymentId = $request->input('data.id') ?? $request->query('data_id') ?? $request->query('id');
            
            if (!$paymentId) {
                return response()->json(['error' => 'No payment id'], 400);
            }

            // Obtener info del pago
            $mpAccessToken = config('services.mercadopago.access_token');
            $response = Http::withToken($mpAccessToken)
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

            if (!$response->successful()) {
                Log::error('Error fetching MercadoPago payment', ['id' => $paymentId, 'response' => $response->json()]);
                return response()->json(['error' => 'Payment not found in API'], 404);
            }

            $paymentData = $response->json();
            $externalReference = $paymentData['external_reference'] ?? null;

            if (!$externalReference) {
                return response()->json(['message' => 'No external reference, ignored'], 200);
            }

            $order = Order::find($externalReference);
            if (!$order) {
                $order = Order::where('code', $externalReference)->first();
            }

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $mpStatus = $paymentData['status'];
            $status = 'pendiente';
            
            if ($mpStatus === 'approved') {
                $status = 'aprobado';
            } elseif (in_array($mpStatus, ['rejected', 'cancelled', 'refunded', 'charged_back'])) {
                $status = 'rechazado';
            }

            $payment = Payment::updateOrCreate(
                ['reference' => $paymentId],
                [
                    'order_id' => $order->id,
                    'method'   => 'mercadopago',
                    'amount'   => $paymentData['transaction_amount'],
                    'status'   => $status,
                    'paid_at'  => $status === 'aprobado' && isset($paymentData['date_approved']) ? Carbon::parse($paymentData['date_approved']) : null,
                ]
            );

            if ($status === 'aprobado' && $order->payment_status !== 'pagado') {
                $order->update(['payment_status' => 'pagado']);
                
                $order->statusHistory()->create([
                    'status' => $order->status,
                    'notes'  => 'Pago registrado a través de MercadoPago.',
                ]);
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['message' => 'Ignored event'], 200);
    }
}
