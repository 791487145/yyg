<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\GoodsOptLog;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\OrderBase;
use App\Models\GuideBilling;
use App\Models\GoodsBase;
use App\Models\OrderGood;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\UserBase;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;


class WithdrawController extends BaseController
{
    protected $page = 5;
    const pageNum = 20;
    const withDraw = '提现';
    const type = 62;
    const passType = 63;
    const payType = 64;
    const withdrawReject = 14;
    const withdrawPay = 13;
    const withdrawPass = 12;
    const supplierWithdraw = 1;
    const guideWithdraw = 2;
    const taWithdraw = 3;

    //提现列表管理
    protected function TXManage($state = GuideBilling::state_withdraw_wait_audit,$action = self::supplierWithdraw){
        if($action == self::supplierWithdraw){
            $tables = new SupplierBilling();
        }
        if($action == self::guideWithdraw){
            $tables = new GuideBilling();
        }
        if($action == self::taWithdraw){
            $tables = new TaBilling();
        }

        $withdraw_name = Input::get('withdraw_name');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        if(empty($end_time)){
            $end_time = date('Y-m-d');
        }
        if(!empty($withdraw_name)){
            $tables = $tables->where('remark','like','%'.$withdraw_name.'%');
        }
        if(!empty($start_time)){
            $tables = $tables->whereBetween('created_at',[$start_time,$end_time]);
        }
        $guideBillings = $tables->where('content','like','%'.self::withDraw.'%')->whereState($state)->orderBy('id','desc')->paginate(self::pageNum);
        if($guideBillings){
            foreach($guideBillings as $guideBilling){
                $remark = json_decode($guideBilling->remark,true);
                $guideBilling->billingSourceCount = $tables->where('withdraw_id',$guideBilling->id)->count();
                $guideBilling->withdraw_name = $remark['withdraw_name'];
                $guideBilling->withdraw_bank = $remark['withdraw_bank'];
                if(isset($remark['withdraw_sub_bank'])){
                    $guideBilling->withdraw_sub_bank = $remark['withdraw_sub_bank'];
                }
                $guideBilling->withdraw_card_number = $remark['withdraw_card_number'];
                if($action == self::supplierWithdraw){
                    $userinfo = SupplierBase::whereId($guideBilling->supplier_id)->first();
                    $guideBilling->real_name = $userinfo->name.'|'.$userinfo->mobile;
                }
                if($action == self::guideWithdraw){
                    $userinfo = GuideBase::whereId($guideBilling->guide_id)->first();
                    $userMobile = UserBase::whereId($userinfo->uid)->pluck('mobile');
                    $guideBilling->real_name = $userinfo->real_name.'|'.$userMobile;
                }
                if($action == self::taWithdraw){
                    $userinfo = TaBase::whereId($guideBilling->ta_id)->first();
                    if($userinfo){
                        $guideBilling->real_name = $userinfo->ta_name.'|'.$userinfo->mobile;
                    }
                }
            }
        }
        return view("boss.withdraw.txmanage_list",compact('guideBillings','withdraw_name','start_time','end_time','state','action'));
    }

