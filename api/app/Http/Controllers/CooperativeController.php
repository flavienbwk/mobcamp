<?php

namespace App\Http\Controllers;

use App\User;
use App\Connection;
use App\ApiResponse;
use App\Cooperative;
use App\CooperativeUser;
use App\CooperativeUserRole;
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

    public function addUser(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'cooperative_id' => "required|integer",
                    'user_ids' => "required|string"
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_query = UserRepository::getIdByIds(Input::get("user_ids"));
            if ($user_query) {
                $User_d = User::find($user_query);
                if ($User_d) {
                    $user_cooperative_query = CooperativeRepository::getUserCooperative($User_d->id, Input::get("cooperative_id"));
                    if (!$user_cooperative_query) {
                        try {
                            $CooperativeUser = new CooperativeUser();
                            $CooperativeUser->user_id = $User_d->id;
                            $CooperativeUser->cooperative_id = Input::get("cooperative_id");
                            $CooperativeUser->save();
                            $ApiResponse->setMessage("L'utilisateur a bien été ajouté à la coopérative.");
                        } catch (Exception $ex) {
                            $ApiResponse->setErrorMessage($ex->getMessage());
                        }
                    } else {
                        $ApiResponse->setErrorMessage("Cet utilisateur fait déjà partie de la coopérative.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Impossible de récupérer les informations de l'utilisateur.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cet utilisateur n'a pas été trouvé.");
            }
        }

        $ApiResponse->setResponse($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function removeUser(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'cooperative_id' => "required|integer",
                    'user_ids' => "required|string"
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_query = UserRepository::getIdByIds(Input::get("user_ids"));
            if ($user_query) {
                $User_d = User::find($user_query);
                if ($User_d) {
                    $user_cooperative_query = CooperativeRepository::getUserCooperative($User_d->id, Input::get("cooperative_id"));
                    if ($user_cooperative_query) {
                        $Cooperative_User = CooperativeUser::where([
                                    ["user_id", $User_d->id],
                                    ["cooperative_id", Input::get("cooperative_id")]
                        ]);
                        if ($Cooperative_User->get()) {
                            try {
                                $Cooperative_User->delete();
                                $ApiResponse->setMessage("L'utilisateur a bien été supprimé de la coopérative.");
                            } catch (Exception $ex) {
                                $ApiResponse->setErrorMessage($ex->getMessage());
                            }
                        } else {
                            $ApiResponse->setErrorMessage("Impossible de trouver la liaison entre l'utilisateur et la coopérative.");
                        }
                    } else {
                        $ApiResponse->setErrorMessage("Cet utilisateur ne fait pas partie de la coopérative.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Impossible de récupérer les informations de l'utilisateur.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cet utilisateur n'a pas été trouvé.");
            }
        }

        $ApiResponse->setResponse($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function addRoles(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'cooperative_id' => "required|integer",
                    'user_ids' => "required|string",
                    'role_id' => "required|integer"
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_query = UserRepository::getIdByIds(Input::get("user_ids"));
            if ($user_query) {
                $User_d = User::find($user_query);
                if ($User_d) {
                    $user_cooperative_query = CooperativeRepository::getUserCooperative($User->id, Input::get("cooperative_id"));
                    if ($user_cooperative_query) {
                        $Role = Role::find(Input::get("role_id"));
                        if ($Role->get()) {
                            $role_exist = CooperativeUserRole::where([
                                        ["role_id", $Role->id],
                                        ["user_id", $User_d->id],
                                        ["cooperative_id", Input::get("cooperative_id")]
                                    ]);
                            if (!$role_exist->first()) {
                                try {
                                    $CooperativeUserRole = new CooperativeUserRole();
                                    $CooperativeUserRole->role_id = $Role->id;
                                    $CooperativeUserRole->user_id = $User_d->id;
                                    $CooperativeUserRole->cooperative_id = Input::get("cooperative_id");
                                    $CooperativeUserRole->save();
                                    $ApiResponse->setMessage($User_d->username . " est devenu " . $Role->name);
                                } catch (Exception $ex) {
                                    $ApiResponse->setErrorMessage($ex->getMessage());
                                }
                            } else {
                                $ApiResponse->setMessage($User_d->username . " est déjà " . $Role->name);
                            }
                        } else {
                            $ApiResponse->setErrorMessage("Ce rôle n'a pas été trouvé.");
                        }
                    } else {
                        $ApiResponse->setErrorMessage("Cet utilisateur ne fait pas partie de la coopérative.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Impossible de récupérer les informations de l'utilisateur.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cet utilisateur n'a pas été trouvé.");
            }
        }

        $ApiResponse->setResponse($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function removeRoles(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'cooperative_id' => "required|integer",
                    'user_ids' => "required|string",
                    'role_id' => "required|integer"
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_query = UserRepository::getIdByIds(Input::get("user_ids"));
            if ($user_query) {
                $User_d = User::find($user_query);
                if ($User_d) {
                    $user_cooperative_query = CooperativeRepository::getUserCooperative($User->id, Input::get("cooperative_id"));
                    if ($user_cooperative_query) {
                        $Role = Role::find(Input::get("role_id"));
                        if ($Role->get()) {
                            $CooperativeUserRole = CooperativeUserRole::where([
                                        ["role_id", $Role->id],
                                        ["user_id", $User_d->id],
                                        ["cooperative_id", Input::get("cooperative_id")]
                                    ]);
                            if ($CooperativeUserRole->first()) {
                                try {
                                    $CooperativeUserRole->delete();
                                    $ApiResponse->setMessage($User_d->username . " n'est plus " . $Role->name);
                                } catch (Exception $ex) {
                                    $ApiResponse->setErrorMessage($ex->getMessage());
                                }
                            } else {
                                $ApiResponse->setMessage($User_d->username . " n'est pas " . $Role->name);
                            }
                        } else {
                            $ApiResponse->setErrorMessage("Ce rôle n'a pas été trouvé.");
                        }
                    } else {
                        $ApiResponse->setErrorMessage("Cet utilisateur ne fait pas partie de la coopérative.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Impossible de récupérer les informations de l'utilisateur.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cet utilisateur n'a pas été trouvé.");
            }
        }

        $ApiResponse->setResponse($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

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
