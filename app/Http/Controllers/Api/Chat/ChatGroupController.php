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

        $group->users()->attach($request->user()->id);

        return response()->json(['group' => $group], 201);
    }

    public function show(ChatGroup $group)
    {
        // Load the owner, messages, and accepted invitations related to the group
        $group->load(['owner','users','invitations','messages']);

        // Get the count of users in the group with accepted invitations
        $userCount = $group->users->count();


        // Append the 'no_of_participant' attribute to the $group object
        $group->no_of_participant = $userCount;

        // Prepare the response data
        $responseData = [
            'group' => $group
        ];

        return response()->json($responseData);
    }

    public function leaveGroup(Request $request, ChatGroup $group)
    {
        // Check if the user is a member of the group
        if (!$group->users->contains($request->user())) {
            return response()->json(['error' => 'You are not a member of this group.'], 403);
        }

        // Check if the user is the owner of the group
        if ($request->user()->id === $group->user_id) {
            return response()->json(['error' => 'Group owner cannot leave the group.'], 403);
        }

        // Remove the user from the group
        $group->users()->detach($request->user()->id);

        // Delete the corresponding invitation (assuming a user can have multiple invitations)
        $group->invitations()->where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'You have left the group successfully.']);
    }

}
