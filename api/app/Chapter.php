<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $table = "chapter";
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name',
        'type',
        'content', 
        'order',
        'formation_id'
    ];
    
    public static function getAll() {
        return self::all();
    }
}
