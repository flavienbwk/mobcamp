<?php

namespace App\Repositories;

use App\User;
use App\Role;
use App\Cooperative;

class CooperativeRepository {

    public static function getUserCooperatives($user_id) {
        return Cooperative::select("cooperative.id", "cooperative.name")
                        ->join("cooperative_user", "cooperative_user.cooperative_id", "=", "cooperative.id")
                        ->where([["cooperative_user.user_id", $user_id]])
                        ->get();
    }

    public static function getUserCooperative($user_id, $cooperative_id) {
        return Cooperative::select("cooperative.id", "cooperative.name")
                        ->join("cooperative_user", "cooperative_user.cooperative_id", "=", "cooperative.id")
                        ->where([
                            ["cooperative_user.user_id", $user_id],
                            ["cooperative_user.cooperative_id", $cooperative_id]
                        ])
                        ->get();
    }

    public static function getUserRoles($user_id, $cooperative_id) {
        return Role::select("role.id", "role.name", "cooperative_user_role.created_at")
                        ->join("cooperative_user_role", "cooperative_user_role.role_id", "=", "role.id")
                        ->where([
                            ["cooperative_user_role.user_id", $user_id],
                            ["cooperative_user_role.cooperative_id", $cooperative_id],
                        ])
                        ->get();
    }

    public static function hasUserRole($user_id, $cooperative_id, $role) {
        return Role::select("role.id", "role.name", "cooperative_user_role.created_at")
                        ->join("cooperative_user_role", "cooperative_user_role.role_id", "=", "role.id")
                        ->where([
                            ["cooperative_user_role.user_id", $user_id],
                            ["cooperative_user_role.cooperative_id", $cooperative_id],
                            ["role.name", $role],
                        ])
                        ->get();
    }

}
