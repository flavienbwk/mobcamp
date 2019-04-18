<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Tour;
use App\Schedule;
use App\TourSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepository;
use App\Repositories\TourRepository;

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

    public function listSchedules(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'tour_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            if (TourRepository::inCooperative(Input::get("tour_id"), Input::get("cooperative_id"))) {
                $Schedules = TourRepository::getTourSchedules(Input::get("tour_id"), Input::get("cooperative_id"));
                $ApiResponse->setResponse($Schedules->toArray());
                $ApiResponse->setMessage("Results found.");
            } else {
                $ApiResponse->setErrorMessage("Cette tournée n'appartient pas à cette coopérative ou n'existe pas.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function addSchedule(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'from' => "required|date",
            'to' => "required|date",
            'place' => "required|string|min:1",
            'tour_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            if (TourRepository::inCooperative(Input::get("tour_id"), Input::get("cooperative_id"))) {
                $from = strtotime(Input::get("from"));
                $to = strtotime(Input::get("to"));
                if ($from && $to) {
                    try {
                        $Schedule = new Schedule();
                        $Schedule->from = gmdate("Y-m-d H:i:s", $from);
                        $Schedule->to = gmdate("Y-m-d H:i:s", $to);
                        $Schedule->save();

                        $TourSchedule = new TourSchedule();
                        $TourSchedule->place = Input::get("place");
                        $TourSchedule->tour_id = Input::get("tour_id");
                        $TourSchedule->schedule_id = $Schedule->id;
                        $TourSchedule->save();

                        $ApiResponse->setData([
                            "schedule_id" => $Schedule->id
                        ]);
                    } catch (\Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Invalid datetimes provided.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cette tournée n'appartient pas à cette coopérative ou n'existe pas.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function removeSchedule(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'tour_id' => "required|integer",
            'schedule_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            if (TourRepository::inCooperative(Input::get("tour_id"), Input::get("cooperative_id"))) {
                $TourSchedule = TourSchedule::where([
                    ["tour_id", Input::get("tour_id")],
                    ["schedule_id", Input::get("schedule_id")]
                ]);
                if ($TourSchedule->count()) {
                    try {
                        $TourSchedule->delete();
                        Schedule::find(Input::get("schedule_id"))->delete();
                        $ApiResponse->setMessage("Supprimé avec succès.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Horaire de tournée introuvable.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cette tournée n'appartient pas à cette coopérative ou n'existe pas.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }
}
