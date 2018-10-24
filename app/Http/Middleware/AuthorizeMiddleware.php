<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthorizeMiddleware
{
    protected $role;
    protected $permission;

    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        $permissions = $this->permission->get();

        $maxRole = $this->role->max('level');

        $uri = $request->route()->uri();

        //此处为原来验证用户状态处  现取消
//        if($user->state != User::STATE_VALID) {
//            Auth::logout();
//            abort(401);
//        }
        //此处为原来验证用户身份的状态处  现取消 且现在角色与用户关系为1对多
//        if($user->roles()->first()->state != Role::STATE_VALID)
//        {
//            abort(403);
//        }
        //dd($user->role);
        if($user->role->level < $maxRole)
        {
            foreach($permissions as $permission)
            {

                if( $permission->name == '/'.$uri && !$this->hasPermission($permission, $user) )
                {
                    abort(403);
                }
            }
        }

        return $next($request);
    }

    private function hasPermission($permission, $user)
    {
        foreach($permission->roles as $role)
        {
            if($role->id == $user->role->id) {
                return true;
            }
        }
        return false;
    }
}
