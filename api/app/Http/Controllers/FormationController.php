<?php

namespace App\Http\Controllers;

use App\Formation;
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
        } 
        else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400); 
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }
}
