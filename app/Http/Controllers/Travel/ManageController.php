<?php

namespace App\Http\Controllers\Travel;

use App\Models\TaBilling;
use App\Models\UserWx;
use Queue;
use App\Jobs\GroupPush;
use App\Models\GoodsBase;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\GuideTum;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\TaGroup;
use App\Models\UserBase;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Travel\TravelController;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Api\GoodsController;
//引入分页类
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Maatwebsite\Excel\Facades\Excel;

class ManageController extends TravelController
{
    function __construct()
    {
        parent::__construct();
        View::share('taId', $this->user['id']);
    }

    private $page = 20;
    /* 导游管理 */
    function guides($flag = 1){
        $keywords['name']       = !empty(input::get('name'))?input::get('name'):'';
        $keywords['mobile']     = !empty(input::get('mobile'))?input::get('mobile'):'';
        $keywords['start_time'] = !empty(input::get('start_time'))?input::get('start_time'):'';
        $keywords['end_time']   = !empty(input::get('end_time'))?input::get('end_time'):'';
        $keywords['guideorder'] = !empty(input::get('guideorder'))?input::get('guideorder'):0;
        Session::put('keywords',$keywords);
        Session::put('guideflag',$flag);
        $orderflag = $keywords['guideorder'];
        $guiders = GuideTum::whereTaId($this->user['id']);

        //下面的代码是用来筛选本旅行社导游和非本旅行社导游的。先在guide_ta里面取出明面上属于该社的导游,然后通过guide_base里面当前导游对应的ta_id，在与本社的ta_id进行比较
        //只有两个ta_id相同的导游才是真正属于当前旅行社的，则先前取出的明面上的导游  余下的要么是借其他旅行社过来带团的，要么就是本社为激活的
        $res = $guiders;
        $res = $res->get();
        $guide_ids1 = array();
        $guide_ids2 = array();
        foreach($res as $v){
            $ture_taid = GuideBase::whereUid($v->uid)->first()->ta_id;
            $user      = UserBase::whereId($v->uid)->first();
            $is_guide  = !empty($user->is_guide)?$user->is_guide:0;
                //将两个ta_id都相等的 且是导游的（$is_guide=1)的所有导游的guide_id取出来放到一个数组里面
                if($this->user['id'] == $ture_taid && $is_guide == 1){
                    $guide_ids1[] = $v->guide_id;
                }elseif($v->ta_id == $this->user['id'] || $is_guide == 0){
                    //将2个ta_id不相等或者还未激活的当前旅行社的导游的guide_id取出来放到一个数组里面
                    $guide_ids2[] = $v->guide_id;
                }
        }
         
        //根据当前的前端选中列表状态进行对应数据的判断
        if($flag == 1){
            $guide_ids = $guide_ids1;
        }else{
            $guide_ids = $guide_ids2;
        }
        //根据对应状态导游的guide_id数组【guide_id 不是uniquekey仍要加上旅行社的id进行进一步的限制】
        $guiders = GuideTum::whereIn('guide_id',$guide_ids)->whereTaId($this->user['id']);
        
        $guiders = $this->guideSearch($guiders,$keywords);
        $guiders = $guiders->orderBy('guide_id','desc')->paginate($this->page);
        foreach ($guiders as $guider){
            //guide_billing获取销售额 ta_group获取直辖人数
            $user = UserBase::whereId($guider->uid)->first();
            $guider->is_guide = isset($user->is_guide)?$user->is_guide:0;
            if (isset($user->is_guide) && $user->is_guide){
                //设置激活时间
                $guider->time = $user->created_at;
            }
            //根据当前的导游的guide_id从guide_base 里面去取出对应的导游,那这个导游信息里面的ta_id 和当前的ta_id进行对比，如果相同则是本旅行社的，如果不相同，则不是做这个旅行社的
            $guider->ture_taid = GuideBase::whereId($guider->guide_id)->first()->ta_id;
            //从user_wx里面取出当前旅行社下的当前都有下关注公众号的人数
            $guider->refNum = UserWx::whereTaId($guider->ta_id)->whereGuideId($guider->guide_id)->where('ref','wx_qrcode')->count();
            //累计销售额
            //$guider->amount = OrderBase::whereGuideId($guider->guide_id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
            //获取导游下的游客数量
            //$guider->num = UserWx::whereGuideId($guider->guide_id)->where('uid','>',0)->count();
        }
        //qrcode用来生成二维码的代码
        $url = array();
        $url['url'] = 'http://'.env('WWW_DOMAIN').'/app/invitation-guide.php?taid='.$this->user['id'];
        $url['shotUrl'] = GoodsController::xlUrlAPI($url['url']);
        return view('travel.manage.guide')->with(['guiders'=>$guiders,'keywords'=>$keywords,'url'=>$url,'flag'=>$flag]);
    }
    
