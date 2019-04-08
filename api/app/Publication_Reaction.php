<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publication_Reaction extends Model
{

    protected $table = "publication_reaction";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "Publication_id",
        "Reaction_id",
        "User_id"
    ];
}
