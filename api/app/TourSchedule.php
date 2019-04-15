<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{

    protected $table = "tour_schedule";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tour_id',
        'schedule_id',
        'place',
        'active',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
