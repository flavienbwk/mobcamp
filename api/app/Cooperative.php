<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model {

    protected $table = "Cooperative";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'geolocation', 'lang'
    ];

}

