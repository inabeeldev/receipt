<?php

namespace App\Models;

use App\Models\User;
use App\Models\ReceiptProduct;
use App\Models\MobileMoneyAgency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'receipt_number', 'total_price', 'total_tax', 'payment_method', 'message', 'is_issued','detailable_id','detailable_type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receiptProducts()
    {
        return $this->hasMany(ReceiptProduct::class, 'receipt_id');
    }

    public function detailable(): MorphTo
    {
        return $this->morphTo();
    }

    // public function detailable()
    // {
    //     return $this->morphTo();
    // }

}
