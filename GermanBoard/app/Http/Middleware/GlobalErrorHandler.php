<?php

namespace App\Http\Middleware;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
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

                 return $this->sendError($response->exception->getMessage(), $response->exception->getCode());
             }

             return $this->sendError($response->exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
         }

        return $response;
    }
}
