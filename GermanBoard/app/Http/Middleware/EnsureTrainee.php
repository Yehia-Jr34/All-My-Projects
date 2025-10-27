<?php

namespace App\Http\Middleware;

use App\Enum\StatusCodeEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTrainee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->hasRole('trainee')) {
            throw new \DomainException('Unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $next($request);
    }
}
