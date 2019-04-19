<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaChapter extends Model {

    protected $table = "media_chapter";
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chapter_id', 
        'media_id' 
    ];
}