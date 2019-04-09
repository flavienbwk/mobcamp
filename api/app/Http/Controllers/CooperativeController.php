<?php

namespace App\Http\Controllers;

use App\User;
use App\Connection;
use App\ApiResponse;
use App\Cooperative;
use App\CooperativeUser;
use App\CooperativeUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\Authentication;

class CooperativeController extends Controller {

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

    public function userCooperatives(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");

        $cooperatives = Cooperative::select("cooperative.id", "cooperative.name")
                ->join("cooperative_user", "cooperative_user.cooperative_id", "=", "cooperative.id")
                ->where([["cooperative_user.user_id", $User->id]])
                ->get()
                ->toArray();
        $ApiResponse->setResponse($cooperatives);

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
