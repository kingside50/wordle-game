<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'friend_email' => 'required|email|exists:users,email',
        ]);

        $friend = User::where('email', $request->input('friend_email'))->first();

        // Controleer of de vriendrelatie al bestaat
        if ($friend && !Auth::user()->friends()->where('friend_id', $friend->id)->exists()) {
            Friend::create([
                'user_id' => Auth::id(),
                'friend_id' => $friend->id,
            ]);

            return back()->with('success', 'Friend added successfully.');
        }

        return back()->with('error', 'User not found or already added as a friend.');
    }
}