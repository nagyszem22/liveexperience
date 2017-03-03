<?php

namespace App\Http\Middleware;

use Closure;
use Config;

class DatabaseHandler
{
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

        // change database config if needed
        if ($route[3] != null && $route[2] != 'init') 
        {
            if (config('clients.'.$route[3]) == null) { return 'No such a database.'; } // @todo handle errors
            Config::set('database.connections.mysql', config('clients.'.$route[3]));
        } 

        // continue request
        return $next($request);
    }
}
