<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = "media";
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name', 
        'type',
        'size',
        'downloadable',
        'hash',
        'uri'
    ];
}
