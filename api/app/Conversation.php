<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = "conversation";
    
    public $timestamps = false;
    
    protected $fillable = [
        "name",
        "User_id"
    ];
}
