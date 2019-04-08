<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    protected $table = "avatar";
    
    public $timestamps = false;
    
    protected $fillable = [
        "local_uri",
        "User_id"
    ];
    
    protected $casts = [
        'added_at' => 'datetime',
    ];
    
}
