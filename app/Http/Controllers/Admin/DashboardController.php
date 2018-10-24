<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Models\GoodsBase;
use App\Models\GuideBase;
use App\Models\OrderBase;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SupplierBase;
use App\Models\TaBase;
use App\Models\UserWx;
use Illuminate\Contracts\Auth\Guard as Auth;

class DashboardController extends BaseController
{
    public function __construct(Permission $permission, Auth $auth, Role $role)
    {
        $this->permission = $permission;
        $this->auth = $auth;
        $this->role = $role;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = $this->permission->whereIsMenu(Permission::IS_MENU)->whereParentId(Permission::PARENT_MENU)->orderBy('display_order', 'asc')->get();
        $maxRole = $this->role->max('level');

        foreach($menus as $key=>$permission)
        {
            $menus[$key]['son_menu'] = Permission::whereParentId($permission->id)->orderBy('display_order', 'asc')->get();
            foreach($menus[$key]['son_menu'] as $k=>$v){
                if(!$this->hasPermission($v, $this->auth->user()) && $this->auth->user()->role->level != $maxRole)
                {
                    unset($menus[$key]['son_menu'][$k]);
                }
            }
            if(count($menus[$key]['son_menu']) == 0)
            {
                unset($menus[$key]);
            }

        }
        
        return view("dash.index", compact("menus"));
    }

    public function dash()
    {
        $now = date("Y-m-d");
        $yesterday = date("Y-m-d",strtotime('-1 day'));
        //营业额
        $order['amount_now'] = OrderBase::whereIn('state',[OrderBase::STATE_FINISHED,OrderBase::STATE_PAYED,OrderBase::STATE_SEND])->where('created_at','>',$now)->sum('amount_real');

        //订单数量
        $order['order_num'] = OrderBase::whereIn('state',[OrderBase::STATE_FINISHED,OrderBase::STATE_PAYED,OrderBase::STATE_SEND])->where('created_at','>',$now)->count();

        //昨日营业额
        $order['amount_yesterday'] = OrderBase::whereIn('state',[OrderBase::STATE_FINISHED,OrderBase::STATE_PAYED,OrderBase::STATE_SEND])->whereBetween('created_at',[$yesterday,$now])->sum('amount_real');

        //昨日订单数量
        $order['order_num_yesterday'] = OrderBase::whereIn('state',[OrderBase::STATE_FINISHED,OrderBase::STATE_PAYED,OrderBase::STATE_SEND])->whereBetween('created_at',[$yesterday,$now])->count();

        //总营业额
        $order['amount_totle'] = OrderBase::whereIn('state',[OrderBase::STATE_FINISHED,OrderBase::STATE_PAYED,OrderBase::STATE_SEND])->sum('amount_real');

        //总订单数
        $order['order_num_totle'] = OrderBase::whereIn('state',[OrderBase::STATE_FINISHED,OrderBase::STATE_PAYED,OrderBase::STATE_SEND])->count();

        //新增旅行社
        $order['taBase_now'] = TaBase::where('created_at','>',$now)->count();
        //总共旅行社
        $order['taBase_num'] = TaBase::whereIn('state',array(TaBase::state_no_check,TaBase::state_check,TaBase::state_close,2,3))->count();

        $order['guide_now'] = GuideBase::where('created_at','>',$now)->count();
        $order['guide_num'] = GuideBase::count();

        $order['user_now'] = UserWx::where('created_at','>',$now)->count();
        $order['user_num'] = UserWx::count();
        //关注公众号游客数
        $order['ref'] = UserWx::whereRef('wx_qrcode')->count();

        $order['supplier_now'] = SupplierBase::where('created_at','>',$now)->count();
        $order['supplier_num'] = SupplierBase::whereState(SupplierBase::STATE_VALID)->count();

        $order['goodBase_now'] = GoodsBase::where('created_at','>',$now)->count();
        $order['goodBase_num'] = GoodsBase::whereIn('state',[GoodsBase::state_check,GoodsBase::state_down,GoodsBase::state_finish,GoodsBase::state_online,GoodsBase::state_return])->count();

        return view("dash.dash",['order' => $order]);
    }
}
