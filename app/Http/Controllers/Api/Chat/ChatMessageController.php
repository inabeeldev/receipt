<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Message;
use App\Models\ChatGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ChatMessageController extends Controller
{
    public function send(Request $request, ChatGroup $group)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        // dd($group->user_id);

        $user = $request->user(); // Assuming you're using authentication
         dd($user->id);

        // Check if the user is the owner of the group
        if ($user->id != $group->user_id) {
            return response()->json(['error' => 'You are not allowed to send messages to this group.'], 403);
        }

        // Alternatively, if you want to check if the user has accepted an invitation
        // and is part of the group, you can modify this check
        if (!$user->invitations()->where('chat_group_id', $group->id)->where('accepted', true)->exists()) {
            return response()->json(['error' => 'You are not allowed to send messages to this group.'], 403);
        }

        $message = Message::create([
            'chat_group_id' => $group->id,
            'user_id' => $user->id,
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => $message], 201);
    }

    public function show(ChatGroup $group)
    {
        $messages = $group->messages()->with('user')->get();

        return response()->json(['messages' => $messages]);
    }
}
