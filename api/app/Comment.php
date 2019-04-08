<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comment";
    
    public $timestamps = false;
    
    protected $fillable = [
        "text",
        "added_at",
        "Publication_id",
        "User_id"
    ];
    
    protected $casts = [
        'added_at' => 'datetime',
    ];
}
