<?php

namespace App\Http\Middleware\Security;

use Closure;
use App\Security\BlacklistManager;

class BlockBlacklistedIP
{
    public function handle($request, Closure $next)
    {
        if (BlacklistManager::isBlacklisted($request->ip())) {
            abort(403, 'Your IP is blocked due to security violations.');
        }
        return $next($request);
    }
}
