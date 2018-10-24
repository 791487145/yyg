<?php

namespace App\Http\Controllers\Admin;

use App\Models\ConfCity;
use App\Models\GuideBase;
use App\Models\OrderPay;
use App\Models\OrderWx;
use App\Models\SupplierBase;
use Log;
use App\Models\GuideBilling;
use App\Models\PlatformBilling;
use App\Models\SupplierBilling;
use App\Models\TaBilling;
use App\Http\Requests;
use App\Models\GoodsImage;
use App\Models\OrderBase;
use App\Models\GoodsBase;
use App\Models\OrderGood;
use App\Models\GoodsSpec;
use App\Models\GoodsGift;
use App\Models\OrderReturnImage;
use App\Models\OrderReturnLog;
use App\Models\PlatformSm;
use App\Models\UserBase;
use Illuminate\Support\Facades\Input;
use App\Models\OrderReturn;
use App\Models\OrderLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseController;
use EasyWeChat\Foundation\Application;

class OrderController extends BaseController
{
    //售后订单审核
    const afterSale_audit = 5;
    //售后打款
    const afterSale_pay = 51;
    //售后退款申请驳回
    const refundAfterSale = 52;

    const PAGE_LIMIT = 20;

    //待审核
    public function check($state = OrderReturn::STATE_NO_CHECK)
    {
        $tmp['order_no'] = Input::get('order_no');
        $tmp['name'] = Input::get('name');
        $tmp['mobile'] = Input::get('mobile');
        $tmp['express_type'] = Input::get('express_type');
        $tmp['pay_type'] = Input::get('pay_type');
        $orders = new OrderReturn();
        $orders = $this->checkList($orders, $state, $tmp);
        return view('boss.orders.check', compact('orders', 'state', 'tmp'));

    }

    //导出退款订单
    public function returnOrderExport($state = OrderReturn::STATE_NO_CHECK,Request $request){
        $tmp['order_no'] = $request->input('order_no');
        $tmp['name'] = $request->input('name');
        $tmp['mobile'] = $request->input('mobile');
        $orders = new OrderReturn();
        $orders = $this->checkList($orders, $state, $tmp,1);
        //dd($orders);
        $i = 1;
        $field = ['订单编号','供应商','商品名称','产品规格','供应单价','单价','商品数量','配送方式','运费','订单金额','订单供应价','买家留言','退款金额','退款原因','收货人姓名','收货地址-省市区','收货地址-街道地址','收货人电话','订单创建时间','物流公司','物流单号','发货时间'];
        $item[] = $field;
        foreach($orders as $value){
            $supplierName = SupplierBase::whereId($value->supplier_id)->pluck('name');
            foreach($value->titles as $v){
                $item[$i] = [$value->order_no,$supplierName,$v['goods_title'],$v['spec_name'],$v['price_buying'],$v['price'],$v['num'],$value->express_type,$value->amount_express,$value->amount_goods,$value->amountBuyPrice,$value->remark,$value->amount,$value->return_content,$value->receiver_name,$value->province_city,$value->address,$value->receiver_mobile,$value->created_at_order,$value->express_name,$value->express_no,$value->express_time];
                $i++;
            }
        }
        Excel::create(date('YmdHis'),function($excel) use ($item){
            $excel->sheet('returnOrder', function($sheet) use ($item){
                $sheet->setColumnFormat(array(
                    'A' => '0',
                    'U' =>'0'
                ));
                $sheet->setWidth(array(
                    'A'=>20,
                    'B'=>15,
                    'C'=>60,
                    'D'=>15,
                    'E'=>15,
                    'F'=>15,
                    'G'=>15,
                    'H'=>15,
                    'I'=>15,
                    'J'=>15,
                    'K'=>15,
                    'L'=>15,
                    'M'=>15,
                    'N'=>15,
                    'O'=>15,
                    'P'=>30,
                    'Q'=>30,
                    'R'=>20,
                    'S'=>20,
                    'T'=>15,
                    'U'=>30,
                    'V'=>20,
                ));
                $sheet->rows($item);
                $arrayCount = count($item);
                $position = 0;
                $rangeArr = ['A','B','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V'];
                foreach($item as $key=>$value){
                    $nextKey = $key+1;
                    $start = $key+1;
                    $end = $nextKey+1;
                    if($nextKey < $arrayCount){
                        if($item[$key][$position] == $item[$nextKey][$position]){
                            foreach ($rangeArr as $v){
                                $range = $v.$start.':'.$v.$end;
                                $sheet->mergeCells($range);
                            }
                        }
                    }
                }
            });
        })->export('xlsx');
    }

    //待退款
    public function refund($state = OrderReturn::STATE_NO_REFUND)
    {
        $tmp['order_no'] = Input::get('order_no');
        $tmp['name'] = Input::get('name');
        $tmp['mobile'] = Input::get('mobile');
        $orders = new OrderReturn();

        if ($tmp['order_no'] != null) {
            $orders = $orders->where('order_no', $tmp['order_no']);
        }

        if ($tmp['name'] != null) {
            $orders = $orders->where('receiver_name','like','%'.$tmp['name'].'%');
        }

        if ($tmp['mobile'] != null) {
            $orders = $orders->where('receiver_mobile', $tmp['mobile']);
        }

        $orders = $orders->whereState($state)->orderBy('id', 'desc');
        $orders = $this->checkList($orders, $state, $tmp);
        return view('boss.orders.refund', compact('orders', 'state', 'tmp'));

    }

    //全部订单
    public function allOrders($state = OrderBase::STATE_ALL)
    {
        $tmp['order_no'] = Input::get('order_no');
        $tmp['goods_title'] = Input::get('goods_title');
        $tmp['guide_name'] = trim(Input::get('guide_name'));
        $tmp['created_at_min'] = Input::get('created_at_min');
        $tmp['created_at_max'] = Input::get('created_at_max');
        $tmp['pay_type'] = Input::get('pay_type');
        $tmp['name'] = Input::get('name');
        $tmp['mobile'] = Input::get('mobile');
        $tmp['supplier'] = Input::get('supplier');
        $tmp['express_type'] = Input::get('express_type');
        $tmp['state'] = Input::get('state',$state);
        $orders = new OrderBase();
        $orders = $orders->orderBy('id','desc');

        $orders = $this->allorderList($orders, $tmp);
        $suppliers = SupplierBase::all();
        $selectedSupplier = SupplierBase::whereId($tmp['supplier'])->first();
        if($selectedSupplier){
            $selectedSupplier = $selectedSupplier->store_name;
        }
        foreach($orders as $values){
            $guideInfo = GuideBase::whereId($values->guide_id)->first();
            if($guideInfo){
                $values->guideName = $guideInfo->real_name;
            }
        }
        return view('boss.orders.allorders', compact('orders', 'state', 'tmp', 'suppliers','selectedSupplier'));

    }

