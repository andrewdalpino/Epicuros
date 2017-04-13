<?php

namespace AndrewDalpino\Epicuros\Middleware;

use AndrewDalpino\Epicuros\Epicuros;
use AndrewDalpino\Epicuros\Context;
use AndrewDalpino\Epicuros\Exceptions\ServiceUnauthorizedException;
use Firebase\JWT\JWT;
use Closure;

class AuthorizeService
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
            $request->merge([
                'context' => $this->epicuros->acquireContext($request->bearerToken()),
            ]);
        } catch (\Exception $e) {
            throw new ServiceUnauthorizedException();
        }

        return $next($request);
    }
}
