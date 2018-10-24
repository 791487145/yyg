<?php

namespace App\Http\Controllers\Wx;

use App\Models\CouponBase;
use App\Models\CouponGood;
use App\Models\CouponUser;
use App\Models\OrderExpress;
use App\Models\UserCart;
use Log;
use Cookie;
use Qiniu\Auth;
use App\Models\UserWx;
use App\Models\TaBase;
use App\Models\OrderLog;
use App\Models\UserBase;
use App\Models\GoodsBase;
use App\Models\GoodsGift;
use App\Models\GuideBase;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\GoodsSpec;
use App\Models\TaBilling;
use App\Models\ConfExpress;
use App\Models\OrderReturn;
use App\Models\SupplierBase;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use App\Models\GuideBilling;
use App\Models\OrderReturnLog;
use App\Models\SupplierBilling;
use App\Models\PlatformBilling;
use App\Models\OrderReturnImage;
use Qiniu\Storage\UploadManager;
use Illuminate\Support\Facades\Input;
use App\Models\CommentGood;
use App\Models\CommentGoodsImage;
use DB;

class MineController extends WxController
{
    const num = 999;
    
    /**
     * 加载已完成订单列表
     * @return Ambigous
     */
    public function goodsCommentList(){
        $open_id = Cookie::get('openid');
        $uid     = UserWx::whereOpenId($open_id)->select(['uid'])->first();
        $orders  = OrderBase::whereUid($uid->uid)->where('state',OrderBase::STATE_FINISHED)->orderBy('id','desc')->get();
        foreach ($orders as $order) {
            //取订单商品
            $orderGoods = OrderGood::whereIsGift(0)->whereOrderNo($order->order_no)->get();
            //取订单退款状态
            $order->returnOrder = OrderReturn::whereOrderNo($order->order_no)->select(['id'])->get()->toArray();
            //商品数
            $order->goodsCount = $orderGoods->count();
            //商品总价
            $order->priceSum = $order->amount_goods+$order->amount_express-$order->amount_coupon;
            //赠品170719152220078  $order->order_no
            $goodsGifts = OrderGood::whereOrderNo($order->order_no)->whereIsGift(1)->get();
            $goodsGiftsNum = OrderGood::whereOrderNo($order->order_no)->whereIsGift(1)->count();
        
            if(!$goodsGifts->isEmpty()){
                $tmp =array();
                foreach ($goodsGifts as $goodsGift) {
                    $goodGiftDetails = GoodsBase::whereId($goodsGift->goods_id)->first();
                    $goodsSpec = GoodsSpec::whereId($goodsGift->spec_id)->first();
                    if (!empty($goodGiftDetails)) {
                        $goodGiftDetails->cover_image = $goodGiftDetails->first_image;
                        $goodGiftDetails->spec_name   = (isset($goodsSpec->name) ? $goodsSpec->name : '无');
                        $tmp[] = array(
                            'title'      => $goodGiftDetails->title,
                            'id'         => $goodGiftDetails->id,//商品的id
                            'spec_name'  =>$goodGiftDetails->spec_name,
                            'price'      => $goodsGift->price,
                            'num'        => $goodsGift->num,
                            'cover_image'=>$goodGiftDetails->cover_image,
                            'order_no'   =>$goodsGift->order_no,
                            'spec_id'    =>$goodsGift->spec_id,
                            
                        );
                    }
                }
                $order->gift = $tmp;
            }
            
            if($order->express_type == 1){
                $order->pay_way = '自提';
            }elseif($order->amount_express == 0){
                $order->pay_way = '包邮';
            }else{
                $order->pay_way = '含运费'.$order->amount_express;
            }
            $data = [];
            foreach ($orderGoods as $goods) {
                //dd($goods);
                //取商品详情
                $goodsData = GoodsBase::whereId($goods->goods_id)->first();
                $order->goodsCount = $order->goodsCount + $goodsGiftsNum;
                $goodsData->cover_image = $goodsData->first_image;
                $data[] = ['goods' => $goodsData,  'orderGoods' => $goods];
            }
            //获取供应商信息
            $order->supplier = SupplierBase::whereId($order->supplier_id)->select(['store_name'])->first();
            $order->data     = $data;
            //$order->hascomment  = CommentGood::whereOrderNo($order->order_no)->whereGoodsId($order->goods_id)->whereSpecId($order->spec_id)->first();
            $flag = CommentGood::whereOrderNo($order->order_no)->first();
            $flag = empty($flag)?'':1;
            $order->hascomment = $flag;
        }
        $count = $this->count($open_id);
        //dd($orders);
        return view('wx.mine.goodscommentlist',['orders' => $orders,'count'=>$count]);
    }
    