    //修改待发货订单地址
    public function changeAddress($orderNo){
        $provinces = ConfCity::whereParentId(ConfCity::PROVINCE_PARENT_ID)->get();
        $orderInfo = OrderBase::whereOrderNo($orderNo)->first();
        $receiver_info = json_decode($orderInfo->receiver_info,true);
        return view('boss.orders.change_address',compact('provinces','orderNo','receiver_info'));
    }

    public function handleChangeAddress($orderNo,Request $request){
        $data = $request->all();
        $user = $request->user();
        if($data){
            $name = $data['name'];
            $mobile = $data['mobile'];
            $address = $data['address'];
            $province = explode('-',$data['province_id']);
            $province_id = $province[0];
            $province_name = $province[1];
            $city = explode('-',$data['city_id']);
            $city_id = $city[0];
            $city_name = $city[1];
            $discount = explode('-',$data['discount_id']);
            $discount_id = $discount[0];
            $discount_name = $discount[1];
            $orderInfo = OrderBase::whereOrderNo($orderNo)->whereState(OrderBase::STATE_PAYED)->first();
            $ReceiverInfoBeforeChange = $orderInfo->receiver_info;
            if($orderInfo){
                $receiver_info = json_decode($orderInfo->receiver_info,true);
                $receiver_info['name'] = urlencode($name);
                $receiver_info['mobile'] = $mobile;
                $receiver_info['province'] = urlencode($province_name);
                $receiver_info['province_id'] = $province_id;
                $receiver_info['city'] = urlencode($city_name);
                $receiver_info['city_id'] = $city_id;
                $receiver_info['district'] = urlencode($discount_name);
                $receiver_info['district_id'] = $discount_id;
                $receiver_info['address'] = urlencode($address);
                $updateDate = [
                    'receiver_name' => $name,
                    'receiver_mobile' => $mobile,
                    'receiver_info' => urldecode(json_encode($receiver_info))
                ];
                $state = OrderBase::whereOrderNo($orderNo)->update($updateDate);
                self::saveOrderLog($user,$ReceiverInfoBeforeChange,$orderNo,'修改地址');
                return $result = ['ret'=>$state,'msg'=>'地址更新成功！'];
            }else{
                return $result = ['ret'=>'no','msg'=>'没有对应状态的订单！'];
            }
        }else{
            return $result = ['ret'=>$data,'msg'=>'地址更新失败！'];
        }
    }

    public function getProvinceCitys($parent_id){
        $citys = ConfCity::whereParentId($parent_id)->get()->toArray();
        return $citys;
    }

    //保存订单日志
    static private function saveOrderLog($user,$data,$orderNo,$action){
        $orderInfo= OrderBase::whereOrderNo($orderNo)->whereState(OrderBase::STATE_PAYED)->first();
        $receiverInfoAfterChange = $orderInfo->receiver_info;
        $content = [
            'before'=>urlencode($data),
            'after'=>urlencode($receiverInfoAfterChange)
        ];
        $orderLog = new OrderLog();
        $orderLog->order_no = $orderNo;
        $orderLog->uid = $user->id;
        $orderLog->action = $action;
        $orderLog->content = urldecode(json_encode($content));
        $orderLog->save();
        return $orderLog;
    }

    //测试人员查看全部订单
    public function QAGetOrders(Request $request){
        $orderNo = $request->input('order_no','');
        $created_at_min = $request->input('created_at_min','');
        $created_at_max = $request->input('created_at_max','');
       $orders = OrderBase::where('state','!=',OrderBase::STATE_DELETE);
        if($orderNo){
            $orders = $orders->whereOrderNo($orderNo);
        }
        if($created_at_min && $created_at_max){
            $orders = $orders->whereBetween('created_at',[$created_at_min,$created_at_max]);
        }
        $orders = $orders->orderBy('id','desc')->paginate(self::PAGE_LIMIT);
        foreach($orders as $order){
            $order->receiver_info = json_decode($order->receiver_info,true);
            $orderGoods = OrderGood::whereOrderNo($order->order_no)->get();
            foreach($orderGoods as $goods){
                $goods->first_img = GoodsBase::whereId($goods['goods_id'])->pluck('first_image');
            }
            $order->goods = $orderGoods;
        }
        return view('boss.orders.qa_orders',compact('orders','orderNo','created_at_min','created_at_max'));
    }

    public function deleteOrder(Request $request){
        $orderNo = $request->input('order_no');
        if($orderNo){
            $orderState = OrderBase::whereOrderNo($orderNo)->update(['state'=>OrderBase::STATE_DELETE]);
            return $orderState;
        }else{
            return $orderNo;
        }
    }

    //自提订单，快递订单
    public function expressType($type){
        $tmp['order_no'] = Input::get('order_no');
        $tmp['goods_title'] = Input::get('goods_title');
        $tmp['created_at_min'] = Input::get('created_at_min');
        $tmp['created_at_max'] = Input::get('created_at_max');
        $tmp['pay_type'] = Input::get('pay_type');
        $tmp['name'] = Input::get('name');
        $tmp['mobile'] = Input::get('mobile');
        $tmp['supplier'] = Input::get('supplier');
        $tmp['express_type'] = Input::get('express_type');
        //$tmp['state'] = Input::get('state');
        $orders = OrderBase::whereState(OrderBase::STATE_PAYED)->orderBy('id','desc');
        $orders = $this->allorderList($orders, $tmp,$type);
        $suppliers = SupplierBase::all();
        $selectedSupplier = SupplierBase::whereId($tmp['supplier'])->first();
        if($selectedSupplier){
            $selectedSupplier = $selectedSupplier->store_name;
        }
        foreach($orders as $values){
            $guideInfo = GuideBase::whereId($values->guide_id)->first();
            if($guideInfo){
                $values->guideName = $guideInfo->real_name;
            }
        }
        return view('boss.orders.express_type',compact('type','orders', 'state', 'tmp', 'suppliers','selectedSupplier'));
    }

