<?php

namespace App\Services\v1;

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
}