<?php

namespace App\Http\Middleware;

use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MembershipInvalidMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->hasRole(RolesEnum::ADMIN->value)) {
            return $next($request);
        }

        // Get the provider with their memberships ordered by latest

        if($user->hasRole(RolesEnum::INTERNAL_TRAINER->value)){
            $provider = $user->internalTrainer->providers->first();

            if($provider->user->hasRole(RolesEnum::ADMIN->value)){
                return $next($request);
            }
        }else {
            $provider = User::with(['provider.memberships' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->find($user->id)->provider;
        }

        // Check if provider exists and has memberships
        if (!$provider || $provider->memberships->isEmpty()) {
            throw new \DomainException("No membership found. Please contact German Board admins.",StatusCodeEnum::UNAUTHORIZED->value);
        }

        // Get the latest membership
        $latestMembership = $provider->memberships->first();

        // Check if membership is revoked
        if ($latestMembership->is_revoked) {
            throw new \DomainException("Your membership has been revoked. Please contact German Board admins.",StatusCodeEnum::UNAUTHORIZED->value);
        }

        // Check if membership has expired
        if (now()->greaterThan($latestMembership->expired_at)) {
            throw new \DomainException("Your membership has expired. Please contact German Board admins to renew.",StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $next($request);
    }}
