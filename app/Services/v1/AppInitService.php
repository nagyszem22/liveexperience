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
    public function getDay() 
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

        /* create output */
        $output = array();
        $output['match_day'] = 0;
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

        /* return content */
		return $content;
	}



    public function initSofaFan($languageId)
    {
        /* get today's match */
        $today = strtotime('00:00:00');
        $tomorrow = strtotime('00:00:00')+86400;
        $today = date('Y-m-d H:i:s', $today);
        $tomorrow = date('Y-m-d H:i:s', $tomorrow);
        $match = DB::table('matches')
            ->where('kickoff', '>', $today)
            ->where('kickoff', '<', $tomorrow)
            ->first();

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

        
        $content['live_action'] = $live_action;

        return $this->createResponse($content);
    }

    public function initNonMatchDay()
    {
        return $this->createResponse();
    }

}