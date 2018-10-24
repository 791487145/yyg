<?php

namespace App\Http\Controllers\Store;

use App\Models\ConfBank;
use App\Models\GoodsBase;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderReturn;
use App\Models\SupplierBase;
use App\Models\SupplierSm;
use App\Models\SupplierWithdraw;
use Illuminate\Http\Request;
use App\Models\SupplierBilling;
use App\Http\Requests;
use App\Http\Controllers\Store\StoreController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
class FundController extends StoreController
{
    private $page = 20;
    const inMoney = 1;
    const state_fund = 1;

    /* 交易记录 */
    function record($inOut = 1){
        $trade_no = Input::get('trade_no');
        $order_no = Input::get('order_no');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        if(intval($inOut == 1)){
            $billings = $this->recordBuild(self::inMoney)->whereSupplierId($this->user['id']);
            if($trade_no){
                $billings = $billings->whereTradeNo($trade_no);
            }
            if($order_no){
                $billings = $billings->whereOrderNo($order_no);
            }
            if($start_time){
                $billings = $billings->whereBetween('created_at',[$start_time,is_null($end_time)?date('Y-m-d H:i:s'):$end_time]);
            }
            $billings = $billings->orderBy('id','desc')->paginate($this->page);
            if (!$billings->isEmpty()){
                foreach ($billings as $billing){
                    $billing->order = OrderBase::whereOrderNo($billing->order_no)->first();
                    $goodsTitles = OrderGood::whereOrderNo($billing->order->order_no)->lists('goods_title')->toArray();
                    $billing->goodsInfo = $goodsTitles;
                    $billing->info = json_decode($billing->remark);
                }
            }
            $option = Input::all();
            return view('store.fund.record')->with(['billings'=>$billings,'option'=>$option,'inOut'=>$inOut,'trade_no'=>$trade_no,'order_no'=>$order_no,'start_time'=>$start_time,'end_time'=>$end_time]);
        }

        if(intval($inOut) == 2){
            $returnOrders =OrderReturn::whereSupplierId($this->user['id']);
            if($trade_no){
                $returnOrders = $returnOrders->whereReturnNo($trade_no);
            }
            if($order_no){
                $returnOrders = $returnOrders->whereOrderNo($order_no);
            }
            if($start_time){
                $returnOrders = $returnOrders->whereBetween('created_at',[$start_time,is_null($end_time)?date('Y-m-d H:i:s'):$end_time]);
            }
            $returnOrders = $returnOrders->orderBy('id','desc')->whereState(OrderReturn::STATE_SUCCESS)->paginate($this->page);
            if($returnOrders){
                foreach ($returnOrders as $value){
                    $value->order = OrderBase::whereOrderNo($value->order_no)->first();
                    $goodsTitles = OrderGood::whereOrderNo($value->order->order_no)->lists('goods_title')->toArray();
                    $value->goodsInfo = $goodsTitles;
                    $value->info = json_decode($value->remark);
                }
            }
            $option = Input::all();
            return view('store.fund.record')->with(['billings'=>$returnOrders,'option'=>$option,'inOut'=>$inOut]);
        }

    }

    /* 营业额统计 */
    function index(){
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        //今日成交额
        $amountToday = SupplierBilling::whereSupplierId($supplier->id)->whereInOut(1)->whereBetween('created_at',[date('Y-m-d',time()),date('Y-m-d',time()+24*3600)])->sum('amount');
        //今日成交单数
        $orderCount = SupplierBilling::whereSupplierId($supplier->id)->whereInOut(1)->whereBetween('created_at',[date('Y-m-d',time()),date('Y-m-d',time()+24*3600)])->count();
        //累计营业额
        $amount = SupplierBilling::whereSupplierId($supplier->id)->whereInOut(1)->sum('amount');
        //累计营业额来源ID
        $billingSourceIds = SupplierBilling::whereSupplierId($supplier->id)->whereInOut(SupplierBilling::income)->whereState(1)->lists('id')->toArray();
        $billingSourceIds = base64_encode(implode(',',$billingSourceIds));
        //待入账金额
        $unAmount = SupplierBilling::whereSupplierId($supplier->id)->whereInOut(1)->whereState(0)->sum('amount');
        $data = [
            'amountToday' => $amountToday,
            'orderCount' =>$orderCount,
            'amount' => $amount,
            'unAmount' => $unAmount
        ];
        $banks = ConfBank::orderBy('display_order')->get();
        return view('store.fund.index')->with(['supplier'=>$supplier,'data'=>$data,'banks'=>$banks,'billingSourceIds'=>$billingSourceIds]);
    }

