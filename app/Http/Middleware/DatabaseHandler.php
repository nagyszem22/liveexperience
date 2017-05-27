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
        $url = explode('/', url()->current());
        array_pop($url);
        if (last($url) != 'v1') {
            $client = $_SERVER['HTTP_CLIENT'];
            if (config('clients.'.$client) == null) {
                return response()->json(
                    $this->error->clientDoesNotExist(),
                200); 
            }

            Config::set('database.connections.mysql', config('clients.'.$client));
        }

        // continue request
        return $next($request);
    }
}
