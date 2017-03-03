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
}
