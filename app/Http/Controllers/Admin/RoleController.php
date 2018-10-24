<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['page'] = $this->page;
        $data['display_name'] = $request->input("display_name", "");

        $roles = new Role();
        if($data['display_name'] != "")
        {
            $roles = $roles->where("display_name", "like", "%{$data['display_name']}%");
        }

        $roles = $roles->paginate($data['page']);
        $roleArr = $roles->toArray();

        return view("perm.role_list", compact("roles", "roleArr"), $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_menu = $this->getAllParentMenus();
        $permissions = $this->getPermissionWithMenus($parent_menu);
        //dd($parent_menu);
        return view("perm.role_add", compact("permissions"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new Role();
        $role->name = $request->input("name", "");
        $role->display_name = $request->input("display_name", "");
        $role->description = $request->input("description", "");

        $role->save();
        $role_check = $request->input("role_check", array());
        if(!empty($role_check))
        {
            $role->perms()->sync($role_check);
        }

        return response()->json(array("ret"=>self::RETSUCCESS, "msg"=>self::CREATESUCCESS));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $parent_menu = $this->getAllParentMenus();
        $permissions = $this->getPermissionWithMenus($parent_menu);
        $role = Role::find($id);
        $this->getMaxRole($role);
//        if(!$this->getMaxRole($role, true))
//        {
//            dd(self::EDITROLEMAX);
//        }
        $perms = $role->perms()->lists("id")->toArray();
        //dd($perms);

        return view("perm.role_edit", compact("permissions", "role", "perms"));
    }

    //检查角色名称是否重复
    public function checkRoleName($name){
        $result = Role::whereName($name)->first();
        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if(!$this->getMaxRole($role,true))
        {
            response()->json(array("ret"=>self::RETFAIL, "msg"=>self::EDITROLEMAX));
        }
        $role->name = $request->input("name", "");
        $role->display_name = $request->input("display_name", "");
        $role->description = $request->input("description", "");

        $role->save();
        $role_check = $request->input("role_check", array());
        //dd($role_check);
        $role->perms()->sync($role_check);
        //dd(1);
        return response()->json(array("ret"=>self::RETSUCCESS, "msg"=>self::EDITSUCCESS));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        $this->getMaxRole($role);
        //dd($role);
        $role->perms()->sync(array());
        $role->delete();

        return response()->json(array("ret"=>self::RETSUCCESS, "msg"=>self::DELSUCCESS));
    }

    private function getMaxRole($role,$edit='')
    {
        $maxRole = Role::max('level');
        if($edit == true && $role->level == $maxRole) {
            return false;
        }
        if($role->level == $maxRole)
        {
            abort(403);
        }
        return true;
    }

    private function getPermissionWithMenus($parent_menu)
    {
        foreach($parent_menu as $key=>$val) {
            $parent_menu[$key]['son_menu'] = Permission::whereParentId($val['id'])->whereIsMenu(Permission::IS_MENU)->orderBy("display_order", "asc")->get();
            foreach($parent_menu[$key]['son_menu'] as $k=>$v) {
                $parent_menu[$key]['son_menu'][$k]['son_perm'] = Permission::whereParentId($v['id'])->orderBy("display_order", "asc")->get();
            }
        }
        return $parent_menu;
    }
}
