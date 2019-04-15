<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $table = "schedule";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from',
        'to'
    ];
}
