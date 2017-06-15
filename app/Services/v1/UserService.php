<?php

namespace App\Services\v1;

use DB;

use App\Services\v1\ErrorService;
use App\Services\v1\AppInitService;

/**
* @todo add comment here
*/
class UserService extends Service
{
	protected $error;
	public function __construct(ErrorService $error, AppInitService $appInit) 
	{
		$this->error = $error;
		$this->appInit = $appInit;
	}



	/* Make current user log in */
	public function ticket($request) 
	{
		/* define input fields */
		$input = $request->input();
		$device = $request->attributes->get('device');

		/* get the ticket */
		$ticket = DB::table('ticket')->where('code', $input['ticket'])->first();
		if ($ticket == null) {
			return $this->error->userDoesNotHaveTicket();
		}

		/* check the date and time */
		$now = strtotime(date("Y-m-d H:i:s"));
		if ($now < $ticket->start) {
			return $this->error->matchHasNotStarted();
		}
		if ($now > $ticket->finish) {
			return $this->error->matchHasFinished();
		}


		$user = DB::table('users')->where('email', $input['email'])->first();
		if ($user == null) {
			/* check if relation exists */
			$relation = DB::table('users_to_ticket')->where('ticket', $ticket->id)->first();
			
			/* if relation exists then it's not the users ticket */
			if ($relation != null) {
				return $this->error->notTheUsersTicket();

			/* if relation does not exist then create user and send a response */
			} else {
				$userId = DB::table('users')->insertGetId(['email' => $input['email'], 'lang' => $device->language_id]);
				DB::table('device_tokens')->where('id', $device->id)->update(['user_id' => $userId, 'expires' => $ticket->finish, 'match_id' => $ticket->match_id]);
				DB::table('users_to_ticket')->insert(['user' => $userId, 'ticket' => $ticket->id]);
				
				return $this->createResponse([
					"is_user" => 0,
					"ticket" => $input['ticket'],
					"email" => $input['email']
				]);
			}
		} 
		else
		{
			/* check if relation exists between the user and the ticket */
			$relation = DB::table('users_to_ticket')->where('user', $user->id)->where('ticket', $ticket->id)->first();

			/* if relation doesn't exist then it's not the users ticket */
			if ($relation == null) {
				DB::table('users_to_ticket')->insert(['user' => $user->id, 'ticket' => $ticket->id]);
				DB::table('device_tokens')->where('id', $device->id)->update(['expires' => $ticket->finish, 'match_id' => $ticket->match_id]);
			} 

			/* if relation exists then send response */
			return $this->createResponse([
				"is_user" => 1
			]);
		}
	}



	/* Make current user log in */
	public function login($request) 
	{
		/* define input fields */
		$input = $request->input();
		$device = $request->attributes->get('device');

		/* make the user log in */
		$user = DB::table('users')->where('id', $device->user_id)->where('password', $input['password'])->first();
		if ($user == null) {
			return $this->error->userAuthFailed();
		}

		/* send the response back */
		DB::table('device_tokens')->where('id', $device->id)->update(['logged_in' => 1]);
		return $this->createResponse([ 
			"app_content" => $this->appInit->initMatchDay($device->match_id, $device->language_id)
		]);
	}



	/* Make current user log out */
	public function logout($device) 
	{
		/* send the response back */
		DB::table('device_tokens')->where('id', $device->id)->update(['logged_in' => 0]);
		return $this->createResponse([ 
			"app_content" => "User has been successfully logged out."
		]);
	}



	public function registration($request)
	{
		/* define input fields */
		$input = $request->input();
		$device = $request->attributes->get('device');

		/* get the user details */
		DB::table('users')->where('id', $device->user_id)->update(['password' => $input['password']]);

		/* send the response back */
		DB::table('device_tokens')->where('id', $device->id)->update(['logged_in' => 1]);
		return $this->createResponse([
			"app_content" => $this->appInit->initMatchDay($device->match_id, $device->language_id)
		]);
	}



	public function passwordReset($request)
	{
		/* define input fields */
		$input = $request->input();
		$device = $request->attributes->get('device');

		/* get the user details */
		DB::table('users')->where('id', $device->user_id)->update(['password' => $input['password']]);

		/* send the response back */
		DB::table('device_tokens')->where('id', $device->id)->update(['logged_in' => 1]);
		return $this->createResponse([
			"app_content" => $this->appInit->initMatchDay($device->match_id, $device->language_id)
		]);
	}



	public function changeLanguage($request)
	{
		/* define input fields */
		$input = $request->input();
		$device = $request->attributes->get('device');

		/* send the response back */
		DB::table('device_tokens')->where('id', $device->id)->update(['language_id' => $input['language_id']]);
		return $this->createResponse([
			"app_content" => "Language of the user has been successfully changed."
		]);
	}



	/////////////////////////////////////////////////////
	/// HELPER FUNCTIONS
	/////////////////////////////////////////////////////

	public function addDevice($user, $ticket, $input)
	{
		$deviceToken = str_random(16);
		DB::table('device_tokens')->insert([
			'token' => $deviceToken, 
			'user_id' => $user->id,
			'match_id' => $ticket->match_id,
			'language_id' => $input['language'],
			'device' => $input['device'],
			'expires' => $ticket->finish,
			'created_at' => date("Y-m-d H:i:s")
		]);

		return $deviceToken;
	}
}