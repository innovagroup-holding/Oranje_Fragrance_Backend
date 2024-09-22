<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function addToCart(Request $request)
    {
        $user = null;
        $token = null;


        if ($request->bearerToken()) {
            try {
                $userId = auth()->user()->id;
            } catch (\Exception $e) {
                error_log('Invalid token: ' . $e->getMessage());
                return response()->json([
                    'success' => 0,
                    'message' => 'Invalid authentication token.'
                ], 401);
            }
        }
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $totalOrderPrice = 0;


        $order = Order::create([
            'user_id' => $userId,
            'total_price' => 0,
            'tax' => 0,
            'price_after_tax' => 0,
            'shipping_cost' => 0,
            'price_after_shipping' => 0,
        ]);

        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            $pricePerUnit = $product->discount_percentage
                ? $product->price_after_discount
                : $product->price;

            $totalProductPrice = $pricePerUnit * $item['quantity'];
            $totalOrderPrice += $totalProductPrice;

            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $pricePerUnit,
                'total_price' => $totalProductPrice
            ]);
        }


        $taxAmount = ($totalOrderPrice * 20) / 100;
        $priceAfterTax = $totalOrderPrice + $taxAmount;
        $shippingCost = 0;
        $priceAfterShipping = $priceAfterTax + $shippingCost;

        $order->update([
            'total_price' => $totalOrderPrice,
            'tax' => 20,
            'price_after_tax' => $priceAfterTax,
            'shipping_cost' => $shippingCost,
            'price_after_shipping' => $priceAfterShipping,
        ]);

        return response()->json([
            'success' => 1,
            'message' => __('messages.order_created_successfully'),
            'result' => $order->load('orderProducts'),

        ], 201);
    }
}