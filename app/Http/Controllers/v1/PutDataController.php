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
            'text'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        /* call ticket method */
        $answer = $this->put->contactus($request->input());

        /* return answer */
        return response()->json($answer);
    }
}
