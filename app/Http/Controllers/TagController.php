<?php

namespace App\Http\Controllers;

use App\Models\ProductTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Exception;

class TagController extends Controller
{

    public function storeTag(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image'
        ]);

        $tag = new Tag();
        $tag->name = $request->name;

        if ($request->hasFile('image')) {
            $originalFileName = $request->file('image')->getClientOriginalName();
            $image = $request->file('image')->storeAs('images/tags', $originalFileName, 'public');
            $tag->image = 'storage/' . $image;
        }

        $tag->save();

        return response()->json([
            'success' => 1,
            'message' => __('messages.tag_created_successfully'),
            'result' => $tag
        ], 201);
    }

    public function update(Request $request, Tag $tag)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image'
            ]);

            $tag->name = $request->name;

            if ($request->hasFile('image')) {
                $tag->image = $request->file('image')->store('images/tags');
            }

            $tag->save();

            return redirect()->route('tags.index')->with('success', 'Tag updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the tag.');
        }
    }

    public function destroy(Request $request)
    {
        $tag = Tag::find($request->id);

        if ($tag) {
            $products = ProductTag::where('tag_id', $tag->id)->get();

            foreach ($products as $productTag) {
                $productTag->delete();
            }

            $tag->delete();

            return response()->json([
                'success' => 1,
                'message' => __('messages.tag_deleted_successfully'),
                'result' => []
            ], 200);
        } else {
            return response()->json([
                'success' => 0,
                'message' => __('messages.tag_not_found'),
                'result' => []
            ], 404);
        }
    }

    public function getTags()
    {
        try {
            $tags = Tag::all();
            return response()->json([
                'success' => 1,
                'result' => $tags,
                'message' => __('messages.tags_retrieved_successfully'),

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => __('messages.error_retrieving_tags'),
                'result' => []
            ], 500);
        }
    }
}