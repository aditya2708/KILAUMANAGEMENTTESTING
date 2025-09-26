<?php
namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = config('app.frontend_origin', '*');

        if (! empty($origin)) {
            $response->header('Access-Control-Allow-Origin', $origin);
        }
        $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->header('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}