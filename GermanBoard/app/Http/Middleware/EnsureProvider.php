<?php

namespace App\Http\Middleware;

use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use Closure;
use Illuminate\Http\Request;

class EnsureProvider
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next){

        $user = auth()->user();

        if (!$user->hasAnyRole([
            RolesEnum::AGENT->value,
            RolesEnum::TRAINER->value,
            RolesEnum::CENTER->value,
            RolesEnum::ADMIN->value
        ])) {
            throw new \DomainException('Unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $next($request);
    }
}
