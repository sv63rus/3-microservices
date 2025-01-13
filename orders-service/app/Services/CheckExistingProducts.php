<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CheckExistingProducts
{
    public function check(array $productIds): bool
    {
        $url = sprintf(
            "%s:%s%s",
                env('APP_PRODUCTS_SERVICE_HOST'),
                env('APP_PRODUCTS_SERVICE_PORT'),
                "/api/products/exist"
        );
        try {
            $response = Http::post($url, [
                'product_ids' => $productIds,
            ]);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'products' => ['Unable to connect to product service.'],
            ]);
        }

        if ($response->failed()) {
            $responseData = $response->json();

            throw ValidationException::withMessages([
                'message' => "some products doesnt exists",
                'products' => $responseData['missing_products'] ?? ['Unknown error with product validation'],
            ]);
        }

        return true;
    }
}