<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptProduct extends Model
{
    use HasFactory;
    protected $fillable = ['receipt_id', 'product_id', 'product_name', 'product_price', 'product_qty'];


    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
