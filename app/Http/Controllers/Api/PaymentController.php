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
        $preference = MercadoPagoController::createPreference($items);
        return response()->json($preference);
    }
}