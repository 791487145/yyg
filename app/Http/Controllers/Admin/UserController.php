<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as Auth;

use App\Http\Requests;

class UserController extends BaseController
{
    const PASS_NOT_SAME = "两次密码不一致";
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['page'] = $this->page;
        $data['name'] = $request->input("name", "");
        $users = new User();
        if($data['name'] != "")
        {
            $users = $users->where("name", "like", "%{$data['name']}%");
        }

        $users = $users->orderBy("created_at", "desc")->paginate($data['page']);
        $userArr = $users->toArray();

        return view("perm.user_list", compact("users", "userArr"), $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();
        return view("perm.user_add", compact("roles"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->input('password') != $request->input('password2')) {
            return response()->json(array('ret'=>self::RETFAIL, 'msg'=>self::PASS_NOT_SAME));
        }

        $oldUser = User::whereEmail($request->input("email"))->first();
        if(!empty($oldUser))
        {
            return response()->json(array('ret'=>self::RETFAIL, 'msg'=>self::EMAIL_EXIST));
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $this->getNewPassWord($request->input('password'));
        $user->role_id = $request->input("role");

        $user->save();

        return response()->json(array('ret'=>self::RETSUCCESS, 'msg'=>self::CREATESUCCESS));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $roles = Role::get();

        return view("perm.user_edit", compact("user", "roles"));
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
        $user = User::find($id);
        $user->name = $request->input('name');
        $user->role_id = $request->input("role");

        $user->save();

        return response()->json(array('ret'=>self::RETSUCCESS, 'msg'=>self::EDITSUCCESS));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if($user->role->level >= 90)
        {
            return response()->json(array('ret'=>self::RETFAIL, 'msg'=>self::EDITROLEMAX));
        }
        $user->delete();

        return response()->json(array('ret'=>self::RETSUCCESS, 'msg'=>self::DELSUCCESS));
    }

    public function resetPassword($id)
    {
        $user = User::find($id);
        $user->password = $this->getNewPassWord("123456");

        $user->save();

        return response()->json(array('ret'=>self::RETSUCCESS, 'msg'=>self::EDITSUCCESS));
    }

    public function showPassword($id)
    {
        $user = User::find($id);

        return view('perm.user_password', compact('user'));
    }

    public function editPassword(Request $request,$id)
    {
        if($request->input('password') != $request->input('password2')) {
            return response()->json(array('ret'=>self::RETFAIL, 'msg'=>self::PASS_NOT_SAME));
        }

        $user = User::find($id);

        $user->password = $this->getNewPassWord($request->input('password'));

        $user->save();

        $this->auth->logout();

        return response()->json(array('ret'=>self::RETSUCCESS, 'msg'=>self::EDITSUCCESS, 'url'=>'/auth/login'));
    }
}
