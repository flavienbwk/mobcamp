<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use \Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use Notifiable;
    protected $table = "formation";
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name', 
        'estimated_duration',
        'level',
        'cooperative_id',
        'local_uri'
    ];
    
    public static function getAll() {
        return self::all();
    }
}
