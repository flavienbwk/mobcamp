<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $table = "formation";
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name', 
        'estimated_duration',
        'level',
        'cooperative_id',
        'local_uri'
    ];
}
