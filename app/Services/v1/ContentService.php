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
        $output = $modules;

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
                array_push($output, $filteredSponsor);
                $place++;
            } elseif ($sponsor->match == $matchId) {
                // @todo make time calculation !!!
                array_push($output, $filteredSponsor);
                $place++;
            } elseif ($sponsor->competition == $competitionId) {
                // @todo make time calculation !!!
                array_push($outputoutput, $filteredSponsor);
                $place++;
            } elseif ($sponsor->basic) {
                array_push($output, $filteredSponsor);
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
            // $post = collect($post);
            // $post->forget('id');
            $output['posts'][] = $post;
        }

        return $output;
    }


    public function live_action($matchId, $languageId)
    {
        /////////////////////////////////////////
        // @todo FINALIZE WITH MARK
        /////////////////////////////////////////
        
        $actions = DB::table('live_action_happening as lah')
            ->leftJoin('language_associations as la', 'lah.text_association_id', '=', 'la.language_association_id')
            ->leftJoin('live_action_events as lae', 'lah.event', '=', 'lae.id')
            ->leftJoin('language_associations as laen', 'lae.name_association_id', '=', 'laen.language_association_id')
            ->leftJoin('language_associations as laet', 'lae.text_association_id', '=', 'laet.language_association_id')
            ->leftJoin('player_to_happening as pth', 'lah.id', '=', 'pth.happening')
            ->leftJoin('players', 'pth.player', '=', 'players.id')
            ->where('lah.match_id', $matchId)
            // ->where('la.language', $languageId)
            // ->where('laen.language', $languageId)
            // ->where('laet.language', $languageId)
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


    /* returns the team details with the inputed id or the client's team details if the id is 0 */
    public function team($teamId)
    {
        $team = DB::table('teams');
        if ($teamId == 0) {
            $team = $team->where('teams.owner_club', 1);
        } else {
            $team = $team->where('teams.id', $teamId);
        }
        $team = $team->select(
            'teams.id as id',
            'teams.name as name',
            // 'teams.nickname as nickname',
            'teams.logo as logo',
            'teams.main_color as color',
            'teams.secondary_color as secondary_color'
        )->first();

        return $team;
    }


    /* returns the inputed team's players */
    public function players($teamId)
    {
        $players = DB::table('players_to_team')
            ->join('players', 'players_to_team.player', '=', 'players.id')
            ->where('players_to_team.team', $teamId)
            ->select(
                'players.name as name', 
                'players.number as number', 
                'players.picture as picture', 
                'players.birthdate as birthday'
            )->get();

        return $players;
    }


    /* get the client's team staff */
    public function staff($languageId)
    {
        $staff = DB::table('staff')
            ->leftJoin('language_associations as title', 'title.language_association_id', '=', 'staff.title_association_id')
            ->leftJoin('language_associations as description', 'description.language_association_id', '=', 'staff.description')
            ->where('title.language', $languageId)
            ->where('description.language', $languageId)
            ->select(
                'staff.name as name', 
                'staff.picture as picture', 
                'title.text as title', 
                'description.text as description'
            )->get();

        return $staff;
    }


    /* get the client's front officers */
    public function articles($languageId)
    {
        $articles = DB::table('articles')
            ->leftJoin('language_associations as title', 'title.language_association_id', '=', 'articles.title_association_id')
            ->leftJoin('language_associations as text', 'text.language_association_id', '=', 'articles.text_association_id')
            ->where('title.language', $languageId)
            ->where('text.language', $languageId)
            ->select(
                'articles.img as image',
                'title.text as title', 
                'text.text as text'
            )->get();

        return $articles;
    }


    /* get the client's front officers */
    public function front_office($languageId)
    {
        $front_office = DB::table('front_office')
            ->leftJoin('language_associations as title', 'title.language_association_id', '=', 'front_office.title_association_id')
            ->leftJoin('language_associations as description', 'description.language_association_id', '=', 'front_office.description_association_id')
            ->where('title.language', $languageId)
            ->where('description.language', $languageId)
            ->select(
                'front_office.name as name', 
                'front_office.picture as picture', 
                'title.text as title', 
                'description.text as description'
            )->get();

        return $front_office;
    }


    /* get the client's team matches */
    public function matches($languageId)
    {
        $matches = DB::table('matches')
            ->join('competitions', 'matches.competition', '=', 'competitions.id')
            ->join('language_associations as competition_name', 'competitions.name_association_id', '=', 'competition_name.language_association_id')
            ->join('teams as home', 'matches.home', '=', 'home.id')
            ->join('teams as away', 'matches.away', '=', 'away.id')
            ->join('stadiums', 'matches.stadium', '=', 'stadiums.id')
            ->join('language_associations as stadium_name', 'stadiums.name_association_id', '=', 'stadium_name.language_association_id')
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
            )->get();

        return $matches;
    }


    /* get the client's team standings */
    public function standings()
    {
        $standings = DB::table('standings')
            ->leftJoin('teams', 'standings.team_id', '=', 'teams.id')
            ->orderBy('competition_id', 'desc')
            ->orderBy('sort')
            ->select(
                'standings.competition_id as competition_id',
                'teams.id as id',
                'standings.sort as sort',
                'standings.points as points',
                'teams.name as name',
                // 'teams.nickname as nickname',
                'teams.logo as logo',
                'teams.main_color as color',
                'teams.secondary_color as secondary_color'
            )->get();

        return $standings;
    }


    /* get the client's team competitions */
    public function competitions($languageId)
    {
        $competitions = DB::table('competitions')
            ->join('language_associations as name', 'competitions.name_association_id', '=', 'name.language_association_id')
            ->where('name.language', $languageId)
            ->select(
                'name.text as name',
                'competitions.logo as logo'
            )->get();

        return $competitions;
    }


    /* get the client's team basic infos */
    public function basics($languageId)
    {
        $basics = DB::table('basics')
            ->leftJoin('language_associations as title', 'title.language_association_id', '=', 'basics.name_association_id')
            ->leftJoin('language_associations as description', 'description.language_association_id', '=', 'basics.text_association_id')
            ->where('title.language', $languageId)
            ->where('description.language', $languageId)
            ->select(
                'title.text as title', 
                'description.text as description',
                'basics.picture as picture'
            )->get();

        return $basics;
    }



    /* get the client's team talents */
    public function talents($languageId)
    {
        $talents = DB::table('talents')
            ->leftJoin('language_associations as description', 'description.language_association_id', '=', 'talents.description_association_id')
            ->leftJoin('language_associations as name', 'name.language_association_id', '=', 'talents.name_association_id')
            ->where('description.language', $languageId)
            ->select(
                'name.text as name',
                'talents.background as background',
                'talents.profile_picture as profile_picture',
                'talents.big_picture as big_picture',
                'description.text as description'
            )->get();

        return $talents;
    }

}