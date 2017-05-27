<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Services\v1\AppInitService;
use App\Services\v1\ErrorService;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator;

class AppInitController extends Controller
{
	/* Define service provider(s) */
	protected $init;
    protected $error;
	public function __construct(AppInitService $service, ErrorService $error)
	{
		$this->init = $service;
        $this->error = $error;
	}

	/* First call before login */
    public function init() 
    {
    	return response()->json($this->init->getClient($_SERVER['HTTP_CLIENT']));
    }

    /* Get the type of the day (match/non-match day) */
    public function getDay(Request $request)
    {
        /* validate request */
        $validator = Validator::make($request->all(), [
            'device' => 'required',
            'language' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->error->formValidationFailed($validator->messages()),
            200);
        }

        return response()->json($this->init->getDay($request->input()));
    }

    /* First call when a user clicks on the sofafan button */
    public function initSofaFan(Request $request)
    {
    	return response()->json($this->init->initSofaFan($request->attributes->get('device')->language_id));
    }

    /* First call when there is no match on the current day */
    public function initNonMatchDay(Request $request)
    {
        return response()->json($this->init->initNonMatchDay($request->attributes->get('device')->language_id));
    }
}
