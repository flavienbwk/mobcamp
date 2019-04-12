<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserItemCooperative extends Model
{

    protected $table = "user_item_cooperative";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'cooperative_id',
    ];
}
