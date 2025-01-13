<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="API gateway",
 *     description="Gateway for products and orders services",
 *     @OA\Contact(name="Valerii Smoliakov")
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="API server"
 * )
 */
abstract class Controller
{
    //
}
