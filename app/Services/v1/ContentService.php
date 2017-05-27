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
            ->join('teams as home', 'matches.home', '=', 'home.id')
            ->join('teams as away', 'matches.away', '=', 'away.id')
            ->join('stadiums', 'matches.stadium', '=', 'stadiums.id')
            ->where('matches.id', $matchId);
        if ($languageId = 1) {
            $match = $match->select( 'matches.kickoff as kickoff', 'matches.kick_off_real as kickoff_real', 'matches.gate_opening as gate_opening', 'matches.home_goal as home_goals', 'matches.halftime_home as home_halftime_goals', 'matches.halftime_away as away_halftime_goals', 'matches.away_goal as away_goals', 'matches.end_time as end_time', 'competitions.logo as competition_logo', 'competitions.id as competition_id', 'home.id as home_team_id', 'home.name as home_team_name', 'home.logo as home_team_logo', 'home.main_color as home_team_color', 'home.secondary_color as home_team_secondary_color', 'away.id as away_team_id', 'away.name as away_team_name', 'away.logo as away_team_logo', 'away.main_color as away_team_color', 'away.secondary_color as away_team_secondary_color', 'competitions.name_hu as competition_name', 'stadiums.name_hu as stadium_name')->first();
        } else {
            $match = $match->select( 'matches.kickoff as kickoff', 'matches.kick_off_real as kickoff_real', 'matches.gate_opening as gate_opening', 'matches.home_goal as home_goals', 'matches.halftime_home as home_halftime_goals', 'matches.halftime_away as away_halftime_goals', 'matches.away_goal as away_goals', 'matches.end_time as end_time', 'competitions.logo as competition_logo', 'competitions.id as competition_id', 'home.id as home_team_id', 'home.name as home_team_name', 'home.logo as home_team_logo', 'home.main_color as home_team_color', 'home.secondary_color as home_team_secondary_color', 'away.id as away_team_id', 'away.name as away_team_name', 'away.logo as away_team_logo', 'away.main_color as away_team_color', 'away.secondary_color as away_team_secondary_color', 'competitions.name_en as competition_name', 'stadiums.name_en as stadium_name')->first();
        }

        return $match;
    }


    /* get referees of the curent match */
    public function referees($matchId, $languageId)
    {
        $referees = DB::table('referee_to_match')
            ->join('referees', 'referee_to_match.referee', '=', 'referees.id')
            ->where('match_id', $matchId);
        if ($languageId = 1) {
            $referees = $referees->select('referees.name as name', 'referee_to_match.role_hu as role')->get();
        } else {
            $referees = $referees->select('referees.name as name', 'referee_to_match.role_en as role')->get();
        }

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
            ->leftJoin('livescreen_hashtags as lh', 'lh.id', '=', 'lth.hashtag')
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

        /* if there are no posts yet */
        if ($posts == []) {
            return array('last_id' => 0, 'posts' => []);
        }

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
            ->leftJoin('live_action_events as lae', 'lah.event', '=', 'lae.id')
            ->leftJoin('language_associations as laen', 'lae.name_association_id', '=', 'laen.language_association_id')
            ->leftJoin('language_associations as laet', 'lae.text_association_id', '=', 'laet.language_association_id')
            ->leftJoin('player_to_happening as pth', 'lah.id', '=', 'pth.happening')
            ->leftJoin('players', 'pth.player', '=', 'players.id')
            ->where('lah.match_id', $matchId)
            ->where('laen.language', $languageId)
            ->where('laet.language', $languageId);
        if ($languageId == 1) {
            $actions = $actions->select('lah.id as id', 'lah.minute as minute', 'lah.expected_minute as expected_minute', 'lah.likes as likes', 'lah.picture as picture', 'lah.video as video', 'lah.text_hu as text', 'lae.color as event_color', 'lae.background as event_background', 'lae.icon as event_icon', 'laen.text as event_name', 'laet.text as event_text', 'players.name as player_name', 'players.number as player_number'
            )->get();
        } else {
            $actions = $actions->select('lah.id as id', 'lah.minute as minute', 'lah.expected_minute as expected_minute', 'lah.likes as likes', 'lah.picture as picture', 'lah.video as video', 'lah.text_en as text', 'lae.color as event_color', 'lae.background as event_background', 'lae.icon as event_icon', 'laen.text as event_name', 'laet.text as event_text', 'players.name as player_name', 'players.number as player_number'
            )->get();
        }

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
        $staff = DB::table('staff');
        if ($languageId == 1) {
            $staff = $staff->select('staff.name_hu as name', 'staff.picture as picture', 'staff.title_hu as title', 'staff.description_hu as description')->get();
        } else {
            $staff = $staff->select('staff.name_en as name', 'staff.picture as picture', 'staff.title_en as title', 'staff.description_en as description')->get();
        }

        return $staff;
    }


    /* get the client's front officers */
    public function articles($languageId)
    {
        $articles = DB::table('articles');
        if ($languageId == 1) {
            $articles = $articles->select('articles.img as image','articles.title_hu as title', 'articles.text_hu as text')->get();
        } else {
            $articles = $articles->select('articles.img as image','articles.title_en as title', 'articles.text_en as text')->get();
        }    

        return $articles;
    }


    /* get the client's front officers */
    public function front_office($languageId)
    {
        $front_office = DB::table('front_office');
            if ($languageId == 1) {
                $front_office = $front_office->select('front_office.name_hu as name', 'front_office.picture as picture', 'front_office.title_hu as title', 'front_office.description_hu as description')->get();
            } else {
                $front_office = $front_office->select('front_office.name_en as name', 'front_office.picture as picture', 'front_office.title_en as title', 'front_office.description_en as description')->get();
            }

        return $front_office;
    }


    /* get the client's team matches */
    public function matches($languageId)
    {
        $matches = DB::table('matches')
            ->join('competitions', 'matches.competition', '=', 'competitions.id')
            ->join('teams as home', 'matches.home', '=', 'home.id')
            ->join('teams as away', 'matches.away', '=', 'away.id')
            ->join('stadiums', 'matches.stadium', '=', 'stadiums.id');
        if ($languageId == 1) {
            $matches = $matches->select('matches.kickoff as kickoff', 'matches.kick_off_real as kickoff_real', 'matches.gate_opening as gate_opening', 'matches.home_goal as home_goals', 'matches.halftime_home as home_halftime_goals', 'matches.halftime_away as away_halftime_goals', 'matches.away_goal as away_goals', 'matches.end_time as end_time', 'competitions.logo as competition_logo', 'stadiums.name_hu as stadium_name', 'competitions.name_hu as competition_name', 'competitions.id as competition_id', 'home.id as home_team_id', 'home.name as home_team_name', 'home.logo as home_team_logo', 'home.main_color as home_team_color', 'home.secondary_color as home_team_secondary_color', 'away.id as away_team_id', 'away.name as away_team_name', 'away.logo as away_team_logo', 'away.main_color as away_team_color', 'away.secondary_color as away_team_secondary_color')->get();
        } else {
            $matches = $matches->select('matches.kickoff as kickoff', 'matches.kick_off_real as kickoff_real', 'matches.gate_opening as gate_opening', 'matches.home_goal as home_goals', 'matches.halftime_home as home_halftime_goals', 'matches.halftime_away as away_halftime_goals', 'matches.away_goal as away_goals', 'matches.end_time as end_time', 'competitions.logo as competition_logo', 'stadiums.name_en as stadium_name', 'competitions.name_en as competition_name', 'competitions.id as competition_id', 'home.id as home_team_id', 'home.name as home_team_name', 'home.logo as home_team_logo', 'home.main_color as home_team_color', 'home.secondary_color as home_team_secondary_color', 'away.id as away_team_id', 'away.name as away_team_name', 'away.logo as away_team_logo', 'away.main_color as away_team_color', 'away.secondary_color as away_team_secondary_color')->get();
        }

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
        $competitions = DB::table('competitions');
        if ($languageId == 1) {
            $competitions = $competitions->select('competitions.name_hu as name', 'competitions.logo as logo')->get();
        } else {
            $competitions = $competitions->select('competitions.name_en as name', 'competitions.logo as logo')->get();
        }

        return $competitions;
    }


    /* get the client's team basic infos */
    public function basics($languageId)
    {
        $basics = DB::table('basics');
        if ($languageId == 1) {
            $basics = $basics->select('basics.name_hu as title', 'basics.text_hu as description', 'basics.picture as picture')->get();
        } else {
            $basics = $basics->select('basics.name_en as title', 'basics.text_en as description', 'basics.picture as picture')->get();
        }

        return $basics;
    }



    /* get the client's team talents */
    public function talents($languageId)
    {
        $talents = DB::table('talents');
        if ($languageId == 1) {
            $talents = $talents->select('talents.name_hu as name', 'talents.background as background', 'talents.profile_picture as profile_picture', 'talents.big_picture as big_picture', 'talents.description_hu as description')->get();
        } else {
            $talents = $talents->select('talents.name_hu as name', 'talents.background as background', 'talents.profile_picture as profile_picture', 'talents.big_picture as big_picture', 'talents.description_hu as description')->get();
        }

        return $talents;
    }



    /* get the message the team details */
    public function message_the_team($languageId)
    {
        $message_the_team = DB::table('message_the_team');
        if ($languageId == 1) {
            $message_the_team = $message_the_team->select('message_the_team.background as background', 'message_the_team.description_hu as description')->first();
        } else {
            $message_the_team = $message_the_team->select('message_the_team.background as background', 'message_the_team.description_en as description')->first();
        }

        return $message_the_team;
    }



    /*  */
    public function ask_the_fans($languageId, $matchId)
    {
        $ask_the_fans = DB::table('ask_the_fans_question as atf')
            ->join('ask_the_fans_question_to_match as match', 'match.question', '=', 'atf.id')
            ->leftJoin('language_associations as question', 'question.language_association_id', '=', 'atf.question_association_id')
            ->leftJoin('ask_the_fans_answer as answers1', 'answers1.id', '=', 'atf.answer1')
            ->leftJoin('ask_the_fans_answer as answers2', 'answers2.id', '=', 'atf.answer2')
            ->leftJoin('language_associations as answer1', 'answer1.language_association_id', '=', 'answers1.answer_association_id')
            ->leftJoin('language_associations as answer2', 'answer2.language_association_id', '=', 'answers2.answer_association_id')
            ->leftJoin('language_associations as response1', 'response1.language_association_id', '=', 'answers1.response_association_id')
            ->leftJoin('language_associations as response2', 'response2.language_association_id', '=', 'answers2.response_association_id')
            ->where('question.language', $languageId)
            ->where('answer1.language', $languageId)
            ->where('answer2.language', $languageId)
            ->where('response1.language', $languageId)
            ->where('response2.language', $languageId)
            ->where('atf.active', 1)
            ->where('match.match_id', $matchId)
            ->select(
                'atf.id as question_id',
                'question.text as question',
                'atf.picture as picture',
                'atf.answer_time as time',
                'answers1.id as answer1_id',
                'answer1.text as answer1',
                'answers2.id as answer2_id',
                'answer2.text as answer2',
                'response1.text as response1',
                'response2.text as response2'
            )->get();

        return $ask_the_fans;
    }



    /* get predict and win questions */
    public function predict_and_win($languageId, $matchId)
    {
        $predict_and_win = DB::table('predict_and_win_questions as pan')
            ->leftJoin('language_associations as question', 'question.language_association_id', '=', 'pan.question_association_id')
            ->leftJoin('predict_and_win_answer_to_question as atq', 'atq.question', '=', 'pan.id')
            ->leftJoin('predict_and_win_anwers as pawa', 'pawa.id', '=', 'atq.answer')
            ->leftJoin('language_associations as answer', 'answer.language_association_id', '=', 'pawa.answer_association_id')
            ->where('question.language', $languageId)
            ->where('answer.language', $languageId)
            ->where('pan.match_id', $matchId)
            ->groupBy('pan.id')
            ->orderBy('pan.ordering')
            ->select(
                'pan.id as question_id',
                'pan.ordering as order',
                'question.text as question',
                DB::raw('group_concat(pawa.id) as answer_ids'),
                DB::raw('group_concat(answer.text) as answers')
            )->get();

        $questions = array();
        $key = 0;
        foreach ($predict_and_win as $question) {
           $questions[$key]['question_id'] = $question->question_id;
           $questions[$key]['order'] = $question->order;
           $questions[$key]['question'] = $question->question;
           
           $answer_ids = explode(',', $question->answer_ids);
           $answers = explode(',', $question->answers);
           $answerKey = 0;
           foreach ($answers as $answer) {
               $questions[$key]['answers'][$answerKey]['answer_id'] = $answer_ids[$answerKey];
               $questions[$key]['answers'][$answerKey]['answer'] = $answer;
               $answerKey++;
           }
           $key++;
        }

        return $questions;
    }

}