<?php

namespace App\Services\v1;

use DB;

/**
* @todo add comment here
*/
class AppInitService extends Service
{
	/* Get current client's details */
	public function getClient($client) 
	{
		$clients = DB::table('clients')->where('url', $client)->get();

		$content = [];
		foreach ($clients as $client) {
			$content['url'] = $client->url;
			$content['logo'] = $client->logo;
			$content['name'] = $client->clients_name;
		}

		return $this->createResponse($content);
	}
}