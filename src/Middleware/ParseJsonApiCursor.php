<?php

namespace AndrewDalpino\Epicuros\Middleware;

use AndrewDalpino\Epicuros\Cursor;
use Closure;

class ParseJsonApiCursor
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
        if ($request->has('cursor')) {
            $data = explode(',', $request->get('cursor'), 3);

            $offset = (int) $data[0] ?? 0;
            $previous = (int) $data[1] ?? null;
            $limit = (int) $data[2] ?? Cursor::DEFAULT_LIMIT;

            $cursor = new Cursor($offset, $previous, $limit);
        } else {
            $cursor = new Cursor(0, null, Cursor::DEFAULT_LIMIT);
        }

        $request->merge([
            'cursor' => $cursor,
        ]);

        return $next($request);
    }
}
