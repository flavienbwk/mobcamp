<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = "lesson";
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
