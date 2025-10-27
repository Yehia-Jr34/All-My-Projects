<?php

namespace App\Http\Middleware;

use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageOnsiteTrainings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->hasRole(RolesEnum::TRAINER->value)) {
            throw new \DomainException('Unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $next($request);
    }
}
