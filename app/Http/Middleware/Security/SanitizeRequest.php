<?php

namespace App\Http\Middleware\Security;

use Closure;

class SanitizeRequest
{
    public function handle($request, Closure $next)
    {
        $clean = filter_var_array($request->all(), FILTER_SANITIZE_STRING);
        $request->merge($clean);
        return $next($request);
    }
}
