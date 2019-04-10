<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Formation;
use App\CooperativeUser;
use App\ChapterCooperativeUser;
use App\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class FormationController extends Controller
{

    public function add(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();
        $rules = [
            'name' => "bail|required|string", // bail = stop running validation rules on an attribute after the first validation failure.
            'estimated_duration' => "bail|required|numeric",
            'level' => 'bail|string|min:1|max:20',
            'cooperative_id' => 'required|numeric'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        // Check user's permissions
        $userRole = DB::select('SELECT role.name FROM cooperative_user_role INNER JOIN role ON role.id = role_id AND user_id=? AND cooperative_id=?', [$User->id, Input::get("cooperative_id")]);
        if (isset($userRole[0])) {
            if ($userRole[0]->name != 'enseignant') {
                $ApiResponse->setErrorMessage("Permission denied, you must be an 'enseignant'.");
                return response()->json($ApiResponse->getResponse(), 403);
            }
        }

        try {
            DB::beginTransaction();
            // Add formation
            $Formation = Formation::create([
                "name" => Input::get("name"),
                'estimated_duration' => Input::get("estimated_duration"),
                'level' => Input::get("level"),
                'cooperative_id' => Input::get("cooperative_id")
            ]);
            $ApiResponse->setMessage("Your formation was created.");
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
            $ApiResponse->setErrorMessage($e->getMessage());
        }

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function formations(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $validator = Validator::make($request->post(), [
            'cooperative_id' => 'integer',
            'pagination_start' => "integer|min:0",
            'interval' => "integer|min:10"
        ]);

        $cooperative_ids = array();
        $formations_response = array();
        $cooperative_ids[0] = ($request->has("cooperative_id")) ? intval(Input::get("cooperative_id")) : 0;
        $pagination_start = ($request->has("pagination_start")) ? intval(Input::get("pagination_start")) : 0;
        $interval = ($request->has("interval")) ? intval(Input::get("interval")) : 10;
        $pagination_start *= $interval;
        $pagination_end = $pagination_start + $interval;

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        if (!$request->has("cooperative_id"))
            $cooperative_ids = CooperativeUser::select('cooperative_id')->where('user_id', $User->id)->get()->toArray();
        else {
            if (CooperativeUser::select('cooperative_id')->where([['user_id', $User->id], ['cooperative_id', $cooperative_ids[0]]])->doesntExist()) {
                $ApiResponse->setErrorMessage('You must be part of the cooperative to see this formation.');
                return response()->json($ApiResponse->getResponse(), 400);
            }
        }

        foreach ($cooperative_ids as $cooperative_id) {
            $formations = Formation::select('*')->where('cooperative_id', $cooperative_id)->orderBy("created_at", "desc")->offset($pagination_start)->limit($pagination_end)->get()->toArray();
            foreach ($formations as $formation)
                $formations_response[] = $formation;
        }

        if ($formations_response && !empty($formations_response))
            $ApiResponse->setData($formations_response);
        else
            $ApiResponse->setErrorMessage("No formation found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function formation(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $validator = Validator::make($request->post(), [
            'formation_id' => "required|numeric"
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }
        
        $Formation = Formation::where("id", Input::get("formation_id"));
        $cooperative_id_formation = CooperativeUser::where('user_id', $User->id);

        if ($Formation->first() && $cooperative_id_formation->first()) {
            if ($cooperative_id_formation->first()->cooperative_id == $Formation->first()->cooperative_id) {
                $formation = $Formation->first()->toArray();
                $formation['chapters'] = Chapter::select('id', 'name', 'type')->where('formation_id', Input::get('formation_id'))->orderBy('order', 'asc')->get()->toArray();
                for ($i = 0; isset($formation['chapters'][$i]); $i++) {
                    $z = ChapterCooperativeUser::select('is_achieved')->where([['chapter_id', $formation['chapters'][$i]['id']], ['user_id', $User->id]])->get()->first();
                    if (isset($z))
                        $formation['chapters'][$i]['is_achieved'] = ($z->is_achieved) ? "true" : "false";
                    else
                        $formation['chapters'][$i]['is_achieved'] = "false";
                }
                $ApiResponse->setData($formation);
            } 
            else
                $ApiResponse->setErrorMessage("You must be part of the cooperative to see this formation.");
        } 
        else
            $ApiResponse->setErrorMessage("Formation not found.");

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
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        // Check user's permissions
        $userRole = DB::select('SELECT role.name FROM cooperative_user_role INNER JOIN role ON role.id = role_id AND user_id=? AND cooperative_id=?', [$User->id, Input::get("cooperative_id")]);
        if (isset($userRole[0])) {
            if ($userRole[0]->name != 'enseignant') {
                $ApiResponse->setErrorMessage("You have no right on this formation.");
                return response()->json($ApiResponse->getResponse(), 403);
            }
        }

        // Delete formation
        $Formation = Formation::where('id', Input::get('formation_id'));
        if ($Formation->first()) {
            try {
                $Formation->delete();
                $ApiResponse->setMessage("Successfuly removed this formation.");
            } catch (Exception $ex) {
                $ApiResponse->setErrorMessage("Failed to remove this formation. Please try again.");
            }
        } else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }
}
