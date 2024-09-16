<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Exception;

class CategoryController extends Controller
{


    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image'
        ]);

        $category = new Category;
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            $originalFileName = $request->file('image')->getClientOriginalName();
            $image = $request->file('image')->storeAs('images/categories', $originalFileName, 'public');
            $category->image = 'storage/' . $image;
        }

        $category->save();

        return response()->json([
            'success' => 1,
            'message' => __('messages.category_created_successfully'),
            'result' => $category
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image'
            ]);

            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $category->image = $request->file('image')->store('images/categories');
            }

            $category->save();

            return redirect()->route('categories.index')->with('success', 'Category updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the category.');
        }
    }

    public function destroy(Request $request)
    {

        $category = Category::find($request->id);
        if ($category) {
            $category->products()->delete();

            $category->delete();
        } else {
            return response()->json([
                'success' => 0,
                'message' => __('messages.category_not_found'),
                'result' => []
            ]);
        }

        return response()->json([
            'success' => 1,
            'message' => __('messages.category_deleted_successfully'),
            'result' => []
        ], 200);
    }


    public function getCategories()
    {

        try {
            $categories = Category::all();
            return response()->json([
                'success' => 1,
                'result' => $categories,
                'message' => __('messages.categories_retrieved_successfully'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => __('messages.error_retrieving_categories'),
                'result' => []
            ], 500);
        }
    }
}