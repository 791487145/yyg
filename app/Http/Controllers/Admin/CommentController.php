<?php
 /**
* @date: 2017-8-17 下午5:50:57
* @description: 订单评价
* @author: LHW
*/
namespace App\Http\Controllers\Admin;
use App\Models\OrderGood;
use App\Models\OrderBase;
use Qiniu\json_decode;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GoodsImage;
use App\Models\SupplierBase;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use App\Models\CommentGood;
use App\Models\CommentGoodsImage;

class CommentController extends BaseController{
    protected $page = 10;
    /**
     * 商品订单展示
     * @return Ambigous 
     */
    public function index(Request $request){
        //进行筛选
        $order_no   = trim($request->input('order_no',''));
        $start_time = $request->input('start_time','');
        $end_time   = $request->input('end_time','');
        $supplier_id= $request->input('supplier_id','0');
        $orders = OrderBase::whereState(OrderBase::STATE_FINISHED)->orderBy('created_at','desc');
        $orders = $this->filtrate($orders,$order_no,$start_time,$end_time,$supplier_id);
        
        //进行评价订单的筛选
        $comments = $orders;
        $commentOrders   = $comments->get();
        $commentOredrnos = array();
        foreach($commentOrders as $val){
            $flag = CommentGood::whereOrderNo($val->order_no)->get()->toArray();
            if(!empty($flag)){
                $commentOredrnos[] = $val->order_no;
            }
        }
        //进行评价订单的筛选
        $orders = $orders->whereIn('order_no',$commentOredrnos)->paginate($this->page);
        //进行筛选
        
        foreach($orders as $order){
            $goodsinfo = OrderGood::whereOrderNo($order->order_no)->get();
            //取商品的封面
            foreach($goodsinfo as $info){
                $info->coverimg = GoodsImage::whereGoodsId($info->goods_id)->first()->name;
            }
            $order->goodsinfo     = $goodsinfo;
            $order->receiver_info = json_decode($order->receiver_info,true);
        }
        $suppliers = SupplierBase::all();
        return view('boss.comment.index',['orders'=>$orders,'suppliers'=>$suppliers,'order_no'=>$order_no,'start_time'=>$start_time,'end_time'=>$end_time,'supplier_id'=>$supplier_id]);
    }
    
    /**
     * 当前订单的评价详情
     * @param int $orderno 订单编号
     * @return Ambigous 
     */
    public function detail($orderno){
        $orderinfo = OrderBase::whereOrderNo($orderno)->first();
        $orderinfo->real_order_pay = $orderinfo->amount_goods + $orderinfo->amount_express - $orderinfo->amount_coupon;
        //获取订单状态的对应时间
        $order_log = OrderLog::whereOrderNo($orderno)->get()->toArray();
        //将下单时间压入到时间数组
        array_unshift($order_log, ['action'=>'买家下单','created_at'=>$orderinfo['created_at']]);
        $order_pay_finish = '';
        foreach($order_log as $log){
             if($log['action'] == '支付成功'){
                 $order_pay_finish = $log['created_at'];
             }  
        }
        $ordergoods= OrderGood::whereOrderNo($orderno)->select('id','is_gift')->get()->toArray();
        //对该商品是否是赠品进行区分
        //非赠品的id集合
        $notgiftids= array();
        //赠品的id集合
        $isgiftids = array();
        foreach($ordergoods as $val){
            if($val['is_gift'] == 0){
                $notgiftids[] = $val['id'];
            }else{
                $isgiftids[]  = $val['id']; 
            }
        }
        //所有当前订单下的商品的信息
        $ordergoodsinfo = OrderGood::whereIn('id',$notgiftids)->get();
        //所有当前订单下的赠品的信息
        $goodsgiftinfo  = OrderGood::whereIn('id',$isgiftids)->get();
        $receiver_info  = json_decode($orderinfo->receiver_info,true);
        //收获地址
        $orderinfo->addr= $receiver_info['name'].' '.$receiver_info['mobile'].' '.$receiver_info['province'].$receiver_info['city'].$receiver_info['district'].$receiver_info['address'];
        //dd($orderinfo->addr);
        //进行评论的获取
        $comments = $this->getAllComments($orderno);
        //dd($comments);
        return view('boss.comment.detail',['orderinfo'=>$orderinfo,'ordergoodsinfo'=>$ordergoodsinfo,'goodsgiftinfo'=>$goodsgiftinfo,'order_pay_finish'=>$order_pay_finish,'order_log'=>$order_log,'comments'=>$comments]);
    }
    
    /**
     * 进行特定条件的筛选
     * @param object $orders    全部完成订单的资源
     * @param string $order_no  订单号
     * @param string $start_time开始时间
     * @param string $end_time  结束时间
     * @param int $supplier_id  供应商id
     */
    public function filtrate($orders,$order_no,$start_time,$end_time,$supplier_id){
        if($order_no != ''){
            $orders->where('order_no',$order_no);
        }
        if($start_time != ''){
            $orders->where('created_at','>',$start_time);
        }
        if($end_time != ''){
            $orders->where('created_at','<',$end_time);
        }
        if($start_time != '' && $end_time != ''){
            $orders->whereBetween('created_at',[$start_time,$end_time]);
        }
        if($supplier_id != 0){
            $orders->where('supplier_id',$supplier_id);
        }
        
        return $orders;
    }
    
    /**
     * 获取当前订单里面所有商品评论的方法
     * @param string $orderno 订单号
     * @return unknown
     */
    public function getAllComments($orderno){
        $orderGoods = OrderGood::whereOrderNo($orderno)->where('is_gift','<>',1)->get();
        foreach($orderGoods as $good){
            //取出当前商品评价
            $comment = CommentGood::whereGoodsId($good->goods_id)->whereOrderNo($orderno)->whereSpecId($good->spec_id)->first();
            if(empty($comment)){
                return '';
            }
            $commment_img = CommentGoodsImage::whereCommentId($comment->id)->select('image_name')->get();
            $good->comment= $comment;
            $good->commment_img= $commment_img;
        }
        //dd($orderGoods);
        return $orderGoods;
    } 
    
    //进行状态的改变
    public function changeState(Request $request){
        $commentid = $request->input('commentid'); 
        //线充数据库里面取出来对state进行判断
        $state = CommentGood::whereId($commentid)->first()->state;
        if($state == 1){
            $state = 2;
        }else{
            $state = 1;
        }
        $bool = CommentGood::whereId($commentid)->update(['state'=>$state]);
        if($bool){
            return response()->json(['ret'=>'yes','state'=>$state,'msg'=>'修改成功']);
        }else{
            return response()->json(['ret'=>'no','msg'=>'修改失败']);
        }

    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}