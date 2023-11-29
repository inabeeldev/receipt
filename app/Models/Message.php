<?php

namespace App\Models;

use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'chat_group_id',
        'user_id',
        'message',
    ];


    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
