<?php

namespace App\Http\Controllers;

use File;
use App\Activity;
use App\Chapter;
use App\Formation;
use App\CooperativeUserFormation;
use App\Lesson;
use App\Media;
use App\MediaChapter;
use App\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

define("UPLOAD_PATH", 'uploads'); // Inside /public

class ChapterController extends Controller
{
    public function addLesson(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'name' => "bail|required|string", // bail = stop running validation rules on an attribute after the first validation failure.
            'chapter_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
            'order' => 'numeric',
            'content' => 'string'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

         // check cooperative
         $Formation = Formation::where('id', Input::get('formation_id'));
         if ($Formation->first()) {
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id],['cooperative_id', Input::get('cooperative_id')],['formation_id', Input::get('formation_id')],['type', 'collaborator']])->exists()) {
                try {
                    DB::beginTransaction();
                    $content = ($request->has("content")) ? Input::get("content") : "";
                    $order = ($request->has("order")) ? intval(Input::get("order")) : count(Chapter::where('formation_id', Input::get('formation_id'))->get()->toArray()) + 1;                    $Chapter = Chapter::create([
                        'name' => Input::get('cooperative_id'),
                        'type' => 'lesson',
                        'content' => $content,
                        'order' => $order,
                        'formation_id' => Input::get('formation_id')
                    ]);
                    Lesson::create([
                        'chapter_id' => $Chapter->id
                    ]);
                    $ApiResponse->setMessage('Lesson created.');
                    DB::commit();
                } catch (\PDOException $e) {
                    DB::rollBack();
                    $ApiResponse->setErrorMessage($e->getMessage());
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

    public function addQuizz(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'name' => "bail|required|string", // bail = stop running validation rules on an attribute after the first validation failure.
            'chapter_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
            'quizz' => 'bail|required|json',
            'order' => 'numeric',
            'content' => 'string'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

         // check cooperative
         $Formation = Formation::where('id', Input::get('formation_id'));
         if ($Formation->first()) {
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id],['cooperative_id', Input::get('cooperative_id')],['formation_id', Input::get('formation_id')],['type', 'collaborator']])->exists()) {
                try {
                    DB::beginTransaction();
                    $content = ($request->has("content")) ? Input::get("content") : "";
                    $order = ($request->has("order")) ? intval(Input::get("order")) : count(Chapter::where('formation_id', Input::get('formation_id'))->get()->toArray()) + 1;
                    $Chapter = Chapter::create([
                        'name' => Input::get('cooperative_id'),
                        'type' => 'quizz',
                        'content' => $content,
                        'order' => $order,
                        'formation_id' => Input::get('formation_id')
                    ]);
                    Quizz::create([
                        'chapter_id' => $Chapter->id
                    ]);
                    $ApiResponse->setMessage('Quizz created.');
                    DB::commit();
                } catch (\PDOException $e) {
                    DB::rollBack();
                    $ApiResponse->setErrorMessage($e->getMessage());
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

    public function addActivity(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'name' => "bail|required|string", // bail = stop running validation rules on an attribute after the first validation failure.
            'chapter_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
            'order' => 'numeric',
            'content' => 'string'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

         // check cooperative
         $Formation = Formation::where('id', Input::get('formation_id'));
         if ($Formation->first()) {
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id],['cooperative_id', Input::get('cooperative_id')],['formation_id', Input::get('formation_id')],['type', 'collaborator']])->exists()) {
                try {
                    DB::beginTransaction();
                    $content = ($request->has("content")) ? Input::get("content") : "";
                    $order = ($request->has("order")) ? intval(Input::get("order")) : count(Chapter::where('formation_id', Input::get('formation_id'))->get()->toArray()) + 1;                    $Chapter = Chapter::create([
                        'name' => Input::get('cooperative_id'),
                        'type' => 'activity',
                        'content' => $content,
                        'order' => $order,
                        'formation_id' => Input::get('formation_id')
                    ]);
                    Activity::create([
                        'chapter_id' => $Chapter->id
                    ]);
                    $ApiResponse->setMessage('Activity created.');
                    DB::commit();
                } catch (\PDOException $e) {
                    DB::rollBack();
                    $ApiResponse->setErrorMessage($e->getMessage());
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

    public function uploadMedia(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'name' => "bail|required|string", // bail = stop running validation rules on an attribute after the first validation failure.
            'chapter_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
            'media' => 'required|file|max:1000000'
        ];

        $validator = Validator::make($request->post(), $rules);

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

         // check cooperative
         $Formation = Formation::where('id', Input::get('formation_id'));
         if ($Formation->first()) {
            if (Input::get('cooperative_id') != $Formation->first()->cooperative_id) {
                $ApiResponse->setErrorMessage("bad cooperative_id");
                return response()->json($ApiResponse->getResponse(), 400);
            }

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id],['cooperative_id', Input::get('cooperative_id')],['formation_id', Input::get('formation_id')],['type', 'collaborator']])->exists()) {
                try {
                    $media = Input::file('media');
                    $mime = $media->getMimeType();
                    $size = $media->getSize();
                    $extension = $media->getClientOriginalExtension();
                    $filename = md5($User->username) . '_' . uniqid() . '.' . $extension;
                    $uri = UPLOAD_PATH . '/' . $filename;

                    DB::beginTransaction();
                    $Media = Media::create([
                        'name' => Input::get('name'),
                        'type' => $mime,
                        'uri' => $uri,
                        'size' => $size,
                        'downloadable' => 1,
                        'hash' => NULL
                    ]);

                    MediaChapter::create([
                        'media_id' => $Media->id,
                        'chapter_id' => Input::get('chapter_id')
                    ]);

                    $media->move(UPLOAD_PATH, $filename);
                    $ApiResponse->setMessage('Media uploaded.');
                    DB::commit();
                } catch (\PDOException $e) {
                    DB::rollBack();
                    $ApiResponse->setErrorMessage($e->getMessage());
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

    public function removeMedia(Request $request)
    {
        $ApiResponse = new ApiResponse();
        $User = \Request::get("User");
        $validator = Validator::make($request->post(), [
            'media_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric'
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
                    $Media = Media::where('id', Input::get('media_id'));
                    if ($Media->first()->uri)
                        File::delete($Media->first()->uri);
                    $Media->delete();
                    $ApiResponse->setMessage("Successfuly removed this media.");
                } catch (Exception $ex) {
                    $ApiResponse->setErrorMessage("Failed to remove this media. Please try again.");
                }
            } 
            else
                $ApiResponse->setErrorMessage("You must be collaborator of the formation.");
        } 
        else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }
}