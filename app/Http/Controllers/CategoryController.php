<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function store (Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        if(Category::where('name', $validated['name'])->exists())
        {
            return response()->json([
                'msg' => 'Category already exists'
            ],);
        }

        $category = Category::create([
            'name' => $validated['name']
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Category created successfully',
                'data' => $category
            ]

        ]);
    }

    public function show () 
    {
        $categories = Category::all();

        if($categories->count() <= 0)
        {
            return response()->json([
                  'msg' => 'No categories found'
            ], 404 );
        }

        return response()->json([
            "data" => [
                'msg' => "{$categories->count()} categories found",
                'data' => $categories
            ]
        ]);
    }

    public function update (Request $request, $id)
    {
        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'msg' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        if(Category::where('name', $validated['name'])
            ->where('id', '!=', $id)
            ->exists())
        {
            return response()->json([
                'msg' => 'Category already exists'
            ],);
        }

        $category->update([
            'name' => $validated['name']
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Category update succesfully'
            ]
            ], 200);


    }

    public function delete($id)
    {
        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'msg' => 'Category not found'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'msg' => 'Category deleted successfully'
        ], 200);

    }
}