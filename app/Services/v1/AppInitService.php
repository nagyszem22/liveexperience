<?php

namespace App\Services\v1;

use DB;
use Config;

use App\Services\v1\ContentService;
use App\Services\v1\ErrorService;

/**
* @todo add comment here
*/
class AppInitService extends Service
{
    /* initialize content provider */
    protected $content;
    protected $error;
    public function __construct(ContentService $content, ErrorService $error) 
    {
        $this->content = $content;
        $this->error = $error;
    }



	/* Get current client's details */
	public function getClient($client_url) 
	{
        /* get client data */
		$client = DB::table('clients')
            ->where('url', $client_url)
            ->select('url', 'logo', 'clients_name as name')
            ->first();

		return $this->createResponse($client);
	}


    
    /* get the type of the current day */    
    public function getDay($input) 
    {
        /* get next match */
        $today = strtotime('00:00:00');
        $tomorrow = strtotime('00:00:00')+86400;
        $today = date('Y-m-d H:i:s', $today);
        $tomorrow = date('Y-m-d H:i:s', $tomorrow);
        $match = DB::table('matches')
            ->where('kickoff', '>', $today)
            ->first();

        if (!$match) {
            return $this->error->noNextMatchFound();
        }

        /* if there is no saved token then create one */
        $token = DB::table('device_tokens')->where('token', $_SERVER['HTTP_TOKEN'])->first();
        if (!$token) {
            $deviceToken = str_random(16);
            DB::table('device_tokens')->insert([
                'token' => $deviceToken, 
                'user_id' => 0,
                'match_id' => 0,
                'language_id' => $input['language'],
                'device' => $input['device'],
                'expires' => 0,
                'created_at' => date("Y-m-d H:i:s")
            ]);
        } else {
            $deviceToken = $_SERVER['HTTP_TOKEN'];
        }

        /* create output */
        $output = array();
        $output['match_day'] = 0;
        $output['device_token'] = $deviceToken;
        if ($match->kickoff < $tomorrow) {
            $output['match_day'] = 1;
        }

        return $this->createResponse($output);
    }



	/* Get application details to set up application after login */
	public function initMatchDay($matchId, $languageId) 
	{
        /* get current match data */
        $match = $this->content->match($matchId, $languageId);

        /* get referees of the current match */
        $referees = $this->content->referees($matchId, $languageId);

        /* get current line ups */
        $line_ups = $this->content->line_ups($matchId, $match->home_team_id, $match->away_team_id);

        /* get current modules in the current time (main menu items) */
        $modules = $this->content->modules($matchId, $match->competition_id);

        /* get games and banner which are available for live experience */
        $live_experience = $this->content->live_experience($matchId, $match->competition_id);

        /* get live posts from users */
        $live_feed = $this->content->live_feed($matchId, 0);

        /* get basic settings of message the team */
        $message_the_team = $this->content->message_the_team($languageId);

        /* initialize content */
        $content = array();

        /* match details */
        $content['match']['goals']['home'] = $match->home_goals;
        $content['match']['goals']['home_halftime'] = $match->home_halftime_goals;
        $content['match']['goals']['away'] = $match->away_goals;
        $content['match']['goals']['away_halftime'] = $match->away_halftime_goals;
        $content['match']['kickoff'] = $match->kickoff;
        $content['match']['kickoff_real'] = $match->kickoff_real;
        $content['match']['end_time'] = $match->end_time;
        $content['match']['stadium'] = $match->stadium_name;
        $content['match']['competition_name'] = $match->competition_name;
        $content['match']['competition_logo'] = $match->competition_logo;
        $content['match']['referees'] = $referees;

        /* team details */
        $content['teams']['home']['name'] = $match->home_team_name;
        $content['teams']['home']['logo'] = $match->home_team_logo;
        $content['teams']['home']['color'] = $match->home_team_color;
        $content['teams']['home']['secondary_color'] = $match->home_team_secondary_color;
        $content['teams']['home']['goals'] = $match->home_goals;
        $content['teams']['home']['goals_halftime'] = $match->home_halftime_goals;
        $content['teams']['home']['line_up'] = $line_ups['home'];

        $content['teams']['away']['name'] = $match->away_team_name;
        $content['teams']['away']['logo'] = $match->away_team_logo;
        $content['teams']['away']['color'] = $match->away_team_color;
        $content['teams']['away']['secondary_color'] = $match->away_team_secondary_color;
        $content['teams']['away']['goals'] = $match->away_goals;
        $content['teams']['away']['goals_halftime'] = $match->away_halftime_goals;
        $content['teams']['away']['line_up'] = $line_ups['away'];

        /* modules (main menu items) */
        $content['modules'] = $modules;

        /* live experience games */
        $content['live_experience'] = $live_experience;

        /* live posts from users */
        $content['live_feed'] = $live_feed;

        /* add stadium tools */
        $content['stadium_tools'] = $this->content->stadium_tools($languageId);

        /* message the team main settings */
        $content['message_the_team'] = $message_the_team;

        /* return content */
		return $content;
	}



