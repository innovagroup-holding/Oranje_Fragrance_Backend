<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return view('categories.index', compact('categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching categories.');
        }
    }
    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image'
            ]);

            $category = new Category;
            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $category->image = $request->file('image')->store('images/categories');
                // $path = $request->file('image')->store('categories', 'public');
            }

            $category->save();

            return redirect()->route('categories.index')->with('success', 'Category created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while creating the category.');
        }
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

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the category.');
        }
    }

    public function getCategories()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'success' => true,
                'result' => $categories,
                'message' => 'Categories retrieved successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving categories',
                'result' => []
            ], 500);
        }
    }
}