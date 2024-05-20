<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'max:2048',
            'price' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'expired_at' => 'required|date',
            'category_id' => 'required'

        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        // Validasi jika nama product sudah digunakan
        if(Product::where('name', $validated['name'])->exists()) {
            return response()->json([
                'msg' => 'Product already exists'
            ], );
        }

        // Validasi jika category_id tidak ada
        $category = Category::where('name', $validated['category_id'])->first();
        if(!$category) {
            return response()->json([
                'msg' => 'Category not found' 
            ], 404);
        }

        // Ambil file image
        $image = $request->file('image');
        $fileName = now()->timestamp.'_'.$image->getClientOriginalName();
        $image->move('Uploads/', $fileName);

        // Ambil email dari middleware
        $user_email = $request['email'];

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image' => 'Uploads/'.$fileName,
            'expired_at' => $validated['expired_at'],
            'category_id' => $category->id,
            'modified_by' => $user_email,
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Product created successfully',
                'data' => $product
            ]
            ], 200);
    }

    public function show()
    {
        $products = Product::all();
        if($products->count() <= 0) {
            return response()->json([
                'msg' => 'Product not found'
            ], 404);
    }

    return response()->json([
        'data' => [
            'msg' => "{$products->count()} products found",
            'data' => $products
        ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if(!$product) {
            return response()->json([
                'msg' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'max:2048',
            'price' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'expired_at' => 'required|date',
            'category_id' => 'required'

        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        // Validasi jika nama product sudah digunakan
        if(Product::where('name', $validated['name'])
            ->where('id', '!=', $id)
            ->exists()) {
            return response()->json([
                'msg' => 'Product already exists'
            ], );
        }

        // Validasi jika category_id tidak ada
        $category = Category::where('name', $validated['category_id'])->first();
        if(!$category) {
            return response()->json([
                'msg' => 'Category not found' 
            ], 404);
        }

        // Ambil file image
        $image = $request->file('image');
        $fileName = now()->timestamp.'_'.$image->getClientOriginalName();
        $image->move('Uploads/', $fileName);

       // Ambil email dari middleware
       $user_email = $request['email'];

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image' => 'Uploads/'.$fileName,
            'expired_at' => $validated['expired_at'],
            'category_id' => $category->id,
            'modified_by' => $user_email,
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Product updated successfully',
                'data' => $product
            ]
            ], 200);
    }

    public function delete ($id)
    {
        $product = Product::find($id);
        if(!$product) {
            return response()->json([
                'msg' => 'Product not found'
            ], 404);
        }

        $product->delete();
        return response()->json([
            "data" => [
                'msg' => "Product with id-{$id} deleted successfully"
            ]
            ], 200);
    }
}