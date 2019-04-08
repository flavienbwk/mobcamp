<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use Notifiable;
    protected $table = "publication";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ids', 'description', 'geolocation', 'User_id',
    ];
    
    public static function getAll() {
        return self::all();
    }
    
    public static function getById($mode, $model_id) {
        $records = self::where(["id" => $model_id])->orderBy('created_at', 'ASC');
        return $records;
    }
    
    public static function getByIds($mode, $model_ids) {
        $records = self::where(["ids" => $model_ids])->orderBy('created_at', 'ASC');
        return $records;
    }
    
}
