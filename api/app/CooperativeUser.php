<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CooperativeUser extends Model {

    protected $table = "cooperative_user";
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'cooperative_id'
    ];

}

