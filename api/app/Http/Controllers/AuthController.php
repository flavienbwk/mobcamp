<?php

namespace App\Http\Controllers;

use App\User;
use App\Connection;
use App\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\Authentication;

class AuthController extends Controller {

    public function expiration(Request $request) {
        $ApiResponse = new ApiResponse();
        if (\Request::get("Connection")) {
            $Connection = \Request::get("Connection");
            $ApiResponse->setData([
                "expires_at" => $Connection->expires_at
            ]);
        } else {
            $ApiResponse->setErrorMessage("No connection found.");
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function info(Request $request) {
        $ApiResponse = new ApiResponse();
        if (\Request::get("Connection")) {
            $Connection = \Request::get("Connection");
            $User = User::find($Connection->User_id);
            if ($User) {
                $details = [
                    "ids" => $User->ids,
                    "first_name" => $User->first_name,
                    "last_name" => $User->last_name,
                    "email" => $User->email,
                    "username" => $User->username
                ];
                $ApiResponse->setData($details);
            } else {
                $ApiResponse->setErrorMessage("Invalid user found for token.");
            }
        } else {
            $ApiResponse->setErrorMessage("No connection found.");
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function login(Request $request) {
        $ApiResponse = new ApiResponse();
        $validator = Validator::make($request->post(), [
                    'username' => 'required|string',
                    'password' => 'required'
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $User = User::where("username", Input::get("username"))->first();
            if ($User && Hash::check(Input::get("password"), $User->password)) {
                $Connection = $this->connectUser($User->id);
                if ($Connection) {
                    $details = [
                        "token" => $Connection->token,
                        "expires_at" => $Connection->expires_at,
                        "ids" => $User->ids,
                        "first_name" => $User->first_name,
                        "last_name" => $User->last_name,
                        "email" => $User->email,
                        "username" => $User->username,
                    ];
                    $ApiResponse->setData($details);
                } else {
                    $ApiResponse->setErrorMessage("Failed to create a connection. Please try again.");
                }
            } else {
                $ApiResponse->setErrorMessage("Invalid username or password.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            $ApiResponse->setMessage("Welcome, " . $User->first_name . ".");
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function register(Request $request) {
        $ApiResponse = new ApiResponse();
        $validator = Validator::make($request->post(), [
                    'email' => 'required|email|unique:user',
                    'username' => 'required|string|max:50|unique:user',
                    'first_name' => 'required|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'password' => 'required|min:4'
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            try {
                DB::beginTransaction();
                $user_fields = [
                    "email" => Input::get("email"),
                    "username" => Input::get("username"),
                    "first_name" => Input::get("first_name"),
                    "last_name" => Input::get("last_name"),
                    "password" => bcrypt(Input::get("password")),
                    "ids" => sha1(uniqid(rand(), true))
                ];
                $User = User::create($user_fields);
                if ($User) {
                    $Connection = $this->connectUser($User->id);
                    if ($Connection) {
                        $details = [
                            "token" => $Connection->token,
                            "expires_at" => $Connection->expires_at,
                            "ids" => $User->ids,
                            "first_name" => $User->first_name,
                            "last_name" => $User->last_name,
                            "email" => $User->email,
                            "username" => $User->username,
                        ];
                        $ApiResponse->setData($details);
                    } else {
                        $ApiResponse->setErrorMessage("Impossible to create the connection.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Impossible to create the user.");
                }
                DB::commit();
            } catch (\PDOException $e) {
                DB::rollBack();
                $ApiResponse->setErrorMessage($e->getMessage());
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            $ApiResponse->setMessage("Thank you for registering.");
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function connectUser($user_id) {
        return Connection::create([
                    "User_id" => $user_id,
                    "token" => sha1(uniqid(rand(), true)),
                    "expires_at" => date('Y-m-d H:i:s', strtotime('+2 day', time())),
                    "ip" => $this->getUserIpAddr()
        ]);
    }

    public function getUserIpAddr() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}
