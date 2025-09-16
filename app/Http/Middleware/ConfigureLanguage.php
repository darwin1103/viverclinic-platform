<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class ConfigureLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->missing('language')) {
            session(['language' => substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2)]);
        }
        App::setLocale(session('language'));
        return $next($request);
    }
}
