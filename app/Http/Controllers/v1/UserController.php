<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\v1\UserService;
use App\Services\v1\ErrorService;

use Validator;

class UserController extends Controller
{
	/* Define service provider(s) */
	protected $user;
	protected $error;
	public function __construct(UserService $user, ErrorService $error)
	{
		$this->user = $user;
		$this->error = $error;
	}



    /* get user logged in */
    public function ticket(Request $request) 
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'ticket' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call ticket method */
        $answer = $this->user->ticket($request->input());

        /* return answer */
        return response()->json($answer);
    }




	/* make user log in */
    public function login(Request $request) 
    {
    	/* validate request */
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'ticket' => 'required',
            'device' => 'required',
            'language' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(
            	$this->error->formValidationFailed($validator->messages()),
           	200);
        }

        /* call login method */
        $answer = $this->user->login($request->input());

        /* return answer */
        return response()->json($answer);
    }



    /* register the current user */
    public function registration(Request $request) 
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'ticket' => 'required',
            'device' => 'required',
            'language' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call registration method */
        $answer = $this->user->registration($request->input());

        /* return answer */
        return response()->json($answer);
    }
}
