<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        $userId = $request->input('user_id');
        $product = Product::where('category_id', 'LIKE', '%' . $categoryId . '%')
        ->where('user_id', 'LIKE', '%' . $userId . '%')->paginate(10)->load('category', 'user');
        return ProductResource::collection($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = Product::create([
          ...$request->validate([
                'name' =>'required|string|max:100',
                'description' =>'required|string',
                'price' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
                'image_url' => 'required',
                'category_id' => 'required',
          ]),
          'user_id' => $request->user()->id,
        ]);

        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'user');
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update(
            $request->validate([
            'name' =>'required|string|max:100',
            'description' =>'required|string',
            'price' =>'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'image_url' =>'required',
            'category_id' =>'required',
        ]));

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'success' => true,
            "message" => "Product deleted successfully"
        ]);
    }
}
