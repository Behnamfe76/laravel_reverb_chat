<?php

namespace App\Http\Controllers;

use App\Events\ChatAddMessage;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();

        // dd($rooms);
        return Inertia::render('Chat/Index', [
            'rooms' => $rooms,
            'messages' => [],
            'room' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $message = Message::create([
            'user' => $request->user(),
            'text' => $request->text,
            'user_id' => $request->user()->id,
            'room_id' => $request->roomId
        ]);

        broadcast(new ChatAddMessage($request->roomId, $message));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Room $room)
    {
        $rooms = Room::all();
        return Inertia::render('Chat/Index', [
            'user' => $request->user(),
            'messages' => $room->messages()->with('user')->get(),
            'rooms' => $rooms,
            'room' => $room,
        ]);
    }
}
