<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = [
        'text',
        'user_id',
        'room_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