    /* Get application details to setup sofa fun part */
    public function initSofaFan($languageId)
    {
        /* get today's match */
        $today = strtotime('00:00:00');
        $tomorrow = strtotime('00:00:00')+86400;
        $today = date('Y-m-d H:i:s', $today);
        $tomorrow = date('Y-m-d H:i:s', $tomorrow);
        $match = DB::table('matches')
            /* uncomment the lines below and delete the third where clause in production */
            //->where('kickoff', '>', $today)
            // ->where('kickoff', '<', $tomorrow)
            ->where('id', 2)->first();

        /* return if there is no match today */
        if (!$match) {
            return $this->error->noMatchToday();
        }

        /* set match id */
        $matchId = $match->id;

        /* get current match data */
        $match = $this->content->match($matchId, $languageId);

        /* get referees of the current match */
        $referees = $this->content->referees($matchId, $languageId);

        /* get current line ups */
        $line_ups = $this->content->line_ups($matchId, $match->home_team_id, $match->away_team_id);

        /* get live posts from users */
        $live_feed = $this->content->live_feed($matchId, 0);
        

        $live_action = $this->content->live_action($matchId, $languageId);


        /* initialize content */
        $content = array();

        /* match details */
        $content['match']['goals']['home'] = $match->home_goals;
        $content['match']['goals']['home_halftime'] = $match->home_halftime_goals;
        $content['match']['goals']['away'] = $match->away_goals;
        $content['match']['goals']['away_halftime'] = $match->away_halftime_goals;
        $content['match']['kickoff'] = $match->kickoff;
        $content['match']['kickoff_real'] = $match->kickoff_real;
        $content['match']['end_time'] = $match->end_time;
        $content['match']['stadium'] = $match->stadium_name;
        $content['match']['competition_name'] = $match->competition_name;
        $content['match']['competition_logo'] = $match->competition_logo;
        $content['match']['referees'] = $referees;

        /* team details */
        $content['teams']['home']['name'] = $match->home_team_name;
        $content['teams']['home']['logo'] = $match->home_team_logo;
        $content['teams']['home']['color'] = $match->home_team_color;
        $content['teams']['home']['secondary_color'] = $match->home_team_secondary_color;
        $content['teams']['home']['goals'] = $match->home_goals;
        $content['teams']['home']['goals_halftime'] = $match->home_halftime_goals;
        $content['teams']['home']['line_up'] = $line_ups['home'];

        $content['teams']['away']['name'] = $match->away_team_name;
        $content['teams']['away']['logo'] = $match->away_team_logo;
        $content['teams']['away']['color'] = $match->away_team_color;
        $content['teams']['away']['secondary_color'] = $match->away_team_secondary_color;
        $content['teams']['away']['goals'] = $match->away_goals;
        $content['teams']['away']['goals_halftime'] = $match->away_halftime_goals;
        $content['teams']['away']['line_up'] = $line_ups['away'];

        /* live posts from users */
        $content['live_feed'] = $live_feed;

        /* live from the team */
        $content['live_action'] = $live_action;

        return $this->createResponse($content);
    }



    public function initNonMatchDay($languageId)
    {
        /* get today's match */
        $today = strtotime('00:00:00');
        $today = date('Y-m-d H:i:s', $today);
        $match = DB::table('matches')
            ->where('kickoff', '>', $today)
            ->first();

        /* initialize content */
        $content = array();

        /* add match deatils if they exist */
        if ($match) {

            /* next match details */
            $matchId = $match->id;

            /* get current match data */
            $match = $this->content->match($matchId, $languageId);

            /* get referees of the current match */
            $referees = $this->content->referees($matchId, $languageId);

            /* match details */
            $content['next_match']['exists'] = 1;
            $content['next_match']['kickoff'] = $match->kickoff;
            $content['next_match']['end_time'] = $match->end_time;
            $content['next_match']['stadium'] = $match->stadium_name;
            $content['next_match']['competition_name'] = $match->competition_name;
            $content['next_match']['competition_logo'] = $match->competition_logo;
            $content['next_match']['referees'] = $referees;

            /* team details */
            $content['next_match']['teams']['exists'] = 1;
            $content['next_match']['teams']['home']['name'] = $match->home_team_name;
            $content['next_match']['teams']['home']['logo'] = $match->home_team_logo;
            $content['next_match']['teams']['home']['color'] = $match->home_team_color;
            $content['next_match']['teams']['home']['secondary_color'] = $match->home_team_secondary_color;

            $content['next_match']['teams']['away']['name'] = $match->away_team_name;
            $content['next_match']['teams']['away']['logo'] = $match->away_team_logo;
            $content['next_match']['teams']['away']['color'] = $match->away_team_color;
            $content['next_match']['teams']['away']['secondary_color'] = $match->away_team_secondary_color;

        } else {

            /* if there is no next match */
            $content['next_match']['exists'] = 0;
        }

        /* add client's articles */
        $content['articles'] = $this->content->articles($languageId);

        /* add client's events */
        $content['events'] = $this->content->events($languageId);

        /* add client's team details */
        $team = $this->content->team(0);
        $content['club_zone']['team'] = $team;

        /* add client's team players */
        $content['club_zone']['players'] = $this->content->players($team->id);

        /* add client's team staff */
        $content['club_zone']['staff'] = $this->content->staff($languageId);

        /* add client's team front office */
        $content['club_zone']['front_office'] = $this->content->front_office($languageId);

        /* add client's basics */
        $content['club_zone']['basics'] = $this->content->basics($languageId);

        /* add client's talents */
        $content['club_zone']['talents'] = $this->content->talents($languageId);

        /* add client's team matches */
        $content['club_zone']['matches'] = $this->content->matches($languageId);

        /* add client's tables */
        $content['club_zone']['competitions'] = $this->content->competitions($languageId);
        $content['club_zone']['standings'] = $this->content->standings();

        return $this->createResponse($content);
    }

}