<?php

namespace App\Http\Controllers;

use App\User;
use App\Avatar;
use App\ApiResponse;
use App\Notification;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\Authentication;

define("UPLOAD_PATH", 'uploads'); // Inside /public

class AccountController extends Controller {

    public function searchUsername(Request $request) {
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

    public function issubscribed(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'ids' => "required|string",
                    'direction' => "required|integer|min:1|max:2"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $details = [];
            $User_given = User::where("ids", Input::get("ids"))->first();
            if ($User_given) {
                if (intval(Input::get("direction")) == 1) {
                    $Subscription = Subscription::where("Subscriber_User_id", $User->id)->where("Subscribed_User_id", $User_given->id)->first();
                } else {
                    $Subscription = Subscription::where("Subscribed_User_id", $User->id)->where("Subscriber_User_id", $User_given->id)->first();
                }
                $details["subscribed"] = ($Subscription) ? 1 : 0;
            } else {
                $ApiResponse->setErrorMessage("Impossible to find the user to check.");
            }
            $ApiResponse->setData($details);
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function subscription(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'ids' => "required|string"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $details = [];
            $User_to_subscribe = User::where("ids", Input::get("ids"))->first();
            if ($User_to_subscribe) {
                if ($User_to_subscribe->id != $User->id) {
                    try {
                        $Subscription = Subscription::create([
                                    "Subscriber_User_id" => $User->id,
                                    "Subscribed_User_id" => $User_to_subscribe->id
                        ]);
                    } catch (Exception $ex) {
                        $ApiResponse->setErrorMessage("Impossible to subscribe to that user for the moment. Please try again.");
                    }
                } else {
                    $ApiResponse->setErrorMessage("You can't subscribe to yourself.");
                }
            } else {
                $ApiResponse->setErrorMessage("Impossible to find the user to subscribe to.");
            }
            $ApiResponse->setData($details);
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function subscriptionsList(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'ids' => "required|string"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $details = [];
            $subscriptions = Subscription::where("Subscriber_User_id", $User->id)->get()->toArray();
            foreach ($subscriptions as $subscription) {
                $User_tmp = User::where("ids", Input::get("ids"))->first();
                if ($User_tmp) {
                    $details[] = $User_tmp->ids;
                }
            }
            $ApiResponse->setData($details);
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function subscribedList(Request $request) {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
                    'ids' => "required|string"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
        } else {
            $details = [];
            $subscriptions = Subscription::where("Subscribed_User_id", $User->id)->get()->toArray();
            foreach ($subscriptions as $subscription) {
                $User_tmp = User::where("ids", Input::get("ids"))->first();
                if ($User_tmp) {
                    $details[] = $User_tmp->ids;
                }
            }
            $ApiResponse->setData($details);
        }

        if ($ApiResponse->getError()) {
            return response()->json($ApiResponse->getResponse(), 400);
        } else {
            return response()->json($ApiResponse->getResponse(), 200);
        }
    }

    public function notificationSeen(Request $request) {
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

    public function notifications(Request $request) {
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

    public function addAvatar(Request $request) {
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

    public function avatar(Request $request) {
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
    public function getAvatarByUserId($user_id) {
        return Avatar::where("User_id", $user_id)->orderBy("added_at", "DESC")->get()->first();
    }

}
