<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormationItem extends Model
{

    protected $table = "item";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity',
        'message',
        'item_id',
        'formation_id',
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
