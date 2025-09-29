<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        // Get distinct users who have sent messages
        $userIds = Message::select('user_id')->distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get();

        // Initially, no messages are displayed
        $messages = collect();

        $page_title = 'Chat';
        // if usertype is Admin return admin chat view and if usertype is User return user chat view
        if (Auth::user()->usertype === 'Admin') {
              $page_title = 'Messages';
            return view('admin.pages.chat.chat', compact('users', 'messages', 'page_title'));
        } else {
              $page_title = 'Contact';
            return view('pages.user.user_chat', compact('users', 'messages', 'page_title'));
        }
    }

    public function fetchMessages($userId)
    {
        // Get the messages where sender is 'admin' and user_id is $userId, as well as messages from the specified user
        $messages = Message::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere(function ($query) use ($userId) {
                    $query->where('sender', 'admin')
                        ->where('user_id', $userId);
                });
        })
            ->orderBy('created_at')
            ->get();

        return view('admin.pages.chat.partials.messages', compact('messages'))->render();
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        $message = new Message();

        // Check the userType column to determine if the logged-in user is an admin
        if ($user->usertype === 'Admin') {
            $message->user_id = $request->user_id; // Set the user_id from the request
            $message->sender = 'admin'; // Set the sender as 'admin'
            $userId = $request->user_id;
        } else {
            $message->user_id = $user->id; // Set the user_id from the logged-in user
            $message->sender = 'user'; // Set the sender as 'user'
            $userId = $user->id;
        }

        $message->message = $request->messages;
        $message->is_read = false;
        $message->save();


        return redirect()->back()->with('user_id', $userId);
    }
}
