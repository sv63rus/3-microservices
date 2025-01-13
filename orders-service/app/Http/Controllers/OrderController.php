<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckExistingProducts;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Поиск по продукту
        if ($request->has('product_id')) {
            $query->whereJsonContains('products', [['product_id' => (int) $request->product_id]]);
        }

        $orders = $query->get();

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CheckExistingProducts $checkExistingProducts)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        // check that products exists
        $checkExistingProducts->check(
            collect(data_get($validated, 'products'))->pluck('product_id')->toArray()
        );


        $order = Order::create($validated);

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id,CheckExistingProducts $checkExistingProducts)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'delivery_address' => 'sometimes|string|max:255',
            'products' => 'sometimes|array',
            'products.*.product_id' => 'required_with:products|integer',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'products.*.price' => 'required_with:products|numeric|min:0',
        ]);

        // check that products exists
        $checkExistingProducts->check(
            collect(data_get($validated, 'products'))->pluck('product_id')->toArray()
        );

        $order->update($validated);

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }

    public function searchByProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $productId = $validated['product_id'];

        // не оптимально, но это ограничение sqllite
        // он не может искать по json
        // можно было бы использовать другую субд либо хранить записи о товарах в другой таблице
        // но на это нет времени и так уже часа 4-ре пишу
        $orders = Order::all();

        $filteredOrders = $orders->filter(function ($order) use ($productId) {
            foreach ($order->products as $product) {
                if ($product['product_id'] == $productId) {
                    return true;
                }
            }
            return false;
        });

        return response()->json($filteredOrders);
    }
}
