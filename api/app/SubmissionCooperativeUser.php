<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmissionCooperativeUser extends Model
{
    protected $table = "submission_cooperative_user";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'submission_id',
        'formation_id',
        'activity_id',
        'user_id',
        'corrector_user_id',
        'cooperative_id',
        'is_validated',
        'message',
        'grade'
    ];
}