    /* 交易记录详情 */
    function show($id){
        //获取订单
        $orderBase = OrderBase::whereSupplierId($this->user['id']);
        $data = OrderBase::getOrder($orderBase,$id);
        //获取订单日志操作时间
            $orderLog = OrderLog::whereOrderNo($data->order_no)->get();
        return view('store.fund.show')->with(['order'=>$data,'logs'=>$orderLog]);
    }

    /* 提现记录 */
    function withdraw(){
        $withdraws = SupplierBilling::whereSupplierId($this->user['id'])->whereInOut(SupplierBilling::outcome)->orderBy('id','desc')->paginate($this->page);
        foreach ($withdraws as $withdraw){
            $withdraw->info = json_decode($withdraw->remark);
        }
        $user = SupplierBase::whereId($this->user['id'])->first();
        $banks = ConfBank::orderBy('display_order')->get();
        return view('store.fund.withdraw')->with(['withdraws'=>$withdraws,'user'=>$user,'banks'=>$banks]);
    }

    /* GET提现申请 */
    function apply(Request $request){
        $billingSourceIds = $request->input('billingSourceIds');
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        if (!$supplier->withdraw_card_number || !$supplier->withdraw_bank){
            echo '<script>alert("请先添加提现账户");window.history.back(-1)</script>';
        }
        return view('store.fund.apply')->with(['user'=>$supplier,'billingSourceIds'=>$billingSourceIds]);
    }

    /* POST提现申请 */
    function postApply(Request $request){
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        if (!$supplier->withdraw_card_number || !$supplier->withdraw_bank){
            return $this->getReturnResult('no','请先添加提现账户');
        }

        $ids = base64_decode($request->input('billingSourceIds'));
        $ids = explode(',',$ids);
        $mobile = $supplier->mobile;
        $mobile_code = \Input::get('mobile_code',0);
        $amount = \Input::get('amount');
        if (!is_numeric($amount)){
            return $this->getReturnResult('no','请输入正确的提现金额');
        }
        if ($amount <= 0){
            return $this->getReturnResult('no','提现金额错误');
        }
        if ($amount > $supplier->amount){
            return $this->getReturnResult('no','超出可提现余额');
        }
        $flag = $this->checkSupplierCode($mobile,\Input::get('type'),$mobile_code);
        if (!$flag){
            return $this->getReturnResult('no','手机验证码错误');
        }
        //修改短信验证码状态
        SupplierSm::whereCode($mobile_code)->whereMobile($supplier->mobile)->whereType(\Input::get('type'))->update(['is_valid'=>-1]);
        $data = [
            'uid' =>$supplier->id,
            'withdraw_name' => $supplier->withdraw_name,
            'withdraw_bank' => $supplier->withdraw_bank,
            'withdraw_sub_bank' => $supplier->withdraw_sub_bank,
            'withdraw_card_number' => $supplier->withdraw_card_number,
            'amount' => \Input::get('amount'),
            'balance' => $supplier->amount - $amount
        ];
        //SupplierWithdraw::create($data);
        //修改账户冻结金额 supplier_base
        SupplierBase::whereId($supplier->id)->update(['amount'=>$supplier->amount - $amount,'freeze_amount'=> $amount+$supplier->freeze_amount]);
        //记账 supplier_billing
        $supplierBillingInfo = SupplierBilling::create([
            'supplier_id' => $supplier->id,
            'in_out' => SupplierBilling::outcome,
            'amount' => $amount,
            'balance' => $supplier->amount,
            'content' => '账户提现',
            'remark' => urldecode(json_encode([
                'withdraw_name' => $supplier->withdraw_name,
                'withdraw_bank' => $supplier->withdraw_bank,
                'withdraw_sub_bank' => $supplier->withdraw_sub_bank,
                'withdraw_card_number' => $supplier->withdraw_card_number,
            ],JSON_UNESCAPED_UNICODE)),
            'state' => SupplierBilling::state_withdraw_wait_buyer_audit
        ]);
        SupplierBilling::whereIn('id',$ids)->where('withdraw_id',0)->whereInOut(SupplierBilling::income)->whereState(self::state_fund)->update(['withdraw_id'=>$supplierBillingInfo->id]);
        return $this->getReturnResult('yes','');
    }

