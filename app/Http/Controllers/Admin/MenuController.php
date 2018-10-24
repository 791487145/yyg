<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    public function index(Request $request)
    {
        $data['display_name'] = $request->input("display_name", "");
        $data['page'] = $this->page;
        $menus = Permission::whereIsMenu(Permission::IS_MENU);
        if($data['display_name'] != "")
        {
            $menus = $menus->where("display_name", "like", "%{$data['display_name']}%");
        }

        $menus = $menus->orderBy("display_order", "asc")->paginate($data['page']);
        $menuArr = $menus->toArray();
        $menus = $this->getParentsName($menus);
        //dd($menus);

        return view("perm.menu_list", compact("menus", "menuArr"), $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_menus = $this->getAllParentMenus();
        return view("perm.menu_add", compact("parent_menus"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Permission::create($request->all());

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
        $menu = Permission::find($id);
        $parent_menus = $this->getAllParentMenus();

        return view("perm.menu_edit", compact("menu", "parent_menus"));
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
        $menu = Permission::find($id);
        $menu->name = $request->input("name");
        $menu->display_name = $request->input("display_name");
        $menu->icon = $request->input("icon", "");
        $menu->display_order = $request->input("display_order", 0);
        $menu->parent_id = $request->input("parent_id", 0);
        $menu->description = $request->input("description", "");

        $menu->save();

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
        $count = Permission::whereParentId($id)->count();
        $menu = Permission::find($id);
        if($count > 0 && $menu->parent_id == Permission::PARENT_MENU) {
            return response()->json(array("ret"=>self::RETFAIL, "msg"=>self::SONSOVERONE));
        }

        if($menu->parent_id != Permission::PARENT_MENU)
        {
            Permission::whereParentId($id)->delete();
        }

        $menu->delete();

        return response()->json(array("ret"=>self::RETSUCCESS, "msg"=>self::DELSUCCESS));
    }

    public function displayOrder(Request $request, $id)
    {
        $result = $this->editDisplayOrder(Permission::find($id), $request->all());

        return response()->json($result);
    }
}
