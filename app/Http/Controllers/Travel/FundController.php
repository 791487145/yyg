<?php

namespace App\Http\Controllers\Travel;

use App\Models\GoodsBase;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderReturn;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\TaSm;
use App\Models\TaWithdraw;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Travel\TravelController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use PetstoreIO\Order;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\ConfBank;
class FundController extends TravelController
{
    private $page = 20;
    function myincome(){
        $user = TaBase::whereId($this->user['id'])->first();
        //今日销售额
        $currentDay = date('Y-m-d').' 00:00:00';
        $orderAmount = OrderBase::whereTaId($this->user['id'])->where('created_at','>',$currentDay)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
        $orderExpessToday = OrderBase::whereTaId($this->user['id'])->where('created_at','>',$currentDay)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
        $orderAmount = $orderAmount + $orderExpessToday;
        //累计销售额
        $totalSales = OrderBase::whereTaId($user->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
        $totalExpressAmount = OrderBase::whereTaId($user->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
        $totalSales = $totalSales + $totalExpressAmount;
        
        //已入账收益来源
        $IncomeAmountSource = TaBilling::whereTaId($this->user['id'])->whereInOut(TaBilling::in_income)->whereState(TaBilling::state_fund)->lists('id')->toArray();
        //dd($IncomeAmountSource);
        $IncomeAmountSource=base64_encode(implode(',',$IncomeAmountSource));

        //待入账收益
        $billingAmount = TaBilling::whereTaId($this->user['id'])->whereInOut(TaBilling::in_income)->whereState(TaBilling::state_nofund)->sum('amount');
        $returnAmount = TaBilling::whereTaId($this->user['id'])->whereInOut(TaBilling::in_income)->whereState(TaBilling::state_nofund)->sum('return_amount');
        $billing = doubleval($billingAmount)-doubleval($returnAmount);
        $banks = ConfBank::orderBy('display_order')->get();//
        return view('travel.fund.myincome')->with(['user'=>$user,'amount'=>$orderAmount,'totalSales'=>$totalSales,'billing'=>$billing,'banks'=>$banks,'IncomeAmountSource'=>$IncomeAmountSource]);
    }
    /* 收益明细 */
    function incomes($state = 0){//1 已入账 0 待入账
        $incomes = $this->incomeBuild($state);
        Session::put('incomesstate',$state);
        //$incomes = $incomes->orderBy('id','desc')->paginate($this->page);
        $incomes = $incomes->paginate($this->page);
        //dd($incomes);
        foreach($incomes as $income){
            //取出下单的时间支付成功后当前的订单写入到tabilling里面，时间相差不多
            //$income->orderBase = OrderBase::whereOrderNo($income->order_no)->first();
            //从order_goods取出商品的名称和当前订单的购买数量
            $income->orderGood = OrderGood::whereOrderNo($income->order_no)->first();
        }
        //dd($incomes);
        $keywords = Input::all();
        return view('travel.fund.incomes')->with(['incomes'=>$incomes,'keywords'=>$keywords,'state'=>$state]);
    }
    /* 查看收益详情 */
    function income($order_no){
        //获取订单
        $order = OrderBase::whereOrderNo($order_no)->first();
        $orderBase = new OrderBase();
        $data = OrderBase::getOrder($orderBase,$order->id);
        //获取订单日志操作时间
        $orderLog = OrderLog::whereOrderNo($data->order_no)->get();
        return view('travel.fund.income')->with(['order'=>$data,'logs'=>$orderLog]);
    }
    /* 提现记录 */
    function withdraws(){
        $withdraws = TaBilling::whereTaId($this->user['id'])->whereInOut(TaBilling::in_out)->orderBy('id','desc')->paginate($this->page);
        foreach ($withdraws as $withdraw){
            $withdraw->info = json_decode($withdraw->remark);
        }
        $user = TaBase::whereId($this->user['id'])->first();
        $banks = ConfBank::orderBy('display_order')->get();
        return view('travel.fund.withdraws')->with(['withdraws'=>$withdraws,'user'=>$user,'banks'=>$banks]);
    }
    
    function apply(Request $request){
        $ids = $request->input('IncomeAmountSource');
        $user = TaBase::whereId($this->user['id'])->first();
        if ($user->state!= 1){
            return Redirect::to('system/authenticate');
        }
        return view('travel.fund.apply')->with(['user'=>$user,'ids'=>$ids]);
    }

    /**
     * 导出收益明细 
     * @param number $state 1 已入账      0 待入账
     */
    function export($state = 1){
        $state   = Session::get('incomesstate');
        $incomes = $this->incomeBuild($state);
        $incomes = $incomes->get();
        //从order_goods表里面关联出商品的名称和数量
        foreach($incomes as $val){
            $val->orderGoods = OrderGood::whereOrderNo($val->order_no)->first();
        }
        //dd($incomes);
        if($state == 0){
            if(!$incomes->isEmpty()){
                $field = ['序号','交易编号','商品名称','数量','退款','旅行社返利收入','入账时间'];
                $data[] = $field;
                $i = 1;
                foreach ($incomes as $order){
                    $data[] = [
                        $i,$order->trade_no,$order->orderGoods->goods_title,$order->orderGoods->num,$order->return_amount,$order->amount,$order->created_at];
                    $i++;
                }
            }
            if(!empty($data)){
                Excel::create(date('YmdHis'),function($excel) use ($data){
                    $excel->sheet('order', function($sheet) use ($data){
                        $sheet->setWidth(array(
                            'A' => 5,
                            'B' => 40,
                            'C' => 30,
                            'D' => 10,
                            'E' => 10,
                            'F' => 20,
                            'G' => 30,
                        ));
                        $sheet->rows($data);
                    });
                })->export('xlsx');
            }
        }else{
            if(!$incomes->isEmpty()){
                $field = ['序号','交易编号','商品名称','数量','旅行社返利收入','入账时间'];
                $data[] = $field;
                $i = 1;
                foreach ($incomes as $order){
                    $data[] = [
                        $i,$order->trade_no,$order->orderGoods->goods_title,$order->orderGoods->num,$order->amount-$order->return_amount,$order->created_at];
                    $i++;
                }
            }
            if(!empty($data)){
                Excel::create(date('YmdHis'),function($excel) use ($data){
                    $excel->sheet('order', function($sheet) use ($data){
                        $sheet->setWidth(array(
                            'A' => 5,
                            'B' => 40,
                            'C' => 30,
                            'D' => 10,
                            'E' => 20,
                            'F' => 30,
                        ));
                        $sheet->rows($data);
                    });
                })->export('xlsx');
            }
        }
        
        
        
        
        
        
        
        
        exit;
    }

    /* 添加提现信息 */
    function withdrawStore(){
        $data = \Input::all();
        $ret = $this->withdrawStoreCheck($data);
        if($ret['ret'] == 'no'){
            return $this->getReturnResult('no',$ret['msg']->errors()->first());
        }else{
            $user = TaBase::whereId($this->user['id'])->first();
            $flag = $this->checkTravelCode($user->mobile,$data['type'],$data['mobile_code']);
            if ($flag){
                //修改短信验证码状态
                TaSm::whereCode($data['mobile_code'])->whereMobile($user->mobile)->whereType($data['type'])->update(['is_valid'=>-1]);
                //更新用户提现信息
                unset($data['type']);
                unset($data['mobile_code']);
                TaBase::whereId($this->user['id'])->update($data);
                return $this->getReturnResult('yes','提现账户添加成功');
            }else{
                return $this->getReturnResult('no','短信验证码错误');
            }

        }

    }

    /* POST提现申请 */
    function postApply(Request $request){

        $user = TaBase::whereId($this->user['id'])->first();
        $mobile = $user->mobile;
        $mobile_code = \Input::get('mobile_code',0);
        $amount = \Input::get('amount');

        $ids = base64_decode($request->input('ids'));
        $ids = explode(',',$ids);
        if (!is_numeric($amount)){
            return $this->getReturnResult('no','请输入正确的提现金额');
        }
        if ($amount <= 0){
            return $this->getReturnResult('no','提现金额错误');
        }
        if ($amount > $user->amount){
            return $this->getReturnResult('no','超出可提现余额');
        }
        $flag = $this->checkTravelCode($mobile,\Input::get('type'),$mobile_code);
        if (!$flag){
            return $this->getReturnResult('no','手机验证码错误');
        }
        //修改短信验证码状态
        TaSm::whereCode($mobile_code)->whereMobile($user->mobile)->whereType(\Input::get('type'))->update(['is_valid'=>-1]);
        $data = [
            'uid' =>$user->id,
            'withdraw_name' => $user->withdraw_name,
            'withdraw_bank' => $user->withdraw_bank,
            'withdraw_sub_bank' => $user->withdraw_sub_bank,
            'withdraw_card_number' => $user->withdraw_card_number,
            'amount' => \Input::get('amount'),
            'balance' => $user->amount - $amount
        ];
        //TaWithdraw::create($data);
        //修改账户冻结金额 supplier_base
        TaBase::whereId($user->id)->update(['amount'=>$user->amount - $amount,'freeze_amount'=> $amount+$user->freeze_amount]);
        //记账 supplier_billing
        $taInfo = TaBilling::create([
            'supplier_id' => $user->id,
            'in_out' => 2,
            'amount' => $amount,
            'balance' => $user->amount,
            'content' => '账户提现',
            'ta_id'=> $user->id,
            'remark' => urldecode(json_encode([
                'withdraw_name' => $user->withdraw_name,
                'withdraw_bank' => $user->withdraw_bank,
                'withdraw_sub_bank' => $user->withdraw_sub_bank,
                'withdraw_card_number' => $user->withdraw_card_number,
            ],JSON_UNESCAPED_UNICODE)),
            'state' => TaBase::state_withdraw_wait_audit,
        ]);

        TaBilling::whereIn('id',$ids)->where('withdraw_id',0)->update(['withdraw_id'=>$taInfo->id]);
        return $this->getReturnResult('yes','');
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

    function incomeBuild($state){
        $incomes = TaBilling::whereTaId($this->user['id'])->whereInOut(1)->whereState($state);
        $trade_no = Input::get('trade_no',null);
        $goods_name = Input::get('goods_name',null);
        $start_time = Input::get('start_time',null);
        $end_time = Input::get('end_time',null);
        if ($trade_no){
            $incomes = $incomes->whereTradeNo($trade_no);
        }
        if ($start_time){
            if ($start_time && $end_time > $start_time){
                $incomes->whereBetween('created_at',[$start_time,$end_time]);
            }else{
                $incomes->where('created_at','>=',$start_time);
            }
        }else{
            if ($end_time){
                $incomes->where('end_time','<=',$end_time);
            }
        }
        if($state == 0){
            $incomes->orderBy('created_at','desc');
        }elseif($state == 1){
            $incomes->orderBy('id','desc');
        }
        return $incomes;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
