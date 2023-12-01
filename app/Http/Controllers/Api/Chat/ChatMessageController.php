<?php

namespace App\Http\Controllers\Api\Chat;

use Log;
use App\Models\Message;
use App\Models\ChatGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnsavedMessages;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ChatMessageController extends Controller
{
    // public function send(Request $request, ChatGroup $group)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'message' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 400);
    //     }


    //     $user = $request->user();
    //     if (!$group->users->contains($user)) {
    //         return response()->json(['error' => 'You are not a member of this group.'], 403);
    //     }

    //     // Continue with sending the message
    //     $message = Message::create([
    //         'chat_group_id' => $group->id,
    //         'user_id' => $user->id,
    //         'message' => $request->input('message'),
    //     ]);

    //     return response()->json(['message' => $message], 201);
    // }


    public function send(Request $request, ChatGroup $group)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = $request->user();

        if (!$group->users->contains($user)) {
            return response()->json(['error' => 'You are not a member of this group.'], 403);
        }

        // Continue with sending the message
        $message = [
            'chat_group_id' => $group->id,
            'user_id' => $user->id,
            'message' => $request->input('message'),
        ];

        // Store the message in cache for the user
        $users = Cache::get('unsaved_messages_users', []);
        $users[$user->id][] = $message;
        Cache::put('unsaved_messages_users', $users, now()->addMinutes(2));

        // Dispatch the ProcessUnsavedMessages job
        ProcessUnsavedMessages::dispatch()->delay(now()->addMinutes(2));

        return response()->json(['message' => 'Message temporarily stored.'], 201);
    }


    public function show(ChatGroup $group)
    {
        $messages = $group->messages()->with('user')->get();

        return response()->json(['messages' => $messages]);
    }
}
