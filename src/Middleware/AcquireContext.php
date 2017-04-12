<?php

namespace AndrewDalpino\Epicuros\Middleware;

use AndrewDalpino\Epicuros\Epicuros;
use AndrewDalPino\Epicuros\Context;
use Firebase\JWT\JWT;
use Closure;

class AcquireContext
{
    /**
     * @var  AndrewDalpino\Epicuros\Epicuros  $epicuros
     */
    protected $epicuros;

    /**
     * Constructor.
     *
     * @param  Epicuros  $epicuros
     */
    public function __construct(Epicuros $epicuros)
    {
        $this->epicuros = $epicuros;
    }

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
            $jwt = $request->getBearer();

            $key = $this->epicuros->getVerifyingKey($jwt);

            $claims = JWT::decode($jwt, $key, $this->epicuros->getAlgorithm());

            $viewerId = $claims->sub ?? null;
            $scopes =  $claims->scopes ?? [];
            $permissions = $claims->permissions ?? [];
            $verified = $claims->verified ?? false;
            $ip = $claims->ip ?? null;

            $context = new Context($viewerId, $scopes, $permissions, $verified, $ip);
        } catch (\Exception $e) {
            $context = new Context(null, [], [], false, null);
        }

        $request->merge([
            'context' => $context,
        ]);

        return $next($request);
    }
}
