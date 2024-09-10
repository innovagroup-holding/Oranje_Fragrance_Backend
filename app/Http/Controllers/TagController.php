<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Exception;

class TagController extends Controller
{
    public function index()
    {
        try {
            $tags = Tag::all();
            return view('tags.index', compact('tags'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching tags.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image'
            ]);

            $tag = new Tag;
            $tag->name = $request->name;

            if ($request->hasFile('image')) {
                $tag->image = $request->file('image')->store('images/tags');
            }

            $tag->save();

            return redirect()->route('tags.index')->with('success', 'Tag created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while creating the tag.');
        }
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

    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();
            return redirect()->route('tags.index')->with('success', 'Tag deleted successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the tag.');
        }
    }

    public function getTags()
    {
        try {
            $tags = Tag::all();
            return response()->json([
                'success' => true,
                'result' => $tags,
                'message' => __('messages.tags_retrieved_successfully'),

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_retrieving_tags'),
                'result' => []
            ], 500);
        }
    }
}