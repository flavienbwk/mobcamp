<?php

namespace App\Repositories;

use App\User;
use App\Cooperative;
use App\CooperativeUser;

class UserRepository {

    public static function inCooperative($user_id, $cooperative_id) {
        $role = Cooperative::select("cooperative.id")
                        ->join("cooperative_user", "cooperative_user.role_id", "=", "cooperative.id")
                        ->where([
                            ["cooperative_user.user_id", $user_id],
                            ["cooperative_user.cooperative_id", $cooperative_id],
                        ])
                        ->get();
        if ($role) {
            return true;
        } else {
            return false;
        }
    }
    
}
