<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model {

    protected $table = "connection";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'ip', 'User_id', 'expires_at', 'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

}
