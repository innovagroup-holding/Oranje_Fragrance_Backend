<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // public function getAllProducts()
    // {
    //     $products = Product::with('tags', 'category')->get();
    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }

    // public function getProductsByCategory($categoryId)
    // {
    //     $products = Product::where('category_id', $categoryId)->with('tags')->paginate(10);
    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }
    // public function getProductsByTag($tagId)
    // {
    //     $products = Product::with('category')->whereHas('tags', function ($query) use ($tagId) {
    //         $query->where('tag_id', $tagId);
    //     })->paginate(10);
    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }
    // public function getProductsByCategoryAndTag(Request $request)
    // {

    //     $categoryId = $request->query('category');
    //     $tagId = $request->query('tag');
    //     $limit = $request->query('limit', 10);

    //     $query = Product::query();

    //     if ($categoryId) {
    //         $query->where('category_id', $categoryId);
    //     }

    //     if ($tagId) {
    //         $query->whereHas('tags', function ($tagQuery) use ($tagId) {
    //             $tagQuery->where('tag_id', $tagId);
    //         });
    //     }

    //     $products = $query->limit($limit)->with('tags')->get();

    //     return response()->json([
    //         'success' => 1,
    //         'result' => $products,
    //         'message' => __('messages.products_retrieved_successfully'),
    //     ], 200);
    // }
    // public function getHeroProducts()
    // {
    //     $heroProducts = Product::with('tags', 'category')->where('is_hero', true)
    //         ->get();

    //     return response()->json([
    //         'success' => 1,
    //         'result' => $heroProducts,
    //         'message' => __('messages.success'),
    //     ]);
    // }
    public function getProducts(Request $request)
    {
        $categoryId = $request->query('category');
        $tagId = $request->query('tag');
        $limit = $request->query('limit', null);
        $isHero = $request->query('only_hero_products', null);


        $query = Product::query();


        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }


        if ($tagId) {
            $query->whereHas('tags', function ($tagQuery) use ($tagId) {
                $tagQuery->where('tag_id', $tagId);
            });
        }

        error_log($isHero);

        if ($isHero === '1') {


            $query->where('is_hero', 1);
        }


        $query->with(['tags', 'category', 'images']);


        if ($limit) {
            $products = $query->limit($limit)->get();
        } else {
            $products = $query->paginate(10);
        }


        return response()->json([
            'success' => 1,
            'result' => $products,
            'message' => __('messages.products_retrieved_successfully'),
        ], 200);
    }
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image'
        ]);


        $latestProduct = Product::latest('id')->first();
        $nextCode = $latestProduct ? 'PR' . str_pad($latestProduct->id + 1, 4, '0', STR_PAD_LEFT) : 'PR0001';


        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->unique_code = $nextCode;
        $product->category_id = $request->category_id;
        $product->save();


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $originalFileName = $image->getClientOriginalName();
                $path = $image->storeAs('images/products', $originalFileName, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'storage/' . $path
                ]);
            }
        }


        return response()->json([
            'success' => 1,
            'message' => __('messages.product_created_successfully'),
            'result' => $product
        ], 200);
    }
    public function getProduct(Request $request)
    {

        $product = Product::with(['tags', 'category', 'images'])->find($request->id);


        if (!$product) {
            return response()->json([
                'success' => 0,
                'result' => [],
                'message' => __('messages.product_not_found')
            ], 404);
        }


        return response()->json([
            'success' => 1,
            'result' => $product,
            'message' => __('messages.product_retrieved_successfully')
        ], 200);
    }
    public function updateProduct(Request $request)
    {

        $product = Product::find($request->id);

        if (!$product) {
            return response()->json([
                'success' => 0,
                'message' => __('messages.product_not_found')
            ], 404);
        }
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'new_images.*' => 'nullable|image',
            'discount_percentage' => 'sometimes|numeric|min:0|max:100',
            'deleted_image_ids' => 'nullable|string'
        ]);


        if ($request->has('name')) {
            $product->name = $request->name;
        }

        if ($request->has('price')) {
            $product->price = $request->price;
        }

        if ($request->has('category_id')) {
            $product->category_id = $request->category_id;
        }
        if ($request->has('discount_percentage')) {
            $product->discount_percentage = $request->discount_percentage;


            if ($product->discount_percentage > 0 && $product->price > 0) {
                $discountAmount = ($product->price * $product->discount_percentage) / 100;
                $product->price_after_discount = $product->price - $discountAmount;
            } else {

                $product->price_after_discount = $product->price;
            }
        }
        $product->save();

        if ($request->has('deleted_image_ids')) {

            $deletedImageIds = explode(',', $request->deleted_image_ids);

            ProductImage::whereIn('id', $deletedImageIds)->where('product_id', $product->id)->delete();
        }

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $originalFileName = $image->getClientOriginalName();
                $path = $image->storeAs('images/products', $originalFileName, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => 'storage/' . $path
                ]);
            }
        }

        return response()->json([
            'success' => 1,
            'message' => __('messages.product_updated_successfully'),
            'result' => $product->load('images')
        ], 200);
    }
    public function deleteProduct(Request $request)
    {

        $product = Product::find($request->id);


        if (!$product) {
            return response()->json([
                'success' => 0,
                'result' => [],
                'message' => __('messages.product_not_found')
            ], 404);
        }


        ProductTag::where('product_id', $request->id)->delete();
        ProductImage::where('product_id', $request->id)->delete();



        $product->delete();


        return response()->json([
            'success' => 1,
            'result' => [],
            'message' => __('messages.product_deleted_successfully')
        ], 200);
    }
    public function addTagToProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tag_id' => 'required|exists:tags,id'
        ]);

        $product = Product::find($request->product_id);

        $product->tags()->attach($request->tag_id);

        return response()->json([
            'success' => 1,
            'message' => __('messages.tag_added_successfully'),
            'result' => $product->load('tags')
        ], 200);
    }

    public function removeTagFromProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tag_id' => 'required|exists:tags,id'
        ]);

        $product = Product::find($request->product_id);

        $product->tags()->detach($request->tag_id);

        return response()->json([
            'success' => 1,
            'message' => __('messages.tag_removed_successfully'),
            'result' => $product->load('tags')
        ], 200);
    }
    public function addDiscountToProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percentage' => 'required|numeric|min:0|max:100'
        ]);

        $product = Product::find($request->product_id);

        $product->discount_percentage = $request->discount_percentage;

        if ($product->price > 0 && $product->discount_percentage > 0) {
            $discountAmount = ($product->price * $product->discount_percentage) / 100;
            $product->price_after_discount = $product->price - $discountAmount;
        } else {
            $product->price_after_discount = $product->price;
        }

        $product->save();

        return response()->json([
            'success' => 1,
            'message' => __('messages.discount_added_successfully'),
            'result' => $product
        ], 200);
    }
}