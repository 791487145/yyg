<?php
 /**
* @date: 2017-8-18 下午6:01:18
* @description: 供应商评价控制器
* @author: LHW
*/
namespace App\Http\Controllers\Store;
use App\Models\OrderGood;
use App\Models\OrderBase;
use Qiniu\json_decode;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use Illuminate\Http\Request;
use App\Models\GoodsImage;
use App\Models\SupplierBase;
use App\Models\OrderLog;
use App\Models\CommentGood;
use App\Models\CommentGoodsImage;
class CommentController extends StoreController{
    protected $page = 10;
    /**
     * 加载完成订单
     * @return Ambigous 
     */
    public function index(Request $request){
        $goods_name = trim($request->input('goods_name',''));
        $order_no   = trim($request->input('order_no',''));
        $start_time = $request->input('start_time','');
        $end_time   = $request->input('end_time','');
        $mobile     = trim($request->input('mobile',''));
        //进行筛选的程序
        $orders = OrderBase::whereState(OrderBase::STATE_FINISHED)->whereSupplier_id($this->user['id'])->orderBy('created_at','desc');
        $orders = $this->filtrate($orders,$goods_name,$order_no,$start_time,$end_time,$mobile);
        //进行筛选的程序
        
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
        
        //dd($commentOredrnos);筛选出已评价的订单
        $orders = $orders->whereIn('order_no',$commentOredrnos)->paginate($this->page);
        foreach($orders as $order){
            $goodsinfo = OrderGood::whereOrderNo($order->order_no)->get();
            //取商品的封面
            foreach($goodsinfo as $info){
                $info->coverimg  = GoodsImage::whereGoodsId($info->goods_id)->first()->name;
            }
            $order->goodsinfo     = $goodsinfo;
            $order->receiver_info = json_decode($order->receiver_info,true);
            //订单是否评价进行判断
            $flag = CommentGood::whereOrderNo($order->order_no)->get()->toArray(); 
            $order->flag = empty($flag)?0:1; 
        }
        //dd($orders);
        return view('store.comment.index',['orders'=>$orders,'goods_name'=>$goods_name,'order_no'=>$order_no,'start_time'=>$start_time,'end_time'=>$end_time,'mobile'=>$mobile]);
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
        
        //获取商品的评论开始
        $goodsComments  = $this->getAllComments($orderno);
        //dd($goodsComments);
        //获取商品的评论结束
        
        return view('store.comment.detail',['orderinfo'=>$orderinfo,'ordergoodsinfo'=>$ordergoodsinfo,'goodsgiftinfo'=>$goodsgiftinfo,'order_pay_finish'=>$order_pay_finish,'order_log'=>$order_log,'goodsComments'=>$goodsComments]);
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
            //dd(empty($comment));
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
    
   /**
    * 回复评论
    * @return \Illuminate\Http\JsonResponse
    */
    public function commentReply(Request $request){
        $commentid = $request->input('commitid');
        $reply_val = $request->input('reply_val');
        $bool = CommentGood::whereId($commentid)->update(['reply_comment'=>$reply_val]);
        if($bool){
            return response()->json(['ret'=>'yes','msg'=>'回复成功']);
        }else{
            return response()->json(['ret'=>'no','msg'=>'回复失败']);
        }
    }
    
    /**
     * 进行筛选的函数
     * @param object $orders 当前供应商的已完成的订单的对象
     * @param string $goods_name 商品名称
     * @param string $order_no   订单号
     * @param string $start_time 开始时间
     * @param string $end_time   结束时间
     * @param int $mobile        手机号
     */
    public function filtrate($orders,$goods_name,$order_no,$start_time,$end_time,$mobile){
        if($goods_name != ''){
            $order_nos = OrderGood::where('goods_title','like','%'.$goods_name.'%')->select('order_no')->distinct()->get()->toArray();
            $arrayorders = OrderBase::whereState(OrderBase::STATE_FINISHED)->whereSupplier_id($this->user['id'])->orderBy('created_at','desc')->select('order_no')->get()->toArray();
            $arrayorder  = array();
            foreach($arrayorders as $val){
                $arrayorder[] = $val['order_no'];
            }
            $trueordernos = array();
            foreach($order_nos as $v){
                if(in_array($v['order_no'],$arrayorder)){
                    $trueordernos[] = $v['order_no'];
                }
            }
            $orders->whereIn('order_no',$trueordernos);   
        }
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
        if($mobile != ''){
            $orders->where('receiver_mobile',$mobile);
        }
        return $orders;
    }
    
    
    
    public function getComment($id){
        $order_no = $id;
        //$order_no = $request->input('order_no');
        if(empty($order_no)){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递订单号']);
        }
        $ordergoods = OrderGood::whereOrderNo($order_no)->where('is_gift','<>',1)->get();
        foreach($ordergoods as $good){
            $goodsData = GoodsBase::whereId($good->goods_id)->first();
            $good->cover_image = $goodsData->first_image;
            $comments  = CommentGood::whereOrderNo($good->order_no)->whereGoodsId($good->goods_id)->whereSpecId($good->spec_id)->first();
            $good->comments = $comments;
            $commentimage  = CommentGoodsImage::whereCommentId($comments->id)->select('image_name')->get()->toArray();
            $good->images  = $commentimage;
        }
        $ordergoods = $ordergoods->toArray();
        return response()->json(['ret'=>self::RET_SUCCESS,'msg'=>'评论加载成功','data'=>$ordergoods]);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
