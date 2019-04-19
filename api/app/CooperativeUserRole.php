<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CooperativeUserRole extends Model {

    protected $table = "cooperative_user_role";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 
        'user_id', 
        'cooperative_id', 
        'created_at',
        'updated_at',
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


