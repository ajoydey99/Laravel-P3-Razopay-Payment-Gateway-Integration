<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistoryCache
{
    // this middleware used to prevent browser's bfcache
    // (back-forward cache)
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Instruct the browser not to cache this response
        return $response->withHeaders([
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => 'Sun, 02 Jan 1990 00:00:00 GMT',
        ]);
    }
}
