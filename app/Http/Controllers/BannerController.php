<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function getBanners()
    {
        $banners = Banner::all();

        foreach ($banners as $banner) {
            switch ($banner->type) {
                case 'category':
                    $banner->category = Category::find($banner->entity_id);
                    break;
                case 'tag':
                    $banner->tag = Tag::find($banner->entity_id);
                    break;
                case 'product':
                    $banner->product = Product::find($banner->entity_id);
                    break;
                default:
                    $banner->entity = null;
                    break;
            }
        }

        return response()->json([
            'success' => 1,
            'message' => __('messages.banners_retrieved_successfully'),
            'result' => $banners
        ], 200);
    }
    public function storeBanner(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:category,tag,product',
            'position' => 'required|string|max:255',
            'entity_id' => 'required|integer'
        ]);

        switch ($request->type) {
            case 'category':
                $entity = Category::find($request->entity_id);
                break;
            case 'tag':
                $entity = Tag::find($request->entity_id);
                break;
            case 'product':
                $entity = Product::find($request->entity_id);
                break;
            default:
                $entity = null;
                break;
        }

        if (!$entity) {
            return response()->json([
                'success' => 0,
                'result' => [],
                'message' => __('messages.entity_not_found')
            ], 404);
        }

        $banner = Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'position' => $request->position,
            'entity_id' => $request->entity_id,
        ]);

        return response()->json([
            'success' => 1,
            'message' => __('messages.banner_created_successfully'),
            'result' => $banner
        ], 201);
    }
}
