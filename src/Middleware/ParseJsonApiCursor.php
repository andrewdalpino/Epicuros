<?php

namespace App\Http\Middleware;

use App\Domain\Common\Cursor;
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

            if ($limit > Cursor::MAX_PER_REQUEST) {
                $limit = Cursor::MAX_PER_REQUEST;
            }

            $cursor = Cursor::build($offset, $previous, $limit, null);
        } else {
            $cursor = Cursor::build(0, null, Cursor::DEFAULT_LIMIT, null);
        }

        $request->merge([
            'cursor' => $cursor,
        ]);

        return $next($request);
    }
}
