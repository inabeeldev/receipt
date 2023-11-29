<?php

namespace App\Models;

use App\Models\User;
use App\Models\Admin;
use App\Models\AdminChat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminMessage extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