    //对导游进行搜索
    function guideSearch($guiders,$keywords){
        $name       = $keywords['name'];
        //对两端的空格进行截取
        $name       = trim($name,'');
        $mobile     = $keywords['mobile'];
        $start_time = $keywords['start_time'];
        $end_time   = $keywords['end_time'];
        $guideorder = $keywords['guideorder'];        
        if($name){
            $guiders->where('name','like','%'.$name.'%');
        }
        if($mobile){
            $guiders->where('mobile',$mobile);
        }
        if($start_time){
            if($start_time && $end_time > $start_time){
                $guiders->whereBetween('created_at',[$start_time,$end_time]);
            }else{
                $guiders->where('created_at','>',$start_time);
            }
        }elseif($end_time){
            $guiders->where('created_at','<',$end_time);
        }
        if($guideorder == 1){
            $guiders->orderBy('vistors_num','desc');            
        }elseif($guideorder == 2){
            $guiders->orderBy('vistors_num','asc');
        }elseif($guideorder == 3){
            $guiders->orderBy('total_sales','desc');
        }elseif($guideorder == 4){
            $guiders->orderBy('total_sales','asc');
        }
        return $guiders;  
    }
    
    /**
     * 旅行团的导出
     */
    function exportGroup(){
        $taGroup = TaGroup::whereTaId($this->user['id']);
        //获取当前的搜素状态的信息
        $keyword = Session::get('keyword');
        //获取当前团的状态  未接团 已接团 已结束
        $status  = Session::get('statusTab'); 
        $taGroup = $this->groupSearch($taGroup,$keyword,$status)->orderBy('id','desc')->get();
        foreach ($taGroup as $group){
            //该团的销量  当前旅行社的当前旅行团下
            $group->groupSaleNum     = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->count();
            //该团的销售额 当前旅行社的当前旅行团下
            $group->groupSalesAmount = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
            //当前所有的运费
            $group->totalAmountExpress = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
            $group->groupSalesAmount   = $group->groupSalesAmount + $group->totalAmountExpress;
            $group->groupSalesAmount   = number_format($group->groupSalesAmount,2,'.','');
            //当前导游在派团的时间段内绑定的游客数
            $group->vistor_num = UserWx::whereGuideId($group->guide_id)->whereBetween('created_at',[$group->start_time,$group->end_time])->count();
        }
        if(!$taGroup->isEmpty()){
            $field = ['序号','旅行团名称','导游姓名','导游手机号','该团绑定游客数','该团销量','该团的销售额','状态'];
            $data[] = $field;
            foreach ($taGroup as $group){
                if($group->state == 0 || $group->state == 10){
                    $state = '未接团';
                }elseif($group->state == 1){
                    $state = '已接团';
                }elseif($group->state == 2){
                    $state = '已结束';
                }
                $data[] = [
                    $group->id,$group->title,$group->guide_name,$group->guide_mobile,$group->vistor_num,$group->groupSaleNum,$group->groupSalesAmount,$state];
            }
        }
        //dd($data);
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 8,
                        'B' => 15,
                        'C' => 10,
                        'D' => 15,
                        'E' => 20,
                        'F' => 15,
                        'G' => 20,
                        'F' =>15,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
        }
        exit;
    }
    
    //根据字段进行排序的函数   测试代码
    function bubbleSort($guiders,$flag){
        foreach($guiders as $key=>$val){
            if($flag == 3 || $flag == 4){
                //销售额
                $volume[$key] = $val['total_sales'];
            }elseif($flag == 1 || $flag == 2){
                //游客数量
                $volume[$key] = $val['vistors_num'];
            }
        }
        if($flag == 1 || $flag == 3){
            array_multisort($volume,SORT_DESC,$guiders);
        }elseif($flag == 2 || $flag == 4){
            array_multisort($volume,SORT_ASC,$guiders);
        }
        return $guiders;
    }
    
    /* 添加导游 */
    function guideStore(Request $request){
        $mobile = $request->mobile?$request->mobile:0;
        $nick_name = $request->nick_name?$request->nick_name:'';
        if ($mobile && $nick_name){
            $user = UserBase::whereMobile($mobile)->first();
            if(!$user){
                //创建用户
                $salt = $this->setSalt();
                $password = $this->passwdEncode('YYG'.uniqid().mt_rand(100000,999999),$salt);
                //将用户添加到user表里面
                $user = UserBase::create(['mobile'=>$mobile,'nick_name'=>$nick_name,'salt'=>$salt,'password'=>$password,'state'=>UserBase::state_zp]);
                $guideBase = GuideBase::whereUid($user->id)->first();
                if (!$guideBase){
                    //创建导游
                    $guideBase = GuideBase::create(['uid'=>$user->id,'ta_id'=>$this->user['id']]);
                }
                $guideTa = GuideTum::whereTaId($this->user['id'])->whereGuideId($guideBase->id)->first();
                if (!$guideTa){
                    //创建对应关系$this->user['id']
                    GuideTum::create(['uid'=>$user->id,'ta_id'=>$this->user['id'],'guide_id'=>$guideBase->id,'name'=>$nick_name,'mobile'=>$mobile]);
                }else{
                    return $this->getReturnResult('yes','手机号码:'.$mobile.'已存在,用户名为:'.$guideTa->name);
                }
            }else{
                //添加用户时，用户存在走的分支
                $guideBase = GuideBase::whereUid($user->id)->first();
                if (!$guideBase){
                    //创建导游
                    $guideBase = GuideBase::create(['uid'=>$user->id,'ta_id'=>$this->user['id']]);
                }
                $guideTa = GuideTum::whereTaId($this->user['id'])->whereGuideId($guideBase->id)->first();
                if (!$guideTa){
                    //如何当前导游不是自当前的旅行社下面,则要根据当前这个已经是导游的人取出他 的旅行社的id
                    //创建对应关系$this->user['id']
                    GuideTum::create(['uid'=>$user->id,'ta_id'=>$this->user['id'],'guide_id'=>$guideBase->id,'name'=>$nick_name,'mobile'=>$mobile]);
                }else{
                    return $this->getReturnResult('yes','手机号码:'.$mobile.'已存在,用户名为:'.$guideTa->name);
                }
            }

        }
        return $this->getReturnResult('yes','添加成功');
    }
    
    //指派旅游团
    public function setGuiderGroup(Request $request){
        //var_dump($_REQUEST);die;
        $ta_id      = $this->user['id'];
        $guide_id   = $request->input('guide_id','');
        $groupName  = $request->input('groupname','');
        $vistorNum  = $request->input('vistornum','');
        $start_time = $request->input('starttime','');
        $end_time   = $request->input('endtime','');
        //对当前的结束时间进行判断，如果小于当前时间直接过滤掉
        $current_time = date('Y-m-d H:i:s');
        if($end_time < $current_time){
            return response()->json(['ret'=>'no','msg'=>'结束时间必须要大于当前时间']);
        }
        //对时间段进行判断，如果当前时间在起止的时间内，将状态改为10 ，允许接团
        if($start_time < $current_time && $current_time < $end_time){
            $state = 10;
        }
        $guide_name = $request->input('guide_name','');
        $guide_mobile = $request->input('guide_mobile','');
        $addResult = TaGroup::create(['ta_id'=>$ta_id,'guide_id'=>$guide_id,'title'=>$groupName,'num'=>$vistorNum,'start_time'=>$start_time,'end_time'=>$end_time,'guide_name'=>$guide_name,'guide_mobile'=>$guide_mobile,'state'=>$state]);
        if($addResult){
            return response()->json(['ret'=>'yes','msg'=>'旅游团创建成功']);
        }else{
            return response()->json(['ret'=>'no','msg'=>'旅游团创建失败']);
        }
    }

    /* 修改导游昵称 */
    public function modifyAlias(){
            $phone = Input::get('phonenum');
            $uid   = Input::get('uid');
            $nickname = Input::get('modifynick_name');
            $num = GuideTum::where(['uid'=>$uid,'mobile'=>$phone])->update(['name'=>$nickname]);
            if($num){
                return response()->json(['ret'=>'yes','msg'=>'导游名修改成功','name'=>$nickname,'phone'=>$phone]);
            }else{
                return response()->json(['msg'=>'导游名修改失败']);
            }        
    }

    /* 建团管理 */
    function visitors($state = 0){
        $keyword['guide_name']       = !empty(input::get('guide_name'))?input::get('guide_name'):'';
        $keyword['guide_name']       = trim($keyword['guide_name'],'');
        $keyword['guide_mobile']     = !empty(input::get('guide_mobile'))?input::get('guide_mobile'):'';
        $keyword['start_time'] = !empty(input::get('start_time'))?input::get('start_time'):'';
        $keyword['end_time']   = !empty(input::get('end_time'))?input::get('end_time'):'';
        $keyword['guideorder'] = !empty(input::get('guideorder'))?input::get('guideorder'):0;
        Session::put('keyword',$keyword);
        Session::put('statusTab',$state);
        $taGroup = TaGroup::whereTaId($this->user['id']);
        //获取搜索条件后的资源
        $groups = $this->groupSearch($taGroup,$keyword,$state);
        //获取该用户下的旅行团
        $groups = $taGroup->orderBy('id','desc')->paginate($this->page);
        foreach ($groups as $group){
            $guideTa = GuideTum::whereGuideId($group->guide_id)->first();
            $group->guide = $guideTa; 
            $user = UserBase::whereId($group->guide->uid)->first();
            if($user){
                $group->is_guide = $user->is_guide;
            }
            //该团的销量  当前旅行社的当前旅行团下
            $group->groupSaleNum     = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->count();
            //该团的销售额 当前旅行社的当前旅行团下
            $group->groupSalesAmount = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
            //该团所有销售额的总邮费
            $group->totalAmountExpress = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
            $group->groupSalesAmount   = $group->groupSalesAmount + $group->totalAmountExpress;
            $group->groupSalesAmount   = number_format($group->groupSalesAmount,2,'.','');
            //当前导游在派团的时间段内绑定的游客数
            $group->vistor_num = UserWx::whereGuideId($group->guide_id)->whereBetween('created_at',[$group->start_time,$group->end_time])->count();
        }
        //dd($groups);
        $guiders = GuideTum::whereTaId($this->user['id'])->get();
        return view('travel.manage.visitors')->with(['groups'=>$groups,'guiders'=>$guiders,'keyword'=>$keyword,'state'=>$state]);
    }
    
    /**
     * 
     * @param object $taGroup 团信息对象
     * @param array $keyword 搜索的条件
     */
    function groupSearch($taGroup,$keyword,$state){
        if($keyword['start_time']){
            if($keyword['start_time'] && $keyword['end_time'] > $keyword['start_time']){
                $taGroup->whereBetween('start_time',[$keyword['start_time'],$keyword['end_time']]);
            }else{
                $taGroup->where('start_time','>',$keyword['start_time']);
            }
        }elseif($keyword['end_time']){
            $taGroup->where('end_time','<',$keyword['end_time']);
        }
        
        if($keyword['guide_name']){
            $taGroup->where('guide_name','like','%'.$keyword['guide_name'].'%');
        }
        if($keyword['guide_mobile']){
            $taGroup->where('guide_mobile',$keyword['guide_mobile']);
        }
        if($keyword['guideorder'] == 1){
            $taGroup->orderBy('num','desc');
        }elseif($keyword['guideorder'] == 2){
            $taGroup->orderBy('num','asc');
        }
        if($state == 0){
            $taGroup->whereIn('state',[0,10]);
        }elseif($state == 1){
            $taGroup->whereState($state);
        }elseif($state == 2){
            $taGroup->whereState($state);
        }
        return $taGroup;
    }


    /* 旅行团详情 */
    //建团管理下的查看详情的函数  老方法，即将作废
    function visitors_order($id){
        $group = TaGroup::whereId($id)->whereTaId($this->user['id'])->first();
        if ($group){
            $guideBilling = GuideBilling::whereGuideId($group->guide_id)->whereInOut(1)->whereGroupId($id)->paginate($this->page);
            dd($guideBilling);
            //获取订单及订单商品
            if(!$guideBilling->isEmpty()){
                foreach ($guideBilling as $billing){
                    $orderBase = OrderBase::whereOrderNo($billing->order_no)->first();
                    $orderBase->state = OrderBase::getStateCN($orderBase->state);
                    $orderGoods = OrderGood::whereOrderNo($billing->order_no)->get();
                    foreach ($orderGoods as $goods){
                        $goodsBase = GoodsBase::whereId($goods->goods_id)->first();
                        $goods->goods_name = $goodsBase->title;
                    }
                    $billing->order = $orderBase;
                    $billing->order_goods = $orderGoods;
                }
            }
            $guideBase = GuideTum::whereTaId($this->user['id'])->whereGuideId($group->guide_id)->first();
            $group->guide = $guideBase;
            //该团的销量  当前旅行社的当前旅行团下
            $group->groupSaleNum     = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->count();
            //该团的销售额 当前旅行社的当前旅行团下
            $group->groupSalesAmount = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
            //总的邮费
            $group->totalAmountExpress = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
            $group->groupSalesAmount   = $group->groupSalesAmount + $group->totalAmountExpress;
        }
        return view('travel.manage.visitors_order')->with(['billings'=>$guideBilling,'group'=>$group]);
    }
    
    /**
     * 用来查看当前旅行团的订单列表
     * @param int $id 旅行团的id
     */
    function groupOrdersDetail($id){
        $group = TaGroup::whereId($id)->whereTaId($this->user['id'])->first();
        if($group){
            //查询出当前旅行社的当前旅行团下的所有的订单信息
            $orderBaseInfo = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->paginate($this->page);
            foreach($orderBaseInfo as $order){
                //通过商品的订单编号从order_goods里面关联出商品的名称
                $order->goodInfo = OrderGood::whereOrderNo($order->order_no)->first();
                //获取旅行社获得返利amount-return_amount
                $order->rebateinfo = TaBilling::whereOrderNo($order->order_no)->first();
                //该团的销量  当前旅行社的当前旅行团下
                $group->groupSaleNum     = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->count();
                //该团的销售额 当前旅行社的当前旅行团下
                $group->groupSalesAmount = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
                //总的邮费
                $group->totalAmountExpress = OrderBase::whereTaId($group->ta_id)->where('group_id',$group->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
                $group->groupSalesAmount   = $group->groupSalesAmount + $group->totalAmountExpress;
            }
            //dd($orderBaseInfo);
            
        }
        return view('travel.manage.visitors_order')->with(['group'=>$group,'orderBaseInfo'=>$orderBaseInfo]);
    }

    /* 添加旅行团 */
    function visitors_group(){
        $data = Input::all();
        if (empty($data['start_time'])){
            return $this->getReturnResult('no','请输入开始时间');
        }
        if (empty($data['end_time'])){
            return $this->getReturnResult('no','请输入结束时间');
        }
        if(empty($data['guide_id'])){
            return $this->getReturnResult('no','请指派导游');
        }
        if ($data['end_time'] <= $data['start_time']){
            return $this->getReturnResult('no','结束时间必须大于开始时间');
        }
        $data['ta_id'] = $this->user['id'];

        if($data['start_time'] < date('Y-m-d H:i:s')){
            $data['state'] = TaGroup::STATE_YES_START;
        }
        $TaGroup = TaGroup::create($data);
        if($TaGroup){
            Queue::push(new GroupPush($TaGroup->id));
            return $this->getReturnResult('yes','建团成功');
        }
    }
    
    /**
     * 导游的导出  先前的无区分导出
     */
    /* function export(){
       $guiders    = GuideTum::whereTaId($this->user['id']);
       $keywords   = Session::get('keywords');
       $guideflag  = Session::get('guideflag');
       $searchInfo = $this->guideSearch($guiders,$keywords)->get();
       //dd($searchInfo);
       if(!$searchInfo->isEmpty()){
           $field  = ['ID','导游姓名','导游手机号','已绑定游客数','累计销售额','注册时间'];
           $data[] = $field;
           //$i = 1;
           foreach ($searchInfo as $info){
               //在此处添加导出时的判断
               $ture_taid = GuideBase::whereId($info->guide_id)->first()->ta_id;
               $user = UserBase::whereId($info->uid)->first();
               $is_guide = isset($user->is_guide)?$user->is_guide:0;
               if($guideflag==1 && $info->ta_id == $ture_taid && $is_guide == 1){
                   //dd(12);
                   $data[] = [
                       $info->guide_id,$info->name,$info->mobile,$info->vistors_num,$info->total_sales,$info->created_at];
               }elseif($guideflag == 0){
                  // dd(56);
                   $data[] = [
                       $info->guide_id,$info->name,$info->mobile,$info->vistors_num,$info->total_sales,$info->created_at];
               }
               
               //$i++;
           }
       }
       //dd($data);
       if(!empty($data)){
           Excel::create(date('YmdHis'),function($excel) use ($data){
               $excel->sheet('order', function($sheet) use ($data){
                   $sheet->setWidth(array(
                       'A' => 5,
                       'B' => 10,
                       'C' => 15,
                       'D' => 15,
                       'E' => 15,
                       'F' => 25,
                   ));
                   $sheet->rows($data);
               });
           })->export('xlsx');
       }
       exit;
       
    } */
    
    
    /**
     * 导游的导出  对应状态的导出
     */
    function export(){
        $guiders    = GuideTum::whereTaId($this->user['id']);
        $keywords   = Session::get('keywords');
        $guideflag  = Session::get('guideflag');
        $searchInfo = $this->guideSearch($guiders,$keywords)->get();
        $searchInfo = $searchInfo->toArray();
        if(!empty($searchInfo)){
            $field  = ['ID','导游姓名','导游手机号','已绑定游客数','下级游客关注公众号数','累计销售额','注册时间'];
            $data1[] = $field;
            $data2[] = $field;
            //$i = 1;
            foreach ($searchInfo as $info){
                //在此处添加导出时的判断
                $ture_taid = GuideBase::whereId($info['guide_id'])->first()->ta_id;
                $user      = UserBase::whereId($info['uid'])->first();
                //取出关注公众号人数
                $refNum    = UserWx::whereTaId($info['ta_id'])->whereGuideId($info['guide_id'])->where('ref','wx_qrcode')->count();
                $is_guide  = isset($user->is_guide)?$user->is_guide:0;
                    if($guideflag==1 && $info['ta_id'] == $ture_taid && $is_guide == 1){
                        $data1[] = [
                            $info['guide_id'],$info['name'],$info['mobile'],$info['vistors_num'],$refNum,$info['total_sales'],$info['created_at']];
                    }elseif($info['ta_id'] != $ture_taid || $is_guide == 0){
                        $data2[] = [
                            $info['guide_id'],$info['name'],$info['mobile'],$info['vistors_num'],$refNum,$info['total_sales'],$info['created_at']];
                    }
            }
        }
        //对当前导出的导游状态进行判断
        if($guideflag == 1){
           $data = $data1;
        }else{
            $data = $data2;
        }
        
        if(!empty($data)){
            Excel::create(date('YmdHis'),function($excel) use ($data){
                $excel->sheet('order', function($sheet) use ($data){
                    $sheet->setWidth(array(
                        'A' => 5,
                        'B' => 10,
                        'C' => 15,
                        'D' => 15,
                        'E' => 25,
                        'F' => 25,
                        'G' => 25,
                    ));
                    $sheet->rows($data);
                });
            })->export('xlsx');
        }
        exit;
         
    }
    
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
