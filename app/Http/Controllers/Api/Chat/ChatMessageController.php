<?php

namespace App\Http\Controllers\Api\Chat;

use Log;
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

    $user = $request->user(); // Assuming you're using authentication
    // dd($group->invitations()->where('accepted', true));
    $acceptedUserIds = $group->invitations()->where('accepted', true)->pluck('user_id')->toArray();
    // dd($acceptedUserIds);
    if (!in_array($user->id, $acceptedUserIds) && $user->id !== $group->user_id) {
        return response()->json(['error' => 'You are not allowed to send messages to this group.'], 403);
    }

    // Continue with sending the message
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
