<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publication_Photo extends Model {

    protected $table = "publication_photo";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "Publication_id",
        "Photo_id",
        "order"
    ];

}
