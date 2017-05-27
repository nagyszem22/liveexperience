<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\v1\ErrorService;

use DB;

class LoggedInToken
{
    protected $error;
    public function __construct(ErrorService $error)
    {
        $this->error = $error;
    }

    /**
     * Changes database connection configuration if needed. 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $device = DB::table('device_tokens')
                  ->where('token', $_SERVER['HTTP_TOKEN'])
                  ->where('expires', '>', strtotime(date("Y-m-d H:i:s")))
                  ->where('logged_in', 1)
                  ->first();
        if (!$device) {
            return response()->json($this->error->deviceDoesNotExist(), 200);
        }

        $user = DB::table('users')->where('id', $device->user_id)->first();
        if (!$user) {
            return response()->json($this->error->deviceDoesNotExist(), 200);
        }

        // continue request
        $request->attributes->add(['device' => $device, 'user' => $user]);
        return $next($request);
    }
}