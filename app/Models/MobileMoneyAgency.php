<?php

namespace App\Models;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileMoneyAgency extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'detailable');
    }
}
