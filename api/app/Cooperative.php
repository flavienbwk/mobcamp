<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model {

    protected $table = "cooperative";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'geolocation',
        'lang',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