    //导出
    public function export($state = OrderBase::STATE_ALL)
    {
        if($state == 'express'){
            $tmp['order_no'] = Input::get('order_no');
            $tmp['goods_title'] = Input::get('goods_title');
            $tmp['created_at_min'] = Input::get('created_at_min');
            $tmp['created_at_max'] = Input::get('created_at_max');
            $tmp['pay_type'] = Input::get('pay_type', 0);
            $tmp['name'] = Input::get('name');
            $tmp['mobile'] = Input::get('mobile');
            $tmp['supplier'] = Input::get('supplier');
            $tmp['express_type'] = Input::get('express_type');
            $type = Input::get('type');
            $tmp['action'] = 1;
            $orders = new OrderBase();
            $orders = $orders->whereState(OrderBase::STATE_PAYED);
            $orders = $this->allorderList($orders,$tmp,$type);
        }else{

            $tmp['order_no'] = Input::get('order_no');
            $tmp['goods_title'] = Input::get('goods_title');
            $tmp['guide_name'] = trim(Input::get('guide_name'));
            $tmp['created_at_min'] = Input::get('created_at_min');
            $tmp['created_at_max'] = Input::get('created_at_max');
            $tmp['pay_type'] = Input::get('pay_type', 0);
            $tmp['name'] = Input::get('name');
            $tmp['mobile'] = Input::get('mobile');
            $tmp['supplier'] = Input::get('supplier');
            $tmp['express_type'] = Input::get('express_type');

            $tmp['state'] = Input::get('state',$state);
            $tmp['action'] = 1;
            $orders = new OrderBase();
            $orders = $this->allorderList($orders, $tmp);
        }
        if (!$orders->isEmpty()) {
            $field = ['订单编号', '订单状态', '供应商','导游姓名', '导游返利', '旅行社返利', '平台返利', '商品名称',
                '产品规格','供应单价','单价','商品数量','配送方式','运费','订单金额','订单供货价','买家留言','收货人姓名','收货地址-省市区','收货地址-街道地址','收货人电话','订单创建时间','物流公司','物流单号','发货时间'];
            $data[] = $field;
            $i = 1;
            foreach ($orders as $order) {
                $pay_type = $order['pay_type'];
                if($pay_type == OrderBase::PAY_TYPE_ALI){
                    $pay_info = 'ping++支付宝支付';
                }else if($pay_type == OrderBase::PAY_TYPE_WX){
                    $pay_info = 'ping++微信支付';
                }else{
                    $pay_info = '微信商户支付';
                }
                $order->supplier_id = SupplierBase::whereId($order->supplier_id)->pluck('name');
                $guide_name = GuideBase::whereId($order->guide_id)->pluck('real_name');
                $order->guide_name = empty($guide_name)? 'GID.'.$order->guide_id : $guide_name;
                $order->express_type = $order->express_type == 0 ? '快递' : '自提';
                $order->province_city = $order->receiver_info['province'].$order->receiver_info['city'].$order->receiver_info['district'];
                $order->address = $order->receiver_info['address'];
                foreach($order->attr as $v){
                    $data[$i] = [
                        $order->order_no, $order->state,$order->supplier_id, $order->guide_name,number_format($v['guide_rebate'],2),number_format($v['travel_agency_rebate'],2),$v['platform_fee'],$v['title'],$v['spec_name'],$v['price_buying'],$v['price'],$v['num'],$order->express_type,$order->amount_express,$order->amount_goods,$order->goodsBuyingPrice,$order->remark,$order->receiver_name,$order->province_city,$order->address,$order->receiver_mobile,$order->created_at,$order->express_name,$order->express_no,$order->express_time];
                    $i++;
                }
            }
        }
        if (!empty($data)) {
            Excel::create(date('YmdHis'), function ($excel) use ($data) {
                $excel->sheet('order', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                        'U' =>'0',
                        'X' =>'0',
                    ));
                    $sheet->setWidth(array(
                        'A'=>20,
                        'B'=>15,
                        'C'=>15,
                        'D'=>15,
                        'E'=>15,
                        'F'=>15,
                        'G'=>15,
                        'H'=>60,
                        'I'=>15,
                        'J'=>15,
                        'K'=>15,
                        'L'=>15,
                        'M'=>15,
                        'N'=>15,
                        'O'=>15,
                        'P'=>15,
                        'Q'=>15,
                        'R'=>20,
                        'S'=>30,
                        'T'=>30,
                        'U'=>20,
                        'V'=>20,
                        'W'=>20,
                        'X'=>20,
                        'Y'=>20,
                    ));
                    $sheet->rows($data);
                    $arrayCount = count($data);
                    $position = 0;
                    $rangeArr = ['A','B','C','D','E','F','G','M','N','O','P','Q','R','S','T','U','V','W','X','Y'];
                    foreach($data as $key=>$value){
                        $nextKey = $key+1;
                        $start = $key+1;
                        $end = $nextKey+1;
                        if($nextKey < $arrayCount){
                            if($data[$key][$position] == $data[$nextKey][$position]){
                                foreach ($rangeArr as $v){
                                    $range = $v.$start.':'.$v.$end;
                                    $sheet->mergeCells($range);
                                }
                            }
                        }
                    }
                });
            })->export('xlsx');
        }
        exit;
    }

    //订单详情
    function ordersDetail($order_no)
    {
        $orders = OrderBase::whereOrderNo($order_no)->first();
        $orders->receiver_info = json_decode($orders->receiver_info, true);
        $orders->addressDetail = $orders->receiver_info['province'].$orders->receiver_info['city'].$orders->receiver_info['district'];
        if($orders->receiver_info['address']){
            $orders->addressDetail = $orders->addressDetail.$orders->receiver_info['address'];
        }
        $orderLogs = OrderLog::whereOrderNo($order_no)->whereIn('action', ['下单', '付款', '发货', '结算'])->get();
        $orders->orderLog = $orderLogs;
        $orderGoods = OrderGood::whereOrderNo($orders->order_no)->get();//一个订单有多个商品

        if(($orders->amount_goods + $orders->amount_express) < $orders->amount_real){//补充逻辑
            $orders->amount_real = $orders->amount_goods + $orders->amount_express;
        }

        $tmp = array();//订单下商品详情
        foreach ($orderGoods as $orderGood) {
            $goodsBase = GoodsBase::where('id', $orderGood->goods_id)->first();

            //赠品
            if ($orderGood->is_gift == 1) {
                $goodsGifts = GoodsGift::where('goods_id', $goodsBase->id)->get();
                $goods_gift = array();
                foreach ($goodsGifts as $goodsGift) {
                    $goodsBase_gift = GoodsBase::where('id', $goodsGift->gift_id)->first();
                    $goodsSpec_gift = GoodsSpec::where('goods_id', $goodsGift->gift_id)->first();
                    $goodsGift->cover = $goodsBase_gift->cover;
                    $goodsGift->title = $goodsBase_gift->title;
                    $goodsGift->packname = $goodsSpec_gift->name;
                    $goodsGift->price = $goodsSpec_gift->price;
                    $goods_gift[] = $goodsGift;
                }
                $orderGood->goods_gift = $goods_gift;
            }

            $orderGood->cover = $goodsBase->cover;
            $orderGood->title = $goodsBase->title;
            $orderGood->packname = $orderGood->spec_name;
            $tmp[] = $orderGood;
        }
        $orders->tmp = $tmp;
        return view('boss.orders.order_detail', ['orders' => $orders,'order_no'=>$order_no]);
    }

    //更新快递单号
    public function updateExpressNumber(Request $request,$order_no){
        $express_no = $request->input('express_no');
        $orderBase = new OrderBase();
        $orderBase->whereOrderNo($order_no)->update(['express_no'=>$express_no]);
    }

    //售后详情
    function checkDetail($order_no,$authority = 0)
    {
        $orders = OrderReturn::whereOrderNo($order_no)->orderBy('id','desc')->first();
        $orderBase = OrderBase::whereOrderNo($order_no)->first();
        $supplierBase = SupplierBase::whereId($orders->supplier_id)->first();
        $orderGoods = OrderGood::whereOrderNo($order_no)->get();
        //$refund_amount = OrderReturnLog::whereOrderNo($order_no)->first();

        switch ($orders->state) {//处理时间
            case '0':
                $ret = "退款申请";
                $orders->returnContent = OrderReturnLog::whereReturnNo($orders->return_no)->whereAction($ret)->first();
                $orderState = '待处理';
                break;

            case '1':
                $ret = "审核通过";
                $orders->returnContent = OrderReturnLog::whereReturnNo($orders->return_no)->whereAction($ret)->first();
                $orderState = '待退款';
                break;

            case '3':
                $ret = "成功退款";
                $orders->returnContent = OrderReturnLog::whereReturnNo($orders->return_no)->whereAction($ret)->first();
                $orderState = '已退款';
                break;

            case '4':
                $ret = "审核驳回";
                $orders->returnContent = OrderReturnLog::whereReturnNo($orders->return_no)->whereAction($ret)->first();
                $orderState = '已驳回';
                break;
        }
        
        $orderReturnLog = OrderReturnLog::whereReturnNo($orders->return_no)->whereAction($ret)->first();
        if (!is_null($orderReturnLog)) {
            $return_information = json_decode($orderReturnLog->content, true);
            $orders->return_info = isset($return_information['content']) ? $return_information['content'] : '';
        }
        $orders->amount_goods = $orderBase->amount_goods;
        $orderAmount = $orderBase->amount_goods + $orderBase->amount_express - $orderBase->amount_coupon;
        if ($orders->state == 0) {
            $orders->amount = $orderBase->amount_goods + $orderBase->amount_express - $orderBase->amount_coupon;
        }

        //商品所属供应商信息
        $orders->supplier_name = $supplierBase->name;
        $orders->supplier_mobile = $supplierBase->mobile;
        $orders->receiver_info = json_decode($orderBase->receiver_info, true);//收货人信息
        $orders->pay_type = $orderBase->pay_type;//支付方式
        $orders->express_type = $orderBase->express_type == 0 ? '快递' : '自提';
        $orders->amount_express = $orderBase->amount_express;
        $orders->buyer_message = $orderBase->buyer_message;//留言
        //图片？？？？
        //快递
        $orders->express_name = $orderBase->express_name;
        $orders->express_no = $orderBase->express_no;

        //商品售后图片
        $returnImg = OrderReturnImage::whereReturnId($orders->id)->get();
        if (!empty($returnImg)) {
            $orders->returnImg = $returnImg->toArray();
        }
        //商品退款说明

        $tmp = array();
        foreach ($orderGoods as $orderGood) {
            $goodsBase = GoodsBase::where('id', $orderGood->goods_id)->first();
            $goodsSpec = GoodsSpec::where('id', $orderGood->spec_id)->first();
            $orderGood->cover = $goodsBase->cover;
            $orderGood->title = $goodsBase->title;
            $orderGood->packname = $goodsSpec->name;
            $tmp[] = $orderGood;
        }
        $orders->tmp = $tmp;
        $action = 'list';
        //售后审核驳回记录
        $refundAuditRecord = self::refundAuditRecord($order_no);
        return view('boss.orders.afterSale_detail', ['orders' => $orders,'action' => $action,'authority'=>$authority,'refundAuditRecord'=>$refundAuditRecord,'orderState'=>$orderState]);

    }

    /**
     * @param $orderNo
     * 售后审核驳回记录
     */
    static private function refundAuditRecord($orderNo){
        $items = OrderReturn::whereOrderNo($orderNo)->whereState(OrderReturn::STATE_REFUSE)->get();
        $orderReturnLog = OrderReturnLog::whereReturnNo("T".$orderNo)->whereAction('审核驳回')->get()->toArray();
        if($items){
            foreach($items as $key =>$v){
                $v->images = OrderReturnImage::whereReturnId($v['id'])->get()->toArray();
                $refundLogContent = isset($orderReturnLog[$key]['content']) ? json_decode($orderReturnLog[$key]['content'],true) : '';
                $v->refuse_content = isset($refundLogContent['content']) ? $refundLogContent['content'] : '';
                $v->auditer = isset($refundLogContent['audit']) ? $refundLogContent['audit'] : '';
            }
        }
        return $items;
    }

    function refuseShow($order_no, $action, Request $request)
    {
        if ($action == 'refuse') {

            return view("boss.orders.afterSale_detail", ['order_no' => $order_no, 'action' => $action]);

        } else {
            $reasons['content'] = Input::get('reasons');
            OrderReturn::whereOrderNo($order_no)->update(['state' => OrderReturn::STATE_REFUSE]);

            $orderReturn = OrderReturn::whereOrderNo($order_no)->first();
            $userInfos = UserBase::whereId($orderReturn->uid)->first();
            $mobile = $userInfos->mobile;
            $type = OrderController::refundAfterSale;
            $ip = ip2long($request->getClientIp());
            $text = "【易游购】您好， 您的订单" . $order_no . "退款申请已被驳回，原因：" . $reasons['content'] . "
";
            $user = $request->user()->name;
            $content = ['audit' => $user,'content' => $reasons['content'], 'sms' => $text];
            $content['audit'] = urlencode($content['audit']);
            $content['content'] = urlencode($content['content']);
            $content['sms'] = urlencode($content['sms']);
            $content = urldecode(json_encode($content));
            $orderReturnLog = new OrderReturnLog();
            $orderReturnLog->return_no = 'T' . $order_no;
            $orderReturnLog->uid = $userInfos->id;
            $orderReturnLog->action = '审核驳回';
            $orderReturnLog->content = $content;
            $orderReturnLog->save();
            parent::platformSendSms($mobile, $ip, $type, $text);
            return response()->json(['ret' => 'yes']);
        }
    }

    function refundPass(Request $request, $action, $order_no, $amount_real)
    {
        if ($action == 'passing') {
            return view("boss.orders.afterSale_detail", ['action' => $action, 'order_no' => $order_no, 'amount_real' => $amount_real]);
        }
        //审核通过
        if ($action == 'passOne') {
            $return_content = Input::get('return_content');
            $amount_real = Input::get('refund');
            if (empty($return_content)) {
                $return_content = "卖家同意了您的退款申请，待退款金额" . $amount_real . "元";
            }
            //return billing
            $OrderBase = OrderBase::whereOrderNo($order_no)->first();
            $orderAmount = $OrderBase->amount_goods + $OrderBase->amount_express - $OrderBase->amount_coupon;
            $rate = number_format($amount_real / $orderAmount, 2);
            if ($rate > 1) {
                return response()->json(['ret' => 'no']);
            }
            $tmp = OrderReturn::whereOrderNo($order_no)->whereState(OrderReturn::STATE_NO_CHECK)->update(['state' => OrderReturn::STATE_NO_REFUND, 'amount' => $amount_real]);
            $orderReturn = OrderReturn::whereOrderNo($order_no)->first();
            $SupplierBilling = SupplierBilling::whereOrderNo($order_no)->first();
            if (!is_null($SupplierBilling)) {
                $SupplierBilling->return_amount = $SupplierBilling->amount * $rate;
                $SupplierBilling->save();
            }

            $GuideBilling = GuideBilling::whereOrderNo($order_no)->first();
            if (!is_null($GuideBilling)) {
                $GuideBilling->return_amount = $GuideBilling->amount * $rate;
                $GuideBilling->save();
            }

            $TaBilling = TaBilling::whereOrderNo($order_no)->first();
            if (!is_null($TaBilling)) {
                $TaBilling->return_amount = $TaBilling->amount * $rate;
                $TaBilling->save();
            }

            $PlatformBilling = PlatformBilling::whereOrderNo($order_no)->first();
            if (!is_null($PlatformBilling)) {
                $PlatformBilling->return_amount = $PlatformBilling->amount * $rate;
                $PlatformBilling->save();
            }

            $orderReturnLog = new OrderReturnLog();
            $text = "【易游购】您好， 您的订单" . $order_no . "退款申请已审核通过。退款金额：" . $amount_real . ",平台将在1-2个工作日内将资金原路返回至您的支付账户。请注意查收！";
            $content = ['content' => $return_content, 'sms' => $text];
            $content['content'] = urlencode($content['content']);
            $content['sms'] = urlencode($content['sms']);
            $content = urldecode(json_encode($content));
            $orderReturnLog->return_no = "T" . $order_no;
            $orderReturnLog->uid = $orderReturn->uid;
            $orderReturnLog->action = '审核通过';
            $orderReturnLog->content = $content;
            $orderReturnLog->save();
            //连接短信接口
//            $OrderNo = OrderReturn::whereOrderNo($order_no)->first();
            $userInfos = UserBase::whereId($orderReturn->uid)->first();
            $mobile = $userInfos->mobile;
            $type = OrderController::afterSale_audit;
            $ip = ip2long($request->getClientIp());
            $text = "【易游购】您好， 您的订单" . $order_no . "退款申请已审核通过。退款金额：" . $amount_real . ",平台将在1-2个工作日内将资金原路返回至您的支付账户。请注意查收！";
            parent::platformSendSms($mobile, $ip, $type, $text);

            return response()->json(['ret' => 'yes']);
        }

        /*if ($action == 'passOrder') {
            OrderReturn::whereOrderNo($order_no)->update(['state' => OrderReturn::STATE_SUCCESS, 'amount' => $amount_real]);
            $orderReturn = OrderReturn::whereOrderNo($order_no)->first();
            $userInfos = UserBase::whereId($orderReturn->uid)->first();
            $mobile = $userInfos->mobile;
            $type = OrderController::afterSale_pay;
            $ip = $ip = ip2long($request->getClientIp());
            $text = "【易游购】您好， 您的订单$order_no  退款金额：" . $amount_real . "，已成功退回至您的支付账户中，具体到账时间以银行到账时间为准，请及时注意查收！";
            $content = ['content' => "退款成功，收到退款金额$amount_real", 'sms' => $text];
            $content['content'] = urlencode($content['content']);
            $content['sms'] = urlencode($content['sms']);
            $content = urldecode(json_encode($content));
            $orderReturnLog = new OrderReturnLog();
            $orderReturnLog->return_no = "T" . $order_no;
            $orderReturnLog->uid = $orderReturn->uid;
            $orderReturnLog->action = '成功退款';
            $orderReturnLog->content = $content;
            $orderReturnLog->save();
            parent::platformSendSms($mobile, $ip, $type, $text);
        }*/

    }

    //自动退款
    public function autoRefund($orderNo,Request $request){
        $return_amount = $request->input('amount');
        $description = $request->input('return_content');
        $orderInfo = OrderBase::whereOrderNo($orderNo)->first();
        $orderReturnInfo = OrderReturn::whereOrderNo($orderNo)->first();
        $orderAmount = $orderInfo->amount_goods + $orderInfo->amount_express - $orderInfo->amount_coupon;
        if($return_amount <= $orderAmount){
            if($orderInfo->pay_type == 3){
                $ip = ip2long($request->getClientIp());
                $result = self::wxAutoRefund($orderNo,$orderReturnInfo,$orderInfo,$return_amount,$ip);
                return $result;
            }elseif($orderInfo->pay_type == 2){
                $result = self::pingxxWxAutoRefund($orderNo,$return_amount,$description);
                return $result;
            }else{
                $result = self::pingxxAliAutoRefund($orderNo,$return_amount,$description);
                return $result;
            }
        }else{
            return $data = ['ret'=>'no','msg'=>'退款金额错误'];
        }
    }

    //微信自动退款业务
    static private function wxAutoRefund($orderNo,$orderReturnInfo,$orderInfo,$return_amount,$ip){
        $result = self::wechatRefund($orderNo,$orderReturnInfo->return_no,$orderInfo->amount_real,$return_amount);
        if($result['return_code'] == 'SUCCESS'){
            if($result['result_code'] == 'FAIL'){
                return $data = ['ret'=>'no','msg'=>$result['err_code_des']];
            }else{
                //记录订单日志
                $result['content'] = urlencode('退款处理中。。。');
                $orderReturnLog = new OrderReturnLog();
                $orderReturnLog->return_no = 'T'.$orderNo;
                $orderReturnLog->action = '自动退款';
                $orderReturnLog->content = urldecode(json_encode($result));
                $orderReturnLog->save();

                OrderReturn::whereOrderNo($orderNo)->update(['state' => OrderReturn::STATE_SUCCESS, 'amount' => $return_amount]);
                $orderReturn = OrderReturn::whereOrderNo($orderNo)->first();
                $userInfos = UserBase::whereId($orderReturn->uid)->first();
                $mobile = $userInfos->mobile;
                $type = OrderController::afterSale_pay;
                $text = "【易游购】您好， 您的订单$orderNo  退款金额：" . $return_amount . "，已成功退回至您的支付账户中，具体到账时间以银行到账时间为准，请及时注意查收！";
                $content = ['content' => "退款成功，收到退款金额$return_amount", 'sms' => $text];
                $content['content'] = urlencode($content['content']);
                $content['sms'] = urlencode($content['sms']);
                $content = urldecode(json_encode($content));
                $orderReturnLog = new OrderReturnLog();
                $orderReturnLog->return_no = "T" . $orderNo;
                $orderReturnLog->uid = $orderReturn->uid;
                $orderReturnLog->action = '成功退款';
                $orderReturnLog->content = json_encode($result);
                $orderReturnLog->save();
                parent::platformSendSms($mobile, $ip, $type, $text);
                return $data =['ret'=>'yes','msg'=>'退款成功'];
            }
        }else{
            return $data = ['ret'=>'no','msg'=>'退款失败'];
        }
    }

    //ping++微信自动退款业务
    static private function pingxxWxAutoRefund($orderNo,$return_amount,$description){
        $orderSn = OrderWx::where('order_no','like','%'.$orderNo.'%')->first();
        $orderSn = isset($orderSn) ? $orderSn->order_sn : $orderNo;
        $orderPayInfos = OrderPay::whereOrderNo($orderSn)->get()->toArray();
        $description = $description ? $description : '售后退款';
        foreach($orderPayInfos as $v){
            $payInfo = json_decode($v['pay_info'],true);
            if($payInfo['channel'] == 'wx'){
                $id = $payInfo['id'];
            }
        }
        if(empty($id)){
            return $data = ['ret'=>'no','msg'=>'该订单ID不存在'];
        }else{
            $result = self::pingxxRefund($id,$return_amount,$description);
            $result['content'] = urlencode('退款处理中。。。');
            if(isset($result['error'])){
                return $data = ['ret'=>'no','msg'=>$result['error']['message']];
            }else{
                //记录订单日志
                $orderReturnLog = new OrderReturnLog();
                $orderReturnLog->return_no = 'T'.$orderNo;
                $orderReturnLog->action = '自动退款';
                $orderReturnLog->content = urldecode(json_encode($result));
                $orderReturnLog->save();
                if($result['status'] == 'pending'){
                    return $data =['ret'=>'yes','msg'=>'退款处理中。。。'];
                }elseif($result['status'] == 'succeeded'){
                    return $data =['ret'=>'yes','msg'=>'退款成功'];
                }else{
                    return $data =['ret'=>'no','msg'=>'退款失败'];
                }
            }
        }
    }

    //ping++支付宝自动退款业务
    static private function pingxxAliAutoRefund($orderNo,$return_amount,$description){
        $orderSn = OrderWx::where('order_no','like','%'.$orderNo.'%')->first();
        $orderSn = isset($orderSn) ? $orderSn->order_sn : $orderNo;
        $orderPayInfos = OrderPay::whereOrderNo($orderSn)->get()->toArray();
        $description = $description ? $description : '售后退款';
        foreach($orderPayInfos as $v){
            $payInfo = json_decode($v['pay_info'],true);
            if($payInfo['channel'] == 'alipay'){
                $id = $payInfo['id'];
            }
        }
        if(empty($id)){
            return $data = ['ret'=>'no','msg'=>'该订单ID不存在'];
        }else {
            $result = self::pingxxRefund($id,$return_amount,$description);
            $result['content'] = urlencode('退款处理中。。。');
            //记录订单日志
            $orderReturnLog = new OrderReturnLog();
            $orderReturnLog->return_no = 'T'.$orderNo;
            $orderReturnLog->action = '自动退款';
            $orderReturnLog->content = urldecode(json_encode($result));
            $orderReturnLog->save();
            if(isset($result['error'])){
                return $data = ['ret'=>'no','msg'=>$result['error']['message']];
            }else{
                if($result['status'] == 'pending' && $result['failure_code'] == 'refund_wait_operation'){
                    $url = explode(':',$result['failure_msg']);
                    return $data = ['ret'=>'ali_pending','msg'=>$url[1].':'.$url[2]];
                }elseif($result['status'] == 'succeeded'){
                    return $data =['ret'=>'yes','msg'=>'退款成功'];
                }else{
                    return $data =['ret'=>'no','msg'=>'退款失败'];
                }
            }
        }
    }

    /**
     * @param $content
     * @param Request $request
     * @return int
     */
    public function orderPressSms($content,Request $request){
        $ip = ip2long($request->getClientIp());
        $mobile = $request->input('mobile');
        $type = PlatformSm::ORDER_PRESS_SMS;
        parent::platformSendSms($mobile, $ip, $type, $content);
        $sendSmsRecord = PlatformSm::whereType(PlatformSm::ORDER_PRESS_SMS)->whereMobile($mobile)->orderBy('id','desc')->first();
        return $sendSmsRecord;
    }

    /**
     * @param $orderNo
     * @return array
     */
    public function getOrderSupplierInfo($orderNo){
        $orderBase = OrderBase::whereOrderNo($orderNo)->first();
        $supplierInfo = SupplierBase::whereId($orderBase->supplier_id)->first()->toArray();
        return $supplierInfo;
    }
    private function checkList($orders, $state, $tmp,$action = 0)
    {
        if ($tmp['order_no'] != null) {
            $orders = $orders->where('order_no', $tmp['order_no']);
        }

        if ($tmp['name'] != null) {
            $orders = $orders->where('receiver_name','like','%'.$tmp['name'].'%');
        }

        if ($tmp['mobile'] != null) {
            $orders = $orders->where('receiver_mobile', $tmp['mobile']);
        }

        $orders = $orders->whereState($state)->orderBy('id', 'desc');

        if($action == 1){
            $orders = $orders->get();
        }else{
            $orders = $orders->paginate($this->page);
        }

        foreach ($orders as $order) {
            if(($order->amount_goods + $order->amount_express) < $order->amount_real){//补充逻辑
                $order->amount_real = $order->amount_goods + $order->amount_express;
            }
            $orderBase = OrderBase::whereOrderNo($order->order_no)->first();
            $supplierInfo = SupplierBase::whereId($order->supplier_id)->first();
            if(!is_null($supplierInfo)){
                $order->supplier_name = $supplierInfo->name;
                $order->supplier_mobile = $supplierInfo->mobile;
            }
            //取出发货的时间
            $order->express_time     = (empty($orderBase->express_time) ? '0000-00-00 00:00:00' : $orderBase->express_time);
            $order->created_at_order = (empty($orderBase->created_at) ? '' : $orderBase->created_at);
            $order->amount_goods = (empty($orderBase->amount_goods) ? '' : $orderBase->amount_goods);
            $order->amount_real  = (empty($orderBase->amount_real) ? '' : $orderBase->amount_real);
            //用户信息
            $tmp = json_decode($orderBase->receiver_info);
            $order->name = $tmp->name;
            $order->pay_type = $orderBase->pay_type;
            $order->mobile = $tmp->mobile;
            $order->address = $tmp->address;
            $order->receiver_info = $tmp;
            //导出用字段start
            $order->province_city = $tmp->province.'-'.$tmp->city.'-'.$tmp->district;
            $order->express_type = $orderBase->express_type == 0 ? '快递' : '自提';
            $order->amount_express = $orderBase->amount_express;
            $order->express_name = $orderBase->express_name;
            $order->express_no = $orderBase->express_no;
            $order->remark = $orderBase->remark;
            //end
            $tem = array();
            $orderGoods = OrderGood::whereOrderNo($order->order_no)->get();

            //导出用字段
            $titles = [];
            $amountBuyPrice = [];
            foreach($orderGoods as $value){
                $amountBuyPrice[] = $value->price_buying * $value->num;
                $titles[] = ['goods_title'=>$value->goods_title,'spec_name'=>$value->spec_name,'price_buying'=>$value->price_buying,'price'=>$value->price,'num'=>$value->num];
            }
            $order->titles = $titles;
            $order->amountBuyPrice = number_format(array_sum($amountBuyPrice),2);
            //end
            foreach ($orderGoods as $orderGood) {
                $goodsSpec = GoodsSpec::whereId($orderGood->spec_id)->first();
                $goodsBase = GoodsBase::whereId($orderGood->goods_id)->first();
                $goodsBase->bannerFirst = GoodsImage::whereGoodsId($orderGood->goods_id)->first();
                $orderGood->cover = (empty($goodsBase->cover) ? '' : $goodsBase->cover);
                $orderGood->title = (empty($goodsBase->title) ? '' : $goodsBase->title);
                $orderGood->packname = (empty($goodsBase->name) ? '' : $goodsBase->name);
                $tem[] = [$orderGood, $goodsSpec, $goodsBase];
            }
            $order->tmp = $tem;

        }
        return $orders;
    }

    private function allorderList($orders, $tmp,$type = '订单状态')
    {
        if ($tmp['order_no']) {
            $orders = $orders->where('order_no', $tmp['order_no']);
        }

        if(isset($tmp['goods_title'])){
            if($tmp['goods_title']){
                $orderNos = OrderGood::where('goods_title','like','%'.$tmp['goods_title'].'%')->lists('order_no')->toArray();
                $orders = $orders->whereIn('order_no',$orderNos);
            }
        }

        if(isset($tmp['guide_name'])){
            if($tmp['guide_name']){
                $guideIds = GuideBase::where('real_name','like','%'.$tmp['guide_name'].'%')->lists('id')->toArray();
                $orders = $orders->whereIn('guide_id',$guideIds);
            }
        }

        if ($tmp['created_at_min']) {
            $orders = $orders->where('created_at', '>', $tmp['created_at_min']);
        }

        if ($tmp['created_at_max']) {
            $orders = $orders->where('created_at', '<', $tmp['created_at_max']);
        }


        if ($tmp['pay_type']) {
            $orders = $orders->where('pay_type', $tmp['pay_type']);
        }

        if ($tmp['name']) {//收货人
            $orders = $orders->where('receiver_name','like','%'. $tmp['name'].'%');
        }

        if ($tmp['mobile']) {
            $orders = $orders->where('receiver_mobile', $tmp['mobile']);
        }

        if ($tmp['supplier']) {
            $orders = $orders->where('supplier_id', $tmp['supplier']);
        }

        if(!is_null($tmp['express_type'])){
            if($tmp['express_type'] != 'value'){
                $orders = $orders->where('express_type', $tmp['express_type']);
            }
        }
        if($type == '订单状态'){
            if($tmp['state'] != OrderBase::STATE_ALL){
                $orders = $orders->where('state',$tmp['state']);
            }
        }else{
            if($type == 'kuaidi'){
                $orders = $orders->where('express_type',0);
            }
            $orders = $orders->where('express_type',$type);
        }
        if(isset($tmp['action'])){
            $orders = $orders->where('state','!=',OrderBase::STATE_DELETE)->orderBy('id','desc')->get();
        }else{
            $orders = $orders->where('state','!=',OrderBase::STATE_DELETE)->orderBy('id','desc')->paginate($this->page);
        }

        foreach ($orders as $key => $order) {
            if(($order->amount_goods + $order->amount_express) < $order->amount_real){//补充逻辑
                $order->amount_real = $order->amount_goods + $order->amount_express;
            }
            //催单记录
            $supplierBase = SupplierBase::whereId($order->supplier_id)->first();
            $sendSmsRecord = PlatformSm::whereType(PlatformSm::ORDER_PRESS_SMS)->where('code','like','%'.$order->order_no.'%')->whereMobile($supplierBase->mobile)->orderBy('id','desc')->first();
            $sendSmsRecordTime = is_null($sendSmsRecord)? '' : $sendSmsRecord->created_at;
            $order->sendSmsRecordTime = $sendSmsRecordTime;


            $orderGoods = OrderGood::whereOrderNo($order->order_no)->get();
            $order->receiver_info = json_decode($order->receiver_info, true);
            $tmp = array();
            $attr = [];
            $goodsBuyingPrice = [];
            foreach($orderGoods as $value){
                $guide_rebate = $value->guide_rate * $value->price * $value->num / 100;
                $travel_agency_rebate = $value->travel_agency_rate * $value->price * $value->num / 100;
                $goodsBuyingPrice[] = $value->price_buying * $value->num;
                $attr[] = ['title'=>$value->goods_title,'spec_name'=>$value->spec_name,'price_buying'=>$value->price_buying,'price'=>$value->price,'num'=>$value->num,'guide_rebate'=>$guide_rebate,'travel_agency_rebate'=>$travel_agency_rebate,'platform_fee'=>$value->platform_fee];
            }
            $order->attr = $attr;
            $order->goodsBuyingPrice = array_sum($goodsBuyingPrice);

            foreach ($orderGoods as $orderGood) {
                $goodsBase = GoodsBase::where('id', $orderGood->goods_id)->first();
                $goodsSpec = GoodsSpec::where('id', $orderGood->spec_id)->first();
                $goodsBase->bannerFirst = GoodsImage::whereGoodsId($orderGood->goods_id)->first();
                $orderGood->cover = (empty($goodsBase->cover) ? '无' : $goodsBase->cover);
                $orderGood->title = (empty($goodsBase->title) ? '无' : $goodsBase->title);
                $orderGood->packname = (empty($goodsSpec->name) ? '无' : $goodsSpec->name);
                $tmp[] = [$orderGood, $goodsSpec, $goodsBase];
            }
            
            switch ($order->state){
                case (OrderBase::STATE_NO_PAY):
                    $order->state = '待付款';
                    break;
                case (OrderBase::STATE_PAYED):
                    $order->state = '待发货';
                    break;
                case (OrderBase::STATE_SEND):
                    $order->state = '待收货';
                    break;
                case (OrderBase::STATE_FINISHED):
                    $order->state = '已完成';
                    break;
                case (OrderBase::STATE_CANCEL_SYSTEM):
                    $order->state = '系统超时取消';
                    break;
                case (OrderBase::STATE_CANCEL_USER):
                    $order->state = '用户主动取消';
                    break;
                case (OrderBase::STATE_FAIL):
                    $order->state = '客服关闭订单（缺货客服关闭，退钱)';
                    break;
                default:
                    $order->state = '已关闭';
                    break;
            }
                $order->tmp = $tmp;
            }
            return $orders;



    }


    //微信退款接口
    static private function wechatRefund($orderNo,$refundNo,$amount,$return_amount){
        $options = [

            'app_id' => env('WX_APPID'),
            'app_secret' => env('WX_APPSECRET'),
            'payment' => [
                'merchant_id'        => env('WECHAT_PAYMENT_MERCHANT_ID'),
                'key'                => env('WECHAT_PAYMENT_KEY'),
                'cert_path'   => env('WECHAT_PAYMENT_CERT_PATH', base_path('cert/apiclient_cert.pem')),
                'key_path'    => env('WECHAT_PAYMENT_CERT_PATH', base_path('cert/apiclient_key.pem')),
            ],
        ];
        $app = new Application($options);
        $payment = $app->payment;
        $result = $payment->refund($orderNo, $refundNo, $amount*100,$return_amount*100); // 总金额 100， 退款 80，操作员：商户号
        return json_decode($result,true);
    }

    //pingxx退款接口
    static private function pingxxRefund($id,$amount,$description){
            $ch = curl_init();
            $data=[
                'description'=>$description,
                'amount' => $amount*100,
            ];
            $parament = http_build_query($data);
            curl_setopt($ch,CURLOPT_URL,'https://api.pingxx.com/v1/charges/'.$id.'/refunds');
            curl_setopt($ch,CURLOPT_USERPWD,env('pingxx_live_key'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parament);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            return json_decode($result,true);
    }


}
