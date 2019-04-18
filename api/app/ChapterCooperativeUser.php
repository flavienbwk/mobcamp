<?php

namespace App;

use \Illuminate\Database\Eloquent\Model;

class ChapterCooperativeUser extends Model
{
    protected $table = "chapter_cooperative_user";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chapter_id',
        'user_id',
        'cooperative_id',
        'is_achieved' 
    ];
    
    public static function getAll() {
        return self::all();
    }
}