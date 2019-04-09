<?php

namespace App\Http\Controllers;

use App\User;
use App\Connection;
use App\ApiResponse;
use App\Cooperative;
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

}