    //提现审核
    /*
     *
     * $actionId  guide_id
     * */
    protected function TXAudit($actionId,$action,$id,$state,$amount,Request $request){
        $order_no = $request->input('order_no');
        $trade_no = $request->input('trade_no');
        $infos = self::getBillingSource($actionId,$action,$id,$state,$amount,$order_no,$trade_no);
        if($action == self::supplierWithdraw){
            $tables = new SupplierBilling();
            $billingWithdrawInfo = $tables->whereId($id)->first();
            if($billingWithdrawInfo){
                $auditorInfo = json_decode($billingWithdrawInfo->auditor);
            }
            $body = SupplierBase::whereId($actionId)->first();
            $waitToAmount = SupplierBilling::whereSupplierId($actionId)->whereInOut(SupplierBilling::income)->whereState(0)->sum('amount');
            $billingSourceCount = $tables->whereSupplierId($actionId)->where('withdraw_id',$id)->count();

        }
        if($action == self::guideWithdraw){
            $tables = new GuideBilling();
            $billingWithdrawInfo = $tables->whereId($id)->first();
            if($billingWithdrawInfo){
                $auditorInfo = json_decode($billingWithdrawInfo->auditor);
            }
            $body = GuideBase::whereId($actionId)->first();
            $body = UserBase::whereId($body->uid)->first();
            $waitToAmount = GuideBilling::whereGuideId($actionId)->whereInOut(GuideBilling::in_income)->whereState(0)->sum('amount');
            $billingSourceCount = $tables->whereGuideId($actionId)->where('withdraw_id',$id)->count();

        }
        if($action == self::taWithdraw){
            $tables = new TaBilling();
            $billingWithdrawInfo = $tables->whereId($id)->first();
            if($billingWithdrawInfo){
                $auditorInfo = json_decode($billingWithdrawInfo->auditor);
            }
            $body = TaBase::whereId($actionId)->first();
            $waitToAmount = TaBilling::whereTaId($actionId)->whereInOut(TaBilling::in_income)->whereState(0)->sum('amount');
            $billingSourceCount = $tables->whereTaId($actionId)->where('withdraw_id',$id)->count();
        }
        return view("boss.withdraw.txaudit_list",compact('infos','orderNo','actionId','action','id','state','amount','body','waitToAmount','billingSourceCount','order_no','trade_no','billingWithdrawInfo','auditorInfo'));
}
    //导出账单提现金额详细来源
    public function export($actionId,$action,$id,$state,$amount,Request $request){
        $order_no = $request->input('order_no');
        $trade_no = $request->input('trade_no');
        $billingSourceCount = $request->input('billingSourceCount');
        $data = self::getBillingSource($actionId,$action,$id,$state,$amount,$order_no,$trade_no);
        $statement = '本次提现金额：￥'.$amount.'       本次提现订单数：'.$billingSourceCount.'笔';
        if($action == 1){
            $field = ['订单编号','交易单号','收款账户','所属供应商','商品名称','商品规格','数量','运费','零售价','供应价单价','订单金额','退款金额','供应总价','状态','提交时间'];
            $item[1] = $statement;
            $item[2] = $field;
            $i = 3;
            foreach($data as $value){
                $value->supplier_id = SupplierBase::whereId($value->supplier_id)->pluck('name');
                switch($value->state){
                    case 0:
                        $value->state = '待入账';
                        break;
                    case 1:
                        $value->state = '已入账';
                        break;
                    case 11:
                        $value->state = '提现审核中';
                        break;
                    case 12:
                        $value->state = '提现待打款';
                        break;
                    case 13:
                        $value->state = '提现已打款';
                        break;
                    case 14:
                        $value->state = '提现已驳回';
                        break;
                }
                foreach($value->attr as $v){
                    $price = bcmul($v['num'],$v['price_buying'],2);
                    $price = bcsub($price,$value->return_amount,2);
                    $item[$i] = [$value->order_no,$value->trade_no,$value->payType,$value->supplier_id,$v['title'],$v['spec_name'],$v['num'],
                        $value->amount_express,$v['price'],$v['price_buying'],$value->amount_order,$value->return_amount,
                        $price,$value->state,$value->created_at];
                    $i++;
                }
            }

            Excel::create(date('YmdHis'),function($excel) use ($item,$statement){
                $excel->sheet('withdraw', function($sheet) use ($item,$statement){
                    $sheet->setWidth(array(
                        'A'=>20,
                        'B'=>30,
                        'C'=>20,
                        'D'=>20,
                        'E'=>60,
                        'F'=>10,
                        'G'=>10,
                        'H'=>10,
                        'I'=>10,
                        'J'=>15,
                        'K'=>10,
                        'L'=>15,
                        'M'=>10,
                        'N'=>20
                    ));
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                    ));

                    $sheet->mergeCells('A1:N1');
                    $sheet->setHeight(1, 70);
                    $sheet->cell('A1', function($row) use ($statement){
                        $row->setBackground('#92d050');
                        $row->setFontWeight('bold');
                        $row->setFontSize(14);
                        $row->setFontColor('#e74430');
                        $row->setValue($statement);
                    });
                    $sheet->rows($item);
                    /*foreach($item as $key=>$v){
                        if($key>2){
                            $orderGoods = OrderGood::whereOrderNo($v[0])->get()->toArray();
                            $mergeCells = count($orderGoods);
                            $cellStart = 3;
                            $rangeArr = ['A','B','C','D','M','N'];
                            foreach ($rangeArr as $v){
                                $range = $v.$cellStart.':'.$v.($mergeCells + $cellStart -1);
                                $sheet->mergeCells($range);
                            }
                            $cellStart += $mergeCells;
                        }
                    }*/
                });
            })->export('xlsx');
        }

        if($action == 2){
            $field = ['订单编号','交易单号','收款账户','导游姓名','商品名称','商品规格','数量','运费','零售价','订单金额','退款金额','导游获得返利','状态','提交时间'];
            $item[1] = $statement;
            $item[2] = $field;
            $i = 3;
            foreach($data as $value){
                $guideBase = GuideBase::whereId($value->guide_id)->first();
                $value->guide_name = empty($guideBase->real_name)? 'GID.'.$value->guide_id : $guideBase->real_name;
                switch($value->state){
                    case 0:
                        $value->state = '待入账';
                        break;
                    case 1:
                        $value->state = '已入账';
                        break;
                    case 11:
                        $value->state = '提现审核中';
                        break;
                    case 12:
                        $value->state = '提现待打款';
                        break;
                    case 13:
                        $value->state = '提现已打款';
                        break;
                    case 14:
                        $value->state = '提现已驳回';
                        break;
                }
                foreach($value->attr as $v){
                    $item[$i] = [$value->order_no,$value->trade_no,$value->payType,$value->guide_name,$v['title'],$v['spec_name'],$v['num'],$value->amount_express,$v['price'],$value->amount_order,$value->return_amount,$value->rebate,$value->state,$value->created_at];
                    $i++;
                }
            }
            Excel::create(date('YmdHis'),function($excel) use ($item,$statement){
                $excel->sheet('withdraw', function($sheet) use ($item,$statement){
                    $sheet->setWidth(array(
                        'A'=>20,
                        'B'=>30,
                        'C'=>15,
                        'D'=>15,
                        'E'=>60,
                        'F'=>20,
                        'G'=>20,
                        'H'=>10,
                        'I'=>10,
                        'J'=>10,
                        'K'=>10,
                        'L'=>20,
                        'M'=>20,
                        'N'=>20

                    ));
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                    ));
                    $sheet->mergeCells('A1:N1');
                    $sheet->setHeight(1, 70);
                    $sheet->cell('A1', function($row) use ($statement){
                        $row->setBackground('#92d050');
                        $row->setFontWeight('bold');
                        $row->setFontSize(14);
                        $row->setFontColor('#e74430');
                        $row->setValue($statement);
                    });
                    $sheet->rows($item);
                    /*  foreach($item as $key=>$v){
                          if($key>2){
                              $orderGoods = OrderGood::whereOrderNo($v[0])->get()->toArray();
                              $mergeCells = count($orderGoods);
                              $cellStart = 3;
                              $rangeArr = ['A','B','C','D','E','F','G','K','L'];
                              foreach ($rangeArr as $v){
                                  $range = $v.$cellStart.':'.$v.($mergeCells + $cellStart -1);
                                  $sheet->mergeCells($range);
                              }
                              $cellStart += $mergeCells;
                          }
                      }*/
                });
            })->export('xlsx');
        }

        if($action == 3){
            $field = ['订单编号','交易单号','收款账户','旅行社名称','负责人名字','商品名称','商品规格','数量','运费','零售价','订单金额','退款金额','旅行社获得返利','状态','提交时间'];
            $item[1] = $statement;
            $item[2] = $field;
            $i = 3;
            foreach($data as $value){
                $taBase = TaBase::whereId($value->ta_id)->first();
                $value->ta_id = $taBase->ta_name;
                $value->opt_name = $taBase->opt_name;
                switch($value->state){
                    case 0:
                        $value->state = '待入账';
                        break;
                    case 1:
                        $value->state = '已入账';
                        break;
                    case 11:
                        $value->state = '提现审核中';
                        break;
                    case 12:
                        $value->state = '提现待打款';
                        break;
                    case 13:
                        $value->state = '提现已打款';
                        break;
                    case 14:
                        $value->state = '提现已驳回';
                        break;
                }
                foreach($value->attr as $v){
                    $item[$i] = [$value->order_no,$value->trade_no,$value->payType,$value->ta_id,$value->opt_name,$v['title'],$v['spec_name'],$v['num'],$value->amount_express,$v['price'],$value->amount_order,$value->return_amount,$value->rebate,$value->state,$value->created_at];
                    $i++;
                }
            }

            Excel::create(date('YmdHis'),function($excel) use ($item,$statement){
                $excel->sheet('withdraw', function($sheet) use ($item,$statement){
                    $sheet->setWidth(array(
                        'A'=>20,
                        'B'=>30,
                        'C'=>15,
                        'D'=>15,
                        'E'=>15,
                        'F'=>60,
                        'G'=>20,
                        'H'=>10,
                        'I'=>10,
                        'J'=>10,
                        'K'=>10,
                        'L'=>20,
                        'M'=>20,
                        'N'=>20

                    ));
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                    ));
                    $sheet->mergeCells('A1:N1');
                    $sheet->setHeight(1, 70);
                    $sheet->cell('A1', function($row) use ($statement){
                        $row->setBackground('#92d050');
                        $row->setFontWeight('bold');
                        $row->setFontSize(14);
                        $row->setFontColor('#e74430');
                        $row->setValue($statement);
                    });
                    $sheet->rows($item);
                  /*  foreach($item as $key=>$v){
                        if($key>2){
                            $orderGoods = OrderGood::whereOrderNo($v[0])->get()->toArray();
                            $mergeCells = count($orderGoods);
                            $cellStart = 3;
                            $rangeArr = ['A','B','C','D','E','F','G','K','L'];
                            foreach ($rangeArr as $v){
                                $range = $v.$cellStart.':'.$v.($mergeCells + $cellStart -1);
                                $sheet->mergeCells($range);
                            }
                            $cellStart += $mergeCells;
                        }
                    }*/
                });
            })->export('xlsx');
        }
    }

    //获取提现金额对应订单来源
    static private function getBillingSource($actionId,$action,$id,$state,$amount,$order_no,$trade_no){
        if($action == self::supplierWithdraw){
            $tables = new SupplierBilling();
            if($order_no){
                $tables = $tables->whereOrderNo($order_no);
            }
            if($trade_no){
                $tables = $tables->whereOrderNo($trade_no);
            }
            $infos = $tables->whereSupplierId($actionId)->where('withdraw_id',$id)->orderBy('id','desc')->get();
        }
        if($action == self::guideWithdraw){
            $tables = new GuideBilling();
            if($order_no){
                $tables = $tables->whereOrderNo($order_no);
            }
            if($trade_no){
                $tables = $tables->whereOrderNo($trade_no);
            }
            $infos = $tables->whereGuideId($actionId)->where('withdraw_id',$id)->orderBy('id','desc')->get();
        }
        if($action == self::taWithdraw){
            $tables = new TaBilling();
            if($order_no){
                $tables = $tables->whereOrderNo($order_no);
            }
            if($trade_no){
                $tables = $tables->whereOrderNo($trade_no);
            }
            $infos = $tables->whereTaId($actionId)->where('withdraw_id',$id)->orderBy('id','desc')->get();
        }
        foreach($infos as $info){
            $goodsSupplierPrice = $specName = $orderGoodsPrice = $num = $titles = [];
            $orderBase = OrderBase::whereOrderNo($info->order_no)->first();
            $payType = $orderBase['pay_type'];
            switch ($payType)
            {
                case 1:$payType = 'ping++支付宝支付';break;
                case 2:$payType = 'ping++微信支付';break;
                case 3:$payType = '微信商户支付';break;
            }
            $info->payType = $payType;
            $info->amount_express = $orderBase['amount_express'];
            $info->amount_goods = $orderBase['amount_goods'];
            $info->amount_order = $orderBase['amount_goods'] + $orderBase['amount_express'] - $orderBase['amount_coupon'];
            $info->rebate = sprintf('%.2f',$info->amount - $info->return_amount);
            if($info->order_no){
                //$orderGoods = OrderGood::whereOrderNo($info->order_no)->get();
                /*$goodsTitles = OrderGood::whereOrderNo($info->order_no)->get();*/
                $goodsSupplierPrice = OrderGood::whereOrderNo($info->order_no)->lists('price_buying')->toArray();
                $specName = OrderGood::whereOrderNo($info->order_no)->lists('spec_name')->toArray();
                $orderGoodsPrice = OrderGood::whereOrderNo($info->order_no)->lists('price')->toArray();
                $num = OrderGood::whereOrderNo($info->order_no)->lists('num')->toArray();
                $titles = OrderGood::whereOrderNo($info->order_no)->lists('goods_title')->toArray();
                /*$sumSupplierPrice = '';
                foreach($orderGoods as $value){
                    if($value->is_gift == OrderGood::is_gift_no){
                        $orderSupplierPrice = $value->num * $value->price_buying;
                        $sumSupplierPrice +=$orderSupplierPrice;
                    }
                }*/

                //判断是否改价
                $orderGoodsSpecInfos =OrderGood::whereOrderNo($info->order_no)->get();
                $isChangePriceBuying = [];
                foreach($orderGoodsSpecInfos as $goodsSpecInfo){
                    $i = 0;
                    $goodsPriceBuyingNow = GoodsSpec::whereId($goodsSpecInfo->spec_id)->pluck('price_buying');
                    $goodsChangeLog = GoodsOptLog::whereType(GoodsOptLog::type_change_priceBuying)->whereGid($goodsSpecInfo->goods_id)->first();
                    if(isset($goodsChangeLog) && $goodsPriceBuyingNow != $goodsSpecInfo->price_buying){
                       $i = $goodsSpecInfo->spec_id;
                    }
                    $isChangePriceBuying[] = $i;
                }
                $info->isChangePriceBuying = $isChangePriceBuying;


                $info->orderSupplierPrice = $info->amount - $info->return_amount;

                $info->goodsTitle = $titles;
                $info->goodsNumSum = $num;
                $info->goodsSupplierPrice = $goodsSupplierPrice;
                //订单没有减去优惠券的价格
                $totalAmount = [];
                if(!empty(intval($info->coupon_amount))){
                    foreach($goodsSupplierPrice as $key=>$v){
                        $totalAmount[] = $num[$key] * $v;
                    }
                }
                $info->totalAmount = number_format(array_sum($totalAmount) + $info->express_amount,2);
                $totalArray =[];
                foreach($titles as $key=>$v){
                    $totalArray[$key] = ['title'=>$v,'num'=>$num[$key],'spec_name'=>$specName[$key],'price'=>$orderGoodsPrice[$key],'price_buying'=>$goodsSupplierPrice[$key]];
                }
                $info->attr = $totalArray;
            }
        }
        return $infos;
    }

    //发送导游提现审核结果
    public function sendRejectMsg($action,Request $request){
        $rejectReason = urlencode(Input::get('reason'));
        $id = Input::get('id');
        $data = urldecode(json_encode(['rejectReason'=>$rejectReason]));
        $reason = urldecode($rejectReason);

        //更新状态 退回冻结余额
        if($action == self::supplierWithdraw){
            $SupplierBilling = SupplierBilling::whereId($id)->first();
            $SupplierBilling->state = self::withdrawReject;
            $SupplierBilling->auditor = $data;
            $SupplierBilling->save();

            SupplierBilling::where('withdraw_id',$id)->update(['withdraw_id'=>0]);

            $SupplierBase = SupplierBase::whereId($SupplierBilling['supplier_id'])->first();
            $SupplierBase->amount = $SupplierBase->amount + $SupplierBilling['amount'];
            $SupplierBase->freeze_amount = $SupplierBase->freeze_amount - $SupplierBilling['amount'];
            $SupplierBase->save();

            $amount = $SupplierBilling['amount'];
            $name = $SupplierBase['name'];
            $mobile = $SupplierBase['mobile'];
        }
        if($action == self::guideWithdraw){
            $GuideBilling = GuideBilling::whereId($id)->first();
            $GuideBilling->state = self::withdrawReject;
            $GuideBilling->auditor = $data;
            $GuideBilling->save();

            GuideBilling::where('withdraw_id',$id)->update(['withdraw_id'=>0]);

            $UserBase = UserBase::whereId($GuideBilling['uid'])->first();
            $UserBase->amount = $UserBase->amount + $GuideBilling['amount'];
            $UserBase->freeze_amount = $UserBase->freeze_amount - $GuideBilling['amount'];
            $UserBase->save();

            $amount = $GuideBilling['amount'];
            $name = $UserBase['nick_name'];
            $mobile = $UserBase['mobile'];

        }
        if($action == self::taWithdraw){
            $TaBilling = TaBilling::whereId($id)->first();
            $TaBilling->state = self::withdrawReject;
            $TaBilling->auditor = $data;
            $TaBilling->save();

            TaBilling::where('withdraw_id',$id)->update(['withdraw_id'=>0]);

            $TaBase = TaBase::whereId($TaBilling['ta_id'])->first();
            $TaBase->amount = $TaBase->amount + $TaBilling['amount'];
            $TaBase->freeze_amount = $TaBase->freeze_amount - $TaBilling['amount'];
            $TaBase->save();

            $amount = $TaBilling['amount'];
            $name = $TaBase['ta_name'];
            $mobile = $TaBase['mobile'];

        }

        $ip = ip2long($request->getClientIp());
        $type = self::type;
        $tpl_value = "【易游购】".$name."你好，你提交的".$amount."提现申请审核不通过，原因：".$reason."。你的提现资金已退回，请重新提交提现申请。
";
        parent::platformSendSms($mobile,$ip,$type,$tpl_value);
        return $data = ['ret'=>'yes','msg'=>'审核驳回'];
    }

    //提现审核成功
    public function sendPassMsg($action){
        $id = Input::get('id');
        $sign = urlencode(Input::get('sign'));
        $data = urldecode(json_encode(['auditor'=>$sign]));
        //更新状态 退回冻结余额
        if($action == self::supplierWithdraw){
            $changeBillingState = SupplierBilling::whereId($id)->update(['state'=>SupplierBilling::state_withdraw_wait_finance_audit,'auditor'=>$data]);
            if($changeBillingState){
                return $data = ['ret'=>'yes','msg'=>'采购审核成功'];
            }else{
                return $data = ['ret'=>'no','msg'=>'采购审核失败'];
            }
        }
        if($action == self::guideWithdraw){
            $guideBillingInfo = GuideBilling::whereId($id)->first();
            $GuideBilling = GuideBilling::whereId($id)->update(['state'=>self::withdrawPass,'auditor'=>$data]);
            if($GuideBilling){
                $UserBase = UserBase::whereId($guideBillingInfo['uid'])->first();
                $UserBase->freeze_amount = $UserBase->freeze_amount - $guideBillingInfo['amount'];
                $UserBase->save();
                return $data = ['ret'=>'yes','msg'=>'提现审核成功'];
            }else{
                return $data = ['ret'=>'no','msg'=>'提现审核失败'];
            }
        }
        if($action == self::taWithdraw){
            $taBillingInfo = TaBilling::whereId($id)->first();
            $TaBilling = TaBilling::whereId($id)->update(['state'=>self::withdrawPass,'auditor'=>$data]);
            if($TaBilling){
                $TaBase = TaBase::whereId($taBillingInfo['ta_id'])->first();
                $TaBase->freeze_amount = $TaBase->freeze_amount - $taBillingInfo['amount'];
                $TaBase->save();
                return $data = ['ret'=>'yes','msg'=>'提现审核成功'];
            }else{
                return $data = ['ret'=>'no','msg'=>'提现审核失败'];
            }

        }


    }

    public function financePass(Request $request){
        $id = $request->input('id');
        $supplierBilling = SupplierBilling::whereId($id)->first();
        $auditor = json_decode($supplierBilling->auditor,true);
        $sign = urlencode(Input::get('sign'));
        $buyer_auditor = urlencode($auditor['auditor']);
        $data = urldecode(json_encode(['auditor'=>$buyer_auditor,'finance_auditor'=>$sign]));

        $billingState = SupplierBilling::whereId($id)->update(['state'=>SupplierBilling::state_withdraw_wait_money,'auditor'=>$data]);

        /*$supplierBilling->state = SupplierBilling::state_withdraw_wait_money;
        $supplierBilling->auditor = $data;
        $supplierBilling->save();*/

        if($billingState){
            $supplierBase = SupplierBase::whereId($supplierBilling['supplier_id'])->first();
            $supplierBase->freeze_amount = $supplierBase->freeze_amount - $supplierBilling['amount'];
            $supplierBase->save();
            return $data = ['ret'=>'yes','msg'=>'提现审核成功'];
        }else{
            return $data = ['ret'=>'no','msg'=>'提现审核失败'];
        }
    }

    public function sendPayMsg($action,Request $request){
        $id = Input::get('id');
        //更新状态
        if($action == self::supplierWithdraw){
            $SupplierBilling = SupplierBilling::whereId($id)->update(['state'=>self::withdrawPay]);
            $SupplierBase = SupplierBase::whereId($SupplierBilling['supplier_id'])->first();
            $amount = $SupplierBilling['amount'];
            $mobile = $SupplierBase['mobile'];
        }
        if($action == self::guideWithdraw){
            $GuideBilling = GuideBilling::whereId($id)->first();
            $GuideBilling->state = self::withdrawPay;
            $GuideBilling->save();

            $UserBase = UserBase::whereId($GuideBilling['uid'])->first();
            $amount = $GuideBilling['amount'];
            $mobile = $UserBase['mobile'];

        }
        if($action == self::taWithdraw){
            $TaBilling = TaBilling::whereId($id)->first();
            $TaBilling->state = self::withdrawPay;
            $TaBilling->save();


            $TaBase = TaBase::whereId($TaBilling['ta_id'])->first();
            $amount = $TaBilling['amount'];
            $mobile = $TaBase['mobile'];

        }

        $ip = ip2long($request->getClientIp());
        $type = self::payType;
        $tpl_value = "【易游购】你申请提现金额$amount,已打款成功，具体到账时间以银行到账时间为准，请及时查收~";
        parent::platformSendSms($mobile,$ip,$type,$tpl_value);
        return $data = ['ret'=>'yes','msg'=>'打款成功'];
    }


    //改价记录
    public function showPriceBuyingChangeRecord($info){
        $goodsPriceBuyingChangeRecord = GoodsOptLog::whereType(GoodsOptLog::type_change_priceBuying)->get();
        $data=[];
        foreach($goodsPriceBuyingChangeRecord as $v){
            $content = json_decode($v->content,true);
            if($content['spec_id'] == $info){
                $spec_name = GoodsSpec::whereId($content['spec_id'])->pluck('name');
                $data[] = ['created_at' => $v->created_at,'spec_name' => $spec_name,'before'=>$content['before'],'after'=>$content['after'],'user'=>$content['user']];
            }
        }
        return view('boss.withdraw.show_change_record',compact('data'));
    }

}