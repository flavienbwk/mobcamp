<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = "certificate";
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'formation_id',
        'user_id',
        'cooperative_id'
    ];
    
    public static function getAll() {
        return self::all();
    }
}