    /**
     * 加载商品评论的页面
     * @param int $orderno 订单号
     * @return Ambigous 
     */
    public function goodsComment($orderno){
        //获取供养上的id
        $supplier_id= OrderBase::whereOrderNo($orderno)->first()->supplier_id;
        $store_name = SupplierBase::whereId($supplier_id)->first()->store_name;
        $ordergoods = OrderGood::whereOrderNo($orderno)->where('is_gift','<>',1)->get();
        foreach($ordergoods as $good){
            //添加封面图片
            $good->coverimg = GoodsBase::whereId($good->goods_id)->first()->first_image;
            $good->uid      = OrderBase::whereOrderNo($good->order_no)->first()->uid;
        }
        //dd($ordergoods);
        return view('wx.mine.goodscomment',['ordergoods'=>$ordergoods,'store_name'=>$store_name]);
    }
    
    /**
     * 保存评论的方法
     * @param Request $request
     */
    public function saveComment(Request $request){
        $params = $request->all();
        $content= $params['content'];
        //判断
        foreach($content as $con){
            if(empty($con)){
                return false;
            }
        }
        //判断
        foreach($content as $key=>$val){
            $data = $params['comment'.$key];
            $data['comment'] = $val;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = $data['created_at'];
            $comment_id      = CommentGood::insertGetId($data);
            $imagekey        = 'image'.$key;
            if(array_key_exists($imagekey, $params)){
                foreach($params[$imagekey] as $val){
                    CommentGoodsImage::create(['comment_id'=>$comment_id,'image_name'=>$val]);
                }
            }
        }
        return redirect('/mine/list');
    }

    /**
     * 加载已经评论过的商品的列表
     * @param int $orderno 订单号
     * @return Ambigous 
     */
    public function goodsCommentDetail($orderno){
        $ordergoods = OrderGood::whereOrderNo($orderno)->where('is_gift','<>',1)->get();
        foreach($ordergoods as $good){
            $goodsData = GoodsBase::whereId($good->goods_id)->first();
            $good->cover_image = $goodsData->first_image;
            $comments  = CommentGood::whereOrderNo($good->order_no)->whereGoodsId($good->goods_id)->whereSpecId($good->spec_id)->first();
            $good->comments = $comments;
            $commentimage  = CommentGoodsImage::whereCommentId($comments->id)->select('image_name')->get()->toArray();
            $good->images  = $commentimage;
        }
        //dd($ordergoods);
        return view('wx.mine.goodscommentdetail',['ordergoods'=>$ordergoods]);
    }
    
    
    //个人中心
    public function Mine(Request $request)
    {
        if(env('APP_ENV') == 'local'){
            $open_id = 'oY1sE1F30sAlbsa1vnSlDU8Jhh6A';
        }else{
            $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
        }
        if($open_id != ''){
            Cookie::queue('openid',$open_id);
        }
        $uid = UserWx::whereOpenId($open_id)->first();
        //dd($uid);
        $couponNum = CouponUser::whereOpenId($open_id)->whereState(CouponUser::state_unused)->count();
        if($uid->uid != 0){
            $couponNum = CouponUser::whereUid($uid->uid)->whereState(CouponUser::state_unused)->count();
        }

        $UserInfo = UserWx::whereUid($uid->uid)->first();
        $UserName = UserBase::whereId($uid->uid)->first();
        $state = 3;
        $count = $this->count($open_id);

        return view('wx.mine.mine', ['userinfo' => $UserInfo, 'username' => $UserName,'state'=>$state,'count'=>$count,'couponNum'=>$couponNum]);
    }

    //用户收藏商品列表
    public function MineCollection()
    {
        $open_id = Cookie::get('openid');
        $pageNum = 0;
        $GoodsLists = $this->collectLimit($open_id,$pageNum);
        return view('wx.mine.collection', ['goodslists' => $GoodsLists]);
    }


