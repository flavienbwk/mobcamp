<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ApiResponse;
use App\Certificate;
use App\Chapter;
use App\CooperativeUser;
use App\CooperativeUserFormation;
use App\Formation;
use App\Submission;
use App\SubmissionCooperativeUser;
use App\SubmissionMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

define("UPLOAD_PATH", 'uploads'); // Inside /public

class SubmissionController extends Controller
{
    public function submissions(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $validator = Validator::make($request->post(), [
            'formation_id' => 'integer',
            'cooperative_id' => 'integer',
            'pagination_start' => "integer|min:0",
            'interval' => "integer|min:10"
        ]);

        $cooperative_ids = array();
        $submission_response = array();
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
                if (CooperativeUserFormation::where([['user_id', $User->id], ['cooperative_id', $cooperative_id], ['formation_id', $formation['id']], ['type', 'collaborator']])->exists()) {
                    $submissions = Submission::where('formation_id', $formation['id'])->orderBy("created_at", "desc")->offset($pagination_start)->limit($pagination_end)->get()->toArray();
                    foreach ($submissions as $submission) {
                        if (SubmissionCooperativeUser::where([['submission_id', $submission['id']], ['activity_id',  $submission['activity_id']], ['formation_id', $formation['id']], ['cooperative_id', $cooperative_id]])->doesntExist()) {
                            $submission_response[] = $submission;
                        }
                    }
                }
            }
        }

        if ($submission_response && !empty($submission_response))
            $ApiResponse->setData($submission_response);
        else
            $ApiResponse->setMessage("No submission found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function submission(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $validator = Validator::make($request->post(), [
            'formation_id' => 'required|integer',
            'cooperative_id' => 'required|integer',
            'submission_id' => 'required|integer'
        ]);

        $cooperative_ids = array();

        if ($validator->fails()) {
            $ApiResponse->setErrorMessage($validator->messages()->first());
            return response()->json($ApiResponse->getResponse(), 400);
        }

        if (!$request->has("cooperative_id"))
            $cooperative_ids = CooperativeUser::select('cooperative_id')->where('user_id', $User->id)->get()->toArray();
        else {
            if (CooperativeUser::select('cooperative_id')->where([['user_id', $User->id], ['cooperative_id', Input::get('cooperative_id')]])->doesntExist()) {
                $ApiResponse->setErrorMessage('You must be part of the cooperative to see this formation.');
                return response()->json($ApiResponse->getResponse(), 400);
            }
        }

        if (CooperativeUserFormation::where([['user_id', $User->id], ['cooperative_id', Input::get("cooperative_id")], ['formation_id', Input::get("formation_id")], ['type', 'collaborator']])->exists()) {
            $Submission = Submission::where('id', Input::get("submission_id"))->first();
            if (isset($Submission)) {
                $submission = $Submission->toArray();
                $submission['is_corrected'] = (bool)SubmissionCooperativeUser::where([['submission_id', $Submission->id], ['activity_id',  $Submission->activity_id], ['formation_id', $Submission->formation_id], ['cooperative_id', Input::get('cooperative_id')]])->exists();
                $submission['correction'] = SubmissionCooperativeUser::select('is_validated', 'grade', 'message', 'grade')->where([['submission_id', $Submission->id], ['activity_id',  $Submission->activity_id], ['formation_id', $Submission->formation_id], ['cooperative_id', Input::get('cooperative_id')]])->first();
                $submission['media'] = DB::table('media')
                    ->join('submission_media', 'media_id', '=', 'media.id')
                    ->where('submission_id', $Submission->id)
                    ->select('media.id', 'media.name', 'media.type', 'uri as local_uri', 'media.size')
                    ->get()->toArray();
                $ApiResponse->setData($submission);
            } else
                $ApiResponse->setErrorMessage('Submission not found.');
        } else
            $ApiResponse->setErrorMessage('You must be a collaborator of the formation.');

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function submit(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'chapter_id' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
            'medias.*' => 'bail|required|file|max:1000000'
        ];

        $validator = Validator::make($request->all(), $rules);

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

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id], ['cooperative_id', Input::get('cooperative_id')], ['formation_id', Input::get('formation_id')], ['type', 'student']])->exists()) {
                $Chapter = Chapter::where('id', Input::get('chapter_id'));
                if ($Chapter->first()) {
                    if ($Chapter->first()->type == 'activity') {
                        $Activity = Activity::where('chapter_id', $Chapter->first()->id)->first();
                        $Submission = Submission::where([['user_id', $User->id], ['activity_id',  $Activity->id], ['formation_id', $Formation->first()->id]])->first();

                        if (!isset($Submission) || SubmissionCooperativeUser::where([['submission_id', $Submission->id], ['activity_id',  $Activity->id], ['formation_id', $Formation->first()->id], ['cooperative_id', Input::get('cooperative_id')], ['is_validated', 0]])->exists()) {
                            try {
                                DB::beginTransaction();

                                $Submission = Submission::create([
                                    'user_id' => $User->id,
                                    'formation_id' => $Formation->first()->id,
                                    'activity_id' => $Activity->id
                                ]);

                                if ($request->hasFile('medias')) {
                                    $medias = $request->file('medias');

                                    foreach ($medias as $media) {
                                        $mime = $media->getMimeType();
                                        $size = $media->getSize();
                                        $extension = $media->getClientOriginalExtension();
                                        $filename = md5($User->username) . '_' . uniqid() . '.' . $extension;
                                        $uri = UPLOAD_PATH . '/' . $filename;
                                        $Media = Media::create([
                                            'type' => $mime,
                                            'uri' => $uri,
                                            'size' => $size,
                                            'downloadable' => 1,
                                        ]);

                                        SubmissionMedia::create([
                                            'media_id' => $Media->id,
                                            'submission_id' => $Submission->id
                                        ]);

                                        $media->move(UPLOAD_PATH, $filename);
                                    }
                                    $ApiResponse->setMessage('Media(s) uploaded.');
                                    DB::commit();
                                } else
                                    $ApiResponse->setErrorMessage('no file.');
                            } catch (\PDOException $e) {
                                DB::rollBack();
                                $ApiResponse->setErrorMessage($e->getMessage());
                            }
                        } else
                            $ApiResponse->setErrorMessage("Submission already done!.");
                    } else
                        $ApiResponse->setErrorMessage("Chapter must be an activity.");
                } else
                    $ApiResponse->setErrorMessage("Chapter not found.");
            } else
                $ApiResponse->setErrorMessage("You must follow this formation.");
        } else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }

    public function correct(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $rules = [
            'grade' => 'bail|required|numeric',
            'submission_id' => 'bail|required|numeric',
            'message' => 'bail|required|string',
            'activity_d' => 'bail|required|numeric',
            'formation_id' => 'bail|required|numeric',
            'cooperative_id' => 'bail|required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

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

            if (CooperativeUserFormation::select('type')->where([['user_id', $User->id], ['cooperative_id', Input::get('cooperative_id')], ['formation_id', Input::get('formation_id')], ['type', 'collaborator']])->exists()) {

                $Activity = Activity::where('id', Input::get('activity_id'))->first();
                $Submission = Submission::where('id', Input::get('submission_id'))->first();
                $Chapter = Chapter::where('id', $Activity->chapter_id);

                if ($Chapter->first()) {
                    if ($Chapter->first()->type == 'activity') {
                        if (isset($Submission) && SubmissionCooperativeUser::where([['submission_id', $Submission->id], ['activity_id',  $Activity->id], ['formation_id', $Formation->first()->id], ['cooperative_id', Input::get('cooperative_id')]])->doesntExist()) {
                            try {
                                DB::beginTransaction();

                                $SubmissionCooperativeUser = SubmissionCooperativeUser::create([
                                    'is_validated' => (Input::get('grade') >= 5) ? 1 : 0,
                                    'grade' =>  Input::get('grade'),
                                    'message' => Input::get('message'),
                                    'corrector_user_id' => $User->id,
                                    'submission_id' => $Submission->id,
                                    'formation_id' => $Formation->first()->id,
                                    'activity_id' => $Activity->id,
                                    'cooperative_id' => $Formation->first()->cooperative_id
                                ]);

                                if (Input::get('grade') >= 5) {
                                    DB::table('chapter_cooperative_user')
                                        ->where([['chapter_id', $Chapter->first()->id], ['user_id', $Submission->user_id], ['cooperative_id', $Formation->first()->cooperative_id]])
                                        ->update(['is_achieved' => 1]);
                                }

                                $chapters = ChapterCooperativeUser::join('chapter', 'chapter.id', '=', 'chapter_id')->where([['formation_id', '=', $Formation->first()->id], ['cooperative_id', $Formation->first()->cooperative_id], ['user_id', $Submission->user_id]])->get()->toArray();

                                $all_validated = true;
                                foreach ($chapters as $chapter) {
                                    if ($chapter['is_achieved'] == 0)
                                        $all_validated = false;
                                }
                                if ($all_validated) {
                                    Certificate::create([
                                        'formation_id' => $Formation->first()->id,
                                        'cooperative_id' => $Formation->first()->cooperative_id,
                                        'user_id' => $Submission->user_id
                                    ]);
                                }
                            } catch (\PDOException $e) {
                                DB::rollBack();
                                $ApiResponse->setErrorMessage($e->getMessage());
                            }
                        } else
                            $ApiResponse->setErrorMessage("Can't correct this submission!.");
                    } else
                        $ApiResponse->setErrorMessage("Chapter must be an activity.");
                } else
                    $ApiResponse->setErrorMessage("Chapter not found.");
            } else
                $ApiResponse->setErrorMessage("You must be collaborator this formation.");
        } else
            $ApiResponse->setErrorMessage("Formation not found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }
}
