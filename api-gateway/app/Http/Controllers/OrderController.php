<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

class OrderController extends Controller
{
    private string $orderServiceUrl;

    public function __construct()
    {
        $this->orderServiceUrl = env('ORDER_SERVICE_URL', 'http://localhost:8002/api/orders');
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Получить список заказов",
     *     tags={"Заказы"},
     *     @OA\Response(
     *         response=200,
     *         description="Список заказов",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function index()
    {
        $response = Http::get($this->orderServiceUrl);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch orders from the order service.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Создать заказ",
     *     tags={"Заказы"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="delivery_address", type="string", example="123 Main Street"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", example=100.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заказ успешно создан",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $response = Http::post($this->orderServiceUrl, $request->all());

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to create order in the order service.',
                'reason' => $response->json()
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Получить заказ по ID",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID заказа",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о заказе",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заказ не найден"
     *     )
     * )
     */
    public function show($id)
    {
        $response = Http::get("{$this->orderServiceUrl}/{$id}");

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch order from the order service.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * Перенаправляет запрос на поиск заказов по ID товара в сервис заказов.
     *
     * @OA\Get(
     *     path="/api/orders/search-by-product",
     *     summary="Поиск заказов по ID товара через сервис заказов",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="ID товара для поиска заказов",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список заказов, содержащих указанный товар",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервиса заказов"
     *     )
     * )
     */

    public function searchByProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $productId = $validated['product_id'];

        // Отправляем запрос к сервису заказов
        $response = Http::get($this->orderServiceUrl . '/search-by-product', [
            'product_id' => $productId,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch orders from the Order Service.',
                'details' => $response->json(),
            ], 500);
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Обновить заказ",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID заказа",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="delivery_address", type="string", example="123 Main Street"),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", example=100.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заказ успешно обновлён"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $response = Http::put("{$this->orderServiceUrl}/{$id}", $request->all());

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to update order in the order service.'
            ], $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Удалить заказ",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID заказа",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заказ успешно удалён"
     *     )
     * )
     */
    public function destroy($id)
    {
        $response = Http::delete("{$this->orderServiceUrl}/{$id}");

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to delete order in the order service.'
            ], $response->status());
        }

        return response()->json(['message' => 'Order deleted successfully.'], 200);
    }
}
