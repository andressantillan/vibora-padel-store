<?php

namespace App\Http\Controllers\MercadoPago;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use App\Models\Order;

class MercadoPagoController
{
    public static function createPreference($items)
    {
        // Configure MercadoPago
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        $client = new PreferenceClient();

        $itemsPreference = [];

        for($i = 0; $i < count($items); $i++) {
            $item = $items[$i];
            $itemsPreference[] = [
                "title" => $item['productName'],
                "quantity" => $item['quantity'],
                "unit_price" => $item['price'],
            ];
        }

        $preferenceAttr = [
            "items" => $itemsPreference
        ];

        $preference = $client->create($preferenceAttr);

        $preference->back_urls = [
            "success" => env('CALLBACK_URL_SUCCESS'),
            "failure" => env('CALLBACK_URL_FAILURE'),
            "pending" => env('CALLBACK_URL_PENDING'),
        ];

        $preference->auto_return = "approved";

        return $preference;
    }
}