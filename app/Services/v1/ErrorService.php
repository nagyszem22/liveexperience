<?php

namespace App\Services\v1;

/**
* @todo add comment here
*/
class ErrorService
{
	public function clientDoesNotExist() 
	{
		return [
    		"status" => [
    			"code" => 1,
    			"name" => "client does not exist"
         	]
    	];
	}

	public function formValidationFailed($message) 
	{
		return [
    		"status" => [
    			"code" => 2,
    			"name" => "form validation failed"
         	],
         	"content" => $message
    	];
	}

    public function userAuthFailed() 
    {
        return [
            "status" => [
                "code" => 3,
                "name" => "user authentication failed"
            ]
        ];
    }

    public function userDoesNotHaveTicket() 
    {
        return [
            "status" => [
                "code" => 4,
                "name" => "user does not have a ticket"
            ]
        ];
    }

    public function notTheUsersTicket() 
    {
        return [
            "status" => [
                "code" => 5,
                "name" => "ticket is not owned by the user"
            ]
        ];
    }

    public function matchHasNotStarted() 
    {
        return [
            "status" => [
                "code" => 6,
                "name" => "match has not started yet"
            ]
        ];
    }

    public function matchHasFinished() 
    {
        return [
            "status" => [
                "code" => 7,
                "name" => "match has already ended"
            ]
        ];
    }

    public function noNextMatchFound() 
    {
        return [
            "status" => [
                "code" => 8,
                "name" => "no next match found"
            ]
        ];
    }

    public function noMatchToday() 
    {
        return [
            "status" => [
                "code" => 9,
                "name" => "there is no match today"
            ]
        ];
    }

    public function deviceDoesNotExist() 
    {
        return [
            "status" => [
                "code" => 10,
                "name" => "device does not exist"
            ]
        ];
    }
    
}