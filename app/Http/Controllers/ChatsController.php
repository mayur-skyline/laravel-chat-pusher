<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use App\Events\OnlineUsersUpdated;

class ChatsController extends Controller
{
    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chat');
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages($id)
    {
        $updatedOnlineUsers = Auth::user();

        broadcast(new OnlineUsersUpdated($updatedOnlineUsers))->toOthers();

        return Message::with('user')->whereIN('sender_user_id',[auth()->user()->id,$id])->whereIN('receiver_user_id',[$id,auth()->user()->id])->orderBy('created_at','ASC')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = Message::create([
            'sender_user_id' => auth()->user()->id,
            'receiver_user_id' => $request->receiver_user_id,
            'message' => $request->message
        ]);

        broadcast(new MessageSent($user, $message));

        return ['status' => 'Message Sent!'];
    }

    public function chatList()
    {
       $users = User::get();
       return $users;
    }
}