    /* 添加提现信息 */
    function withdrawStore(){
        $data = \Input::all();
        $ret = $this->withdrawStoreCheck($data);
        if($ret['ret'] == 'no'){
            return $this->getReturnResult('no',$ret['msg']->errors()->first());
        }else{
            $user = SupplierBase::whereId($this->user['id'])->first();
            $flag = $this->checkSupplierCode($user->mobile,$data['type'],$data['mobile_code']);
            if ($flag){
                //修改短信验证码状态
                SupplierSm::whereCode($data['mobile_code'])->whereMobile($user->mobile)->whereType($data['type'])->update(['is_valid'=>-1]);
                //更新用户提现信息
                unset($data['type']);
                unset($data['mobile_code']);
                SupplierBase::whereId($this->user['id'])->update($data);
                return $this->getReturnResult('yes','提现账户修改成功');
            }else{
                return $this->getReturnResult('no','短信验证码错误');
            }

        }

    }


    /* GET提现账户 */
    function supplierWithdraw(){
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        return view('store.supplier.withdraw')->with('supplier',$supplier);
    }

    /* 添加提现信息 验证 */
    function withdrawStoreCheck($data){
        //验证表单
        $validator=Validator::make(
            array(
                'withdraw_card_number'  =>  $data['withdraw_card_number'],
                'withdraw_bank'         =>  $data['withdraw_bank'],
                'withdraw_sub_bank'     =>  $data['withdraw_sub_bank'],
                'mobile_code'           =>  $data['mobile_code'],

            ),
            array(
                'withdraw_card_number'  =>  'required',
                'withdraw_bank'         =>  'required',
                'withdraw_sub_bank'     =>  'required',
                'mobile_code'           =>  'required',
            ),
            array(
                'withdraw_card_number.required'  =>  '请输入银行卡号',
                'withdraw_bank.required'         =>  '请选择银行',
                'withdraw_sub_bank.required'     =>  '请输入支行',
                'mobile_code.required'           =>  '请输入手机验证码',
            )
        );
        if($validator->fails()){
            return $this->getReturnResult('no',$validator);
        }else{
            return $this->getReturnResult('yes','');
        }
    }

    /* 获取交易记录提交信息 */
    function recordBuild($inOut){
        $option = \Input::all();
        $billings = SupplierBilling::whereInOut($inOut);
        extract($option);
        if (isset($trade_no) && $trade_no){
            $billings->whereTradeNo($trade_no);
        }
        if(isset($order_no)  && $order_no){
            $billings->whereOrderNo($order_no);
        }
        if(isset($pay_type) && $pay_type > 0){
            $orderNos = OrderBase::whereSupplierId($this->user['id'])->wherePayType($pay_type)->lists('order_no');
            $billingNos = SupplierBilling::whereIn('order_no',$orderNos)->lists('order_no');
            $billings->whereIn('order_no',$billingNos);
        }
        if (isset($start_time)){
            if(isset($end_time) && $start_time){
                if ($start_time && $end_time > $start_time){
                    $billings->whereBetween('created_at',[$start_time,$end_time]);
                }else{
                    $billings->where('created_at','>=',$start_time);
                }
            }
        }else{
            if (isset($end_time) && $end_time){
                $billings->where('created_at','<=',$end_time);
            }
        }
        return $billings;
    }

