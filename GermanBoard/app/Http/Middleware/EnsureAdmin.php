<?php

namespace App\Http\Middleware;

use App\Enum\StatusCodeEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return $next($request);
        }
        throw new \DomainException('Unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
    }
}
