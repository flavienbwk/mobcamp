<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {

    protected $table = "subscription";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "Subscribed_User_id",
        "Subscriber_User_id"
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'subscribed_at' => 'datetime',
    ];

}
