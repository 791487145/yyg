<?php
 /**
* @date: 2017-8-21 上午10:37:49
* @description: 数据报表控制器
* @author: LHW
*/
namespace App\Http\Controllers\Admin;
use App\Models\OrderBase;
use App\Models\OrderGood;
use Illuminate\Support\Facades\DB;

class DatareportController extends BaseController{
    
    public function index(){
        return view('boss.datareport.index');
    }
    
    function bubbleSort($arr,$flag){
        foreach($arr as $key=>$val){
            $volume[$key] = $val[$flag];
        }
        array_multisort($volume,SORT_DESC,$arr);
        return $arr;
    }
    
    /**
     * 按商品销售额排名
     */
    public function goodsSale(){
        //1.取出所有订单状态时1,2,5的订单
        $orderbases = OrderBase::whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->select('order_no')->get()->toArray();
        $ordernos   = '';
        foreach($orderbases as $val){
            $ordernos.= $val['order_no'].',';
        }
        $ordernos = rtrim($ordernos,',');
        $saleinfo = DB::select("select id,order_no,goods_id,goods_title,price,num,sum(num) as taotalbuynum,count(id) as ordernum from order_goods where order_no in (".$ordernos.") group by goods_id");
        foreach($saleinfo as $v){
            $v->totalaccount = $v->taotalbuynum * $v->price;
        }
        $rankinfo = array();
        foreach($saleinfo as $info){
            $rankinfo[] = (array)$info;
        }
        $rankinfo = $this->bubbleSort($rankinfo,'totalaccount');
        
        //dd($rankinfo);
        return view('boss.datareport.goodssale',['results'=>$rankinfo]);
    }
    
    //按商品售后率排名
    public function goodsSalePercent(){
        return view('boss.datareport.goodssalepercent');
    }
    
    //按地方馆销售额排名
    public function copnSale(){
        return view('boss.datareport.copnsale');
    }
    
    //平台用户新增报表
    public function addMember(){
        return view('boss.datareport.addmember');
    }
    
    //导游按累计销售额排名
    public function guidesSale(){
        return view('boss.datareport.guidessale');
    }
    
    //旅行社按累计销售额排名
    public function taSale(){
        return view('boss.datareport.tasale');
    }
    
    //导游按绑定人数排名
    public function guideBandMember(){
        return view('boss.datareport.guidebandmember');
    }
    
    //旅行社按绑定导游排名
    public function taBandMember(){
        return view('boss.datareport.tabandmember');
    }
    
    //导游按绑定公众号人数排名
    public function guideBandHkmovie(){
        return view('boss.datareport.guidebandhkmovie');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}



















































