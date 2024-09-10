<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $acceptedLanguages = ['en', 'nl'];

        $locale = $request->header('Accept-Language');

        if (!in_array($locale, $acceptedLanguages)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}