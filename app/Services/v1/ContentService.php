<?php

namespace App\Services\v1;

use DB;

/**
* @todo add comment here
*/
class ContentService
{
    /* get all necessary information of the current match */
	public function match($matchId, $languageId)
    {
        $match = DB::table('matches')
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

        return $match;
    }


    /* get referees of the curent match */
    public function referees($matchId, $languageId)
    {
        $referees = DB::table('referee_to_match')
            ->join('referees', 'referee_to_match.referee', '=', 'referees.id')
            ->join('language_associations as role', 'referee_to_match.role_association_id', '=', 'role.language_association_id')
            ->where('match_id', $matchId)
            ->where('role.language', $languageId)
            ->select(
                'referees.name as name',
                'role.text as role'
            )->get();

        return $referees;
    }


    /* get the line ups of the current teams */
    public function line_ups($matchId, $homeId, $awayId)
    {
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

        $output = array('home' => [], 'away' => []);
        foreach ($line_ups as $player) {
            if ($player->team_id == $homeId) {
                $player->team_id = 0;
                $output['home'][] = $player;
            } else if ($player->team_id == $awayId) {
                $player->team_id = 0;
                $output['away'][] = $player;
            }
        }

        return $output;
    }


    /* get modules of the current match in the current time (main menu items) */
    public function modules($matchId, $competitionId)
    {
        $modules = DB::table('modules')
            ->orderBy('place')
            ->select('name', 'logo', 'place')->get();

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

        /* modules - built in mudules */
        $output = array();
        $output['modules'] = $modules;

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
                array_push($output['modules'], $filteredSponsor);
                $place++;
            } elseif ($sponsor->match == $matchId) {
                // @todo make time calculation !!!
                array_push($output['modules'], $filteredSponsor);
                $place++;
            } elseif ($sponsor->competition == $competitionId) {
                // @todo make time calculation !!!
                array_push($outputoutput['modules'], $filteredSponsor);
                $place++;
            } elseif ($sponsor->basic) {
                array_push($output['modules'], $filteredSponsor);
                $place++;
            }
        }

        return $output;
    }


    /* get banner and games of live experience menu */
    public function live_experience($matchId, $competitionId)
    {
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

        /* live experience games */
        $output['games'] = $games;

        /* live experience banner */
        // @todo make time calculations !!!
        foreach ($banners as $banner) {
            // set filtered sponsor
            $filteredBanner['name'] = $banner->name;
            $filteredBanner['picture'] = $banner->picture;
            $filteredBanner['link'] = $banner->link;

            // set current sponsors
            if ($banner->active) {
                $output['banner'] = $filteredBanner;
                break;
            } elseif ($banner->match == $matchId) {
                // @todo make time calculation !!!
                $output['banner'] = $filteredBanner;
                break;
            } elseif ($banner->competition == $competitionId) {
                // @todo make time calculation !!!
                $output['banner'] = $filteredBanner;
                break;
            } elseif ($banner->basic) {
                $output['banner'] = $filteredBanner;
                break;
            }
        }

        return $output;
    }


    /* get live posts from users */
    public function live_feed($matchId, $lastId)
    {
        $posts = DB::table('livescreen as ls')
            ->leftJoin('livescreen_to_hashtag as lth', 'lth.livescreen', '=', 'ls.id')
            ->join('livescreen_hashtags as lh', 'lh.id', '=', 'lth.hashtag')
            // ->join('users', 'users.id', '=', 'ls.userid')
            ->where('ls.matchid', '=', $matchId)
            ->where('ls.id', '>', $lastId)
            ->groupBy('ls.id')
            ->select(
                'ls.id as id',
                'ls.url as url',
                'ls.type as type',
                'ls.likes as likes',
                'ls.created_at as posted_at',
                DB::raw('group_concat(lh.hashtag) as hashtags')
            )->get();

        $output = array('last_id' => last($posts)->id, 'posts' => []);
        foreach ($posts as $post) {
            $post = collect($post);
            $post->forget('id');
            $output['posts'][] = $post;
        }

        return $output;
    }


    public function live_action($matchId, $languageId)
    {
        $actions = DB::table('live_action_happening as lah')
            ->leftJoin('language_associations as la', 'lah.text_association_id', '=', 'la.language_association_id')
            ->leftJoin('live_action_events as lae', 'lah.event', '=', 'lae.id')
            ->join('language_associations as laen', 'lae.name_association_id', '=', 'laen.language_association_id')
            ->join('language_associations as laet', 'lae.text_association_id', '=', 'laet.language_association_id')
            ->leftJoin('player_to_happening as pth', 'lah.id', '=', 'pth.happening')
            ->leftJoin('players', 'pth.player', '=', 'players.id')
            ->where('lah.match_id', $matchId)
            ->where('la.language', $languageId)
            ->where('laen.language', $languageId)
            ->where('laet.language', $languageId)
            ->select(
                'lah.id as id',
                'lah.minute as minute',
                'lah.expected_minute as expected_minute',
                'lah.likes as likes',
                'lah.picture as picture',
                'lah.video as video',
                'la.text as text',
                'lae.color as event_color',
                'lae.background as event_background',
                'lae.icon as event_icon',
                'laen.text as event_name',
                'laet.text as event_text',
                'players.name as player_name',
                'players.number as player_number'
            )->get();

        return $actions;
    }

}