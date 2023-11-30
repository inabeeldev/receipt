<?php

namespace App\Providers;

use App\Models\CarWash;
use App\Models\CateringService;
use App\Models\Cosmetic;
use App\Models\Courier;
use App\Models\Furniture;
use App\Models\GeneralStore;
use App\Models\HouseLocationService;
use App\Models\Kiosk;
use App\Models\LongDistanceTransport;
use App\Models\Mechanic;
use App\Models\MobileMoneyAgency;
use App\Models\RestaurantService;
use App\Models\Salon;
use App\Models\TaxiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Relation::morphMap([
            'mobile_money_agency' => MobileMoneyAgency::class,
            'house_location_service' => HouseLocationService::class,
            'catering_service' => CateringService::class,
            'car_wash' => CarWash::class,
            'taxi_service' => TaxiService::class,
            'mechanic' => Mechanic::class,
            'long_distance_transport' => LongDistanceTransport::class,
            'restaurant_service' => RestaurantService::class,
            'kiosk' => Kiosk::class,
            'salon' => Salon::class,
            'general_store' => GeneralStore::class,
            'courier' => Courier::class,
            'cosmetics' => Cosmetic::class,
            'furniture' => Furniture::class,
            // Add other mappings as needed
        ]);
    }
}
