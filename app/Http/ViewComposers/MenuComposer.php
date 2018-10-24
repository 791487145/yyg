<?php

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;

class MenuComposer
{
    protected $permission;

    public function __construct(Permission $permission, Auth $auth, Role $role, Request $request)
    {
        // Dependencies automatically resolved by service container...
        $this->permission = $permission;
        $this->auth = $auth;
        $this->role = $role;
        $this->request = $request;
    }

    /**
     * 绑定数据到视图.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $route = '/'.$this->request->route()->uri();
        $menu = array();
        $menu['menu'] = $this->permission->whereIsMenu(Permission::IS_MENU)->whereName($route)->first();
        if(!empty($menu['menu'])) {
            $menu['menu'] = $menu['menu']->toArray();
            $menu['parent'] = $this->permission->whereIsMenu(Permission::IS_MENU)->whereId($menu['menu']['parent_id'])->first()->toArray();
        }

        $view->with('menus', $menu);
    }
}