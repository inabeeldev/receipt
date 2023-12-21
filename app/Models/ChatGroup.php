<?php

namespace App\Models;

use App\Models\User;
use App\Models\Message;
use App\Models\ChatInvitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name','user_id','description','image'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_chat_groups', 'chat_group_id', 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invitations()
    {
        return $this->hasMany(ChatInvitation::class, 'chat_group_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_group_id');
    }
}
