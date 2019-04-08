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

class Authentication {

    public function handle($request, Closure $next) {
        $ApiResponse = new ApiResponse();
        $headers = Authentication::getRequestHeaders();
        if (isset($headers["X-Ov-Token"])) {
            $Connection = Connection::where("token", $headers["X-Ov-Token"])->first();
            if ($Connection) {
                // Expiration check
                if (time() - strtotime($Connection->expires_at) <= 172800) {
                    // Connection update
                    $Connection->expires_at = date('Y-m-d H:i:s', strtotime('+2 day', time()));
                    try {
                        $Connection->save();
                        $User = User::find($Connection->User_id);
                    } catch (\Exception $e) {
                        $ApiResponse->setErrorMessage($e->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Your token has expired.");
                }
            } else {
                $ApiResponse->setErrorMessage("Invalid X-Ov-Token.");
            }
        } else {
            $ApiResponse->setErrorMessage("X-Ov-Token not found.");
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 403);
        } else {
            $request->attributes->add(["Connection" => $Connection]);
            $request->attributes->add(["User" => $User]);
            return $next($request);
        }
    }

    public static function getRequestHeaders() {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

}
