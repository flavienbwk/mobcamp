<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmissionMedia extends Model
{
    protected $table = "submission_media";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'submission_id',
        'media_id',
    ];
}