    /* 交易记录导出 */
    function export($inOut){
        if(intval($inOut) == 1){
            $billings = $this->recordBuild(self::inMoney);
            $billings = $billings->whereSupplierId($this->user['id'])->orderBy('id','desc')->get();
            if(!$billings->isEmpty()){
                $field = ['交易编号','订单编号','商品名称','产品规格','商品数量','实收金额','收款时间','订单状态',
                    '收货人姓名','收货地址-省市区','收货地址-街道地址','收货人电话','物流公司','物流单号'
                ];
                $data = [];

                foreach ($billings as $billing){
                    $billing->order = OrderBase::whereOrderNo($billing->order_no)->first();
                    $goodsInfos = OrderGood::whereOrderNo($billing->order_no)->get();
                    $receiverInfo = json_decode($billing->order->receiver_info,true);
                    $billing->info = json_decode($billing->remark);
                    $orderState = OrderBase::getStateCN($billing->order->state);
                    foreach($goodsInfos as $value){
                        $goodsTitles = $value->goods_title;
                        if($value->is_gift == 1){
                            $goodsTitles = $value->goods_title."(赠品)";
                        }
                        $data[] = [
                            $billing->trade_no,$billing->order_no,$goodsTitles,$value->spec_name,$value->num,$billing->amount,$billing->created_at,$orderState,
                            $billing->order->receiver_name,$receiverInfo['province'].$receiverInfo['city'].$receiverInfo['district'],$receiverInfo['address'],
                            $billing->order->receiver_mobile,$billing->order->express_name,$billing->order->express_no
                        ];
                    }
                }

            }
        }

        if(intval($inOut) == 2){
            $returnOrders =OrderReturn::whereSupplierId($this->user['id'])->whereState(OrderReturn::STATE_SUCCESS)->orderBy('id','desc')->get();
            if(!$returnOrders->isEmpty()){
               // $field = ['退款单号','订单编号','商品名称','退款金额','退款时间'];
                $field = ['交易编号','订单编号','商品名称','产品规格','商品数量','实收金额','退款金额','退款时间','状态',
                    '收货人姓名','收货地址-省市区','收货地址-街道地址','收货人电话','物流公司','物流单号'
                ];
                $data = [];
                foreach ($returnOrders as $value){
                    $value->order = OrderBase::whereOrderNo($value->order_no)->first();
                    $goodsInfos = OrderGood::whereOrderNo($value->order_no)->get();
                    $receiverInfo = json_decode($value->order->receiver_info,true);
                    $orderState = OrderReturn::getStateCN($value->state);
                    foreach($goodsInfos as $v){
                        $goodsTitles = $v->goods_title;
                        if($v->is_gift == 1){
                            $goodsTitles = $v->goods_title."(赠品)";
                        }
                        $data[] = [
                            $value->return_no,$value->order_no,$goodsTitles,$v->spec_name,$v->num,$value->order->amount_real,$value->amount,
                            $value->created_at,$orderState,$value->receiver_name,$receiverInfo['province'].$receiverInfo['city'].$receiverInfo['district'],
                            $receiverInfo['address'],$value->receiver_mobile,$value->order->express_name,$value->order->express_no
                        ];
                    }
                }
            }
        }
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data,$field,$inOut){
                $excel->sheet('order', function($sheet) use ($data,$field,$inOut){
                    $sheet->setColumnFormat(array(
                        'B' => '0',
                        'C' => '0',
                        'N' => '0',
                    ));
                    $sheet->appendRow(1, $field);
                    $sheet->setWidth(array(
                        'A'=>30,
                        'B'=>30,
                        'C'=>30,
                        'D'=>30,
                        'E'=>30,
                        'F'=>30,
                        'G'=>20,
                        'H'=>20,
                        'I'=>20,
                        'J'=>20,
                        'K'=>30,
                        'L'=>30,
                        'M'=>20,
                        'N'=>20,
                    ));
                    $sheet->rows($data);
                    //合并单元格
                    $arrayCount = count($data);
                    if($inOut == 1){
                        $rangeArr = ['A','B','F','G','H','I','J','K','L','M','N'];
                        $position = 1;
                    }
                    if($inOut == 2){
                        $rangeArr = ['A','B','F','G','H','I','J','K','L','M','N'];
                        $position = 1;
                    }
                    foreach($data as $key=>$value){
                        $nextKey = $key+1;
                        $start = $key+2;
                        $end = $nextKey+2;
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
    }

}
