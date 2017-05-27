<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\v1\ErrorService;

use DB;

class VerifyDeviceToken
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
                  ->first();
        if (!$device) {
            return response()->json($this->error->deviceDoesNotExist(), 200);
        }

        // continue request
        $request->attributes->add(['device' => $device]);
        return $next($request);
    }
}