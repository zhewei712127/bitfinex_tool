<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtAuthenticate extends BaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $this->checkForToken($request);

        $token = '';

        try {
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }
        } catch (TokenExpiredException $e) {
            try {
                $token = $this->auth->refresh();
                auth()->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
            } catch (JWTException $e) {
                return response(['code' => 401, 'msg' => $e->getMessage()], 401);
            }
        }

        return $this->setAuthenticationHeader($next($request), $token);
    }
}
