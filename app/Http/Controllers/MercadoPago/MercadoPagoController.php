<?php

namespace App\Http\Controllers\MercadoPago;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class MercadoPagoController
{
    public static function createPreference($items)
    {
        $itemsPreference = [];

        for($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $itemsPreference[] = [
                "title" => $item['productName'],
                "quantity" => (int) $item['quantity'],
                "unit_price" => (float) $item['price'],
            ];
        }

        $preferenceAttr = [
            "items" => $itemsPreference,
        ];

        $successUrl = env('CALLBACK_URL_SUCCESS');
        if ($successUrl) {
            $preferenceAttr["back_urls"] = [
                "success" => $successUrl,
                "failure" => env('CALLBACK_URL_FAILURE'),
                "pending" => env('CALLBACK_URL_PENDING'),
            ];
            $preferenceAttr["auto_return"] = "all";
        }

        $response = Http::withToken(env('MP_ACCESS_TOKEN'))
            ->post('https://api.mercadopago.com/checkout/preferences', $preferenceAttr);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => 'Error creating preference',
            'details' => $response->json()
        ];
    }
}