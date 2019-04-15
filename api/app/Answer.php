<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {

    protected $table = "answer";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'quizz_id',
        'question_id',
        'is_correct'
    ];
}

