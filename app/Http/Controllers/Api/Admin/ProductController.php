<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $products = Product::all();
        return response()->json(['products' => $products], 200);
    }

    // Get a specific product
    public function show(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product], 200);
    }

    // Create a new product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'admin_id' => 'required|exists:admins,id',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'business_type' => ['required', 'string', 'in:house_location_service,catering_service,car_wash,taxi_service,mechanic,long_distance_transport,restaurant_service,kiosk,salon,general_store,courier,cosmetics,furniture,mobile_money_agency'],
            'stock_count' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'business_type.in' => 'The business type must be one of the following: house_location_service, catering_service, car_wash, taxi_service, mechanic, long_distance_transport, restaurant_service, kiosk, salon, general_store, courier, cosmetics, furniture, mobile_money_agency.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $imagePath  = null;

        if (!empty($request->file('image'))) {
            $imagePath = $request->file('image')->store('product/images');
            $imagePath = str_replace('product/', '', $imagePath);
        }

        $product = Product::create([
            'admin_id' => $request->input('admin_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock_count' => $request->input('stock_count'),
            'image' => $imagePath,
            'business_type' => $request->input('business_type'),
        ]);

        return response()->json(['product' => $product], 201);
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'admin_id' => 'exists:admins,id',
            'title' => 'string',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'stock_count' => 'integer|min:0',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'business_type' => ['string', 'in:house_location_service,catering_service,car_wash,taxi_service,mechanic,long_distance_transport,restaurant_service,kiosk,salon,general_store,courier,cosmetics,furniture,mobile_money_agency'],
        ], [
            'business_type.in' => 'The business type must be one of the following: house_location_service, catering_service, car_wash, taxi_service, mechanic, long_distance_transport, restaurant_service, kiosk, salon, general_store, courier, cosmetics, furniture, mobile_money_agency.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $product = Product::findOrFail($id);

        // Delete previous image
        if ($request->hasFile('image')) {
            Storage::delete('product/' . $product->image);
            // Handle file upload
            $imagePath = $request->file('image')->store('product/images');
            $imagePath = str_replace('product/', '', $imagePath);
            $product->image = $imagePath;
        }

        // Update product details
        $product->update([
            'admin_id' => $request->input('admin_id', $product->admin_id),
            'title' => $request->input('title', $product->title),
            'description' => $request->input('description', $product->description),
            'price' => $request->input('price', $product->price),
            'stock_count' => $request->input('stock_count', $product->stock_count),
            'business_type' => $request->input('business_type', $product->business_type),
        ]);

        return response()->json(['product' => $product], 200);
    }

    // Delete a product
    public function destroy(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
