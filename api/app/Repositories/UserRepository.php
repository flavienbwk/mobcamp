<?php

namespace App\Repositories;

use App\User;
use App\Cooperative;
use App\CooperativeUser;

class UserRepository {

    public static function inCooperative($user_id, $cooperative_id) {
        $role = Cooperative::select("cooperative.id")
                ->join("cooperative_user", "cooperative_user.cooperative_id", "=", "cooperative.id")
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

    public static function getIdByIds($user_ids) {
        $user = User::select("user.id")->where("user.ids", $user_ids)->first();
        if ($user) {
            return $user->toArray()["id"];
        } else {
            return false;
        }
    }

}