    private function collectLimit($open_id,$pageNum)
    {
        $offset = $pageNum * (self::page);
        $GoodsId = UserFavorite::where('open_id',$open_id)->offset($offset)->limit(self::page)->lists('goods_id');
        $GoodsLists = GoodsBase::wherein('id', $GoodsId)->get();

        if(!$GoodsLists->isEmpty()){
            $GoodsLists = GoodsSpec::goodsSpecPriceCartNum($GoodsLists,$open_id);
        }

        return $GoodsLists;
    }


    public function goodCollectListPage(Request $request)
    {
        $open_id = Cookie::get('openid');
        $pageNum = $request->input('pageNum',1);
        $GoodsLists = $this->collectLimit($open_id,$pageNum);
        if($GoodsLists->isEmpty()){
            return response()->json(['ret' => 'no']);
        }
        return response()->json(['GoodBases' => $GoodsLists,'page_num'=>$pageNum]);
    }

    //用户设置
    public function SettIng()
    {
        $state = 3;
        $open_id = Cookie::get('openid');
        $count = $this->count($open_id);
        $uid = UserWx::whereOpenId($open_id)->first();
        $UserName = UserBase::whereId($uid->uid)->first();

        return view('wx.mine.setting', ['username' => $UserName,'state' => $state,'count'=>$count]);
    }


    //保存修改
    public function SaveChanges()
    {
        $open_id = Cookie::get('openid');
        $uid = UserWx::whereOpenId($open_id)->first();
        $nick_name = Input::get('nick_name');
        $SaveNickName = new UserBase();
        $Name = $SaveNickName->whereId($uid->uid)->first();
        if ($Name['nick_name'] == $nick_name) {
            return $uid->uid;
        } else {
            $SaveNickName->whereId($uid->uid)->update(['nick_name' => $nick_name]);
        }
    }

