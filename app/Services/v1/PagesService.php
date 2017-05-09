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
    public function messageTheTeam($input)
    {
        /* get the device of the current user and validate it */
        if (!$device = $this->device($input['device_token'])) {
            return $this->error->deviceDoesNotExist();
        }

        /* get message the team details */
        $content = $this->content->message_the_team($device->language_id);

        return $this->createResponse($content);
    }



    /* ask the fans details */
    public function askTheFans($input)
    {
        /* get the device of the current user and validate it */
        if (!$device = $this->device($input['device_token'])) {
            return $this->error->deviceDoesNotExist();
        }

        /* get ask the fans details */
        $content = $this->content->ask_the_fans($device->language_id, $device->match_id);

        return $this->createResponse($content);
    }



    /* predict and win */
    public function predictAndWin($input)
    {
        /* get the device of the current user and validate it */
        if (!$device = $this->device($input['device_token'])) {
            return $this->error->deviceDoesNotExist();
        }

        /* get predict and win questions */
        $content = $this->content->predict_and_win($device->language_id, $device->match_id);

        return $this->createResponse($content);
    }
}