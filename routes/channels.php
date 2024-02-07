<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat.{userId}', function ($user) {
    if (auth()->check()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
});

Broadcast::channel('online-users', function ($user) {
    // Ensure only authenticated users can listen to this channel
    if (auth()->check()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
});


