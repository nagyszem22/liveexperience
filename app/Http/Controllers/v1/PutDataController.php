<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\v1\PutService;
use App\Services\v1\ErrorService;

use Validator;

class PutDataController extends Controller
{
	/* Define service provider(s) */
	protected $put;
	protected $error;
	public function __construct(PutService $put, ErrorService $error)
	{
		$this->put = $put;
		$this->error = $error;
	}



    public function contactus(Request $request)
    {
    	/* validate request */
        $validator = Validator::make($request->all(), [
        	'name'  => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'text'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call contact us method */
        $answer = $this->put->contactus($request->input());

        /* return answer */
        return response()->json($answer);
    }



    public function messageTheTeam(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'message'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* return answer */
        return response()->json($this->put->messageTheTeam($request));
    }



    public function askTheFans(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'question_id'  => 'required|numeric|exists:ask_the_fans_question,id',
            'answer_id' => 'required|numeric|exists:ask_the_fans_answer,id'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call ask the fans method */
        $answer = $this->put->askTheFans($request);

        /* return answer */
        return response()->json($answer);
    }



    public function predictAndWin(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'question_id.*'  => 'required|numeric|exists:predict_and_win_questions,id',
            'answer_id.*' => 'required|numeric|exists:predict_and_win_anwers,id'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call message the team method */
        $answer = $this->put->predictAndWin($request);

        /* return answer */
        return response()->json($answer);
    }


    /* spotify */
    public function spotify(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'spotify_id' => 'required',
            'artist'  => 'required',
            'song' => 'required',
            'album_cover' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call message the team method */
        $answer = $this->put->spotify($request);

        /* return answer */
        return response()->json($answer);
    }



    /* fanshelp */
    public function fanshelp(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|numeric|exists:live_action_happening,id',
            'vote' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call message the team method */
        $answer = $this->put->fanshelp($request);

        /* return answer */
        return response()->json($answer);
    }



    public function mall(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'coupon' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call message the team method */
        $answer = $this->put->mall($request);

        /* return answer */
        return response()->json($answer);
    }
}
