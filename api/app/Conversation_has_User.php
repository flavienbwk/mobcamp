<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation_has_User extends Model {

    protected $table = "conversation_has_user";
    public $timestamps = false;
    protected $fillable = [
        "User_id",
        "Conversation_id"
    ];
    protected $casts = [
        'added_at' => 'datetime',
    ];

}
