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



	/* Get application details to set up application after login */
	public function initMatchDay($matchId, $languageId) 
	{
		$content = [];
		$query = DB::table('matches')
            ->join('competitions', 'matches.competition', '=', 'competitions.id')
            ->join('language_associations as competition_name', 'competitions.name_association_id', '=', 'competition_name.language_association_id')
            ->join('teams as home', 'matches.home', '=', 'home.id')
            ->join('teams as away', 'matches.away', '=', 'away.id')
            ->join('stadiums', 'matches.stadium', '=', 'stadiums.id')
            ->join('language_associations as stadium_name', 'stadiums.name_association_id', '=', 'stadium_name.language_association_id')
            ->where('matches.id', $matchId)
            ->where('competition_name.language', $languageId)
            ->where('stadium_name.language', $languageId)
            ->select(
            	'matches.kickoff as kickoff',
            	'matches.kick_off_real as kickoff_real',
            	'matches.gate_opening as gate_opening',
            	'matches.home_goal as home_goals',
            	'matches.halftime_home as home_halftime_goals',
            	'matches.halftime_away as away_halftime_goals',
            	'matches.away_goal as away_goals',
            	'matches.end_time as end_time',
            	'competitions.logo as competition_logo',
            	'stadium_name.text as stadium_name',
            	'competition_name.text as competition_name',
            	'competitions.id as competition_id',
            	'home.id as home_team_id',
            	'home.name as home_team_name',
            	'home.logo as home_team_logo',
            	'home.main_color as home_team_color',
            	'home.secondary_color as home_team_secondary_color',
            	'away.id as away_team_id',
            	'away.name as away_team_name',
            	'away.logo as away_team_logo',
            	'away.main_color as away_team_color',
            	'away.secondary_color as away_team_secondary_color'
            )->first();

        $line_ups = DB::table('line_up')
        	->join('players', 'line_up.player', '=', 'players.id')
        	->where('match_id', $matchId)
        	->select(
        		'line_up.role as role',
        		'line_up.team as team_id',
        		'line_up.change_status as changed',
        		'players.name as name', 
        		'players.number as number', 
        		'players.picture as picture', 
        		'players.birthdate as birthday'
        	)->get();

        $referees = DB::table('referee_to_match')
        	->join('referees', 'referee_to_match.referee', '=', 'referees.id')
        	->join('language_associations as role', 'referee_to_match.role_association_id', '=', 'role.language_association_id')
        	->where('match_id', $matchId)
        	->where('role.language', $languageId)
        	->select(
        		'referees.name as name',
        		'role.text as role'
        	)->get();

        $sponsors = DB::table('sponsors')
        	->leftJoin('sponsor_time as time', 'sponsors.id', '=', 'time.sponsor')
        	->select(
        		'sponsors.name as name',
        		'sponsors.logo as logo',
        		'sponsors.basic as basic',
        		'time.type as type',
        		'time.start as start',
        		'time.finish as finish',
        		'time.break_point_start as break_point_start',
        		'time.break_point_finish as break_point_finish',
        		'time.clear_time as clear_time',
        		'time.competition as competition',
        		'time.active as active',
        		'time.match_id as match'
        	)->get();

        $modules = DB::table('modules')
        	->orderBy('place')
        	->select('name', 'logo', 'place')->get();

        $games = DB::table('games')
        	->leftJoin('game_time', 'games.id', '=', 'game_time.game')
        	->orderBy('games.place')
        	->select(
        		'games.name as name', 
        		'games.logo as logo',
        		'games.place as place',
        		'game_time.start as start',
        		'game_time.finish as finish'
        	)->get();

        $banners = DB::table('banners')
        	->leftJoin('banner_time as time', 'banners.id', '=', 'time.banner')
        	->select(
        		'banners.name as name',
        		'banners.picture as picture',
        		'banners.link as link',
        		'banners.basic as basic',
        		'time.start as start',
        		'time.finish as finish',
        		'time.break_point_start as break_point_start',
        		'time.break_point_finish as break_point_finish',
        		'time.competition as competition',
        		'time.active as active',
        		'time.match_id as match'
        	)->get();

        /* match details */
        $content['match']['goals']['home'] = $query->home_goals;
        $content['match']['goals']['home_halftime'] = $query->home_halftime_goals;
        $content['match']['goals']['away'] = $query->away_goals;
        $content['match']['goals']['away_halftime'] = $query->away_halftime_goals;
        $content['match']['kickoff'] = $query->kickoff;
        $content['match']['kickoff_real'] = $query->kickoff_real;
        $content['match']['end_time'] = $query->end_time;
        $content['match']['stadium'] = $query->stadium_name;
        $content['match']['competition_name'] = $query->competition_name;
        $content['match']['competition_logo'] = $query->competition_logo;
        $content['match']['referees'] = $referees;

        /* team details */
        $content['teams']['home']['name'] = $query->home_team_name;
        $content['teams']['home']['logo'] = $query->home_team_logo;
        $content['teams']['home']['color'] = $query->home_team_color;
        $content['teams']['home']['secondary_color'] = $query->home_team_secondary_color;
        $content['teams']['home']['goals'] = $query->home_goals;
        $content['teams']['home']['goals_halftime'] = $query->home_halftime_goals;

        $content['teams']['away']['name'] = $query->away_team_name;
        $content['teams']['away']['logo'] = $query->away_team_logo;
        $content['teams']['away']['color'] = $query->away_team_color;
        $content['teams']['away']['secondary_color'] = $query->away_team_secondary_color;
        $content['teams']['away']['goals'] = $query->away_goals;
        $content['teams']['away']['goals_halftime'] = $query->away_halftime_goals;

        /* lineups */
        foreach ($line_ups as $player) {
        	if ($player->team_id == $query->home_team_id) {
        		$player->team_id = 0;
        		$content['teams']['home']['line_up'][] = $player;
        	} else if ($player->team_id == $query->away_team_id) {
        		$player->team_id = 0;
        		$content['teams']['away']['line_up'][] = $player;
        	}
        }

        /* modules - built in mudules */
        $content['modules'] = $modules;

        /* modules - sponsors */
        // @todo make time calculations !!!
        $place = 5;
        foreach ($sponsors as $sponsor) {
        	// set filtered sponsor
        	$filteredSponsor['name'] = $sponsor->name;
        	$filteredSponsor['logo'] = $sponsor->logo;
        	$filteredSponsor['place'] = $place;

        	// set current sponsors
        	if ($sponsor->active) {
        		array_push($content['modules'], $filteredSponsor);
        		$place++;
        	} elseif ($sponsor->match == $matchId) {
        		// @todo make time calculation !!!
        		array_push($content['modules'], $filteredSponsor);
        		$place++;
        	} elseif ($sponsor->competition == $query->competition_id) {
        		// @todo make time calculation !!!
        		array_push($content['modules'], $filteredSponsor);
        		$place++;
        	} elseif ($sponsor->basic) {
        		array_push($content['modules'], $filteredSponsor);
        		$place++;
        	}
        }

        /* live experience games */
        $content['live_experience']['games'] = $games;

        /* live experience banner */
        // @todo make time calculations !!!
        foreach ($banners as $banner) {
        	// set filtered sponsor
        	$filteredBanner['name'] = $banner->name;
        	$filteredBanner['picture'] = $banner->picture;
        	$filteredBanner['link'] = $banner->link;

        	// set current sponsors
        	if ($banner->active) {
        		$content['live_experience']['banner'] = $filteredBanner;
        		break;
        	} elseif ($banner->match == $matchId) {
        		// @todo make time calculation !!!
        		$content['live_experience']['banner'] = $filteredBanner;
        		break;
        	} elseif ($banner->competition == $query->competition_id) {
        		// @todo make time calculation !!!
        		$content['live_experience']['banner'] = $filteredBanner;
        		break;
        	} elseif ($banner->basic) {
        		$content['live_experience']['banner'] = $filteredBanner;
        		break;
        	}
        }

		return $content;
	}

}