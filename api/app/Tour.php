<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{

    protected $table = "tour";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'active',
        'cooperative_id',
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
