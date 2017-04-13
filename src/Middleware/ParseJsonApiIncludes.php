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
        $includes = $request->has('include') ? explode(',', str_replace(' ', '', $request->get('include'))) : [];

        $request->merge([
            'includes' => $includes,
        ]);

        return $next($request);
    }
}
