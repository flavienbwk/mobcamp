<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Certificate;
use App\CooperativeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{
    public function certificates(Request $request)
    {
        $User = \Request::get("User");
        $ApiResponse = new ApiResponse();

        $validator = Validator::make($request->post(), [
            'cooperative_id' => 'integer',
            'pagination_start' => "integer|min:0",
            'interval' => "integer|min:10"
        ]);

        $cooperative_ids = array();
        $certificates_response = array();
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
            $certificates = Certificate::select('id', 'formation_id', 'cooperative_id', 'created_at', 'updated_at')->where([
                ['user_id', $User->id],
                ['cooperative_id', $cooperative_id],
            ])->orderBy("created_at", "desc")->offset($pagination_start)->limit($pagination_end)->get()->toArray();
            foreach ($certificates as $certificate)
                $certificates_response[] = $certificate;
        }

        if ($certificates_response && !empty($certificates_response))
            $ApiResponse->setData($certificates_response);
        else
            $ApiResponse->setErrorMessage("No certificate found.");

        if ($ApiResponse->getError())
            return response()->json($ApiResponse->getResponse(), 400);
        else
            return response()->json($ApiResponse->getResponse(), 200);
    }
}
