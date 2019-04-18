<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderUserItem extends Model
{

    protected $table = "order_user_item";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'cooperative_id',
        'user_item_id',
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
