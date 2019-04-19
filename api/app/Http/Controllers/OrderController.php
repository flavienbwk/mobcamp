<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Tour;
use App\Schedule;
use App\Media;
use App\OrderTourSchedule;
use App\OrderUserItem;
use App\UserItem;
use App\Item;
use App\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepository;

class OrderController extends Controller
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
            $Orders = Order::select("order.id as order_id", "user.ids as buyer_ids", "user.username as buyer_username", "schedule.from", "schedule.to", "tour_schedule.place", "order.type", "order.confirmed_at")
                ->join("user", "user.id", "=", "order.buyer_user_id")
                ->leftJoin("order_tour_schedule", "order_tour_schedule.order_id", "=", "order.id")
                ->leftJoin("schedule", "schedule.id", "=", "order_tour_schedule.schedule_id")
                ->leftJoin("tour_schedule", "tour_schedule.schedule_id", "=", "schedule.id")
                ->where([
                    ["order.cooperative_id", Input::get("cooperative_id")]
                ]);
            if ($Orders->count()) {
                $ApiResponse->setData($Orders->get()->toArray());
            } else {
                $ApiResponse->setErrorMessage("No order found.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function listUser(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_id = ($request->has('user_ids')) ? UserRepository::getIdByIds(Input::get("user_ids")) : $User->id;
            if ($user_id == $User->id || CooperativeRepository::hasUserRole($user_id, Input::get("cooperative_id"), "administrateur") || CooperativeRepository::hasUserRole($user_id, Input::get("cooperative_id"), "commercial")) {
                $Orders = Order::select("order.id as order_id", "user.ids as buyer_ids", "user.username as buyer_username", "schedule.from", "schedule.to", "tour_schedule.place", "order.type")
                    ->join("user", "user.id", "=", "order.buyer_user_id")
                    ->leftJoin("order_tour_schedule", "order_tour_schedule.order_id", "=", "order.id")
                    ->leftJoin("schedule", "schedule.id", "=", "order_tour_schedule.schedule_id")
                    ->leftJoin("tour_schedule", "tour_schedule.schedule_id", "=", "schedule.id")
                    ->where([
                        ["order.cooperative_id", Input::get("cooperative_id")],
                        ["order.buyer_user_id", $user_id]
                    ]);
                if ($Orders->count()) {
                    $ApiResponse->setData($Orders->get()->toArray());
                } else {
                    $ApiResponse->setErrorMessage("No order found.");
                }
            } else {
                $ApiResponse->setErrorMessage("Vous n'êtes pas autorisé à découvrir cette liste.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function listItems(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'order_id' => "integer|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $in_cooperative = UserRepository::inCooperative($User->id, Input::get("cooperative_id"));
            if ($in_cooperative) {
                $Items = Item::select("item.id", "item.name", "item.description", "item.created_at", "item.updated_at", "item.unit", "item.formation_id", "formation.name")
                    ->leftJoin("formation", "formation.id", "=", "item.formation_id")
                    ->join("user_item", "user_item.item_id", "=", "item.id")
                    ->join("order_user_item", "order_user_item.user_item_id", "=", "user_item.id")
                    ->where([
                        ["order_user_item.order_id", Input::get("order_id")],
                        ["order_user_item.cooperative_id", Input::get("cooperative_id")]
                    ]);
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
                    $ApiResponse->setErrorMessage("Aucun item n'a été trouvé pour cette commande");
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

    public function buy(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'schedule_id' => "required|integer",
            'user_items_id' => "required|array",
            'user_items_id.*' => "integer",
            'quantities' => "required|array",
            'quantities.*' => "integer|min:1",
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Schedule = OrderRepository::getCooperativeSchedule(Input::get("cooperative_id"), Input::get("schedule_id"));
            if ($Schedule && $Schedule->count()) {
                $items_id = Input::get("user_items_id");
                $quantities = Input::get("quantities");
                if (sizeof($items_id) == sizeof($quantities)) {
                    $items_valid = true;
                    foreach ($items_id as $id) {
                        if (!UserItem::find($id)) {
                            $items_valid = false;
                            break;
                        }
                    }

                    if ($items_valid) {
                        $valid = true;
                        for ($i = 0; $i < sizeof($quantities); $i++) {
                            $object_quantity_ordered = OrderRepository::getItemQuantityOrdered($items_id[$i]);
                            $object_quantity_total = OrderRepository::getItemQuantityTotal($items_id[$i]);
                            if (!($object_quantity_ordered < $object_quantity_total && $object_quantity_ordered + $quantities[$i] <= $object_quantity_total)) {
                                $valid = false;
                                break;
                            }
                        }

                        if ($valid) {
                            try {
                                $Order = Order::create([
                                    "buyer_user_id" => $User->id,
                                    "cooperative_id" => Input::get("cooperative_id"),
                                    "type" => "buy"
                                ]);

                                $schedule_tour_id = OrderRepository::getTourIdByScheduleId(Input::get("schedule_id"));
                                $OrderTourSchedule = OrderTourSchedule::create([
                                    "order_id" => $Order->id,
                                    "cooperative_id" => Input::get("cooperative_id"),
                                    "schedule_id" => Input::get("schedule_id"),
                                    "tour_id" => $schedule_tour_id
                                ]);

                                for ($a = 0; $a < sizeof($items_id); $a++) {
                                    $OrderUserItem = OrderUserItem::create([
                                        "order_id" => $Order->id,
                                        "cooperative_id" => Input::get("cooperative_id"),
                                        "user_item_id" => $items_id[$a],
                                        "quantity" => $quantities[$a]
                                    ]);
                                }

                                $ApiResponse->setMessage("La commande a été créée avec succès.");
                                $ApiResponse->setData(["order_id" => $Order->id]);
                            } catch (Exception $ex) {
                                $ApiResponse->setErrorMessage($ex->getMessage());
                            }
                        } else {
                            $ApiResponse->setErrorMessage("Stock épuisé pour certains items ou vous ne pouvez pas acheter + d'items que ceux disponibles.");
                        }
                    } else {
                        $ApiResponse->setErrorMessage("Certains items sont invalides.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Nombre différent de quantités et d'objets.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cet horaire n'est pas disponible.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function sell(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'schedule_id' => "required|integer",
            'user_items_id' => "required|array",
            'user_items_id.*' => "integer",
            'quantities' => "required|array",
            'quantities.*' => "integer|min:1",
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Schedule = OrderRepository::getCooperativeSchedule(Input::get("cooperative_id"), Input::get("schedule_id"));
            if ($Schedule && $Schedule->count()) {
                $items_id = Input::get("user_items_id");
                $quantities = Input::get("quantities");
                if (sizeof($items_id) == sizeof($quantities)) {
                    $items_valid = true;
                    foreach ($items_id as $id) {
                        if (!UserItem::find($id)) {
                            $items_valid = false;
                            break;
                        }
                    }

                    if ($items_valid) {
                        $valid = true;
                        for ($i = 0; $i < sizeof($quantities); $i++) {
                            $object_quantity_ordered = OrderRepository::getItemQuantityOrdered($items_id[$i]);
                            $object_quantity_total = OrderRepository::getItemQuantityTotal($items_id[$i]);
                            
                            if (!($object_quantity_ordered < $object_quantity_total && $object_quantity_ordered + $quantities[$i] <= $object_quantity_total)) {
                                $ApiResponse->setErrorMessage("Stock épuisé pour certains items ou vous ne pouvez pas acheter + d'items que ceux disponibles.");
                                $valid = false;
                                break;
                            }

                            // Checking if the item requires formation, and the user has realized this formation
                            $Item = Item::join("user_item", "user_item.item_id", "=", "item.id")
                            ->where([["user_item.id", $items_id[$i]]])->first();
                            $Certificate = Certificate::join("item", "item.formation_id", "=", "certificate.formation_id")
                            ->where([
                                ["certificate.user_id", $User->id],
                                ["certificate.formation_id", $Item->id]
                            ])->whereNotNull("item.formation");
                            if ($Certificate && $Certificate->count()){
                                $ApiResponse->setErrorMessage("Vous n'êtes pas habilité à vendre ce produit, vous devez avoir un certificat.");
                                $valid = false;
                                break;
                            }
                        }

                        if ($valid) {
                            try {
                                $Order = Order::create([
                                    "buyer_user_id" => $User->id,
                                    "cooperative_id" => Input::get("cooperative_id"),
                                    "type" => "sell"
                                ]);

                                $schedule_tour_id = OrderRepository::getTourIdByScheduleId(Input::get("schedule_id"));
                                $OrderTourSchedule = OrderTourSchedule::create([
                                    "order_id" => $Order->id,
                                    "cooperative_id" => Input::get("cooperative_id"),
                                    "schedule_id" => Input::get("schedule_id"),
                                    "tour_id" => $schedule_tour_id
                                ]);

                                for ($a = 0; $a < sizeof($items_id); $a++) {
                                    $OrderUserItem = OrderUserItem::create([
                                        "order_id" => $Order->id,
                                        "cooperative_id" => Input::get("cooperative_id"),
                                        "user_item_id" => $items_id[$a],
                                        "quantity" => $quantities[$a]
                                    ]);
                                }

                                $ApiResponse->setMessage("La commande a été créée avec succès.");
                                $ApiResponse->setData(["order_id" => $Order->id]);
                            } catch (Exception $ex) {
                                $ApiResponse->setErrorMessage($ex->getMessage());
                            }
                        }
                    } else {
                        $ApiResponse->setErrorMessage("Certains items sont invalides.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("Nombre différent de quantités et d'objets.");
                }
            } else {
                $ApiResponse->setErrorMessage("Cet horaire n'est pas disponible.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function approve(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'order_id' => "integer|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Order = Order::where([
                ["id", Input::get("order_id")],
                ["cooperative_id", Input::get("cooperative_id")]
            ])->first();
            if ($Order) {
                if ($Order->confirmed_at == null) {
                    try {
                        $Order->confirmed_at = new \DateTime();
                        $Order->save();
                        $ApiResponse->setMessage("La commande a été approuvée.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Commande déjà approuvée.");
                }
            } else {
                $ApiResponse->setErrorMessage("Commande introuvable.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function desapprove(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => "required|integer",
            'order_id' => "integer|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Order = Order::where([
                ["id", Input::get("order_id")],
                ["cooperative_id", Input::get("cooperative_id")]
            ])->first();
            if ($Order) {
                if ($Order->confirmed_at == null) {
                    try {
                        OrderUserItem::where([
                            ["order_id", Input::get("order_id")]
                        ])->delete();
                        $Order->delete();
                        $ApiResponse->setMessage("La commande a bien été désapprouvée.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Commande déjà approuvée.");
                }
            } else {
                $ApiResponse->setErrorMessage("Commande introuvable.");
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
            'order_id' => "integer|integer"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Order = Order::where([
                ["id", Input::get("order_id")],
                ["cooperative_id", Input::get("cooperative_id")]
            ])->first();
            if ($Order) {
                if ($Order->confirmed_at == null) {
                    try {
                        OrderUserItem::where([
                            ["order_id", Input::get("order_id")]
                        ])->delete();
                        $Order->delete();
                        $ApiResponse->setMessage("La commande a bien été désapprouvée.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                } else {
                    $ApiResponse->setErrorMessage("Commande déjà approuvée.");
                }
            } else {
                $ApiResponse->setErrorMessage("Commande introuvable.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }
}
