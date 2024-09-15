<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'number' => 'required|string|max:20',
            'agreed_privacy_policy' => 'required|boolean',
            'agreed_terms_of_use' => 'required|boolean',
            'password' => 'required|string|min:8',
            'house_number' => 'required|string|max:10',
            'post_code' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'country' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'number' => $request->input('number'),
                'post_code' => $request->input('post_code'),
                'country' => $request->input('country'),
                'street' => $request->input('street'),
                'location' => $request->input('location'),
                'house_number' => $request->input('house_number'),
                'agreed_privacy_policy' => $request->input('agreed_privacy_policy'),
                'agreed_terms_of_use' => $request->input('agreed_terms_of_use'),
            ]);
            $token = $user->createToken('API Token')->accessToken;
            $res = array(
                'user' => $user,
                'token' => $token,
            );

            return response()->json([
                'success' => 1,
                'result' => $res,
                'message' => __('messages.signup_success'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => __('messages.some_thing_worng'),
                'result' => []
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'result' => [],
                'message' => $validator->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => 0,
                'result' => [],
                'message' => __('messages.user_not_found'),
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => 0,
                'result' => [],
                'message' => __('messages.failed'),
            ], 401);
        }

        $token = $user->createToken('authToken')->accessToken;

        $res = ['token' => $token, 'user' => $user];

        return response()->json([
            'success' => 1,
            'result' => $res,
            'message' => __('messages.login_success'),
        ], 200);
    }


    public function logout()
    {
        try {
            auth()->user()->token()->revoke();
            return response()->json([
                'success' => 1,
                'message' => __('messages.successfully_logged_out'),
                'result' => []
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => __('messages.some_thing_wrong'),
                'result' => []
            ], 500);
        }
    }


    public function getUser(Request $request)
    {
        $user = null;

        $token = $request->bearerToken();

        if ($token) {
            try {
                $user = auth()->user();
            } catch (\Exception $e) {
                error_log('Invalid token: ' . $e->getMessage());
            }
        }
        $recommendedProducts = Product::with('tags')
            ->inRandomOrder()
            ->limit(5)
            ->get();
        $res = ['recommendedProducts' => $recommendedProducts, 'user' => $user];
        return response()->json([
            'success' => 1,
            'result' => $res,
            'message' => __('messages.success'),
        ], 200);
    }
}