<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Store\StoreController;
use App\Models\ConfCity;
use App\Models\CsNews;
use App\Models\GoodsBase;
use App\Models\OrderBase;
use App\Models\OrderReturn;
use App\Models\ParcelBase;
use App\Models\PlatformNotice;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use Illuminate\Http\Request;

use App\Http\Requests;
use PetstoreIO\Order;

class DashboardController extends StoreController
{
    public function index()
    {
        $currentDay = date('Y-m-d').' 00:00:00';
        //订单统计
        $data['order_sale'] = SupplierBilling::whereSupplierId($this->user['id'])->whereInOut(1)->where('created_at','>',$currentDay)->sum('amount');
        $data['order_today'] = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereSupplierId($this->user['id'])->where('created_at','>',$currentDay)->count();
        $data['payed_order'] = OrderBase::whereState(OrderBase::STATE_PAYED)->whereSupplierId($this->user['id'])->count();
        $data['after_order'] = OrderReturn::whereSupplierId($this->user['id'])->count();
        $data['all_order'] = OrderBase::whereSupplierId($this->user['id'])->count();
        //商品统计
        $data['goods_sale'] = GoodsBase::whereState(GoodsBase::state_online)->whereSupplierId($this->user['id'])->count();
        $data['goods_num'] = GoodsBase::whereState(GoodsBase::state_online)->whereSupplierId($this->user['id'])->whereNum(0)->count();

        $data['goods_down'] = GoodsBase::whereState(GoodsBase::state_down)->whereSupplierId($this->user['id'])->count();
        $data['goods_check'] = GoodsBase::whereState(GoodsBase::state_check)->whereSupplierId($this->user['id'])->count();
        $data['goods_return'] = GoodsBase::whereState(GoodsBase::state_return)->whereSupplierId($this->user['id'])->count();
        return view("store.dash.index")->with(['data'=>$data]);
    }
}
