<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\ChatGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ChatGroupController extends Controller
{
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = $request->user(); // Assuming you're using authentication
        $group = $user->ownedGroups()->create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['group' => $group], 201);
    }

    public function show(ChatGroup $group)
    {
        // Load the users and messages related to the group
        $group->load('users', 'messages');

        return response()->json(['group' => $group]);
    }
}
