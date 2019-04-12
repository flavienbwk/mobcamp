<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemMedia extends Model
{

    protected $table = "item_media";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'media_id'
    ];
    
}
