<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getAllProducts()
    {
        $products = Product::with('tags')->get();
        return response()->json([
            'success' => 1,
            'result' => $products,
            'message' => __('messages.products_retrieved_successfully'),
        ], 200);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->with('tags')->paginate(10);
        return response()->json([
            'success' => 1,
            'result' => $products,
            'message' => __('messages.products_retrieved_successfully'),
        ], 200);
    }
    public function getProductsByTag($tagId)
    {
        $products = Product::whereHas('tags', function ($query) use ($tagId) {
            $query->where('tag_id', $tagId);
        })->paginate(10);
        return response()->json([
            'success' => 1,
            'result' => $products,
            'message' => __('messages.products_retrieved_successfully'),
        ], 200);
    }
    public function getProductsByCategoryAndTag(Request $request)
    {

        $categoryId = $request->query('category');
        $tagId = $request->query('tag');
        $limit = $request->query('limit', 10);

        $query = Product::query();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($tagId) {
            $query->whereHas('tags', function ($tagQuery) use ($tagId) {
                $tagQuery->where('tag_id', $tagId);
            });
        }

        $products = $query->limit($limit)->with('tags')->get();

        return response()->json([
            'success' => 1,
            'result' => $products,
            'message' => __('messages.products_retrieved_successfully'),
        ], 200);
    }
}