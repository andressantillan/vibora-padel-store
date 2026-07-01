<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyStoreApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = env('STOREFRONT_API_KEY');
        
        $requestKey = $request->header('X-API-KEY') ?? $request->bearerToken();

        if (!$apiKey || $requestKey !== $apiKey) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
