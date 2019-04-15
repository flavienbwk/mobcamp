<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    protected $table = "quizz";
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
