<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model 
{

    protected $table = "notification";
    
    public $timestamps = false;
    
    protected $fillable = [
        "message",
        "Publication_id",
        "Target_User_id",
        "seen",
        "User_id"
    ];
    
    protected $casts = [
        'added_at' => 'datetime',
    ];

}
