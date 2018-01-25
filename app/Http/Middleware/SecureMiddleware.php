<?php

namespace GeoLV\Http\Middleware;

use Closure;

class SecureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->secure())
            return redirect()->secure($request->getRequestUri());
        else
            return $next($request);
    }
}
