<?php

namespace App\Services\v1;

use DB;

/**
* @todo add comment here
*/
class AppInitService
{
	/* Get current client's details */
	public function getClient($client) 
	{
		$output = array();
		$clients = DB::table('clients')->where('url', $client)->get();

		foreach ($clients as $client) {
			$output['url'] = $client->url;
			$output['logo'] = $client->logo;
			$output['name'] = $client->clients_name;
		}

		return $output;
	}
}