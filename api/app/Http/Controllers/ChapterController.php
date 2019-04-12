<?php

namespace App\Http\Controllers;

use File;
use App\Chapter;
use App\Formation;
use App\CooperativeUser;
use App\CooperativeUserFormation;
use App\ChapterCooperativeUser;
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
    {}

    public function addQuizz(Request $request)
    {}

    public function addActivity(Request $request)
    {}

    public function uploadMedia(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'name' => "bail|required|string", // bail = stop running validation rules on an attribute after the first validation failure.
            'chapter_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
            //'media' => 'required|max:1000000'
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
                    $media->move(UPLOAD_PATH, $filename);
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

    public function remove()
    {}
}
