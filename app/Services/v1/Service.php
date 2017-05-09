<?php

namespace App\Services\v1;

use DB;

/**
* @todo add comment here
*/
class Service
{
	/* Create response array */
	public function createResponse($content) 
	{
		return [
			"status" => [
				"code" => 0,
				"name" => "successfull request"
			],
			"content" => $content
		];
	}

	/* Get user's device */
	public function device($token)
	{
		/* get current the device of the current user */
        return DB::table('device_tokens')
                  ->where('token', $token)
                  ->where('expires', '>', strtotime(date("Y-m-d H:i:s")))
                  ->first();
	}
}