<?php

use App\Models\Room;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.rooms.{roomId}', function ($user, $roomId) {
    return [
        'user' => $user,
        'room' => Room::find($roomId)
    ];
});
