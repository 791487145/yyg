<?php namespace App\Http\Controllers\Api;

use App\Models\GuideTum;
use Queue;
use App\Jobs\AuditGuideSendMail;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\GuideStoreGood;
use App\Models\OrderBase;
use App\Models\SmsVerificationCode;
use App\Models\TaBase;
use App\Models\TaGroup;
use App\Models\TaGroupUser;
use App\Models\UserBase;
use App\Models\UserWx;
use Log;
use Lang;
use App\Models\StoreUser;
use Illuminate\Http\Request;
use App\Http\Controllers\SignController;
use zgldh\QiniuStorage\QiniuStorage;


class StoreController extends SignController
{


    /**
     * @SWG\Get(path="/v1/store/{id}",
     *   tags={"store"},
     *   summary="店铺信息",
     *   description="",
     *   operationId="getStoreInfo",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getStoreInfo($id){
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => self::getGuideUserInfo($id));
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/store/{id}",
     *   tags={"store"},
     *   summary="修改店铺信息",
     *   description="",
     *   operationId="changStoreInfo",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="nick_name",
     *     in="query",
     *     description="昵称",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="avatar",
     *     in="query",
     *     description="头像",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="store_cover",
     *     in="query",
     *     description="店铺封面",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function changStoreInfo($id,Request $request){


        $nickName = $request->input('nick_name','');
        $avatar = $request->input('avatar','');
        $store_cover = $request->input('store_cover','');
        
        $GuideBase = GuideBase::whereId($id)->first();
        if($store_cover != ''){
            $GuideBase->store_cover = str_replace('http://'.env("IMAGE_DOMAIN").'/', '', $store_cover);
        }
        if($avatar != '') {
            $GuideBase->avatar = str_replace('http://'.env("IMAGE_DOMAIN").'/', '', $avatar);
        }
        if($store_cover != '' || $avatar != ''){
            $GuideBase->save();
        }

        $UserBase = UserBase::whereId($GuideBase['uid'])->first();
        if($nickName != ''){
            $UserBase->nick_name = $nickName;
            $UserBase->save();
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => self::getGuideUserInfo($id));
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/guide/{id}",
     *   tags={"store"},
     *   summary="认证导游信息",
     *   description="",
     *   operationId="uploadGuideInfo",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="real_name",
     *     in="query",
     *     description="真实姓名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="guide_no",
     *     in="query",
     *     description="导游证号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="guide_photo",
     *     in="query",
     *     description="认证照片",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function uploadGuideInfo($id,Request $request){
        $realName = $request->input('real_name');
        $guideNo = $request->input('guide_no');
        $guidePhoto = $request->input('guide_photo');
        $GuideBase = GuideBase::whereId($id)->first();
        $GuideBase->real_name = $realName;
        $GuideBase->guide_no = $guideNo;
        $GuideBase->guide_photo_1 = str_replace( 'http://'.env("IMAGE_DOMAIN").'/', '', $guidePhoto);
        $GuideBase->save();

        UserBase::whereId($GuideBase['uid'])->update(['state'=>UserBase::state_upload_2cert]);


        GuideTum::whereUid($GuideBase['uid'])->update(array('name'=>$realName));

        Log::alert('push mail');

        \Queue::push(new AuditGuideSendMail($id));

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($id)));
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/store/{id}/goods",
     *   tags={"store"},
     *   summary="获取店铺里的商品",
     *   description="",
     *   operationId="getStoreInfo",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getStoreGoods($id,Request $request){
        $uid = intval($request->input('uid', 0));
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $GuideStoreGoods = GuideStoreGood::whereGuideId($uid)->offset($offset)->limit($limit)->orderBy('created_at','desc')->get();
        $result = array();
        foreach($GuideStoreGoods as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['goods_id'] = strval($v['goods_id']);
            $goods = GoodsBase::whereId($v['goods_id'])->first();
            $tmp['title'] = isset($goods['title']) ? $goods['title'] : '';
            $GoodsSpec = GoodsSpec::whereGoodsId($v['goods_id'])->first();
            $tmp['price'] = strval($GoodsSpec['price']);
            $tmp['price_market'] = strval($GoodsSpec['price_market']);
            $tmp['rebate'] = strval(number_format(($GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100),2));
            $tmp['num'] = strval($goods['num']);
            $tmp['cover'] = $goods['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$goods['cover'] : '';
            if($goods['state'] != 1){
                $tmp['state'] = '2';
            }else{
                $tmp['state'] = '1';
            }
            $result[] = $tmp;
        }
        return response()->json(array('ret' => self::RET_SUCCESS, 'msg' => '', 'data'=>$result));
    }

    /**
     * @SWG\Post(path="/v1/store/{id}/goods",
     *   tags={"store"},
     *   summary="添加店铺商品",
     *   description="",
     *   operationId="getStoreInfo",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="goods_id",
     *     in="query",
     *     description="商品id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addStoreGoods($id,Request $request){
        $goods_id = $request->input('goods_id',0);
        $GuideBase = GuideBase::whereId($id)->first();
        $GuideStoreGood = GuideStoreGood::whereGuideId($GuideBase['uid'])->whereGoodsId($goods_id)->first();
        if(is_null($GuideStoreGood)){
            $GuideStoreGood = new GuideStoreGood();
            $GuideStoreGood->guide_id = $GuideBase['uid'];
            $GuideStoreGood->goods_id = $goods_id;
            $GuideStoreGood->save();
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($GuideStoreGood->id)));
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/store/goods",
     *   tags={"store"},
     *   summary="删除店铺商品",
     *   description="",
     *   operationId="deleteStoreGoods",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="goods_id",
     *     in="query",
     *     description="商品ID",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function deleteStoreGoods(Request $request){
        Log::alert('input'.print_r($request->input(),true));
        $uid = $request->input('uid');
        $goods_id = $request->input('goods_id');
        $ret = GuideStoreGood::whereGuideId($uid)->whereGoodsId($goods_id)->delete();
        $result = array('ret' => self::RET_FAIL, 'msg' => '移除失败', 'data' => array('id'=>''));
        if($ret == 1){
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($goods_id)));
        }
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/store/{id}/orders",
     *   tags={"store"},
     *   summary="获取店铺订单",
     *   description="",
     *   operationId="getStoreOrders",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="is_today",
     *     in="query",
     *     description="1表示今日订单（选填参数)",
     *     required=false,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="state",
     *     in="query",
     *     description="0.待付款,1.待发货,2.待收货,5.已完成,99.全部订单",
     *     required=false,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getStoreOrders($id,Request $request){
        $state = $request->input('state',0);
        $is_today = $request->input('is_today',0);
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $orders = OrderBase::whereGuideId($id);
        //今日订单
        if($is_today == 1){
            $orders = $orders->where('created_at','>',date("Y-m-d").' 00:00:00')->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED));
        }
        //订单状态
        if(in_array($state,array(OrderBase::STATE_NO_PAY,OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED))){
            $orders = $orders->whereState($state);
        }
        $orders = $orders->OrderBy('id','desc')->offset($offset)->limit($limit)->get();

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>self::formatOrderData($orders) );
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/store/{id}/users",
     *   tags={"store"},
     *   summary="获取导游的用户",
     *   description="",
     *   operationId="getStoreUsers",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="is_today",
     *     in="query",
     *     description="1表示今日订单（选填参数)",
     *     required=false,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="is_subscribe",
     *     in="query",
     *     description="1表示已关注",
     *     required=false,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getStoreUsers($id,Request $request){
        $is_today = $request->input('is_today',0);
        $is_subscribe = $request->input('is_subscribe',0);
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $users = UserWx::whereGuideId($id);
        //->where('uid','>',0)
        //今日订单
        if($is_today == 1){
            $users = $users->where('created_at','>',date("Y-m-d").' 00:00:00');
        }
        if($is_subscribe == 1){
            $users = $users->whereSubscribe(1);
        }
        $users = $users->offset($offset)->limit($limit)->get();
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>self::formatGuideUser($users) );
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/guide/remark_name",
     *   tags={"store"},
     *   summary="备注游客姓名",
     *   description="",
     *   operationId="postGuideUserRemarkName",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     description="游客id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="remark_name",
     *     in="query",
     *     description="备注姓名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function postGuideUserRemarkName(Request $request){
        Log::alert('orderAdd request:' . print_r($request->input(), true));
        $id = $request->input('id',0);
        $remark_name = $request->input('remark_name','');
        UserWx::whereId($id)->update(['remark_name'=>$remark_name]);
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($id)));
        return response()->json($result);
    }



    /**
     * @SWG\Get(path="/v1/store/{id}/groups",
     *   tags={"store"},
     *   summary="获取团列表",
     *   description="",
     *   operationId="getTaGroup",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getTaGroups($id,Request $request){
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $TaGroup = TaGroup::whereGuideId($id)->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        $result = array();
        foreach($TaGroup as $v){
            $TaBase = TaBase::whereId($v['ta_id'])->first();
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['taid'] = strval($v['ta_id']);
            $tmp['title'] = $v['title'];
            $tmp['num'] = strval($v['num']);
            $tmp['ta_name'] = strval($TaBase['ta_name']);
            $tmp['ta_icon'] = $TaBase['ta_name'] == '' ? '' : env('IMAGE_DISPLAY_DOMAIN').$TaBase['ta_logo'];
            $tmp['title'] = $v['title'];
            $tmp['start_timestamp'] = strval(strtotime($v['start_time']));
            $tmp['end_timestamp'] = strval(strtotime($v['end_time']));

            $tmp['start_time'] = date('Y-m-d H:i',strtotime($v['start_time']));
            $tmp['end_time'] = date('Y-m-d H:i',strtotime($v['end_time']));
            $tmp['state'] = strval($v['state']);
            $tmp['state_cn'] = TaGroup::getStateCN($v['state']);
            $result[] = $tmp;
        }
        return response()->json(array('ret' => self::RET_SUCCESS, 'msg' => '', 'data'=>$result));
    }

    /**
     * @SWG\Get(path="/v1/groups/orders",
     *   tags={"store"},
     *   summary="获取某个团的订单列表",
     *   description="",
     *   operationId="getTaGroupOrders",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="guide_id",
     *     in="query",
     *     description="导游id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="group_id",
     *     in="query",
     *     description="团id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getTaGroupOrders(Request $request){

        $guide_id = intval($request->input('guide_id', 0));
        $group_id = intval($request->input('group_id', 0));

        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        if($guide_id ==0 || $group_id == 0){
            $result = array('ret' => self::RET_USER_FAIL, 'msg' => '参数错误', 'data' => array());
            return response()->json($result);
        }

        $orders = OrderBase::whereGuideId($guide_id)
                ->whereGroupId($group_id)
                ->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED))
                ->OrderBy('id','desc')->offset($offset)->limit($limit)->get();
        return response()->json(array('ret' => self::RET_SUCCESS, 'msg' => '', 'data'=>self::formatOrderData($orders)));
    }

    /**
     * @SWG\Post(path="/v1/group/{id}",
     *   tags={"store"},
     *   summary="接团状态更新",
     *   description="",
     *   operationId="changTaGroupState",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="获取团列表接口返回的id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="state",
     *     in="query",
     *     description="状态(1表示开始接团,2表示结束接团)",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function changTaGroupState($id,Request $request){
        $uid = $request->input('uid',0);
        $state = $request->input('state',0);

        $GuideBase = GuideBase::whereUid($uid)->first();

        $ret = 0;
        if($state == TaGroup::STATE_START){

            //正在接团
            $TaGroup = TaGroup::whereGuideId($GuideBase['id'])->whereState(TaGroup::STATE_START)->first();
            if(!is_null($TaGroup)){
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => '系统检测到'.$TaGroup['title'].' 正在接团，必须先结束该团才能接新团', 'data' => array('id'=>'','taid'=>'')));
            }

            $ret = TaGroup::whereId($id)->whereState(TaGroup::STATE_YES_START)->update(array('state'=>$state));
        }


        if($state == TaGroup::STATE_END){
            $ret = TaGroup::whereId($id)->whereState(TaGroup::STATE_START)->update(array('state'=>$state,'end_time'=>date('Y-m-d H:i:s')));
        }

        $TaGroup = TaGroup::whereId($id)->first();
        if($ret == 1){
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($id),'taid'=>strval($TaGroup['ta_id'])));
        }else{
            $result = array('ret' => self::RET_FAIL, 'msg' => '接团失败', 'data' => array('id'=>'','taid'=>''));
        }
        return response()->json($result);
    }

    private function formatGuideUser($users){
        $result = array();
        foreach($users as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['uid'] = strval($v['uid']);

            $UserBase = UserBase::whereId($v['uid'])->first();
            $tmp['avatar'] = $UserBase['avatar'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$UserBase['avatar'] : '';
            $tmp['name'] = $UserBase['nick_name'] == '' ? strval($UserBase['mobile']) : strval($UserBase['nick_name']);
            if($tmp['name'] == ''){
                $tmp['name'] = '微信ID:'.$v['union_id'];
            }
            $tmp['remark_name'] = strval($v['remark_name']);
            $OrderBase = OrderBase::whereUid($v['uid'])->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED));
            $goods_amount = $OrderBase->sum('amount_goods');
            $express_amount = $OrderBase->sum('amount_express');
            $tmp['order_num'] = strval($OrderBase->count());
            $tmp['order_amount'] = strval($goods_amount + $express_amount);
            $result[] = $tmp;
        }
        return $result;
    }


}

