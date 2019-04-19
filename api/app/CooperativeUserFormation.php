<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CooperativeUserFormation extends Model {

    protected $table = "cooperative_user_formation";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'cooperative_id', 
        'formation_id',
        'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}


