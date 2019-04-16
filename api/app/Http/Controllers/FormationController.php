<?php

namespace App\Http\Controllers;

use File;
use App\ApiResponse;
use App\Answer;
use App\Certificate;
use App\Chapter;
use App\CooperativeUser;
use App\CooperativeUserFormation;
use App\ChapterCooperativeUser;
use App\Formation;
use App\Question;
use App\Quizz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

define("UPLOAD_PATH", 'uploads'); // Inside /public

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
            'cooperative_id' => 'required|numeric',
            'main_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:1000000'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        // check cooperative
        $cooperative_id_formation = CooperativeUser::select('cooperative_id')->where('user_id', $User->id)->get()->toArray();
        $cooperative_id_column = array_column($cooperative_id_formation, 'cooperative_id');
        if (($key = array_search(Input::get("cooperative_id"), $cooperative_id_column)) !== FALSE) {
            try {
                $picture = Input::file('main_pic');
                $extension = $picture->getClientOriginalExtension();
                $filename = md5($User->username) . '_' . uniqid() . '.' . $extension;
                $uri = UPLOAD_PATH . "/" . $filename;

                DB::beginTransaction();
                $Formation = Formation::create([
                    "name" => Input::get("name"),
                    'estimated_duration' => Input::get("estimated_duration"),
                    'level' => Input::get("level"),
                    'cooperative_id' => Input::get("cooperative_id"),
                    'local_uri' => $uri
                ]);

                CooperativeUserFormation::create([
                    'user_id' => $User->id,
                    'formation_id' => $Formation->id,
                    'cooperative_id' => Input::get("cooperative_id"),
                    'type' => "collaborator"
                ]);

                $picture->move(UPLOAD_PATH, $filename);
                $ApiResponse->setMessage("Your formation was created.");
                DB::commit();
            } catch (\PDOException $e) {
                DB::rollBack();
                $ApiResponse->setErrorMessage($e->getMessage());
            }
        }
        else
            $ApiResponse->setErrorMessage("You must be part of the cooperative to add this formation.");

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
            foreach ($formations as $formation) {
                $formation['is_followed'] = (bool)CooperativeUserFormation::where([['user_id', $User->id], ['cooperative_id', Input::get('cooperative_id')], ['formation_id', Input::get('formation_id')], ['type', 'student']])->exists();
                $formation['hasCertificate'] = (bool)Certificate::where([
                    ['user_id', $User->id],
                    ['formation_id', $formation['id']],
                    ['cooperative_id', $formation['cooperative_id']]
                ])->exists();
                $formations_response[] = $formation;
            }
        }

        if ($formations_response && !empty($formations_response))
            $ApiResponse->setData($formations_response);
        else
            $ApiResponse->setMessage("No formation found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function formationsFollowed(Request $request)
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
            foreach ($formations as $formation) {
                if (CooperativeUserFormation::where([['user_id', $User->id], ['cooperative_id', $formation['cooperative_id']], ['formation_id', $formation['id']], ['type', 'student']])->exists()) {
                    $formation['hasCertificate'] = (bool)Certificate::where([
                        ['user_id', $User->id],
                        ['formation_id', $formation['id']],
                        ['cooperative_id', $formation['cooperative_id']]
                    ])->exists();
                    $formations_response[] = $formation;
                }
            }
        }

        if ($formations_response && !empty($formations_response))
            $ApiResponse->setData($formations_response);
        else
            $ApiResponse->setMessage("No formation found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function formationsByName(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $validator = Validator::make($request->post(), [
            'pattern' => 'required|string', 
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
            $formations = Formation::select('*')->where([['cooperative_id', $cooperative_id], ['name', 'like', '%' . Input::get("pattern") . '%']])->orderBy("created_at", "desc")->offset($pagination_start)->limit($pagination_end)->get()->toArray();
            foreach ($formations as $formation) {
                $formation['hasCertificate'] = (bool)Certificate::where([
                    ['user_id', $User->id],
                    ['formation_id', $formation['id']],
                    ['cooperative_id', $formation['cooperative_id']]
                ])->exists();
                $formations_response[] = $formation;
            }
        }

        if ($formations_response && !empty($formations_response))
            $ApiResponse->setData($formations_response);
        else
            $ApiResponse->setMessage("No formation found.");

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
        $cooperative_id_formation = CooperativeUser::select('cooperative_id')->where('user_id', $User->id)->get()->toArray();
        $cooperative_id_column = array_column($cooperative_id_formation, 'cooperative_id');

        if ($Formation->first()) {
            if (($key = array_search($Formation->first()->cooperative_id, $cooperative_id_column)) !== FALSE) {
                $formation = $Formation->first()->toArray();
                $formation['hasCertificate'] = (bool)Certificate::where([
                    ['user_id', $User->id],
                    ['formation_id', Input::get("formation_id")],
                    ['cooperative_id', Input::get("cooperative_id")]
                ])->exists();
                $formation['collaborators'] = DB::table('user')
                    ->join('cooperative_user_formation', 'user.id', '=', 'user_id')
                    ->where([
                        ['cooperative_id', '=', $Formation->first()->cooperative_id],
                        ['formation_id', '=', Input::get("formation_id")],
                        ['type', '=', 'collaborator']
                    ])
                    ->select('id', 'first_name', 'last_name')
                    ->get()->toArray();
                $formation['chapters'] = Chapter::select('*')
                    ->where('formation_id', Input::get('formation_id'))
                    ->orderBy('order', 'asc')
                    ->get()->toArray();
                for ($i = 0; isset($formation['chapters'][$i]); $i++) {
                    $z = ChapterCooperativeUser::select('is_achieved')->where([['chapter_id', $formation['chapters'][$i]['id']], ['user_id', $User->id]])->get()->first();
                    if (isset($z))
                        $formation['chapters'][$i]['is_achieved'] = ($z->is_achieved) ? "true" : "false";
                    else
                        $formation['chapters'][$i]['is_achieved'] = "false";

                    $formation['chapters'][$i]['medias'] = DB::table('media')
                                                                ->join('media_chapter', 'media_id', '=', 'media.id')
                                                                ->where('chapter_id', $formation['chapters'][$i]['id'])
                                                                ->select('media.id', 'media.name', 'media.type', 'uri as local_uri', 'media.size')
                                                                ->get()->toArray();

                    if ($formation['chapters'][$i]['type'] == 'quizz') {
                        $Quizz = Quizz::where('chapter_id', $formation['chapters'][$i]['id'])->first();
                        $formation['chapters'][$i]['questions'] = Question::select('id', 'value')->where('quizz_id', $Quizz->id)->get()->toArray();
                        for ($j = 0; isset($formation['chapters'][$i]['questions'][$j]); $j++) {
                            $answers = Answer::where([['quizz_id', $Quizz->id], ['question_id', $formation['chapters'][$i]['questions'][$j]['id']]])->get()->toArray();
                            foreach ($answers as $answer) {
                                $formation['chapters'][$i]['questions'][$j]['answers'][] = [
                                    'id' => $answer['id'],
                                    'value'=> $answer['value'],
                                    'is_right' => (bool)$answer['is_correct']
                                ];
                            }
                        }
                    }
                }
                $ApiResponse->setData($formation);
            } 
            else
                $ApiResponse->setErrorMessage("You must be part of the cooperative to see this formation.");
        } 
        else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);

    }

    public function follow(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();
        $rules = [
            'formation_id' => "bail|required|numeric",
            'cooperative_id' => 'required|numeric'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        $Formation = Formation::where('id', Input::get('formation_id'));
        if ($Formation->first()) {
            // check cooperative
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            $cooperative_id_formation = CooperativeUser::select('cooperative_id')->where('user_id', $User->id)->get()->toArray();
            $cooperative_id_column = array_column($cooperative_id_formation, 'cooperative_id');
            if (($key = array_search($Formation->first()->cooperative_id, $cooperative_id_column)) !== FALSE) {
                if (CooperativeUserFormation::where([['user_id', $User->id], ['cooperative_id', Input::get('cooperative_id')], ['formation_id', Input::get('formation_id')], ['type', 'student']])->doesntExist()) {
                    try {
                        DB::beginTransaction();
                        // Add formation
                        $Formation = CooperativeUserFormation::create([
                            'user_id' => $User->id,
                            'formation_id' => Input::get("formation_id"),
                            'cooperative_id' => Input::get("cooperative_id"),
                            'type' => "student"
                        ]);
                        $ApiResponse->setMessage("Formation followed.");
                        DB::commit();
                    } catch (\PDOException $e) {
                        DB::rollBack();
                        $ApiResponse->setErrorMessage($e->getMessage());
                    }
                } else
                    $ApiResponse->setErrorMessage("Formation already followed.");
            } else
                $ApiResponse->setErrorMessage("You must be part of the cooperative to follow this formation.");
        } else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function isFollowed(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();
        $rules = [
            'formation_id' => "bail|required|numeric",
            'cooperative_id' => 'required|numeric'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        $Formation = Formation::where('id', Input::get('formation_id'));
        if ($Formation->first()) {
            // check cooperative
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }
            $result = CooperativeUserFormation::where([['user_id', $User->id], ['cooperative_id', Input::get('cooperative_id')], ['formation_id', Input::get('formation_id')], ['type', 'student']])->exists();
            $response = ['is_followed' => (bool)$result];
            $ApiResponse->setData($response);
        } else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function unfollow(Request $request)
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

        // Delete formation
        $Formation = Formation::where('id', Input::get('formation_id'));
        if ($Formation->first()) {
            // check cooperative
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            $follow = CooperativeUserFormation::where([
                ['user_id', $User->id],
                ['cooperative_id', Input::get('cooperative_id')],
                ['formation_id', Input::get('formation_id')],
                ['type', 'student']
            ]);

            if ($follow->first()) {
                try {
                    $follow->delete();
                    $ApiResponse->setMessage("Successfuly unfollow this formation.");
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage("Failed to unfollow this formation. Please try again.");
                }
            }
            else
                $ApiResponse->setErrorMessage("You must follow this formation.");
        } 
        else
            $ApiResponse->setErrorMessage("Formation not found.");

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

        // Delete formation
        $Formation = Formation::where('id', Input::get('formation_id'));
        if ($Formation->first()) {
            // check cooperative
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id],['cooperative_id', Input::get('cooperative_id')],['formation_id', Input::get('formation_id')],['type', 'collaborator']])->exists()) {
                try {
                    if ($Formation->first()->local_uri)
                        File::delete($Formation->first()->local_uri);
                    $Formation->delete();
                    $ApiResponse->setMessage("Successfuly removed this formation.");
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage("Failed to remove this formation. Please try again.");
                }
            } 
            else
                $ApiResponse->setErrorMessage("You must be collaborator of this formation.");
        } 
        else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }
}
