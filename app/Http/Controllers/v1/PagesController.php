<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\v1\PagesService;
use App\Services\v1\ErrorService;

use Validator;

class PagesController extends Controller
{
    /* Define service provider(s) */
	protected $page;
	protected $error;
	public function __construct(PagesService $page, ErrorService $error)
	{
		$this->page = $page;
		$this->error = $error;
	}



	public function messageTheTeam(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call message the team method */
        $answer = $this->page->messageTheTeam($request->input());

        /* return answer */
        return response()->json($answer);
    }



    public function askTheFans(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call message the team method */
        $answer = $this->page->askTheFans($request->input());

        /* return answer */
        return response()->json($answer);
    }



    public function predictAndWin(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call predict and win method */
        $answer = $this->page->predictAndWin($request->input());

        /* return answer */
        return response()->json($answer);
    }
}
