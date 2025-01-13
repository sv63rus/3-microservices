<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function checkExistence(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer',
        ]);

        // Find existent
        $existingProducts = Product::whereIn('id', $validated['product_ids'])->pluck('id')->toArray();

        // Find absent
        $missingProducts = array_diff($validated['product_ids'], $existingProducts);

        if (!empty($missingProducts)) {
            return response()->json([
                'message' => 'Some products do not exist.',
                'missing_products' => $missingProducts,
            ], 422);
        }

        return response()->json([
            'message' => 'All products exist.',
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'categories' => 'nullable|array',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'categories' => 'nullable|array',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
                'message' => "Product not found",
            ]);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'message' => "Something went wrong",
                'reason' => $e->getMessage(),
            ]);
        }
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
