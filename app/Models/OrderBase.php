<?php

namespace App\Models;

use App\Console\Commands\Order;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderBase
 *
 * @property integer $id
 * @property integer $uid
 * @property string $supplier_id
 * @property string $order_no
 * @property float $amount_goods
 * @property float $amount_goods_origin
 * @property float $amount_express
 * @property integer $coupon_user_id
 * @property boolean $pay_type
 * @property string $pay_info
 * @property string $express_type
 * @property string $express_name
 * @property string $express_no
 * @property string $express_time
 * @property string $receiver_info
 * @property integer $ta_id
 * @property float $ta_amount
 * @property integer $guide_id
 * @property float $guide_amount
 * @property integer $group_id
 * @property string $buyer_message
 * @property boolean $state -1.删除订单, 0.待付款,1.待发货 2.待收货 5.已完成，11系统超时取消, 12用户主动取消, 13.客服关闭订单（缺货客服关闭，退钱
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereSupplier_id($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereOrderNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountGoods($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountGoodsOrigin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountExpress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase wherePayType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase wherePayInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereReceiverInfo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereTaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereTaAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereGuideId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereAmountReal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressNo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereExpressTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereReceiverName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereReceiverMobile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereHasGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereGuideAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereBuyerMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\OrderBase whereSupplierId($value)
 * @property integer //$uid 用户id
 * @property integer //$uid 用户id
 * @property float $amount_real
 * @property string $receiver_name 收货人姓名
 * @property string $receiver_mobile 收货人手机
 * @property boolean $has_gift 是否有赠品
 * @mixin \Eloquent
 */
class OrderBase extends Model
{
    protected $table = 'order_base';

    //-1.删除订单, 0.待付款,1.待发货 2.待收货 5.已完成，11系统超时取消, 12用户主动取消, 13.客服关闭订单（缺货客服关闭，退钱） 99.所有订单
    const STATE_DELETE = -1;
    const STATE_NO_PAY = 0;
    const STATE_PAYED = 1;
    const STATE_SEND = 2;
    const STATE_FINISHED = 5;
    const STATE_CANCEL_SYSTEM = 11;
    const STATE_CANCEL_USER = 12;
    const STATE_FAIL = 13;
    const STATE_TEST = 14;

    const STATE_ALL = 99;

    //1.支付宝，2.微信
    const PAY_TYPE_ALI  = 1;
    const PAY_TYPE_WX   = 2;
    const Pay_TYPE_WX_JS = 3;

    public $timestamps = true;

    protected $fillable = [
        'uid',
        'supplier_id',
        'coupon_user_id',
        'order_no',
        'amount_goods',
        'amount_goods_origin',
        'amount_coupon',
        'coupon_user_id',
        'amount_express',
        'pay_type',
        'pay_info',
        'express_type',
        'express_name',
        'express_time',
        'express_no',
        'receiver_info',
        'ta_id',
        'ta_amount',
        'guide_id',
        'guide_amount',
        'group_id',
        'buyer_message',
        'state',
        'remark'

    ];

    protected $guarded = [];

    static public function getStateCN($state){
        $stateArray = array(
            OrderBase::STATE_NO_PAY=> '待付款',
            OrderBase::STATE_PAYED=> '待发货',
            OrderBase::STATE_SEND=> '待收货',
            OrderBase::STATE_FINISHED=> '已完成',
            OrderBase::STATE_CANCEL_SYSTEM=> '已关闭',
            OrderBase::STATE_CANCEL_USER=> '已关闭',
        );
        return  isset($stateArray[$state]) ? $stateArray[$state] : '';
    }

    static public function getStateDescription($state,$created_at,$express_time){

        $minute = floor((($created_at+30*60) - time()) / 60 );
        $day = floor((($express_time+7*24*3600) - time()) / (24*3600));

        $stateArray = array(
            OrderBase::STATE_NO_PAY=> '剩余'.$minute.'分钟自动关闭',
            OrderBase::STATE_PAYED=> '等待卖家发货',
            OrderBase::STATE_SEND=> '剩余'.$day.'天自动确定收货',
            OrderBase::STATE_FINISHED=> '期待下次光临',
            OrderBase::STATE_CANCEL_SYSTEM=> '期待下次光临',
            OrderBase::STATE_CANCEL_USER=> '期待下次光临',
        );
        return  isset($stateArray[$state]) ? $stateArray[$state] : '';
    }

    static function getList($orderBase,$option=[],$offset = 20){
        $orders = [];

        if (!empty($option)){
            extract($option);
            //根据下单时间
            if(!empty($timeStart) && !empty($timeEnd)){
                $orderBase = $orderBase->whereBetween('created_at', [$timeStart, $timeEnd]);
            }
            //根据订单编号
            if(!empty($order_no)){
                $orderBase = $orderBase->where('order_no','like','%'.$order_no.'%');
            }
            //根据收件人
            if(!empty($receiver_name)){
                $orderBase = $orderBase->where('receiver_name','like','%'.$receiver_name.'%');
            }
            //根据支付方式
            if(!empty($pay_type) && $pay_type > 0){
                $orderBase = $orderBase->wherePayType($pay_type);
            }
            //根据收货人手机
            if(!empty($receiver_mobile)){
                $orderBase = $orderBase->where('receiver_mobile','like','%'.$receiver_mobile.'%');
            }
            //根据是否有赠品
            if(!empty($has_gift) && $has_gift > 0){
                //dd($has_gift);
                $orderBase = $orderBase->whereHasGift($has_gift);
            }
            /* 售后订单 */
            if(!empty($after_sale)){
                $orderBase = $orderBase->whereIn('order_no',$after_sale);
            }
            //配送方式
            if(isset($express_type) && $express_type > -1){
                $orderBase = $orderBase->whereExpressType($express_type);
            }
            //根据商品名称
            if(!empty($goods_name)){
                $goodsBase = GoodsBase::where('title','like','%'.$goods_name.'%')->get();
                $orders_no = null;
                foreach ($goodsBase as $goods){
                    $orderGoods = OrderGood::whereGoodsId($goods->id)->get();
                    $goods_ids[] = $goods->id;
                    foreach ($orderGoods as $order){
                        $orders_no[] = $order->order_no;
                    }
                }
                $orderBase = $orderBase->whereIn('order_no',$orders_no);
            }
        }
        //取订单商品
        $orders = $orderBase->orderBy('id','desc')->paginate($offset);
        foreach ($orders as $order){
            Switch($order->express_type){
                case 0:
                    $order->express_type = "快递";
                    break;
                case 1:
                    $order->express_type = "自提";
                    break;
            }
            //根据商品名称搜索商品
            if(isset($goods_ids)){
                $orderGoods = OrderGood::whereOrderNo($order->order_no)->whereIn('goods_id',$goods_ids)->get();
            }else{
                $orderGoods = OrderGood::whereOrderNo($order->order_no)->get();
            }
            $order_goods = [];
            foreach ($orderGoods as $goods){
                $ordergoodsgift = OrderGood::whereOrderNo($order->order_no)->whereGoodsId($goods->goods_id)->first();
                $goodsData = GoodsBase::whereId($goods->goods_id)->first();
                //dd($goodsData);
                $goodsSpec = GoodsSpec::whereId($goods->spec_id)->first();
                $goods->title = isset($goodsData->title) ? $goodsData->title : '';
                if($ordergoodsgift->is_gift == 1){
                    $goods->title = isset($goodsData->title) ? $goodsData->title."(赠品）" : '';
                }
                $goods->cover = isset($goodsData->cover) ? $goodsData->cover : '';
                $goods->price = isset($goodsSpec->price) ? $goodsSpec->price : '';
                $goods->spec = isset($goodsSpec->name) ? $goodsSpec->name : '';
                $goods->img = GoodsImage::whereGoodsId($goods->goods_id)->first()->name;
                $order_goods[]=$goods;
            }

            if($order->amount_real > $order->amount_goods + $order->amount_express){
                $order->amount_real = $order->amount_goods + $order->amount_express;
            }
            $order->goods = $order_goods;
            $order->receiver_info = json_decode($order->receiver_info);
            $order->status = self::getStateCN($order->state);
            //是否售后订单
            $aftersale = OrderReturn::whereOrderNo($order->order_no)->first();
            if ($aftersale){
                $order->after = 1;
                //暂时不需要用到的数据
                //$order->aftersale = $aftersale;
            }
        }
        return $orders;
    }

    /* 获取订单详情 */
    static function getOrder($orderBase,$id){
        if ($id){
            $order = $orderBase->whereId($id)->first();
        }
        if($order){
            Switch($order->express_type){
                case 0:
                    $order->express_type = "快递";
                    break;
                case 1:
                    $order->express_type = "自提";
                    break;
            }

            if($order->amount_real > $order->amount_goods + $order->amount_express){
                $order->amount_real = $order->amount_goods + $order->amount_express;
            }

            //获取订单商品--赠品
            $order->goods = OrderGood::whereOrderNo($order->order_no)->whereIsGift(0)->get();
            if(!empty($order->goods)){
                foreach ($order->goods as $goods){
                    $goods->data = GoodsBase::whereId($goods->goods_id)->first();
                    $goods->data->img = GoodsImage::whereGoodsId($goods->goods_id)->first()->name;
                    $giftBase = OrderGood::whereOrderNo($order->order_no)->whereIsGift(1)->get();
                    $goods->data->gift = [];
                    if(!$giftBase->isEmpty()){
                        $giftGoods = '';
                        foreach ($giftBase as $gift){
                            $giftGoods[] = OrderGood::whereGoodsId($gift->goods_id)->first();
                        }
                        $goods->data->gift = $giftGoods;
                    }
                    $goods->spec = GoodsSpec::whereId($goods->spec_id)->first();
                }
            }
            $order->receiver_info = json_decode($order->receiver_info);
            //获取操作日志
            $payLog = OrderLog::whereOrderNo($order->order_no)->whereAction('支付成功')->first();
            $order->log = $payLog ?$payLog->created_at:'无付款记录';
            //是否售后订单 --获取售后信息
            $orderReturn = OrderReturn::whereOrderNo($order->order_no)->first();
            if($orderReturn){
                $order->after = 1;
                $orderReturn->images = OrderReturnImage::whereReturnId($orderReturn->id)->get();
                //0.待审核 1.审核通过待退款 4.审核驳回 3成功退款
                $orderReturn->status = OrderReturn::getStateCN($orderReturn->status);
                $order->aftersale = $orderReturn;
            }
        }
        return $order;
    }


        
}