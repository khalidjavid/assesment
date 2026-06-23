<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'csrfToken'    => csrf_token(),
            'currentRoute' => fn() => optional($request->route())->getName() ?? '',
            'flash' => fn() => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'role'        => $user->role,
                    'role_label'  => $user->getRoleLabel(),
                    'branch_id'   => $user->branch_id,
                    'branch'      => $user->branch?->only('id', 'name', 'city'),
                    'permissions' => $user->permissions ?? [],
                    'tenant_id' => $user->tenant_id ?? [],
                    'is_admin'    => $user->isAdmin(),
                    'is_manager'  => $user->isManager(),
                ] : null,
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
