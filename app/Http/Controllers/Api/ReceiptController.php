<?php

namespace App\Http\Controllers\Api;

use App\Models\Kiosk;
use App\Models\Salon;
use App\Models\CarWash;
use App\Models\Courier;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Cosmetic;
use App\Models\Mechanic;
use App\Models\Furniture;
use App\Models\TaxiService;
use Illuminate\Support\Str;
use App\Models\GeneralStore;
use Illuminate\Http\Request;
use App\Models\ReceiptProduct;
use App\Models\CateringService;
use App\Models\MobileMoneyAgency;
use App\Models\RestaurantService;
use App\Http\Controllers\Controller;
use App\Models\HouseLocationService;
use App\Models\LongDistanceTransport;

class ReceiptController extends Controller
{

    public function showReceipts()
    {
        $user = auth()->user();

        // Retrieve all receipts for the authenticated user with related data
        $receipts = $user->receipts()
            ->with('receiptProducts', 'detailable') // Eager load related models
            ->get();

        return response()->json(['receipts' => $receipts]);
    }

    public function getReceipt($receiptId)
    {
        $user = auth()->user();

        // Retrieve the specific receipt for the authenticated user with related data
        $receipt = $user->receipts()
            ->with('receiptProducts', 'detailable') // Eager load related models
            ->find($receiptId);

        if ($receipt) {
            return response()->json(['receipt' => $receipt]);
        } else {
            return response()->json(['message' => 'Receipt not found.'], 404);
        }
    }


