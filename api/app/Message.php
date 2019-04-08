<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {

    protected $table = "message";
    public $timestamps = false;
    protected $fillable = [
        "value",
        "Conversation_id",
        "User_id"
    ];
    protected $casts = [
        'added_at' => 'datetime',
    ];

}
