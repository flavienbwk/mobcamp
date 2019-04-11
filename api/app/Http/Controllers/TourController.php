<?php

namespace App\Http\Controllers;

use App\User;
use App\Connection;
use App\ApiResponse;
use App\Cooperative;
use App\Tour;
use App\Schedule;
use App\TourSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CooperativeRepository;
use App\Repositories\UserRepository;

class TourController extends Controller
{

    public function list(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $in_cooperative = UserRepository::inCooperative($User->id, Input::get("cooperative_id"));
            if ($in_cooperative) {
                $Tours = Tour::select("id AS tour_id", "name", "type", "created_at")->where([
                    ["cooperative_id", Input::get("cooperative_id")],
                    ["active", 1]
                ]);
                if ($Tours->count()) {
                    $ApiResponse->setResponse($Tours->get()->toArray());
                } else {
                    $ApiResponse->setErrorMessage("Aucune tournée n'a été programmée pour cette coopérative.");
                }
            } else {
                $ApiResponse->setErrorMessage("Vous n'êtes pas dans cette coopérative.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function add(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'name' => 'present',
            'type' => 'required|in:gathering,distribution'
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $is_valid = Tour::where([
                ["active", 1],
                ["name", Input::get("name")]
            ]);
            if ($is_valid->count() == 0) {
                try {
                    $Tour = new Tour();
                    $Tour->name = Input::get("name");
                    $Tour->type = Input::get("type");
                    $Tour->cooperative_id = Input::get("cooperative_id");
                    $Tour->save();
                    $ApiResponse->setResponse([
                        "tour_id" => $Tour->id
                    ]);
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage($ex->getMessage());
                }
            } else {
                $ApiResponse->setErrorMessage("Une tournée du même nom existe déjà.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function remove(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'tour_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Tour = Tour::find(Input::get("tour_id"));
            if ($Tour->count()) {
                if ($Tour->active == 1) {
                    try {
                        $Tour->active = 0;
                        $Tour->save();
                        $ApiResponse->setMessage("Tournée supprimée avec succès.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Cette tournée a déjà été supprimée.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cette tournée n'a pas été trouvée.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }
}
