<?php

namespace App\Http\Controllers\MercadoPago;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class MercadoPagoController
{
    public static function createPreference($items, $orderId = null)
    {
        $itemsPreference = [];
        
        $preferenceUrl = config('services.mercadopago.preferences_url');
        $frontendUrl = config('services.frontend.url');
        $backUrls = config('services.mercadopago.back_urls');
        $mpAccessToken = config('services.mercadopago.access_token');
        $webhookUrl = config('services.mercadopago.webhook_url', url('/api/webhooks/mercadopago'));

        $itemsPreference = self::prepareItems($items);

        $payload = [
            "items" => $itemsPreference,
            "back_urls" => $backUrls,
            "notification_url" => $webhookUrl,
        ];

        if ($orderId) {
            $payload["external_reference"] = (string)$orderId;
        }

        if(str_starts_with($frontendUrl, 'https://')) {
            $payload["auto_return"] = "all";
        }
        
        $response = Http::withToken($mpAccessToken)->post($preferenceUrl, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => 'Error creating preference',
            'details' => $response->json()
        ];
    }

    private static function prepareItems($items)
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
        return $itemsPreference;
    }
}