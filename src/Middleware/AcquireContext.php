<?php

namespace AndrewDalpino\Epicuros\Middleware;

use AndrewDalPino\Epicuros\Context;
use Firebase\JWT\JWT;
use Closure;

class AcquireContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $bearer = $request->getBearer();

            $key = $this->getPublicKey();

            $jwt = JWT::decode($bearer, $key, ['RS256']);

            $viewerId = $jwt->sub ?? null;
            $scopes =  $jwt->scopes ?? [];
            $permissions = $jwt->permissions ?? [];
            $verified = $jwt->verified ?? false;
            $ip = $jwt->ip ?? null;

            $context = Context::build($viewerId, $scopes, $permissions, $verified, $ip);

        } catch (\Exception $e) {
            $context = Context::build(null, [], [], false, null);
        }

        $request->merge([
            'context' => $context,
        ]);

        return $next($request);
    }
}
