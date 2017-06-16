<?php

namespace App\Services\v1;

use App\Services\v1\ErrorService;
use App\Services\v1\ContentService;

use DB;

/**
* @todo add comment here
*/
class PutService extends Service
{    
    /* Define service provider(s) */
    protected $error;
    public function __construct(ErrorService $error, ContentService $content)
    {
        $this->error = $error;
        $this->content = $content;
    }



    /* save contact us form response */
    public function contactus($input)
    {
        DB::table('emails')->insert([
            'sender_name' => $input['name'], 
            'sender_email' => $input['email'],
            'subject' => $input['subject'],
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
    public function askTheFans($request)
    {
        /* get the device of the current user and validate it */
        $input = $request->input();
        $device = $request->attributes->get('device');

        /* save the message in the database */
        DB::table('ask_the_fans_user_tipps')->insert([
            'user' => $device->user_id,
            'question' => $input['question_id'],
            'answer' => $input['answer_id']
        ]);

        return $this->createResponse(['answer' => 'Answer has been successfully saved.']);
    }



    /* save the user answer on predict and win game */
    public function predictAndWin($request)
    {
        /* get the device of the current user and validate it */
        $input = $request->input();
        $device = $request->attributes->get('device');

        /* save predict and win tip in database */
        DB::table('predict_and_win_user_tipps')->insert([
            'user' => $device->user_id,
            'question' => $input['question_id'],
            'answer' => $input['answer_id']
        ]);

        return $this->createResponse(['answer' => 'Answer has been successfully saved.']);
    }



    /* save or update spotify songs */
    public function spotify($request)
    {
        /* get the device of the current user and validate it */
        $input = $request->input();
        $device = $request->attributes->get('device');

        /* save or update spotify sonng */
        if (!DB::table('spotify')->where('spotify_id', $input['spotify_id'])->first()) {
            DB::table('spotify')->insert([
                'userid' => $device->user_id,
                'spotify_id' => $input['spotify_id'],
                'vote' => 1,
                'song' => $input['song'],
                'album_cover' => $input['album_cover'],
                'artist' => $input['artist']
            ]);
        } else {
            DB::table('spotify')->where('spotify_id', $input['spotify_id'])->increment('vote');
        }

        return $this->createResponse(['answer' => 'Song has been successfully saved or updated.']);
    }



    /* save fanshelp vote */
    public function fanshelp($request)
    {
        /* get the device of the current user and validate it */
        $input = $request->input();
        $device = $request->attributes->get('device');

        /* increase the current type of vote number */
        $query = DB::table('live_action_happening')->where('id', $input['question_id'])->where('able_to_vote', 1)->where('match_id', $device->match_id);
        if ($input['vote'] == 'agree') {
            $query->increment('agree');
        } elseif ($input['vote'] == 'disagree') {
            $query->increment('disagree');
        } elseif ($input['vote'] == 'canttel') {
            $query->increment('canttel');
        }

        return $this->createResponse(['answer' => 'The vote has been successfully saved.']);
    }



    /* mall */
    public function mall($request)
    {
        /* get the device of the current user and validate it */
        $input = $request->input();
        $device = $request->attributes->get('device');

        // @todo save email and check coupon logic comes here

        return $this->createResponse(['coupons' => $this->content->mall($device->language_id)]);
    }
}