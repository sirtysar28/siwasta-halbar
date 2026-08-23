<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Activitylog\Facades\Activity;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && $request->method() !== 'GET' && !str_starts_with($request->path(), 'livewire')) {
            Activity::performedOn(new \stdClass())
                ->causedBy(auth()->user())
                ->withProperties([
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'input' => $request->except(['password', 'password_confirmation', '_token']),
                ])
                ->log('Mengakses ' . $request->method() . ' ' . $request->path());
        }

        return $response;
    }
}
