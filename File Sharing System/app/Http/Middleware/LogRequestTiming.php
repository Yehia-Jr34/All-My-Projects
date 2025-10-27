<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRequestTiming
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Log the start of the request
        Log::info('Request started', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
        ]);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        // Log the end of the request
        Log::info('Request ended', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'duration' => $duration,
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
        ]);

        return $response;
    }
}
