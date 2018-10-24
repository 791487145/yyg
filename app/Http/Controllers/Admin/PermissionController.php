<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\Request;

use App\Http\Requests;

class PermissionController extends BaseController
{
    public function index($id, Request $request)
    {
        $data['display_name'] = $request->input("display_name", "");
        $data['id'] = $id;
        $data['page'] = $this->page;
        $menu_name = Permission::whereId($id)->pluck("display_name");
        $permissions = Permission::whereParentId($id);
        if($data['display_name'] != "")
        {
            $permissions = $permissions->where("display_name", "like", "%{$data['display_name']}%");
        }

        $permissions = $permissions->orderBy("display_order")->paginate($data['page']);
        $permissionArr = $permissions->toArray();

        return view("perm.permission_list", compact("menu_name", "permissions", "permissionArr"), $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $menu = Permission::find($id);
        return view("perm.permission_add", compact("menu"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $params = $request->all();
        $params['parent_id'] = $id;

        Permission::create($params);

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
        $perm = Permission::find($id);

        return view("perm.permission_edit", compact("perm"));
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
        $perm = Permission::find($id);
        $perm->name = $request->input("name");
        $perm->display_name = $request->input("display_name");
        $perm->display_order = $request->input("display_order", 0);
        $perm->description = $request->input("description", "");

        $perm->save();

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
        Permission::destroy($id);

        return response()->json(array("ret"=>self::RETSUCCESS, "msg"=>self::DELSUCCESS));
    }
}
