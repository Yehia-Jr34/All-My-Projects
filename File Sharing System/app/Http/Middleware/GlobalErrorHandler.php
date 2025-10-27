<?php

namespace App\Http\Middleware;

use App\Enums\StatusCodeEnum as EnumsStatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GlobalErrorHandler extends BaseApiController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $response = $next($request);


        if ($response->exception) {

            if ($response->exception instanceof \DomainException) {

                return $this->sendError($response->exception->getMessage(), EnumsStatusCodeEnum::BAD_REQUEST);
            }
            return $this->sendError($response->exception->getMessage(), EnumsStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
