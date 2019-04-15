<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserItem extends Model
{

    protected $table = "user_item";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'cooperative_id',
        'message',
        'price',
        'quantity',
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
