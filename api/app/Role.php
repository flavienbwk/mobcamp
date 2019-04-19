<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    protected $table = "role";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

}

