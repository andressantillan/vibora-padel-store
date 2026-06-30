<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Controllers\MercadoPago\MercadoPagoController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;


class PaymentController extends Controller
{
    public function createPreference(Request $request)
    {
        $items = $request->input('items');
        $orderId = $request->input('order_id');
        $preference = MercadoPagoController::createPreference($items, $orderId);
        return response()->json($preference);
    }

    public function getPaymentMethods()
    {
        return response()->json([
            [
                'id'   => 'mercadopago',
                'name' => \App\Models\Payment::METHODS['mercadopago'] ?? 'Mercado Pago',
            ]
        ]);
    }
}