<?php

namespace App\Http\Controllers\Admin;

use App\Models\GuideBase;
use App\Models\SupplierBase;
use App\Models\TaBase;
use App\Models\UserBase;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\GoodsBase;
use App\Models\TaBilling;
use Illuminate\Http\Request;
use App\Models\ConfPavilion;
use App\Models\GuideBilling;
use App\Models\PlatformBilling;
use App\Models\SupplierBilling;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;


class FundController extends BaseController
{
    const supplierAction = 1;
    const guideAction = 2;
    const taAction = 3;
    const platformAction = 4;
    const state_fund = 1;
    const not_withdraw = 0;
    //交易额
    protected function BuyFundList(Request $request)
    {
        $today = date("Y-m-d");
        $yestorday = date("Y-m-d",strtotime('-1 day'));
        $dateStart = $request->input('created_at_min',$yestorday);
        $dateEnd = $request->input('created_at_max',$today);
        $orderBase = OrderBase::whereBetween('created_at',[$dateStart,$dateEnd])->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED]);

        //搜索日期对应成交额，成交单数
        $search_amount_goods = $orderBase->sum('amount_goods');
        $search_amount_express = $orderBase->sum('amount_express');
        $search_amount_coupon = $orderBase->sum('amount_coupon');
        //成交額
        $search_amount = self::amount($search_amount_goods,$search_amount_express,$search_amount_coupon);
        //成交单数
        $search_order_count = $orderBase->count();

        //累计营业额
        $amount_goods = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
        $amount_express = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
        $amount_coupon = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_coupon');
        $amount = self::amount($amount_goods,$amount_express,$amount_coupon);


        $array = [1,2,3,4,5,8];
        foreach($array as $v){
            if($v > 4){
                if($v != 8){
                    $condition = OrderBase::whereBetween('created_at',[$dateStart,$dateEnd])->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->where('supplier_id','>','4')->where('supplier_id','!=',8);
                    $pavilionId = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->where('supplier_id','>','4')->where('supplier_id','!=',8);
                }
                else{
                    $condition = OrderBase::whereBetween('created_at',[$dateStart,$dateEnd])->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereSupplierId($v);
                    $pavilionId = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereSupplierId($v);
                }
            }else{
                $condition = OrderBase::whereBetween('created_at',[$dateStart,$dateEnd])->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereSupplierId($v);
                $pavilionId = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereSupplierId($v);
            }
            //馆_累计营业额
            $pavilion_amount_goods =$pavilionId->sum('amount_goods');
            $pavilion_amount_express = $pavilionId->sum('amount_express');
            $pavilion_amount_coupon = $pavilionId->sum('amount_coupon');
            $pavilion_amount[] = self::amount($pavilion_amount_goods,$pavilion_amount_express,$pavilion_amount_coupon);

            //馆
            $pavilion_yestorday_amount_goods = $condition->sum('amount_goods');
            $pavilion_yestorday_amount_express = $condition->sum('amount_express');
            $pavilion_yestorday_amount_coupon = $condition->sum('amount_coupon');
            //馆_昨日成交額
            $pavilion_yestorday_amount[] = self::amount($pavilion_yestorday_amount_goods,$pavilion_yestorday_amount_express,$pavilion_yestorday_amount_coupon);
            //馆_昨日成交单数
            $pavilion_yestorday_order_count[] = $condition->count();
        }

        return view("boss.fund.buyfund_list",compact('search_order_count','search_amount','amount','pavilion_yestorday_amount','pavilion_yestorday_order_count','pavilion_amount','today','yestorday','dateStart','dateEnd'));
    }


    //交易记录
    protected  function TransactionRecord($action = self::platformAction){
        if($action == self::platformAction){
            $tables = new PlatformBilling();
        }
        if($action == self::supplierAction){
            $tables = new SupplierBilling();
        }
        if($action == self::guideAction){
            $tables = new GuideBilling();
        }
        if($action == self::taAction){
            $tables = new TaBilling();
        }
        $trade_no = Input::get('trade_no');
        $pay_type = Input::get('pay_type');
        $order_no = Input::get('order_no');
        $contentType = Input::get('contentType');
        $ta_id = Input::get('ta_id');
        $supplier_id = Input::get('supplier_id');
        $guide_name = Input::get('guide_name');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        if(!empty($trade_no)){
            $tables=$tables->where('trade_no',$trade_no);
        }
        if(!empty($order_no)){
            $tables=$tables->whereOrderNo($order_no);
        }
        if(isset($contentType)){
            if($contentType != 'value'){
                if($contentType == 10){
                    $tables=$tables->whereState(self::state_fund)->whereWithdrawId(self::not_withdraw);
                }elseif($contentType == 11){
                    $tables=$tables->whereState(self::state_fund)->where('withdraw_id','!=',self::not_withdraw);
                }else{
                    $tables=$tables->whereState($contentType);
                }
            }
        }


        if ($pay_type) {
            $order_nos = OrderBase::wherePayType($pay_type)->lists('order_no')->toArray();
                $tables = $tables->whereIn('order_no', $order_nos);
        }


        if($ta_id){
            $tables=$tables->whereTaId($ta_id);
        }
        if($supplier_id){
            $tables=$tables->whereSupplierId($supplier_id);
        }
        if($guide_name){
            $guideIds = GuideBase::where('real_name','like','%'.$guide_name.'%')->lists('id')->toArray();
            $tables = $tables->whereIn('guide_id',$guideIds);
        }
        if(!empty($start_time)){
            $tables = $tables->whereBetween('created_at',[$start_time,$end_time]);
        }

        $tables = $tables->whereIn('state',[0,1])->orderBy('id','desc')->paginate($this->page);
        $currentPage = $tables->currentPage();
        foreach($tables as $table){
            $orderInfo = OrderBase::whereOrderNo($table->order_no)->first();
            switch ($table->state){
                case 0:
                    $table->state = '待入帐';
                    break;
                case 1:
                    $table->withdraw_id == 0 ? $table->state = '已入账未提现' : $table->state ='已提现';
                    break;
            }
            if($orderInfo){
                $table->pay_type = $orderInfo->pay_type;
            }
        }
        $suppliers = SupplierBase::whereState(SupplierBase::STATE_VALID)->get();
        $travels = TaBase::get();
        return view("boss.fund.transactionrecord",compact('tables','action','contentType','trade_no','order_no','start_time','end_time','currentPage','suppliers','travels','ta_id','supplier_id','guide_name','pay_type','pay_type'));
    }

    //查询交易记录
    private function selectOrderRecord($action = self::platformAction,$state,$formData,$type)
    {
        if($action == self::platformAction){
            $tables = new PlatformBilling();
        }
        if($action == self::supplierAction){
            $tables = new SupplierBilling();
        }
        if($action == self::guideAction){
            $tables = new GuideBilling();
        }
        if($action == self::taAction){
            $tables = new TaBilling();
        }
        if($state == 11){
            $tables = $tables->whereState($state);
        }
        if($state == 12){
            $tables = $tables->whereState($state);
        }
        if($state == 13){
            $tables = $tables->whereState($state);
        }
        if($state == 14){
            $tables = $tables->whereState($state);
        }
        if($type == 'record'){
            if($formData['order_no']){
                $tables = $tables->whereOrderNo($formData['order_no']);
            }
            if($formData['trade_no']){
                $tables = $tables->whereTradeNo($formData['trade_no']);
            }

            if($formData['pay_type']){
                $order_nos = OrderBase::wherePayType($formData['pay_type'])->lists('order_no')->toArray();
                $tables = $tables->whereIn('order_no', $order_nos);
            }


            if(isset($formData['contentType'])){
                if($formData['contentType'] != 'value'){
                    $contentType = $formData['contentType'];
                    if($contentType == 10){
                        $tables=$tables->whereState(self::state_fund)->whereWithdrawId(self::not_withdraw);
                    }elseif($contentType == 11){
                        $tables=$tables->whereState(self::state_fund)->where('withdraw_id','!=',self::not_withdraw);
                    }elseif($contentType == 1){
                        $tables=$tables->whereState($contentType);
                    }elseif($contentType === "0"){
                        $tables=$tables->whereState($contentType);
                    }
                }

            }
            if($formData['ta_id']){
                $tables = $tables->whereTaId($formData['ta_id']);
            }
            if($formData['supplier_id']){
                $tables = $tables->whereSupplierId($formData['supplier_id']);
            }
            if($formData['guide_name']){
                $guideIds = GuideBase::where('real_name','like','%'.$formData['guide_name'].'%')->lists('id')->toArray();
                $tables = $tables->whereIn('guide_id',$guideIds);
            }
            if($formData['start_time'] && $formData['end_time'] > $formData['start_time']){
                $tables = $tables->whereBetween('created_at',[$formData['start_time'],$formData['end_time']]);
            }
            $tables = $tables->whereIn('state',[0,1])->orderBy('id','desc')->get();
        }
        if($type == 'withdraw'){
            if($formData['withdraw_name']){
                $tables = $tables->where('remark','like','%'.$formData['withdraw_name'].'%');
            }
            if($formData['start_time'] && $formData['end_time'] > $formData['start_time']){
                $tables = $tables->whereBetween('created_at',[$formData['start_time'],$formData['end_time']]);
            }
            $tables = $tables->whereState($state)->orderBy('id','desc')->get();
        }


        $contentTypes=[];
        foreach($tables as $table){
            $orderInfo = OrderBase::whereOrderNo($table->order_no)->first();
            /*if($orderInfo->guide_id != 0){
                $guide_name = GuideBase::whereId($orderInfo->guide_id)->pluck('real_name');
                $table->guide_name = empty($guide_name)?'GID.'.$orderInfo->guide_id : $guide_name;
            }else{
                $table->guide_name = 'GID.'.$orderInfo->guide_id;
            }*/
            $userInfo = UserBase::whereId($table->uid)->first();
            switch ($table->state){
                case 0:
                    $table->state = '待入帐';
                    break;
                case 1:
                    $table->withdraw_id == 0 ? $table->state = '已入账未提现' : $table->state = '已提现';
                    break;
            }
            if($userInfo){
                $table->nick_name = $userInfo->nick_name;
            }
            if($orderInfo){
                $table->pay_type = $orderInfo->pay_type;
            }
            $contentTypes[] = $table->content;
        }
        return $tables;
    }

    //导出交易记录
    public function exportRecord(Request $request,$action = self::platformAction,$state = 0,$type='record'){
        $formData = $request->all();
        $orderRecord = self::selectOrderRecord($action,$state,$formData,$type);
        foreach($orderRecord as $orderInfo){
            $pay_type = $orderInfo->pay_type;
            if($pay_type == OrderBase::PAY_TYPE_ALI){
                $orderInfo->pay_type = 'ping++支付宝支付';
            }else if($pay_type == OrderBase::PAY_TYPE_WX){
                $orderInfo->pay_type = 'ping++微信支付';
            }else{
                $orderInfo->pay_type = '微信商户支付';
            }
            $orderGoods = OrderGood::whereOrderNo($orderInfo->order_no)->get();
            $orderBase = OrderBase::whereOrderNo($orderInfo->order_no)->first();
            $supplier_name = SupplierBase::whereId($orderBase['supplier_id'])->pluck('name');
            $orderInfo->supplier_name = $supplier_name;
            $orderInfo->express_amount = $orderBase['amount_express'];
            $orderInfo->amount_goods = $orderBase['amount_goods'];
            $goodsInfo = [];
            foreach($orderGoods as $value){
                $goodsInfo[] =['title'=>$value->goods_title,'spec'=>$value->spec_name,'num'=>$value->num,'price'=>$value->price,'price_buying'=>$value->price_buying];
            }
            $orderInfo->goods_spec = $goodsInfo;
        }

        //平台交易记录导出
        if($action == self::platformAction){
            $field = ['订单编号','交易单号','收款类型','支付方式','平台利润','状态','所属供应商','商品名称','商品规格','数量','单价','运费','订单金额','提交时间'];
            $data[] = $field;
            $i = 1;
            foreach($orderRecord as $value){
                $value->rebat =number_format($value->amount - $value->return_amount,2);
                foreach($value->goods_spec as $v){
                   $data[$i] = [$value->order_no,$value->trade_no,$value->content,$value->pay_type,$value->rebat,$value->state,$value->supplier_name,$v['title'],$v['spec'],$v['num'],$v['price'],$value->express_amount,$value->amount_goods,$value->created_at];
                    $i++;
                }
            }

            Excel::create(date('YmdHis'), function ($excel) use ($data) {
                $excel->sheet('record', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                        'B' =>'0'
                    ));
                    $sheet->setWidth(array(
                        'A'=>'20',
                        'B'=>'30',
                        'C'=>'20',
                        'D'=>'20',
                        'E'=>'20',
                        'F'=>'20',
                        'G'=>'20',
                        'H'=>'50',
                        'I'=>'10',
                        'J'=>'10',
                        'K'=>'10',
                        'L'=>'10',
                        'M'=>'10',
                        'N'=>'30',
                    ));
                    $sheet->rows($data);
                    $arrayCount = count($data);
                    $position = 0;
                    $rangeArr = ['A','B','C','D','E','F','L','M','N'];
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

        //供应商交易记录导出
        if($action == self::supplierAction){
            $field = ['订单编号','交易单号','收款类型','支付方式','所属供应商','商品名称','商品规格','数量','单价','供应价单价','运费','订单金额','订单供应金额','状态','提交时间'];
            $data[] = $field;
            $i = 1;
            foreach($orderRecord as $value){
                $value->goodsBuyPrice = number_format($value->amount - $value->return_amount,2);
                foreach($value->goods_spec as $v){
                    $data[$i] = [$value->order_no,$value->trade_no,'供应价',$value->pay_type,$value->supplier_name,$v['title'],$v['spec'],$v['num'],$v['price'],$v['price_buying'],$value->express_amount,$value->amount_goods,$value->goodsBuyPrice,$value->state,$value->created_at];
                    $i++;
                }
            }
            Excel::create(date('YmdHis'), function ($excel) use ($data) {
                $excel->sheet('record', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                        'B' =>'0'
                    ));
                    $sheet->setWidth(array(
                        'A'=>'20',
                        'B'=>'30',
                        'C'=>'20',
                        'D'=>'20',
                        'E'=>'20',
                        'F'=>'50',
                        'G'=>'20',
                        'H'=>'20',
                        'I'=>'10',
                        'J'=>'10',
                        'K'=>'20',
                        'L'=>'10',
                        'M'=>'20',
                        'N'=>'20',
                        'O'=>'20'
                    ));
                    $sheet->rows($data);
                    $arrayCount = count($data);
                    $position = 0;
                    $rangeArr = ['A','B','C','D','K','L','M','N'];
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

        //旅行社交易记录导出
        if($action == self::taAction){
            $field = ['订单编号','交易单号','收款类型','支付方式','旅行社获得返利','状态','所属旅行社','商品名称','商品规格','数量','运费','订单金额','提交时间'];
            $data[] = $field;
            $i = 1;
            foreach($orderRecord as $value){
                $value->goodsBuyPrice = number_format($value->amount - $value->return_amount,2);
                if($value->ta_id != 0){
                    $value->ta_name = TaBase::whereId($value->ta_id)->pluck('ta_name');
                }else{
                    $value->ta_name = 'TID.'.$value->ta_id;
                }
                foreach($value->goods_spec as $v){
                    $data[$i] = [$value->order_no,$value->trade_no,$value->content,$value->pay_type,$value->goodsBuyPrice,$value->state,$value->ta_name,$v['title'],$v['spec'],$v['num'],$value->express_amount,$value->amount_goods,$value->created_at];
                    $i++;
                }
            }
            Excel::create(date('YmdHis'), function ($excel) use ($data) {
                $excel->sheet('record', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                        'B' =>'0'
                    ));
                    $sheet->setWidth(array(
                        'A'=>'20',
                        'B'=>'30',
                        'C'=>'20',
                        'D'=>'20',
                        'E'=>'20',
                        'F'=>'20',
                        'G'=>'20',
                        'H'=>'50',
                        'I'=>'10',
                        'J'=>'10',
                        'K'=>'20',
                        'L'=>'10',
                        'M'=>'20',
                    ));

                    $sheet->rows($data);
                    $arrayCount = count($data);
                    $position = 0;
                    $rangeArr = ['A','B','C','D','E','F','G','K','L','M'];
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

        if($action == self::guideAction){
            $field = ['订单编号','交易单号','收款类型','支付方式','导游获得返利','状态','所属导游','商品名称','商品规格','数量','运费','订单金额','提交时间'];
            $data[] = $field;
            $i = 1;
            foreach($orderRecord as $value){
                $value->goodsBuyPrice = number_format($value->amount - $value->return_amount,2);
                if($value->guide_id != 0){
                    $value->guide_name = GuideBase::whereId($value->guide_id)->pluck('real_name');
                }else{
                    $value->guide_name = 'GID.'.$value->guide_id;
                }
                foreach($value->goods_spec as $v){
                    $data[$i] = [$value->order_no,$value->trade_no,$value->content,$value->pay_type,$value->goodsBuyPrice,$value->state,$value->guide_name,$v['title'],$v['spec'],$v['num'],$value->express_amount,$value->amount_goods,$value->created_at];
                    $i++;
                }
            }
            Excel::create(date('YmdHis'), function ($excel) use ($data) {
                $excel->sheet('record', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                        'B' =>'0'
                    ));
                    $sheet->setWidth(array(
                        'A'=>'20',
                        'B'=>'30',
                        'C'=>'20',
                        'D'=>'20',
                        'E'=>'20',
                        'F'=>'20',
                        'G'=>'20',
                        'H'=>'50',
                        'I'=>'10',
                        'J'=>'10',
                        'K'=>'20',
                        'L'=>'10',
                        'M'=>'20',
                    ));

                    $sheet->rows($data);
                    $arrayCount = count($data);
                    $position = 0;
                    $rangeArr = ['A','B','C','D','E','F','G','K','L','M'];
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

    }
    //导出提现记录
    public function export(Request $request,$action = self::platformAction,$state = 0,$type='record'){
        $formData = $request->all();
        $orderRecord = self::selectOrderRecord($action,$state,$formData,$type);
        $orderRecord = $orderRecord->toArray();
        $items = $orderRecord;
        $i = 1;
  /*      if($type == 'record'){
            if($action == self::platformAction){
                $field = ['订单编号','交易单号','收款类型','支付方式','平台利润','状态','所属供应商','商品名称','商品规格','数量','运费','单价','订单金额','提交时间'];
            }
            if($action == self::taAction){
                $field = ['订单编号','交易单号','旅行社姓名','收款类型','支付方式','利润','状态','商品名称','商品规格','数量','订单金额','提交时间'];
            }
            if($action == self::supplierAction){
                $field = ['订单编号','交易单号','供应商姓名','收款类型','支付方式','利润','状态','商品名称','商品规格','数量','订单金额','提交时间'];
            }
            if($action == self::guideAction){
                $field = ['订单编号','交易单号','导游姓名','收款类型','支付方式','利润','状态','商品名称','商品规格','数量','订单金额','提交时间'];
            }

            $orderInfo[] = $field;
            foreach($items as $item){
                $orderGoods =OrderGood::whereOrderNo($item['order_no'])->get();
                foreach($orderGoods as $value){
                    $orderInfo[$i]['order_no'] = $item['order_no'];
                    $orderInfo[$i]['trade_no'] = $item['trade_no'];
                    if($action == self::taAction){
                        $travel = TaBase::whereId($item['ta_id'])->first();
                        if($travel){
                            $orderInfo[$i]['ta_id'] = $travel->ta_name;
                        }else{
                            $orderInfo[$i]['ta_id'] = 'TID.'.$item['ta_id'];
                        }
                    }
                    if($action == self::supplierAction){
                        $supplier = SupplierBase::whereId($item['supplier_id'])->first();
                        $orderInfo[$i]['supplier_id'] = $supplier->name;
                    }
                    if($action == self::guideAction){
                        $guideId = OrderBase::whereOrderNo($item['order_no'])->pluck('guide_id');
                        if($guideId != 0){
                            $guideName = GuideBase::whereId($guideId)->pluck('real_name');
                            $orderInfo[$i]['guide_name'] = $guideName;
                        }else{
                            $orderInfo[$i]['guide_name'] = 'GID.'.$guideId;
                        }
                    }
                    $orderInfo[$i]['content'] = $item['content'];
                    if(isset($item['pay_type'])){
                        if($item['pay_type'] == OrderBase::PAY_TYPE_ALI){
                            $orderInfo[$i]['pay_type'] = 'ping++支付宝支付';
                        }else if($item['pay_type'] == OrderBase::PAY_TYPE_WX){
                            $orderInfo[$i]['pay_type'] = 'ping++微信支付';
                        }else{
                            $orderInfo[$i]['pay_type'] = '微信商户支付';
                        }
                    }
                    $orderInfo[$i]['amount'] = $item['amount'] - $item['return_amount'];
                    $orderInfo[$i]['state'] = $item['state'];
                    $orderInfo[$i]['goodsTitle'] = $value->goods_title;
                    $orderInfo[$i]['specName'] = $value->spec_name;
                    $orderInfo[$i]['num'] = $value->num;
                    $orderInfo[$i]['orderAmount'] = $value->price * $value->num;
                    $orderInfo[$i]['created_at'] = $item['created_at'];
                    $i++;
                }
            }
        }*/
        if($type == 'withdraw'){
            $field = ['用户昵称','提现账户信息','提现金额','状态','提交时间'];
            $orderInfo[] = $field;
            foreach($items as $item){
                $withdrawInfo = json_decode($item['remark'],true);
                if(isset($item['ta_id'])){
                    $real_name = TaBase::whereId($item['ta_id'])->pluck('mobile');
                }
                if(isset($item['supplier_id'])){
                    $real_name = SupplierBase::whereId($item['supplier_id'])->pluck('mobile');
                }
                if(isset($item['guide_id'])){
                    $real_name = UserBase::whereId($item['uid'])->pluck('mobile');
                }
                $orderInfo[$i]['nick_name'] = $withdrawInfo['withdraw_name'].'|'.$real_name;
                if(isset($withdrawInfo['withdraw_sub_bank'])){
                    $orderInfo[$i]['remark'] = $withdrawInfo['withdraw_name'].'_'.$withdrawInfo['withdraw_bank'].'_'.$withdrawInfo['withdraw_sub_bank'].'_'.$withdrawInfo['withdraw_card_number'];
                }else{
                    $orderInfo[$i]['remark'] = $withdrawInfo['withdraw_name'].'_'.$withdrawInfo['withdraw_bank'].'_'.$withdrawInfo['withdraw_card_number'];
                }
                $orderInfo[$i]['amount'] = $item['amount'];
                if(isset($item['state'])){
                    if($item['state'] == 11){
                        $stateType ='提现审核中';
                    }
                    if($item['state'] == 15){
                        $stateType ='提现财务审核中';
                    }
                    if($item['state'] == 12){
                        $stateType ='提现待打款';
                    }
                    if($item['state'] == 13){
                        $stateType ='提现已打款';
                    }
                    if($item['state'] == 14){
                        $stateType ='提现已驳回';
                    }
                    $orderInfo[$i]['state'] = $stateType;
                }
                $orderInfo[$i]['created_at'] = $item['created_at'];
                $i++;
            }
        }
        Excel::create(date('YmdHis'),function($excel) use ($orderInfo,$action){
            $excel->sheet('score', function($sheet) use ($orderInfo,$action){
                if($action == self::platformAction){
                    $sheet->setWidth(array(
                        'A'=>30,
                        'B'=>30,
                        'C'=>15,
                        'D'=>15,
                        'E'=>10,
                        'F'=>10,
                        'G'=>40,
                    ));
                }else{
                    $sheet->setWidth(array(
                        'A'=>30,
                        'B'=>30,
                        'C'=>15,
                        'D'=>15,
                        'E'=>15,
                        'F'=>10,
                        'G'=>10,
                        'H'=>40,
                    ));
                }
                $sheet->setColumnFormat(array(
                    'A' => '0',
                ));
                $sheet->rows($orderInfo);
            });
        })->export('xlsx');
    }

    //计算营业额
    static public function amount($amount_goods,$amount_express,$amount_coupon){
        $amount = number_format($amount_goods + $amount_express - $amount_coupon,2);
        return $amount;
    }


}