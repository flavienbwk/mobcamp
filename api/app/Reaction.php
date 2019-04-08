<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model {

    protected $table = "reaction";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "image_uri"
    ];

}
