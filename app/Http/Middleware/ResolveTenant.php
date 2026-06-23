<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host      = $request->getHost();
        $appDomain = config('app.domain', 'printos.com');

        // Extract subdomain: "acme.printos.com" → "acme"
        $subdomain = null;
        if (str_ends_with($host, '.' . $appDomain)) {
            $subdomain = str_replace('.' . $appDomain, '', $host);
        }

        // Super admin accesses from main domain or "admin" subdomain
        if (!$subdomain || $subdomain === 'www' || $subdomain === 'admin') {
            return $next($request);
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        // Bind to container so BelongsToTenant trait can access it
        app()->instance('tenant', $tenant);

        // Share tenant info with Inertia
        \Inertia\Inertia::share('tenant', [
            'id'        => $tenant->id,
            'name'      => $tenant->name,
            'subdomain' => $tenant->subdomain,
            'logo_path' => $tenant->logo_path,
        ]);

        return $next($request);
    }
}
