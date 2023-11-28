<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'title', 'description', 'price', 'stock_count', 'image','business_type'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
