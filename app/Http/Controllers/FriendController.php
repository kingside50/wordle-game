<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Friend;
use App\Models\User;

class FriendController extends Controller
{
    public function add(Request $request)
    {
        $friendEmail = $request->input('friend_email');
        $friend = User::where('email', $friendEmail)->first();

        if ($friend) {
            Friend::create([
                'user_id' => Auth::id(),
                'friend_id' => $friend->id,
            ]);
            return back()->with('success', 'Friend added successfully.');
        }

        return back()->with('error', 'User not found.');
    }
}
