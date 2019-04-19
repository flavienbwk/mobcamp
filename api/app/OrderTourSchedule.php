<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTourSchedule extends Model
{

    protected $table = "order_tour_schedule";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'cooperative_id',
        'tour_id',
        'schedule_id',
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
