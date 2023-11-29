<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Http\Request;
use App\Models\ChatInvitation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ChatInvitationController extends Controller
{
    public function send(Request $request, ChatGroup $group)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $userToInvite = User::findOrFail($request->input('user_id'));

        // Check if the user is the owner of the group
        if ($request->user()->id !== $group->user_id) {
            return response()->json(['error' => 'You are not the owner of this group.'], 403);
        }

        // Check if the user is already a member of the group
        if ($group->users->contains($userToInvite)) {
            return response()->json(['error' => 'User is already a member of the group.'], 400);
        }

        // Check if there is already a pending invitation for the user
        if ($group->invitations()->where('user_id', $userToInvite->id)->exists()) {
            return response()->json(['error' => 'Invitation already sent to this user.'], 400);
        }

        $invitation = ChatInvitation::create([
            'chat_group_id' => $group->id,
            'user_id' => $userToInvite->id,
            'accepted' => false,
            'group_owner_id' => $group->user_id,
        ]);

        return response()->json(['invitation' => $invitation], 201);
    }

    public function accept(ChatInvitation $invitation)
    {
        $invitation->update(['accepted' => true]);

        // Perform any additional actions if needed

        return response()->json(['message' => 'Invitation accepted successfully.']);
    }

    public function show(ChatInvitation $invitation)
    {
        // Load additional information if needed
        $invitation->load('group', 'user');

        return response()->json(['invitation' => $invitation]);
    }

}
