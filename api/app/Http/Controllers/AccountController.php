<?php

namespace App\Http\Controllers;

use App\Item;
use App\UserItem;
use App\User;
use App\Avatar;
use App\ApiResponse;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

define("UPLOAD_PATH", 'uploads'); // Inside /public

class AccountController extends Controller
{

    public function searchUsername(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'username' => "required|string|min:1"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $details = [];
            $Users = User::select("user.ids", "user.username")->where('username', 'like', '%' . Input::get('username') . '%')->limit(6)->get();
            if ($Users) {
                $details = $Users->toArray();
            }
            $ApiResponse->setData($details);
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function notificationSeen(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'id' => "integer|min:1",
            'seen' => "integer|min:0|max:1"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Notification = Notification::where("User_id", $User->id)->where("id", Input::get("id"))->first();
            if ($Notification) {
                $Notification->seen = intval(Input::get("seen"));
                try {
                    $Notification->save();
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage("Impossible to change the notification's status : " . $ex->getMessage());
                }
            } else {
                $ApiResponse->setErrorMessage("This notification has not been found for this user.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function notifications(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'pagination_start' => "integer|min:0",
            'interval' => "integer|min:10"
        ]);

        $pagination_start = ($request->has("pagination_start")) ? intval(Input::get("pagination_start")) : 0;
        $interval = ($request->has("interval")) ? intval(Input::get("interval")) : 10;
        $pagination_start *= $interval;
        $pagination_end = $pagination_start + $interval;

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $notifications = Notification::where("User_id", $User->id)->offset($pagination_start)->limit($pagination_end)->get()->toArray();
            $notifs = [];
            foreach ($notifications as $notification) {
                $target_type = "";
                $target_ids = "";

                if (!empty($notification["Publication_id"])) {
                    $Publication = Publication::find($notification["Publication_id"]);
                    if ($Publication) {
                        $target_type = "publication";
                        $target_ids = $Publication->ids;
                    }
                }

                if (!empty($notification["Target_User_id"])) {
                    $User_n = User::find($notification["Target_User_id"]);
                    if ($Publication) {
                        $target_type = "user";
                        $target_ids = $User_n->ids;
                    }
                }

                $notifs[] = [
                    "id" => $notification["id"],
                    "message" => $notification["message"],
                    "seen" => $notification["seen"],
                    "target_type" => $target_type,
                    "target_ids" => $target_ids
                ];
            }
            $ApiResponse->setData($notifs);
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function addAvatar(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->file(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:1000000'
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $avatar = Input::file('avatar');
            $extension = $avatar->getClientOriginalExtension();
            $filename = md5($User->username) . '_' . uniqid() . '.' . $extension;
            try {
                $avatar->move(UPLOAD_PATH, $filename);
                $uri = UPLOAD_PATH . "/" . $filename;
                $ApiResponse->setData([
                    "uri" => $uri
                ]);

                $Avatar = Avatar::create([
                    "local_uri" => UPLOAD_PATH . "/" . $filename,
                    "User_id" => $User->id
                ]);
                if (!$Avatar) {
                    $ApiResponse->setErrorMessage("Failed to insert your image in database. Please try again.");
                } else {
                    $ApiResponse->setErrorMessage("Successfuly added your avatar.");
                }
            } catch (Exception $ex) {
                $ApiResponse->setErrorMessage("Failed to upload your image. Please try again : " . $ex->getMessage());
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function avatar(Request $request)
    {
        $details = [];
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();
        $validator = Validator::make($request->post(), [
            'ids' => 'required|string'
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $User_query = User::where("ids", Input::get("ids"))->first();
            if ($User_query) {
                $Avatar = $this->getAvatarByUserId($User_query->id);
                if ($Avatar) {
                    $details = [
                        "uri" => $Avatar->local_uri,
                        "added_at" => $Avatar->added_at
                    ];
                } else {
                    $ApiResponse->setErrorMessage("No avatar found for this user.");
                }
            } else {
                $ApiResponse->setErrorMessage("No user found with this ID.");
            }
        }

        $ApiResponse->setData($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    /**
     * Getting last avatar by User_id.
     * 
     * @param string $user_id
     */
    public function getAvatarByUserId($user_id)
    {
        return Avatar::where("User_id", $user_id)->orderBy("added_at", "DESC")->get()->first();
    }

    public function inventory(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => 'required|integer'
        ]);

        $details = [];
        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $user_id = ($request->has('user_ids')) ? UserRepository::getIdByIds(Input::get("user_ids")) : $User->id;
            if ($user_id == $User->id || CooperativeRepository::hasUserRole($user_id, Input::get("cooperative_id"), "commercial")) {
                $Items = Item::select("user_item.id as user_item_id", "user_item.item_id", "item.name", "item.description", "item.unit", "item.formation_id", "media.uri as image", "user_item.quantity", "user_item.price")
                    ->join("user_item", "user_item.item_id", "=", "item.id")
                    ->leftJoin("item_media", "item_media.item_id", "=", "item.id")
                    ->leftJoin("media", "media.id", "=", "item_media.media_id")
                    ->where([
                        ["user_item.user_id", $user_id]
                    ]);
                var_dump($Items->get()->toArray());
                if ($Items) {
                    if ($Items->count()) {
                        $details = $Items->get()->toArray();
                    } else {
                        $ApiResponse->setErrorMessage("Aucun item trouvé.");
                    }
                } else {
                    $ApiResponse->setError("Cette coopérative n'a pas été trouvée.");
                }
            } else {
                $ApiResponse->setErrorMessage("Vous n'êtes pas autorisé à découvrir les items de cet utilisateur.");
            }
        }

        $ApiResponse->setResponse($details);
        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function inventoryAdd(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'cooperative_id' => 'required|integer',
            'item_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'message' => 'present|min:1',
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $Item = Item::find(Input::get("item_id"))->where("cooperative_id", Input::get("cooperative_id"));
            if ($Item->count()) {
                /*
                $exist = UserItem::where([
                    ["user_id", Input::get("user_id")],
                    ["quantity", Input::get("quantity")],
                    ["price", Input::get("price")]
                ]);
                if (!$exist) {
                    */
                    $Item = $Item->first();
                    try {
                        $UI = UserItem::create([
                            "item_id" => $Item->id,
                            "message" => Input::get("message"),
                            "price" => Input::get("price"),
                            "user_id" => $User->id,
                            "quantity" => Input::get("quantity"),
                            "cooperative_id" => Input::get("cooperative_id")
                        ]);
                        $ApiResponse->setData(["user_item_id" => $UI->id]);
                        $ApiResponse->setMessage("Item ajouté avec succès à l'inventaire.");
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage($ex->getMessage());
                    }
                    /*
                } else {
                    $ApiResponse->setError("L'item existe déjà avec cette quantity et ce prix.");
                }
                */
            } else {
                $ApiResponse->setError("Cet item n'a pas été trouvé.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function inventoryRemove(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $validator = Validator::make($request->post(), [
            'cooperative_id' => 'required|integer',
            'user_item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $UserItem = UserItem::find(Input::get("user_item_id"));
            if ($UserItem && $UserItem->count()) {
                try {
                    $UserItem->delete();
                    $ApiResponse->setData("Association d'item supprimée avec succès.");
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage($ex->getMessage());
                }
            } else {
                $ApiResponse->setError("Cet association d'item n'a pas été trouvée.");
            }
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }
}
