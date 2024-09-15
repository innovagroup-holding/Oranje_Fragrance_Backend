<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function getVideos()
    {
        $videos = Video::all();

        $videoLinks = $videos->map(function ($video) {
            return asset($video->path);
        });

        return response()->json([
            'success' => 1,
            'result' => $videoLinks,
            'message' => __('messages.success'),
        ]);
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,mov,ogg,avi|max:20000',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('video')) {

            $originalFileName = $request->file('video')->getClientOriginalName();


            $videoPath = $request->file('video')->storeAs('videos', $originalFileName, 'public');

            // $videoPath = $request->file('video')->storeAs('videos', $originalFileName);

            $video = Video::create([
                'title' => $request->title,
                'path' => 'storage/' . $videoPath,
            ]);
            return response()->json([
                'success' => 1,
                'result' => $video,
                'message' => __('messages.success'),
            ]);
        }
        return response()->json([
            'success' => 0,
            'result' => [],
            'message' => __('messages.failed uploded'),
        ], 400);
    }
}