    //个人订单
    /*
     * $uid 用户uid
     * */
    public function Order(Request $request, $state = self::num)
    {
        $open_id = Cookie::get('openid');
        $uid = UserWx::whereOpenId($open_id)->select(['uid'])->first();
        //异常补充逻辑
        if(is_null($uid)){
            if(env('APP_ENV') == 'local'){
                $open_id = 'o1-zuw6uMAPVZB5Oc-uQUcBiQw-Q';
            }else{
                $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
            }
            if($open_id != ''){
                Cookie::queue('openid',$open_id);
            }
            $uid = UserWx::whereOpenId($open_id)->select(['uid'])->first();
        }
        //$uid->uid = 24;
        //判断订单状态
        if($state == self::num){
            $orders = OrderBase::whereUid($uid->uid)->whereIn('state',[OrderBase::STATE_NO_PAY,OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->orderBy('id','desc')->get() ;
        }else{
            $orders = OrderBase::whereUid($uid->uid)->whereState($state)->orderBy('id','desc')->get();
        }
        foreach ($orders as $order) {
            //取订单商品
            $orderGoods = OrderGood::whereIsGift(0)->whereOrderNo($order->order_no)->get();
            //取订单退款状态
            //$order->returnOrder = OrderReturn::whereOrderNo($order->order_no)->select(['id'])->get()->toArray();
            $order->returnOrder = OrderReturn::whereOrderNo($order->order_no)->orderBy('id','desc')->first();
            //商品数
            $order->goodsCount = $orderGoods->count();
            //商品总价
            $order->priceSum = $order->amount_goods+$order->amount_express-$order->amount_coupon;
            //赠品
            $goodsGifts = OrderGood::whereOrderNo($order->order_no)->whereIsGift(1)->get();
            $goodsGiftsNum = OrderGood::whereOrderNo($order->order_no)->whereIsGift(1)->count();

            if(!$goodsGifts->isEmpty()){
                $tmp =array();
                foreach ($goodsGifts as $goodsGift) {
                    $goodGiftDetails = GoodsBase::whereId($goodsGift->goods_id)->first();
                    $goodsSpec = GoodsSpec::whereId($goodsGift->spec_id)->first();
                    if (!empty($goodGiftDetails)) {
                        $goodGiftDetails->cover_image = $goodGiftDetails->first_image;
                        $goodGiftDetails->spec_name = (isset($goodsSpec->name) ? $goodsSpec->name : '无');
                        $tmp[] = array(
                            'title' => $goodGiftDetails->title,
                            'id' => $goodGiftDetails->id,
                            'spec_name'=>$goodGiftDetails->spec_name,
                            'price' => $goodsGift->price,
                            'num' => $goodsGift->num,
                            'cover_image'=>$goodGiftDetails->cover_image
                        );
                    }
                }
                $order->gift = $tmp;
            }

            if($order->express_type == 1){
                $order->pay_way = '自提';
            }elseif($order->amount_express == 0){
                $order->pay_way = '包邮';
            }else{
                $order->pay_way = '含运费'.$order->amount_express;
            }
            $data = [];
            foreach ($orderGoods as $goods) {
                //取商品详情
                $goodsData = GoodsBase::whereId($goods->goods_id)->first();
                $order->goodsCount = $order->goodsCount + $goodsGiftsNum;
                $goodsData->cover_image = $goodsData->first_image;
                $data[] = ['goods' => $goodsData,  'orderGoods' => $goods];
            }

            //获取供应商信息
            $order->supplier = SupplierBase::whereId($order->supplier_id)->select(['store_name'])->first();
            $order->data = $data;
        }
        $count = $this->count($open_id);
        //dd($orders);
        return view('wx.mine.order')->with(['orders' => $orders,'state' => $state,'count'=>$count]);
    }

    /*
     * 订单详情
     * $OrderNo 订单编号
     * */
    public function OrderPayDetail($OrderNo)
    {
        $OrderDetails =self::HandleOrder($OrderNo);

        foreach($OrderDetails as $orderDetail){
            $time = date('Y-m-d H:i:s');
            if(!empty($orderDetail->express_name)){
                $expressTel = ConfExpress::whereName($orderDetail->express_name)->first();
                if($expressTel){
                    $orderDetail->expressTel = $expressTel->tel;
                }
            }
            $orderExpress = OrderExpress::whereOrderNo($OrderNo)->get();
            if(!$orderExpress->isEmpty()){
                $orderDetail->express_more = $orderExpress;
            }
            if($orderDetail->state == OrderBase::STATE_NO_PAY){
                $num = floor((strtotime($time)-strtotime($orderDetail->created_at))/60);
                $orderDetail->receiveGoodTime = 30 - $num;
            }
            if($orderDetail->state == OrderBase::STATE_SEND){
                $num = floor((strtotime($time)-strtotime($orderDetail->express_time))/86400);
                $orderDetail->receiveGoodTime = 7 - $num;
            }
            $order_no = $orderDetail->order_no;
            $orderDetail->returnState = OrderReturn::whereOrderNo($order_no)->get()->toArray();

            if($orderDetail->express_type == 1){
                $orderDetail->pay_way = '自提';
            }elseif($orderDetail->amount_express == 0){
                $orderDetail->pay_way = '包邮';
            }else{
                $orderDetail->pay_way = '含运费'.$orderDetail->amount_express;
            }

        }
        return view('wx.mine.order_paydetail')->with(['orderdetails'=>$OrderDetails,'order_no'=>$order_no]);
    }

    //取消订单
    public function CancelOrder($order_no)
    {
        $Orderbases = new OrderBase();
        $Orderbases->whereOrderNo($order_no)->update(['state'=>OrderBase::STATE_CANCEL_USER]);
        $OrderBase = $Orderbases->whereOrderNo($order_no)->first();
        if($OrderBase->coupon_user_id > 0){
            CouponUser::whereId($OrderBase->coupon_user_id)->update(array('state'=>CouponUser::state_unused));
        }
        return response()->json(['ret'=>'yes']);
    }

    //文件上传
    public function upload(Request $request)
    {
        $uploadMgr = new UploadManager();
        $auth = new Auth(env('IMAGE_ACCESS_KEY'), env('IMAGE_SECRET_KEY'));
        $bucket = env('IMAGE_BUCKET');
        $upToken = $auth->uploadToken($bucket);//获取上传所需的token
        $filePath = $request->imgOne;
        $key = 'wx'.substr(md5($filePath), 10) . '.png';
        list($ret, $err) = $uploadMgr->putFile($upToken, $key, $filePath);
        if ($err !== null) {
        } else {
            $picName = $key;
            return $picName;
        }
    }
    //申请售后
    public function AfterSales($orderNo)
    {
        $orderDetails =self::HandleOrder($orderNo);
        foreach($orderDetails as $orderDetail){
            if($orderDetail->express_type == 1){
                $orderDetail->pay_way = '自提';
            }elseif($orderDetail->amount_express == 0){
                $orderDetail->pay_way = '包邮';
            }else{
                $orderDetail->pay_way = '含运费'.$orderDetail->amount_express;
            }
        }
        return view('wx.mine.aftersales')->with(['orderdetails'=>$orderDetails]);
    }

    public function AfterSalesState(Request $request)
    {
        $order_no = $request->input('order_no',0);
        $orderReturns = OrderReturn::whereOrderNo($order_no)->orderBy('id','desc')->first();
        if(is_null($orderReturns) || $orderReturns->state == 4){
            return response()->json(['ret'=>'yes','msg'=>'提交成功']);
        }
        return response()->json(['ret'=>'no','msg'=>'重复提交']);
    }

    //处理售后申请
    public function handleAfterSales($orderNo){
        $orderReturn = new OrderReturn();
        $orderReturnLog = new OrderReturnLog();
        $returnNo = "T".$orderNo;
        $content = empty(Input::get('content')) ? '' : Input::get('content');
        $data = ['content'=>urlencode($content)];
        $dataJson = urldecode(json_encode($data));
        $images = Input::get('image');
        $orderDetails = self::HandleOrder($orderNo);
        foreach($orderDetails as $orderDetail){
            //order_return表插入数据
            $orderReturn->order_no = $orderNo;
            $orderReturn->uid = $orderDetail->uid;
            $orderReturn->supplier_id = $orderDetail->supplier_id;
            $orderReturn->receiver_name = $orderDetail->receiver_name;
            $orderReturn->receiver_mobile = $orderDetail->receiver_mobile;
            $orderReturn->return_no = $returnNo;
            $orderReturn->amount = $orderDetail->amount;
            $orderReturn->return_content = $content;
            $orderReturn->state = 0;
            $orderReturn->save();

            //order_return_log写入日志
            $orderReturnLog->return_no = $returnNo;
            $orderReturnLog->uid = $orderDetail->uid;
            $orderReturnLog->action = "退款申请";
            $orderReturnLog->content = $dataJson;
            $orderReturnLog->save();
        }
        $orderReturn = $orderReturn->whereOrderNo($orderNo)->orderBy('id','desc')->first();
        //order_return_image表插入数据
        if(!empty($images)){
            foreach($images as $image) {
                $orderReturnImages = new OrderReturnImage();
                $orderReturnImages->return_id = $orderReturn->id;
                $orderReturnImages->name = $image;
                $orderReturnImages->save();
            }
        }
        return view('wx.mine.aftersales-success')->with(['orderno'=>$orderNo]);

    }

    //申请售后列表
    public function afterSalesList($orderNo){
        $orderDetails = self::HandleOrder($orderNo);
        //dd($orderDetails);
        foreach($orderDetails as $orderDetail){
            if($orderDetail->express_type == 1){
                $orderDetail->pay_way = '自提';
            }elseif($orderDetail->amount_express == 0){
                $orderDetail->pay_way = '包邮';
            }else{
                $orderDetail->pay_way = '含运费'.$orderDetail->amount_express;
            }
            $orderDetail->returnState = OrderReturn::whereOrderNo($orderNo)->first();
        }
        return view('wx.mine.aftersaleslist')->with(['orderdetails'=>$orderDetails]);
    }

    //售后详情
    public function afterSalesDetail($orderNo)
    {
        $orderReturnInfoes = OrderReturn::whereOrderNo($orderNo)->orderBy('id','asc')->get();
        //dd($orderReturnInfoes);
        $checkout = 0;
        $aplyReturn = 0;
        foreach($orderReturnInfoes as $k => $orderReturnInfo){
            switch ($orderReturnInfo->state) {//处理时间
                case '0':
                    $RET = "退款申请";
                    break;
                case '1':
                    $RET = "审核通过";
                    break;
                case '3':
                    $RET = "成功退款";
                    break;
                case '4':
                    $RET = "审核驳回";
                    break;
            }

            $param = OrderReturnLog::whereReturnNo($orderReturnInfo->return_no)->whereAction($RET)->orderBy('id','asc')->get();

            if($RET == "审核驳回"){
                if(isset($param[$checkout])){
                    $param = json_decode($param[$checkout]->content,true);
                }else{
                    $param['content'] = '暂无原因';
                }
                $orderReturnInfo->return_reason = $param['content'];
                $checkout = $checkout +1;
            }

            if($RET == "退款申请"){
                $param = json_decode($param[$aplyReturn]->content,true);
                $orderReturnInfo->return_reason = $param['content'];
                $aplyReturn = $aplyReturn +1;
            }

            $orderReturnInfo->return_content = $orderReturnInfo->return_content;
            $orderReturnInfo->ReturnImgs = OrderReturnImage::whereReturnId($orderReturnInfo->id)->get();
            //dd($orderReturnInfo);

        }
        //dd($orderReturnInfoes);
        return view('wx.mine.aftersales-detail')->with(['orderreturninfos'=>$orderReturnInfoes]);
    }

    //售后退款
    public function afterSalesRefund(Request $request)
    {
        $open_id = Cookie::get('openid');
        //补充逻辑
        if(is_null($open_id)){
            if(env('APP_ENV') == 'local'){
                $open_id = 'o1-zuw6uMAPVZB5Oc-uQUcBiQw-Q';
            }else{
                $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
            }
            if($open_id != ''){
                Cookie::queue('openid',$open_id);
            }
        }
        //Log::alert('$open_id'.$open_id);
        $uid = UserWx::whereOpenId($open_id)->first();
        //$uid->uid = 24;
        $orderNoes = OrderReturn::whereUid($uid->uid)->distinct('order_no')->orderBy('id','desc')->get()->toArray();
        //dd($orderNoes);
        $tmp = array();

        if(empty($orderNoes)){
            return view('wx.mine.saleOrder_null');
        }else {
            foreach ($orderNoes as $orderNo) {
                $state = 0;
                if(!is_null($tmp)){
                    foreach($tmp as $t){
                        if($t == $orderNo['order_no']){
                            $state = 1;
                            break;
                        }
                    }
                }
                if($state == 1){
                    continue;
                }
                $tmp[] = $orderNo['order_no'];
                $orderDetails = self::HandleOrder($orderNo['order_no']);
                foreach($orderDetails as $orderDetail){
                    if($orderDetail->express_type == 1){
                        $orderDetail->pay_way = '自提';
                    }elseif($orderDetail->amount_express == 0){
                        $orderDetail->pay_way = '包邮';
                    }else{
                        $orderDetail->pay_way = '含运费'.$orderDetail->amount_express;
                    }
                }
                $items[] = $orderDetails;
            }
            return view('wx.mine.aftersaleslist')->with(['items' => $items, 'ordernoes' => $orderNoes]);
        }
    }
    
    //确认收货
    public function finishedOrder($order_no){
        $uid = Input::get('uid');
        $OrderBase = OrderBase::whereOrderNo($order_no)->whereUid($uid)->first();
        $OrderReturnNum = OrderReturn::whereOrderNo($order_no)->whereIn('state',array(OrderReturn::STATE_NO_CHECK,OrderReturn::STATE_NO_REFUND))->count();

        if($OrderReturnNum > 0){
            $result = array('ret' => self::RET_FAIL, 'msg' => '订单售后正在处理中，暂不能确认收货', 'data' => array('order_no'=>strval($order_no)));
            return response()->json($result);
        }
        if(isset($OrderBase['state']) && $OrderBase['state'] != OrderBase::STATE_SEND){
            $result = array('ret' => self::RET_FAIL, 'msg' => '订单状态不正确', 'data' => array('order_no'=>strval($order_no)));
            return response()->json($result);
        }
        if(!is_null($OrderBase) && $OrderBase->state == OrderBase::STATE_SEND){

            //更新订单状态
            OrderBase::whereId($OrderBase['id'])->update(['state'=>OrderBase::STATE_FINISHED]);

            //导游分成到账
            $GuideBilling = GuideBilling::whereOrderNo($OrderBase['order_no'])->first();
            if(!empty($GuideBilling)){
                $GuideBilling->state = GuideBilling::state_fund;
                $GuideBilling->save();

                $GuideBase = GuideBase::whereId($GuideBilling['guide_id'])->first();
                $UserBase = UserBase::whereId($GuideBase['uid'])->first();
                $amount = $GuideBilling->amount - $GuideBilling->return_amount;
                if ($amount > 0 && !is_null($UserBase)) {
                    $UserBase->amount = $UserBase->amount + $amount;
                    $UserBase->save();
                }
            }

            //旅行社分成到账
            $TaBilling = TaBilling::whereOrderNo($OrderBase['order_no'])->first();
            if(!empty($TaBilling) && $TaBilling->ta_id != 0) {
                $TaBilling->state = TaBilling::state_fund;
                $TaBilling->save();
                $TaBase = TaBase::whereId($TaBilling['ta_id'])->first();
                $amount = $TaBilling->amount - $TaBilling->return_amount;

                if ($amount > 0 && !is_null($TaBase)) {
                    $TaBase->amount = $TaBase->amount + $amount;
                    $TaBase->save();
                }
            }

            //供应商到账
            $SupplierBilling = SupplierBilling::whereOrderNo($OrderBase['order_no'])->first();
            $SupplierBilling->state = TaBilling::state_fund;
            $SupplierBilling->save();

            $SupplierBase = SupplierBase::whereId($SupplierBilling['supplier_id'])->first();
            $amount = $SupplierBilling->amount - $SupplierBilling->return_amount;
            if($amount > 0 && !is_null($SupplierBase)) {
                $SupplierBase->amount = $SupplierBase->amount + $amount;
                $SupplierBase->save();
            }

            //平台到账
            $PlatformBilling = PlatformBilling::whereOrderNo($OrderBase['order_no'])->first();
            $PlatformBilling->state = TaBilling::state_fund;
            $PlatformBilling->save();

            $OrderLog = new OrderLog();
            $OrderLog->action = '用户完成订单';
            $OrderLog->order_no = $OrderBase['order_no'];
            $OrderLog->content  = json_encode(array('before_state'=>OrderBase::STATE_SEND,'after_state'=>OrderBase::STATE_FINISHED));
            $OrderLog->save();
            return response()->json(['ret'=>'yes']);
        }
    }


    //处理订单内容函数
    public function HandleOrder($OrderNo){
        $OrderDetails = OrderBase::whereOrderNo($OrderNo)->get();
        foreach($OrderDetails as $orderDetail){
            //获取供应商信息
            $Supplierinfos = SupplierBase::whereId($orderDetail->supplier_id)->get();
            $orderDetail->supplierinfo = $Supplierinfos;
            //获取地址
            $address = $orderDetail->receiver_info;
            $address = json_decode($address,true);
            $orderDetail->receiver_info = $address;
            //订单商品
            $OrderGoods = OrderGood::whereIsGift(0)->whereOrderNo($orderDetail->order_no)->get();
            //商品数
            $orderDetail->goodsnum = $OrderGoods->count();
            //订单价格
            $orderDetail->sumprice = $orderDetail->amount_goods + $orderDetail->amount_express-$orderDetail->amount_coupon;
            //赠品
            $goodsGifts = OrderGood::whereOrderNo($OrderNo)->whereIsGift(1)->get();
            $goodsGiftsNum = OrderGood::whereOrderNo($OrderNo)->whereIsGift(1)->count();
            $orderDetail->goodsnum = $orderDetail->goodsnum + $goodsGiftsNum;

            if(!$goodsGifts->isEmpty()){
                $tmp =array();
                foreach ($goodsGifts as $goodsGift) {
                    $goodGiftDetails = GoodsBase::whereId($goodsGift->goods_id)->first();
                    $goodsSpec = GoodsSpec::whereId($goodsGift->spec_id)->first();
                    if (!empty($goodGiftDetails)) {
                        $goodGiftDetails->cover_image = $goodGiftDetails->first_image;
                        $goodGiftDetails->spec_name = $goodsGift->spec_name;
                        $tmp[] = array(
                            'title' => $goodGiftDetails->title,
                            'id' => $goodGiftDetails->id,
                            'price' => $goodsGift->price,
                            'num' => $goodsGift->num,
                            'spec_name'=>$goodGiftDetails->spec_name,
                            'cover_image'=>$goodGiftDetails->cover_image);
                    }
                }
                $orderDetail->gift = $tmp;
            }
            $data = [];
            foreach($OrderGoods as $orderGood){
                //商品详情
                $GoodsBases = GoodsBase::whereId($orderGood->goods_id)->first();
                $GoodsBases->cover_image = $GoodsBases->first_image;
                //商品规格
                /*$GoodsSpecs = GoodsSpec::whereId($orderGood->spec_id)->first();
                $GoodsSpecs->price = $orderGood->price;*/
                $data[] = ['goodsbase'=>$GoodsBases,'goodsinfo'=>$orderGood];
            }
            $orderDetail->data = $data;
        }
        return $OrderDetails;

    }

    public function MineCollectionDel(Request $request)
    {
        $id = $request->input('id');
        $open_id = Cookie::get('openid');
        UserFavorite::whereOpenId($open_id)->whereGoodsId($id)->delete();
        return response()->json(['ret'=>'yes']);
    }

    //优惠券
    public function MineCoupon($state)
    {
        $open_id = Cookie::get('openid');
        $uid = UserWx::whereOpenId($open_id)->pluck('uid');
        if($uid != 0){
            if($state == 1){
                $CouponUsers = CouponUser::whereUid($uid)->whereState($state)->orderBy('used_time','desc')->get();
            }else{
                $CouponUsers = CouponUser::whereUid($uid)->whereState($state)->orderBy('id','desc')->get();
            }
            $CouponUsersNum = array(
                '0'=>CouponUser::whereUid($uid)->whereState(CouponUser::state_unused)->count(),
                '1'=>CouponUser::whereUid($uid)->whereState(CouponUser::state_used)->count(),
                '2'=>CouponUser::whereUid($uid)->whereState(CouponUser::state_expired)->count(),
            ) ;
        }else{
            if($state == 1){
                $CouponUsers = CouponUser::whereOpenId($open_id)->whereState($state)->orderBy('used_time','desc')->get();
            }else{
                $CouponUsers = CouponUser::whereOpenId($open_id)->whereState($state)->orderBy('id','desc')->get();
            }
            $CouponUsersNum = array(
                '0'=>CouponUser::whereOpenId($open_id)->whereState(CouponUser::state_unused)->count(),
                '1'=>CouponUser::whereOpenId($open_id)->whereState(CouponUser::state_used)->count(),
                '2'=>CouponUser::whereOpenId($open_id)->whereState(CouponUser::state_expired)->count(),
            ) ;
        }

        return view('wx.mine.myCoupon',compact('CouponUsers','CouponUsersNum','state'));
    }

    public function CouponGoods($id)
    {
        $open_id = Cookie::get('openid');
        $supplierId = CouponBase::whereId($id)->pluck('supplier_id');
        //dd($id);
        $CouponGoodsId = CouponGood::whereState(CouponGood::STATE_NORMAL)->whereSupplierId($supplierId)->lists('goods_id');
        $GoodsBases = GoodsBase::whereIn('id',$CouponGoodsId)->whereState(GoodsBase::state_online)->get();
        if(!$GoodsBases->isEmpty()){
            $GoodsBases = GoodsSpec::goodsSpecPriceCartNum($GoodsBases,$open_id);
        }
        return view('wx.mine.couponGoods',compact('GoodsBases'));
    }

    public function couponGiven(Request $request)
    {
        $open_id = $request->input('open_id');
        $couponId = explode(",",env('COUPON_ID'));
        $CouponBases = CouponBase::whereIn('id',$couponId)->get();
        $userWx = UserWx::whereOpenId($open_id)->first();
        $CouponGoodsBases = array();
        foreach($CouponBases as $couponBase){
            $count = CouponUser::whereOpenId($open_id)->whereCouponId($couponBase->id)->whereSupplierId($couponBase->supplier_id)->count();
            if($count == 0) {
                $couponUser = new CouponUser();
                $couponUser->open_id = $open_id;
                $couponUser->uid = empty($userWx->uid) ? 0 : $userWx->uid;
                $couponUser->supplier_id = $couponBase->supplier_id;
                $couponUser->coupon_id = $couponBase->id;
                $couponUser->title = $couponBase->title;
                $couponUser->amount_order = $couponBase->amount_order;
                $couponUser->amount_coupon = $couponBase->amount_coupon;
                $couponUser->start_time = $couponBase->start_time;
                $couponUser->end_time = $couponBase->end_time;
                $couponUser->state = 0;
                $couponUser->save();
            }
            $CouponGoodsId = CouponGood::whereState(CouponGood::STATE_NORMAL)->whereSupplierId($couponBase->supplier_id)->lists('goods_id');
            $GoodsBases = GoodsBase::whereIn('id',$CouponGoodsId)->whereState(GoodsBase::state_online)->get();
            if(!$GoodsBases->isEmpty()){
                $GoodsBases = GoodsSpec::goodsSpecPriceCartNum($GoodsBases,$open_id);
                $CouponGoodsBases[$couponBase->supplier_id] = $GoodsBases;
            }

        }

        //Log::alert('$CouponGoodsBases'.print_r($CouponGoodsBases,true));

        $CouponUsers = CouponUser::whereOpenId($open_id)->get();
        $num = count($CouponUsers);
        return view('wx.mine.coupon_given',compact('CouponUsers','CouponGoodsBases','num'));
    }



}
