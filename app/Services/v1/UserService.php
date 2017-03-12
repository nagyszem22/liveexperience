<?php

namespace App\Services\v1;

use DB;

use App\Services\v1\ErrorService;

/**
* @todo add comment here
*/
class UserService extends Service
{
	protected $error;
	public function __construct(ErrorService $error) 
	{
		$this->error = $error;
	}



	/* Make current user log in */
	public function ticket($input) 
	{
		/* get the ticket */
		$ticket = DB::table('ticket')->where('code', $input['ticket'])->first();
		if ($ticket == null) {
			return $this->error->userDoesNotHaveTicket();
		}

		/* check the date and time */
		/* @todo: convert it to the local match time */
		$now = strtotime(date("Y-m-d H:i:s"));
		$now = 2147483647; // @todo: remove this line on production
		if ($now < $ticket->start) {
			return $this->error->matchHasNotStarted();
		}
		if ($now > $ticket->start) {
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
				$userId = DB::table('users')->insertGetId(['email' => $input['email']]);
				DB::table('users_to_ticket')->insert([ 'user' => $userId, 'ticket' => $ticket->id]);
				return $this->createResponse([
					"email" => $input['email'], 
					"ticket" => $input['ticket'],
					"is_user" => 0
				]);
			}
		} 
		else
		{
			/* check if relation exists between the user and the ticket */
			$relation = DB::table('users_to_ticket')->where('user', $user->id)->where('ticket', $ticket->id)->first();

			/* if relation doesn't exist then it's not the users ticket */
			if ($relation == null) {
				return $this->error->notTheUsersTicket();
			} 

			/* if relation exists then send response */
			else {
				return $this->createResponse([
					"email" => $input['email'], 
					"ticket" => $input['ticket'],
					"is_user" => 1
				]);
			}
		}
	}



	/* Make current user log in */
	public function login($input) 
	{
		$user = DB::table('users')->where('email', $input['email'])->where('password', $input['password'])->first();
		if ($user == null) {
			return $this->error->userAuthFailed();
		}

		$ticket = DB::table('ticket')->where('code', $input['ticket'])->first();
		if ($ticket == null) {
			return $this->error->userDoesNotHaveTicket();
		}

		$relation = DB::table('users_to_ticket')->where('user', $user->id)->where('ticket', $ticket->id)->first();
		if ($relation == null) {
			return $this->error->notTheUsersTicket();
		}

		/* @todo: convert it to the local match time */
		$now = strtotime(date("Y-m-d H:i:s"));
		$now = 2147483647;
		if ($now < $ticket->start) {
			return $this->error->matchHasNotStarted();
		}

		if ($now > $ticket->start) {
			return $this->error->matchHasFinished();
		}

		$deviceToken = str_random(16);
		DB::table('device_tokens')->insertGetId(
		    ['token' => $deviceToken, 'user_id' => $user->id, 'device' => $input['device']]
		);

		return $this->createResponse(["device_token" => $deviceToken]);
	}



	/* Make current user log out */
	public function logout($deviceToken) 
	{
		
	}
}