<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role', 'route_name',
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 5;

    /**
     * The list of roles authenticated routes.
     *
     * @return array
     */
    public static function rolesRouteNamesList(): array
    {
        return [
            'pages',
            'nav-menus',
            'users',
            'dashboard',
            'user-permissions',
        ];
    }

    /**
     * Check whether the given role has the right to access the given route name.
     *
     * @param  string $userRole
     * @param  string $routeName
     * @return bool
     */
    public static function isRoleHaveRightToAccess(string $userRole, string $routeName): bool
    {
        try {
            $permission = self::where([
                'role'       => $userRole,
                'route_name' => $routeName,
            ])->first();

            return $permission ? true : false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
