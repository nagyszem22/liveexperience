<?php

namespace App\Services\v1;

use DB;

/**
* @todo add comment here
*/
class PutService extends Service
{    
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
}