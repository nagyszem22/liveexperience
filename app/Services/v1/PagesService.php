<?php

namespace App\Services\v1;

use App\Services\v1\ContentService;
use App\Services\v1\ErrorService;

use DB;

/**
* @todo add comment here
*/
class PagesService extends Service
{    
    /* initialize content provider */
    protected $content;
    protected $error;
    public function __construct(ContentService $content, ErrorService $error) 
    {
        $this->content = $content;
        $this->error = $error;
    }



    /* message the team details */
    public function messageTheTeam($device)
    {
        return $this->createResponse($this->content->message_the_team($device->language_id));
    }



    /* ask the fans details */
    public function askTheFans($device)
    {
        return $this->createResponse($this->content->ask_the_fans($device->language_id, $device->match_id));
    }



    /* predict and win */
    public function predictAndWin($device)
    {
        return $this->createResponse($this->content->predict_and_win($device->language_id, $device->match_id));
    }

    /* predict and win history */
    public function predictAndWinHistory($device)
    {
        return $this->createResponse($this->content->predict_and_win_history($device->language_id, $device->user_id));
    }



    /* spotify */
    public function spotify()
    {
        return $this->createResponse($this->content->spotify());
    }



    /* fans help */
    public function fanshelp($device)
    {
        return $this->createResponse($this->content->fanshelp($device->language_id, $device->match_id));
    }
}