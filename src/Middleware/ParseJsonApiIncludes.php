<?php

namespace AndrewDalpino\Epicuros\Middleware;

use Closure;

class ParseJsonApiIncludes
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
        $request->merge([
            'includes' => $request->has('include') ? explode(',', str_replace(' ', '', $request->get('include'))) : [],
        ]);

        return $next($request);
    }
}
