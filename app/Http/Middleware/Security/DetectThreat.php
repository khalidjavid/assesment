<?php

namespace App\Http\Middleware\Security;

use Closure;
use App\Security\BlacklistManager;

class DetectThreat
{
    public function handle($request, Closure $next)
    {
        $url = $request->fullUrl();
        $ua  = $request->header('User-Agent');
        if (preg_match('/(union|select|script|base64)/i', $url)) {
            BlacklistManager::add($request->ip(), 'Suspicious URL Attack');
            abort(403, 'Suspicious activity detected.');
        }
        if (empty($ua) || strlen($ua) < 5) {
            BlacklistManager::add($request->ip(), 'Invalid User-Agent');
            abort(403, 'Suspicious activity detected.');
        }

        return $next($request);
    }
}
