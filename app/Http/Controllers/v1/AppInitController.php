<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Services\v1\AppInitService;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AppInitController extends Controller
{
	/* Define service provider(s) */
	protected $init;
	public function __construct(AppInitService $service)
	{
		$this->init = $service;
	}

	/* First call before login */
    public function init($client) 
    {
    	return response()->json($this->init->getClient($client));
    }

    /* Get the type of the day (match/non-match day) */
    public function getDay()
    {
        return response()->json($this->init->getDay());
    }

    /* First call when after clicks on sofafan button */
    public function initSofaFan($client, $languageId)
    {
    	return response()->json($this->init->initSofaFan($languageId));
    }
}
