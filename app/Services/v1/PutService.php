<?php

namespace App\Services\v1;

use App\Services\v1\ErrorService;

use DB;

/**
* @todo add comment here
*/
class PutService extends Service
{    
    /* Define service provider(s) */
    protected $error;
    public function __construct(ErrorService $error)
    {
        $this->error = $error;
    }



    /* save contact us form response */
    public function contactus($input)
    {
        DB::table('emails')->insert([
            'sender_name' => $input['name'], 
            'sender_email' => $input['email'],
            'text' => $input['text']
        ]);

        return $this->createResponse(['answer' => 'Email has been sent successfully.']);
    }



    /* save message on message the team game */
    public function messageTheTeam($request)
    {
        /* get the device of the current user and validate it */
        $input = $request->input();
        $device = $request->attributes->get('device');

        /* save the message in the database */
        DB::table('message_the_team_messages')->insert([
            'user' => $device->user_id,
            'matchid' => $device->match_id,
            'message' => $input['message']
        ]);

        return $this->createResponse(['answer' => 'Message has been saved successfully.']);
    }



    /* save the user answer on ask the fans game */
    public function askTheFans($input)
    {
        /* get the device of the current user and validate it */
        if (!$device = $this->device($input['device_token'])) {
            return $this->error->deviceDoesNotExist();
        }

        /* save the message in the database */
        DB::table('ask_the_fans_user_tipps')->insert([
            'user' => $device->user_id,
            'question' => $input['question_id'],
            'answer' => $input['answer_id']
        ]);

        return $this->createResponse(['answer' => 'Answer has been successfully saved.']);
    }



    /* save the user answer on predict and win game */
    public function predictAndWin($input)
    {
        /* get the device of the current user and validate it */
        if (!$device = $this->device($input['device_token'])) {
            return $this->error->deviceDoesNotExist();
        }

        /* save predict and win tip in database */
        DB::table('predict_and_win_user_tipps')->insert([
            'user' => $device->user_id,
            'question' => $input['question_id'],
            'answer' => $input['answer_id']
        ]);

        return $this->createResponse(['answer' => 'Answer has been successfully saved.']);
    }
}