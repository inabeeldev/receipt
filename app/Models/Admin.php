<?php

namespace App\Models;

use App\Models\Product;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password','auth_token','image','contact','role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public function sentMessages()
    {
        return $this->hasMany(AdminMessage::class, 'admin_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'admin_id');
    }

}