    public function generateReceipt(Request $request)
    {
        // Assuming you receive product_ids and quantities in the request
        $productIds = $request->input('product_ids');
        $quantities = $request->input('quantities');

        // Validate input or handle errors as needed

        // Create a new receipt
        $receipt = new Receipt();
        $receipt->user_id = auth()->id(); // Assuming you have authentication
        $receipt->receipt_number = uniqid(); // You might want to generate a unique receipt number
        $receipt->total_price = 0;
        $receipt->total_tax = 0;
        $receipt->payment_method = $request->input('payment_method');
        $receipt->message = $request->input('message');
        $receipt->save();

        // Calculate total price and tax
        $totalPrice = 0;
        $totalTax = 0;

        // Attach products to the receipt
        foreach ($productIds as $key => $productId) {
            $quantity = $quantities[$key];

            $product = Product::find($productId);

            if ($product) {
                $totalPrice += $product->price * $quantity;

                $receiptProduct = ReceiptProduct::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $productId,
                    'product_name' => $product->title,
                    'product_price' => $product->price,
                    'product_qty' => $quantity,
                ]);

                $totalTax += ($product->price * $quantity * $product->tax_percentage) / 100;

                $receipt->receiptProducts()->save($receiptProduct);
            }
        }

        // Update the receipt with total price and total tax
        $receipt->total_price = $totalPrice;
        $receipt->total_tax = $totalTax;
        $receipt->save();

        $this->handleBusinessTypeDetails($receipt, $request);


        return response()->json(['message' => 'Receipt generated successfully', 'receipt' => $receipt]);
    }



    private function handleBusinessTypeDetails(Receipt $receipt, Request $request)
    {
        $businessType = auth()->user()->business_type;

        switch ($businessType) {
            case 'house_location_service':
                $house_location_service = HouseLocationService::create([
                    'receipt_id' => $receipt->id,
                    'order_number' => $request->input('order_number'),
                    'house_location' => $request->input('house_location'),
                ]);
                $receipt->detailable()->associate($house_location_service)->save();
                break;

            case 'catering_service':
                $catering_service = CateringService::create([
                    'receipt_id' => $receipt->id,
                    'service_description' => $request->input('service_description')
                ]);
                $receipt->detailable()->associate($catering_service)->save();
                break;

            case 'car_wash':
                $car_wash = CarWash::create([
                    'receipt_id' => $receipt->id,
                    'service_description' => $request->input('service_description')
                ]);
                $receipt->detailable()->associate($car_wash)->save();
                break;

            case 'taxi_service':
                $taxi_service = TaxiService::create([
                    'receipt_id' => $receipt->id,
                    'trip_number' => $request->input('trip_number'),
                    'driver_name' => $request->input('driver_name'),
                    'driver_photo' => $request->input('driver_photo'),
                    'distance_travel' => $request->input('distance_travel'),
                    'trip_duration' => $request->input('trip_duration')
                ]);
                $receipt->detailable()->associate($taxi_service)->save();
                break;

            case 'mechanic':
                $mechanic = Mechanic::create([
                    'receipt_id' => $receipt->id,
                    'invoice_number' => $request->input('invoice_number'),
                    'repairs_description' => $request->input('repairs_description')
                ]);
                $receipt->detailable()->associate($mechanic)->save();
            break;

            case 'long_distance_transport':
                $long_distance_transport = LongDistanceTransport::create([
                    'receipt_id' => $receipt->id,
                    'reservation_number' => $request->input('reservation_number'),
                    'driver_name' => $request->input('driver_name'),
                    'license_plate_number' => $request->input('license_plate_number'),
                    'journey_duration' => $request->input('journey_duration'),
                    'ticket_price' => $request->input('ticket_price'),
                ]);
                $receipt->detailable()->associate($long_distance_transport)->save();
            break;

            case 'restaurant_service':
                $restaurant_service = RestaurantService::create([
                    'receipt_id' => $receipt->id,
                    'order_number' => $request->input('order_number'),
                    'customer_name' => $request->input('customer_name'),
                    'order_description' => $request->input('order_description')
                ]);
                $receipt->detailable()->associate($restaurant_service)->save();
                break;

            case 'kiosk':
                $kiosk = Kiosk::create([
                    'receipt_id' => $receipt->id,
                    'purchased_item_description' => $request->input('purchased_item_description')
                ]);
                $receipt->detailable()->associate($kiosk)->save();
                break;

            case 'salon':
                $salon = Salon::create([
                    'receipt_id' => $receipt->id,
                    'reservation_number' => $request->input('reservation_number'),
                    'customer_name' => $request->input('customer_name'),
                    'service_provided' => $request->input('service_provided')
                ]);
                $receipt->detailable()->associate($salon)->save();
                break;

            case 'general_store':
                $general_store = GeneralStore::create([
                    'receipt_id' => $receipt->id,
                    'transaction_number' => $request->input('transaction_number'),
                    'product_description' => $request->input('product_description')
                ]);
                $receipt->detailable()->associate($general_store)->save();
                break;

            case 'courier':
                $courier = Courier::create([
                    'receipt_id' => $receipt->id,
                    'customer_name' => $request->input('customer_name'),
                    'delivery_address' => $request->input('delivery_address'),
                    'order_description' => $request->input('order_description'),
                    'delivery_fee' => $request->input('delivery_fee'),
                ]);
                $receipt->detailable()->associate($courier)->save();
                break;

            case 'cosmetics':
                $cosmetics = Cosmetic::create([
                    'receipt_id' => $receipt->id,
                    'order_number' => $request->input('order_number'),
                    'product_description' => $request->input('product_description')
                ]);
                $receipt->detailable()->associate($cosmetics)->save();
                break;

            case 'furniture':
                $furniture = Furniture::create([
                    'receipt_id' => $receipt->id,
                    'order_number' => $request->input('order_number'),
                    'furniture_description' => $request->input('furniture_description'),
                    'rent_duration' => $request->input('rent_duration')
                ]);
                $receipt->detailable()->associate($furniture)->save();
                break;

            case 'mobile_money_agency':
                $mobile_money_agency = MobileMoneyAgency::create([
                    'receipt_id' => $receipt->id,
                    'transaction_type' => $request->input('transaction_type'),
                    'chosen_product' => $request->input('chosen_product'),
                    'customer_mobile' => $request->input('customer_mobile'),
                    'transaction_amount' => $request->input('transaction_amount'),
                    'withdrawals_fee' => $request->input('withdrawals_fee'),
                    'wave_transaction' => $request->input('wave_transaction'),
                ]);
                $receipt->detailable()->associate($mobile_money_agency)->save();
                break;
        }
    }
}
