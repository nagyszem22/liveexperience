<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use App\Services\v1\ErrorService;

class DatabaseHandler
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
        // parse current url
        $route = explode('/', $request->path());

        if (!isset($route[4])) { $route[4] = null; }

        // change database config if needed
        if ($route[4] != null && $route[2] != 'init') 
        {
            if (config('clients.'.$route[4]) == null) {
                return response()->json(
                    $this->error->clientDoesNotExist(),
                200); 
            }

            Config::set('database.connections.mysql', config('clients.'.$route[4]));

        }

        // return error if client doesn't exist
        elseif (config('clients.'.$route[3]) == null) {
            return response()->json(
                $this->error->clientDoesNotExist(),
            200);
        }

        // continue request
        return $next($request);
    }
}
