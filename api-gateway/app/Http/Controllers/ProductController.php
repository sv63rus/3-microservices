<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    private string $productServiceUrl;

    public function __construct()
    {
        $this->productServiceUrl = env('PRODUCT_SERVICE_URL', 'http://localhost:8001/api/products');
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Получить список продуктов c возможностью поиска. Укажите параметр search для поиска по имени",
     *     tags={"Продукты"},
     *     @OA\Parameter(
     *       name="search",
     *       in="query",
     *       description="Поисковая строка для поиска по имени продукта",
     *       required=false,
     *       @OA\Schema(type="string", example="example product name")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список продуктов",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $queryParams = [];
        if (!empty($search)) {
            $queryParams['search'] = $search;
        }

        $response = Http::get($this->productServiceUrl, $queryParams);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch products from the product service.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Создать продукт",
     *     tags={"Продукты"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Продукт 1"),
     *             @OA\Property(property="price", type="number", example=1000),
     *             @OA\Property(property="description", type="string", example="Описание продукта"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Продукт успешно создан",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $response = Http::post($this->productServiceUrl, $request->all());

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to create product in the product service.',
                'reason' => $response->json()
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Получить продукт по ID",
     *     tags={"Продукты"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID продукта",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о продукте",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Продукт не найден"
     *     )
     * )
     */
    public function show($id)
    {
        $response = Http::get("{$this->productServiceUrl}/{$id}");

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch product from the product service.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Обновить продукт",
     *     tags={"Продукты"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID продукта",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Продукт 1"),
     *             @OA\Property(property="price", type="number", example=1000),
     *             @OA\Property(property="description", type="string", example="Описание продукта"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Продукт успешно обновлён",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $response = Http::put("{$this->productServiceUrl}/{$id}", $request->all());

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to update product in the product service.',
                'reason' => $response->json()
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Удалить продукт",
     *     tags={"Продукты"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID продукта",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Продукт успешно удалён"
     *     )
     * )
     */
    public function destroy($id)
    {
        $response = Http::delete("{$this->productServiceUrl}/{$id}");

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to delete product in the product service.',
                'reason' => $response->json()
            ], $response->status());
        }

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }
}
