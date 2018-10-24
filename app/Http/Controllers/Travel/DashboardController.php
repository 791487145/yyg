<?php

namespace App\Http\Controllers\Travel;

use App\Models\GuideBase;
use App\Models\GuideTum;
use App\Models\OrderBase;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\UserBase;
use App\Models\UserWx;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Travel\TravelController;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Api\GoodsController;
use Session;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class DashboardController extends TravelController
{   
    const inCome = 1;
    //控制排名数的显示
    const RANK   = 20;
    function index(){
        $currentDay = date('Y-m-d').' 00:00:00';
        $user = TaBase::whereId($this->user['id'])->first();
        //今日收益
        $incomeTodayAmount = TaBilling::whereTaId($user->id)->whereInOut(self::inCome)->where('created_at','>',$currentDay)->sum('amount');
        $incomeTodayReturn = TaBilling::whereTaId($user->id)->whereInOut(self::inCome)->where('created_at','>',$currentDay)->sum('return_amount');
        $incomeToday = $incomeTodayAmount - $incomeTodayReturn;

        //今日销售额
        $orderToday = OrderBase::whereTaId($user->id)->where('created_at','>',$currentDay)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
        $orderExpessToday = OrderBase::whereTaId($user->id)->where('created_at','>',$currentDay)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
        $orderToday = $orderToday + $orderExpessToday;
        //累计销售额
        $totalSales = OrderBase::whereTaId($user->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
        $totalExpressAmount = OrderBase::whereTaId($user->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
        $totalSales = $totalSales + $totalExpressAmount;
        //待入账收益
        //$income = TaBilling::whereTaId($user->id)->whereInOut(self::inCome)->whereState(TaBilling::state_nofund)->sum('amount');
        $billingAmount = TaBilling::whereTaId($user->id)->whereInOut(self::inCome)->whereState(TaBilling::state_nofund)->sum('amount');
        $billingReturn = TaBilling::whereTaId($user->id)->whereInOut(self::inCome)->whereState(TaBilling::state_nofund)->sum('return_amount');
        $income = doubleval($billingAmount)-doubleval($billingReturn);
        //今日新增导游
        $guideToday = GuideTum::whereTaId($user->id)->where('created_at','>',$currentDay)->count();
       
        //导游总数
        $guideCount = GuideTum::whereTaId($user->id)->count();

        //今日新增游客
        $userToday = UserWx::whereTaId($user['id'])->where('created_at','>',$currentDay)->count();
        //游客总数
        $userCount = UserWx::whereTaId($user['id'])->count();

        $data = [
            'incomeToday' => $incomeToday,
            'orderToday'  => $orderToday,
            'totalSales'  => $totalSales,
            'income' => $income,
            'amount' => $user->amount,
            'guideToday' => $guideToday,
            'guideCount' => $guideCount,
            'userToday' => $userToday,
            'userCount' => $userCount
        ];
        Session::put('data',$data);

        //取出绑定人数的排行
        $guidersinfo = $this->vistorNum()->toArray();
        if(!empty($guidersinfo)){
            $guidersinfo = $this->bubbleSort($guidersinfo,'vistorsnum');
        }
        //取出排序的销售额
        $salesinfo = $this->totalSale()->toArray();
        if(!empty($salesinfo)){
            $salesinfo = $this->bubbleSort($salesinfo,'totalsales');
        }
        //取出绑定公众号的数
        $refsinfo = $this->refNum()->toArray();
        if(!empty($refsinfo)){
            $refsinfo = $this->bubbleSort($refsinfo,'refNum');
        }
        
        $numsAndSalesInfo = array('guidersinfo'=>$guidersinfo,'salesinfo'=>$salesinfo,'refsinfo'=>$refsinfo);
        $leftState  = 0;
        $rightState = 0;
        $botomState = 0;
        Session::put('lefts',$leftState);
        session::put('right',$rightState);
        Session::put('botom',$botomState);
        return view('travel.dash.index')->with(['data'=>$data,'numsAndSalesInfo'=>$numsAndSalesInfo,'lefts'=>$leftState,'right'=>$rightState,'botom'=>$botomState]);
    }
    
    /**
     * 排序函数
     * @param array $arr 要排序的数组
     * @param string $flag 要排序的字段
     * @return array 排序后的字段
     */
    function bubbleSort($arr,$flag){
        foreach($arr as $key=>$val){
            $volume[$key] = $val[$flag];
        }
        array_multisort($volume,SORT_DESC,$arr);
        //在此对数组进行排名前20的截取
        $arr = $this->getFirstTwenty($arr,self::RANK);
        return $arr;
    }
    
    
    
    //排行榜先前的逻辑代码  请不要乱动   开始（即支持分页的取出的数据类型是资源类型的）
    /**
     * 用来取不同时间段的信息
     * @param string $state 用来判断是左边的状态还是右边的状态
     */
    /* function numAndSale($state){
        $data  = Session::get('data'); 
        $state = trim($state);
        //区分左右
        $flag  = substr($state,0,5);
        if($flag == 'lefts'){
            //区分时间段
            $leftState = substr($state,5);
            Session::put('lefts',$leftState);
            $rightState = Session::get('right');
            //根据两个时间状态进行查询
            //取左边的数据
            $guidersinfo = $this->vistorNum($leftState);
            //取右边的数据
            $salesinfo   = $this->totalSale($rightState);
        }else{
            //右边的状态
            $rightState = substr($state,5);
            Session::put('right',$rightState);
            $leftState = Session::get('lefts');
            $salesinfo   = $this->totalSale($rightState);
            $guidersinfo = $this->vistorNum($leftState);
        }
        $numsAndSalesInfo = array('guidersinfo'=>$guidersinfo,'salesinfo'=>$salesinfo);
        return view('travel.dash.index')->with(['data'=>$data,'numsAndSalesInfo'=>$numsAndSalesInfo,'lefts'=>$leftState,'right'=>$rightState]);
        
    } */
 
    /**
     * 计算导游绑定游客数guide_ta里面已经加了2个字段绑定的游客数和导游的销售表
     * @param number $state 0 当天游客数 ，1 本周游客数 ，2 本月游客数 ，
     */
    /* function vistorNum($state = 0){
        $pagesize  = 20;
        $guiders   = GuideTum::whereTaId($this->user['id']);
        $guideinfo = $this->sort($guiders,$state);
        $guidersinfo = $guideinfo->orderBy('vistors_num','desc')->paginate($pagesize);
        return $guidersinfo;
    } */
    
    /**
     * 计算导游的销售额的方法
     * @param number $state 0当天的销售额，1本周的销售额，1本月的销售额
     */
    /* function totalSale($state = 0){
        //取得导游的销售额，降序
        $pagesize = 20;
        $guiders  = GuideTum::whereTaId($this->user['id']);
        $saleinfo = $this->sort($guiders,$state);
        $salesinfo   = $saleinfo->orderBy('total_sales','desc')->paginate($pagesize);
        //在此处取出订单的笔数
        foreach($salesinfo as $info){
            //当前旅行社下的交易数
            $info->billNums = OrderBase::whereGuideId($info->guide_id)->where('ta_id',$info->ta_id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->count();
        }
        return $salesinfo;
    } */
    //排行榜先前的逻辑代码  结束（即支持分页的取出的数据类型是资源类型的）
    
    
    
    //将取出数据转换为数组格式的排行榜代码  开始
    /**
     * 计算导游绑定游客数
     * @param number $state 0 当天游客数 ，1 本周游客数 ，2 本月游客数 
     */
    function vistorNum($state = 0){
        $guiders   = GuideTum::whereTaId($this->user['id'])->get();
        foreach($guiders as $key=>$val){
            //取出当前时间段内的当前导游在当前旅行社下面绑定的游客数   在此处加上is_guide筛选掉未激活的导游
            $userinfo = UserWx::whereTaId($this->user['id'])->where('guide_id',$val['guide_id']);
            $userinfo = $this->sort($userinfo,$state);
            $guiders[$key]['vistorsnum'] = $userinfo->count();
            //取出当前导游的真正的旅行社id
            $guiders[$key]['true_taid']  = GuideBase::whereId($val['guide_id'])->first()->ta_id;
        }
        return $guiders;
    }
    
    /**
     * 计算导游的销售额的方法
     * @param number $state 0当天的销售额，1本周的销售额，1本月的销售额
     */
    function totalSale($state = 0){
        //取得导游的销售额
        $salesinfo  = GuideTum::whereTaId($this->user['id'])->get();
        foreach($salesinfo as $key=>$val){
            //取出导游在当前旅行社下的当前时间段内订单表里面的销量
            $orderinfo = OrderBase::whereGuideId($val['guide_id'])->whereTaId($this->user['id'])->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED]);
            $orderinfo = $this->sort($orderinfo,$state);
            //取出当前段的销售额--此处的字段由amount_goods 改为amount_real
            $salesinfo[$key]['totalsales']     = $orderinfo->sum('amount_goods');
            $salesinfo[$key]['amount_express'] = $orderinfo->sum('amount_express');
            //当前的总的商品价格加上总的运费
            $salesinfo[$key]['totalsales']     = $salesinfo[$key]['totalsales'] + $salesinfo[$key]['amount_express'];
            //取出当前时间段销售额对应的销量
            $salesinfo[$key]['sales_num'] = $orderinfo->count();
            //取出当前导游的真正的旅行社id
            $salesinfo[$key]['true_taid'] = GuideBase::whereId($val['guide_id'])->first()->ta_id;
        }
        return $salesinfo;
    }
    
    /**
     * 计算当前旅行社的当前导游下绑定公众号好的人数
     * @param number $state 0当天的销售额，1本周的销售额，1本月的销售额
     */
    function refNum($state = 0){
        //取得导游的绑定公众号人数
        $refsInfo    = GuideTum::whereTaId($this->user['id'])->get();
        foreach($refsInfo as $info){
            $refInfo = UserWx::whereTaId($info->ta_id)->whereGuideId($info->guide_id)->whereRef('wx_qrcode');
            $refInfo = $this->sortByUpdated($refInfo,$state);
            $info->refNum = $refInfo->count();
        }
        return $refsInfo;
    }
    
    /**
     * 用来取不同时间段的排行榜信息
     * @param string $state 用来判断是左边的状态还是右边的状态
     */
    function numAndSale($state){
        $data  = Session::get('data');
        $state = trim($state);
        //区分左，右，下
        $flag  = substr($state,0,5);
        if($flag == 'lefts'){
            //区分时间段
            $leftState  = substr($state,5);
            Session::put('lefts',$leftState);
            $rightState = Session::get('right');
            $botomState = Session::get('botom');
            //根据3个时间状态进行查询
            //取左边的数据
            $guidersinfo     = $this->vistorNum($leftState)->toArray();
            if(!empty($guidersinfo)){
                $guidersinfo = $this->bubbleSort($guidersinfo,'vistorsnum');
            }
            //取右边的数据
            $salesinfo     = $this->totalSale($rightState)->toArray();
            if(!empty($salesinfo)){
                $salesinfo = $this->bubbleSort($salesinfo,'totalsales');
            }
            //取下面的数据
            $refsinfo     = $this->refNum($botomState)->toArray();
            if(!empty($refsinfo)){
                $refsinfo = $this->bubbleSort($refsinfo,'refNum');
            }
        }elseif($flag == 'right'){
            //右边的状态
            $rightState = substr($state,5);
            Session::put('right',$rightState);
            $leftState  = Session::get('lefts');
            $botomState = Session::get('botom');
            $salesinfo  = $this->totalSale($rightState)->toArray();
            if(!empty($salesinfo)){
                $salesinfo = $this->bubbleSort($salesinfo,'totalsales');
            }
            $guidersinfo     = $this->vistorNum($leftState)->toArray();
            if(!empty($guidersinfo)){
                $guidersinfo = $this->bubbleSort($guidersinfo,'vistorsnum');
            }
            //取下面的数据
            $refsinfo     = $this->refNum($botomState)->toArray();
            if(!empty($refsinfo)){
                $refsinfo = $this->bubbleSort($refsinfo,'refNum');
            }
        }elseif($flag == 'botom'){
            $botomState = substr($state,5);
            Session::put('botom',$botomState);
            $leftState  = Session::get('lefts');
            $rightState = Session::get('right');
            //取下面的数据
            $refsinfo     = $this->refNum($botomState)->toArray();
            if(!empty($refsinfo)){
                $refsinfo = $this->bubbleSort($refsinfo,'refNum');
            }
            $guidersinfo     = $this->vistorNum($leftState)->toArray();
            if(!empty($guidersinfo)){
                $guidersinfo = $this->bubbleSort($guidersinfo,'vistorsnum');
            }
            $salesinfo     = $this->totalSale($rightState)->toArray();
            if(!empty($salesinfo)){
                $salesinfo = $this->bubbleSort($salesinfo,'totalsales');
            }
            
        }
        $numsAndSalesInfo = array('guidersinfo'=>$guidersinfo,'salesinfo'=>$salesinfo,'refsinfo'=>$refsinfo);
        return view('travel.dash.index')->with(['data'=>$data,'numsAndSalesInfo'=>$numsAndSalesInfo,'lefts'=>$leftState,'right'=>$rightState,'botom'=>$botomState]);
    
    }
    //将取出数据转换为数组格式的排行榜代码  结束
    
    
    
    
    /**
     * 取出数组中排好序的前20的数据
     * @param array $arr 要分割的数组
     * @return array 
     */
    function getFirstTwenty($arr,$num){
        $res = array();
        foreach($arr as $key=>$val){
            if($key < $num){
                $res[$key] = $val;
            }
        }
        return $res;
    }
    
    /**
     * 根据不同的时间状态进行信息的区分
     * @param object $guidersinfo
     * @param int $state 0 当天 ，1 本周 ，2 本月
     */
    function sort($guidersinfo,$state = 0){
        $t = time();
        //当天的起止时间
        $currentDayStart  = date("Y-m-d H:i:s",mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t)));  
        $currentDayEnd    = date("Y-m-d H:i:s",mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t))); 
        //本周的起止的时间
        $currentWeekStart = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
        $currentWeekEnd   = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
        //本月的起止时间
        $currentMonthStart = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y")));
        $currentMonthEnd   = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y")));
        if($state == 0){
            $guidersinfo->whereBetween('created_at',[$currentDayStart,$currentDayEnd]);
        }elseif($state == 1){
            $guidersinfo->whereBetween('created_at',[$currentWeekStart,$currentWeekEnd]);
        }elseif($state == 2){
            $guidersinfo->whereBetween('created_at',[$currentMonthStart,$currentMonthEnd]);
        }
        return $guidersinfo;
    }
    
    /**
     * 对updated_at字段进行更新
     * @param object $guidersinfo
     * @param int $state 0 当天 ，1 本周 ，2 本月
     * @return object
     */
    function sortByUpdated($guidersinfo,$state = 0){
        $t = time();
        //当天的起止时间
        $currentDayStart  = date("Y-m-d H:i:s",mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t)));
        $currentDayEnd    = date("Y-m-d H:i:s",mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t)));
        //本周的起止的时间
        $currentWeekStart = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
        $currentWeekEnd   = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
        //本月的起止时间
        $currentMonthStart = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y")));
        $currentMonthEnd   = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y")));
        if($state == 0){
            $guidersinfo->whereBetween('updated_at',[$currentDayStart,$currentDayEnd]);
        }elseif($state == 1){
            $guidersinfo->whereBetween('updated_at',[$currentWeekStart,$currentWeekEnd]);
        }elseif($state == 2){
            $guidersinfo->whereBetween('updated_at',[$currentMonthStart,$currentMonthEnd]);
        }
        return $guidersinfo;
    }
    
    /**
     * 各排行榜的对应的excel导出
     * @param string $flag 用来判断是左边导出还是右边导出
     */
    function export($flag){
        //$flag = trim($flag);
        if($flag == 'lefts'){
            $leftState  = Session::get('lefts');
            //进行左边的当前时间段的导出操作
            $this->exportLeft($leftState);
        }elseif($flag == 'right'){
            $rightState = Session::get('right'); 
            //进行右边的当前时间段的导出操作
            $this->exportRight($rightState);
        }elseif($flag == 'botom'){
            $botomState = Session::get('botom');
            //进行下面数据的导出
            $this->exportBotom($botomState);
        }
    }
    
    /**
     * 导出全部是的排序函数
     * @param array $arr 要排序的数组
     * @param string $flag 要排序的字段
     * @return array 排序后的字段
     */
    function exportBubbleSort($arr,$flag){
        foreach($arr as $key=>$val){
            $volume[$key] = $val[$flag];
        }
        array_multisort($volume,SORT_DESC,$arr);
        return $arr;
    }    
    
    
    //数据格式为资源的先前导出的代码       请不要乱动     开始
    /**
     * 导出左边的数据
     * @param int $timeState 判断时间状态 0当天   1本周   2 本月
     */
    /* function exportLeft($timeState){
        $guiders   = GuideTum::whereTaId($this->user['id']);
        $guideinfo = $this->sort($guiders,$timeState);
        $vistorsNumInfo = $guideinfo->orderBy('vistors_num','desc')->get();
        //$vistorsNumInfo = $this->vistorNum($timeState);
        if(!$vistorsNumInfo->isEmpty()){
            $field = ['排名','导游姓名','手机号','已绑定的游客数'];
            $data[] = $field;
            $i = 1;
            foreach ($vistorsNumInfo as $num){
                $data[] = [
                    $i,$num->name,$num->mobile,$num->vistors_num];
                $i++;
            }
        }
        //dd($data);
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 10,
                        'C' => 15,
                        'D' => 20,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
        }
        exit;
    } */
    
    /**
     * 导出右边的数据
     * @param int $timeState 判断时间状态 0当天   1本周   2 本月
     */
    /* function exportRight($timeState){
        $guiders  = GuideTum::whereTaId($this->user['id']);
        $saleinfo = $this->sort($guiders,$timeState);
        $salesinfo   = $saleinfo->orderBy('total_sales','desc')->get();
        if(!$salesinfo->isEmpty()){
            $field = ['排名','导游姓名','手机号','销量 (笔)','销售额  (元)'];
            $data[] = $field;
            $i = 1;
            foreach ($salesinfo as $sale){
                $data[] = [
                    $i,$sale->name,$sale->mobile,$sale->billNums,$sale->total_sales];
                $i++;
            }
        }
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 10,
                        'C' => 15,
                        'D' => 10,
                        'E' => 15,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
            return response()->json(['ret'=>'yes','msg'=>'数据导出成功']);
        }
        exit;
    } */
    //数据格式为资源的先前导出的代码       请不要乱动     结束
    
    
    
    //数据类型为数组的的导出数据的代码   开始
    /**
     * 导出左边的数据
     * @param int $timeState 判断时间状态 0当天   1本周   2 本月
     */
    function exportLeft($timeState){
        $guiders   = GuideTum::whereTaId($this->user['id'])->get();
        foreach($guiders as $key=>$val){
            //取出当前时间段内的游客
            $userinfo = UserWx::whereGuideId($val['guide_id']);
            $userinfo = $this->sort($userinfo,$timeState);
            $guiders[$key]['vistornum'] = $userinfo->count();
        }
        $guiders = $guiders->toArray();
        $guiders = $this->exportBubbleSort($guiders,'vistornum');

        if(!empty($guiders)){
            $field = ['排名','导游姓名','手机号','已绑定的游客数'];
            $data[] = $field;
            $i = 1;
            foreach ($guiders as $num){
                $data[] = [
                    $i,$num['name'],$num['mobile'],$num['vistornum']];
                $i++;
            }
        }
        //dd($data);
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 10,
                        'C' => 15,
                        'D' => 20,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
        }
        exit;
    }
    
    /**
     * 导出右边的数据
     * @param int $timeState 判断时间状态 0当天   1本周   2 本月
     */
    function exportRight($timeState){
        $salesinfo  = GuideTum::whereTaId($this->user['id'])->get();
        foreach($salesinfo as $key=>$val){
            //取出当前时间段内订单表里面的销量1.待发货 2.待收货 5.已完成
            $orderinfo = OrderBase::whereGuideId($val['guide_id'])->whereTaId($this->user['id'])->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED]);
            $orderinfo = $this->sort($orderinfo,$timeState);
            //取出当前段的销售额  amount_goods字段被修改为amount_real
            $salesinfo[$key]['totalsale']      = $orderinfo->sum('amount_goods');
            $salesinfo[$key]['amount_express'] = $orderinfo->sum('amount_express');
            //当前的总的商品价格加上总的运费
            $salesinfo[$key]['totalsale']      = $salesinfo[$key]['totalsale'] + $salesinfo[$key]['amount_express'];
            //取出当前时间段销售额对应的销量
            $salesinfo[$key]['sales_num'] = $orderinfo->count();
        }
        $salesinfo = $salesinfo->toArray();
        $salesinfo = $this->exportBubbleSort($salesinfo,'totalsale');
        
        
        if(!empty($salesinfo)){
            $field = ['排名','导游姓名','手机号','销量 (笔)','销售额  (元)'];
            $data[] = $field;
            $i = 1;
            foreach ($salesinfo as $sale){
                $data[] = [
                    $i,$sale['name'],$sale['mobile'],$sale['sales_num'],$sale['totalsale']];
                $i++;
            }
        }
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 10,
                        'C' => 15,
                        'D' => 10,
                        'E' => 15,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
            return response()->json(['ret'=>'yes','msg'=>'数据导出成功']);
        }
        exit;
    }
    
    /**
     * 导出右边的数据
     * @param int $timeState 判断时间状态 0当天   1本周   2 本月
     */
    function exportBotom($timeState){
        $refsInfo  = GuideTum::whereTaId($this->user['id'])->get();
        foreach($refsInfo as $key=>$info){
            //取出当前时间段内订单表里面的销量1.待发货 2.待收货 5.已完成
            $refinfo   = UserWx::whereTaId($info->ta_id)->whereGuideId($info->guide_id)->where('ref','wx_qrcode');
            $refinfo = $this->sortByUpdated($refinfo,$timeState);
            //当前的总的商品价格加上总的运费
            $refsInfo[$key]['refNum'] = $refinfo->count();

        }
        $refsInfo = $refsInfo->toArray();
        $refsInfo = $this->exportBubbleSort($refsInfo,'refNum');
    
    
        if(!empty($refsInfo)){
            $field = ['排名','导游姓名','手机号','关注公众号人数'];
            $data[] = $field;
            $i = 1;
            foreach ($refsInfo as $info){
                $data[] = [
                    $i,$info['name'],$info['mobile'],$info['refNum']];
                $i++;
            }
        }
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 10,
                        'C' => 15,
                        'D' => 20,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
            return response()->json(['ret'=>'yes','msg'=>'数据导出成功']);
        }
        exit;
    }
    //数据类型为数组的的导出数据的代码   结束
    
    
    
    /**
     * 获取全部的函数
     */
    function getAllInfo($flag){
        $data       = Session::get('data');
        $leftState  = Session::get('lefts');
        $rightState = Session::get('right');
        $guiders    = GuideTum::whereTaId($this->user['id']);       
        if($flag == 'left'){
            $guiders   = GuideTum::whereTaId($this->user['id']);
            $guideinfo = $this->sort($guiders,$leftState);
            $guidersinfo = $guideinfo->orderBy('vistors_num','desc')->get();
            $saleinfo    = $this->sort($guiders,$rightState);
            $salesinfo   = $saleinfo->orderBy('total_sales','desc')->get();
        }else{
            $guiders   = GuideTum::whereTaId($this->user['id']);
            $guideinfo = $this->sort($guiders,$leftState);
            $guidersinfo = $guideinfo->orderBy('vistors_num','desc')->get();
            $saleinfo    = $this->sort($guiders,$rightState);
            $salesinfo   = $saleinfo->orderBy('total_sales','desc')->get();
        }
        $numsAndSalesInfo = array('guidersinfo'=>$guidersinfo,'salesinfo'=>$salesinfo);
        return view('travel.dash.index')->with(['data'=>$data,'numsAndSalesInfo'=>$numsAndSalesInfo,'lefts'=>$leftState,'right'=>$rightState]);
    }
    
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
