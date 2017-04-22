<?php

namespace AndrewDalpino\Epicuros\Middleware;

use AndrewDalpino\Epicuros\Epicuros;
use AndrewDalpino\Epicuros\Context;
use Closure;

class AcquireContext
{
    /**
     * @var  \AndrewDalpino\Epicuros\Epicuros  $epicuros
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
     * @param  mixed  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $context = $this->epicuros->authorize($request);
        } catch (\Exception $e) {
            $context = Context::build();
        }

        if ($request instanceof LaravelRequest) {
            $request->merge(['context' => $context]);
        } else if ($request instanceof RequestInterface) {
            $request->withAttribute('context', $context);
        }

        return $next($request);
    }
}
