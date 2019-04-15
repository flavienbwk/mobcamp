<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Tour;
use App\Item;
use App\ItemMedia;
use App\UserItem;
use App\UserItemCooperative;
use App\Schedule;
use App\TourSchedule;
use App\Media;
use App\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepository;
use App\Repositories\TourRepository;

class ItemController extends Controller
{

    public function list(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'offset' => "integer|min:1",
            'interval' => "interger|min:1"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $offset = ($request->has("offset")) ? Input::get("offset") : 1;
            $interval = ($request->has("interval")) ? Input::get("interval") : 20;
            $in_cooperative = UserRepository::inCooperative($User->id, Input::get("cooperative_id"));
            if ($in_cooperative) {
                $Items = Item::select("id", "name", "formation_id")->where("cooperative_id", Input::get("cooperative_id"))->limit($interval)->offset(($offset - 1) * $interval);
                if ($Items->get()) {
                    $items = $Items->get()->toArray();
                    foreach ($items as &$item) {
                        $image = Media::select("media.uri")
                            ->join("item_media", "item_media.media_id", "=", "media.id")
                            ->where("item_media.item_id", $item["id"])->first();
                        if ($image)
                            $item["image"] = $image->uri;
                        else
                            $item["image"] = null;
                    }
                    $ApiResponse->setResponse($items);
                } else {
                    $ApiResponse->setErrorMessage("Aucun item n'a été trouvé pour cette coopérative");
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

    public function details(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'item_id' => "required|integer",
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $in_cooperative = UserRepository::inCooperative($User->id, Input::get("cooperative_id"));
            if ($in_cooperative) {
                $Items = Item::select("item.id", "item.name", "item.description", "item.created_at", "item.updated_at", "item.unit", "item.formation_id", "formation.name")
                    ->leftJoin("formation", "formation.id", "=", "item.formation_id")
                    ->where([
                        ["item.cooperative_id", Input::get("cooperative_id")],
                        ["item.id", Input::get("item_id")]
                    ])->get();
                if ($Items) {
                    $items = $Items->toArray();
                    foreach ($items as &$item) {
                        $image = Media::select("media.id", "media.uri")
                            ->join("item_media", "item_media.media_id", "=", "media.id")
                            ->where("item_media.item_id", $item["id"])->get();
                        if ($image)
                            $item["images"] = $image->toArray();
                        else
                            $item["images"] = null;
                    }
                    $ApiResponse->setResponse($items);
                } else {
                    $ApiResponse->setErrorMessage("Aucun item n'a été trouvé pour cette coopérative");
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
            'formation_id' => "present",
            'name' => "required|string",
            'description' => "required|string",
            'unit' => "required|string|in:g,mg,kg,t,L,mL",
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $formation_id = ($request->has("formation_id")) ? Input::get("formation_id") : null;
            if ($formation_id == null || Formation::where([
                ["cooperative_id", Input::get("cooperative_id")],
                ["id", Input::get("formation_id")]
            ])->count()) {
                if (Item::where([
                    ["cooperative_id", Input::get("cooperative_id")],
                    ["name", Input::get("name")]
                ])->count() == 0) {
                    try {
                        $Item = new Item();
                        $Item->cooperative_id = Input::get("cooperative_id");
                        $Item->formation_id = $formation_id;
                        $Item->unit = Input::get("unit");
                        $Item->name = Input::get("name");
                        $Item->description = Input::get("description");
                        $Item->save();
                        $ApiResponse->setData(["id" => $Item->id]);
                        $ApiResponse->setMessage("Successfuly created item.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Cet item existe déjà.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cette formation n'existe pas pour cette coopérative.");
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
            'item_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Item = Item::where([
                ["id", Input::get("item_id")],
                ["cooperative_id", Input::get("cooperative_id")]
            ]);

            if ($Item) {
                try {
                    ItemMedia::where([
                        ["item_id", $Item->first()->id]
                    ])->delete();
                    $Item->delete();
                    $ApiResponse->setMessage("Cet item a bien été supprimé.");
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage($ex->getMessage());
                }
            } else {
                $ApiResponse->setErrorMessage("Cet item n'a pas été trouvé.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }
}
