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

        $imagePath = null;

        if (!empty($request->file('image'))) {
            $imagePath = $request->file('image')->store('group/image');
            $imagePath = str_replace('group/', '', $imagePath);
        }

        $user = $request->user(); // Assuming you're using authentication
        $group = $user->ownedGroups()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imagePath,
        ]);

        return response()->json(['group' => $group], 201);
    }

    public function show(ChatGroup $group)
    {
        // Load the owner, messages, and accepted invitations related to the group
        $group->load(['owner', 'messages', 'invitations' => function ($query) {
            $query->where('accepted', true);
        }]);

        // Get the count of users in the group with accepted invitations
        $userCount = $group->invitations->count();
        if ($group->owner) {
            $userCount++;
        }

        // Append the 'no_of_participant' attribute to the $group object
        $group->no_of_participant = $userCount;

        // Prepare the response data
        $responseData = [
            'group' => $group
        ];

        return response()->json($responseData);
    }

}
