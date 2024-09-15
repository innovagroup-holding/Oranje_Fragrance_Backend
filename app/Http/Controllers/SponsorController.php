<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;
use Exception;


class SponsorController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->hasFile('logo')) {

            $originalFileName = $request->file('logo')->getClientOriginalName();


            $logoPath = $request->file('logo')->storeAs('images/sponsors', $originalFileName, 'public');


            $sponsor = Sponsor::create([
                'name' => $request->name,
                'logo' => 'storage/' . $logoPath,
            ]);

            return response()->json([
                'success' => 1,
                'result' => $sponsor,
                'message' => __('messages.success'),
            ]);
        }

        return response()->json([
            'success' => 0,
            'result' => [],
            'message' => __('messages.failed'),
        ]);
    }


    public function getSponsors()
    {

        try {
            $sponsors = Sponsor::all();
            return response()->json([
                'success' => 1,
                'result' => $sponsors,
                'message' => __('messages.success'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => __('messages.failed'),
                'result' => []
            ], 500);
        }
    }
}
