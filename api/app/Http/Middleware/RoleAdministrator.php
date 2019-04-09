<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Connection;
use App\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CooperativeRepository;
use App\Repositories\UserRepository;

class RoleAdministration {
    
    private $_role = "administrateur";

    public function handle($request, Closure $next) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'cooperative_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $role = CooperativeRepository::hasUserRole($User->id, Input::get("cooperative_id"), $this->_role);
            $in_cooperative = UserRepository::inCooperative($User->id, Input::get("cooperative_id"));
            if (!$in_cooperative || !$role)
                $ApiResponse->setErrorMessage("Accès interdit, vous devez être " . $this->_role . " dans la coopérative.");
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 403);
        } else {
            return $next($request);
        }
    }

}
