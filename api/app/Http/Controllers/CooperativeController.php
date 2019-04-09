<?php

namespace App\Http\Controllers;

use App\User;
use App\Connection;
use App\ApiResponse;
use App\Cooperative;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\Authentication;
use App\Repositories\CooperativeRepository;
use App\Repositories\UserRepository;

class CooperativeController extends Controller {

    public function roles(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'cooperative_id' => "required|integer",
                    'user_ids' => "string"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_id = ($request->has('user_ids')) ? UserRepository::getIdByIds(Input::get("user_ids")) : $User->id;
            if ($user_id == $User->id || CooperativeRepository::hasUserRole($user_id, Input::get("cooperative_id"), "administrateur")) {
                $Roles = CooperativeRepository::getUserRoles($user_id, Input::get("cooperative_id"));
                $in_cooperative = UserRepository::inCooperative($user_id, Input::get("cooperative_id"));
                if ($in_cooperative) {
                    if ($Roles) {
                        $ApiResponse->setResponse($Roles->toArray());
                    } else {
                        $ApiResponse->setResponse([]);
                    }
                } else {
                    $ApiResponse->setErrorMessage("Vous n'êtes pas dans cette coopérative.");
                }
            } else {
                $ApiResponse->setErrorMessage("Vous n'êtes pas autorisé à découvrir les rôles.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function cooperatives(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");

        $ApiResponse->setResponse(Cooperative::select("id", "name", "geolocation")->get()->toArray());

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function rolesList(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");

        $ApiResponse->setResponse(Role::select("id", "name")->get()->toArray());

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function userCooperatives(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");

        $cooperatives = CooperativeRepository::getUserCooperatives($User->id);
        if ($cooperatives) {
            $ApiResponse->setResponse($cooperatives->toArray());
        } else {
            $ApiResponse->setErrorMessage("Vous n'avez aucune coopérative.");
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function cooperative(Request $request) {
        $ApiResponse = new ApiResponse();
        $validator = Validator::make($request->post(), [
                    'id' => 'required|integer'
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Cooperative = Cooperative::where("id", Input::get("id"))->select("name", "geolocation", "created_at");
            if ($Cooperative) {
                $details = $Cooperative->get()->toArray()[0];
            } else {
                $ApiResponse->setError("Cette coopérative n'a pas été trouvée.");
            }
        }

        $ApiResponse->setResponse($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

}
