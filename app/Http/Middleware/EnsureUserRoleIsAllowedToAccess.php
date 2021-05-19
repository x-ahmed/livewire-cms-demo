<?php

namespace App\Http\Middleware;

use App\Models\UserPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRoleIsAllowedToAccess
{
    /**
     * Handle an incoming request on dashboard, pages, nav-menus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userRole = auth()->user()->role;
        $currentRoute = Route::currentRouteName();

        try {
            if (
                !UserPermission::isRoleHaveRightToAccess(userRole: $userRole, routeName: $currentRoute)
                and
                !\in_array(needle: $currentRoute, haystack: $this->defaultUserAccessRole()[$userRole])
            ) {
                abort(
                    Response::HTTP_FORBIDDEN,
                    'Unauthorized action',
                );
            }
            return $next($request);
        } catch (\Throwable $th) {
            abort(
                Response::HTTP_FORBIDDEN,
                'Unauthorized action',
            );
        }
    }

    /**
     * admin default access role.
     *
     * @return array
     */
    private function defaultUserAccessRole(): array
    {
        return [
            'admin' => [
                'user-permissions',
            ],
        ];
    }
}
