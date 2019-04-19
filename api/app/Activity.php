<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = "activity";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'chapter_id'
    ];
}
