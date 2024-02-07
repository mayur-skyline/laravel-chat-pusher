<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['message','sender_user_id','receiver_user_id'];

    //Add the below function
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_user_id', 'id');
    }
}
