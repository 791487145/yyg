<?php
namespace App\Http\Controllers\Admin;

use App\Models\OrderBase;
use Log;
use Redirect;
use Validator;
use App\Models\GuideBilling;
use App\Models\PlatformSm;
use App\Models\TaBase;
use App\Models\GuideBase;
use App\Models\TaBilling;
use App\Models\TaLog;
use App\Models\GuideTum;
use App\Models\UserBase;
use App\Models\GuideAudit;
use App\Models\UserWx;
use App\Models\ConfCity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Admin\BaseController;

class TaController extends BaseController
{

    const inCome = 1;
    const states = 1;
    const pageNum = 20;
    //旅行社列表
   protected function TAManages(Request $request)
    {
        $data = $request->all();
        $confcitys = ConfCity::where('parent_id',1)->lists('name','id');
        $ta_name = empty($data['ta_name']) ? '' :$data['ta_name'];
        $mobile = empty($data['mobile']) ? '' :$data['mobile'];
        $pavilion_id = empty($data['pavilion_id']) ? '' :$data['pavilion_id'];
        $provincename = ConfCity::whereId($pavilion_id)->lists('name');
        $datemin = empty($data['datemin']) ? '' :$data['datemin'];
        $datemax = empty($data['datemax']) ? '' :$data['datemax'];
        $tabases = self::getTasInfo($data);
        return view("boss.ta.ta_lists", ['tabases' => $tabases, 'ta_name' => $ta_name, 'mobile' => $mobile,'pavilion_id'=>$pavilion_id,'datemin' => $datemin, 'datemax' => $datemax,'confcitys'=>$confcitys,'provincename'=>$provincename]);
    }


    /**
     * @param Request $request
     */
    public function exportTaLists(Request $request){
        $data = $request->all();
        $tabases = self::getTasInfo($data);
        $field = ['旅行社名称','手机号码','旅行社绑定导游人数','旅行社累计收益','旅行社累计销售额','本月旅行社累计销售额','注册地','注册时间','状态'];
        $items[] = $field;
        foreach($tabases as $key =>$v){
            $items[$key+1] = [$v->ta_name,$v->mobile,$v->guide_count,$v->amount,$v->taTurnover,$v->currentMonthTurnover,$v->address,$v->created_at,$v->state == 1 ? '开' : '关'];
        }
        Excel::create(date('YmdHis'),function($excel) use ($items){
            $excel->sheet('goods', function($sheet) use ($items){
                $sheet->setWidth(array(
                    'A'=>25,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>20,
                    'H'=>20,
                    'I'=>20,
                ));
                $sheet->rows($items);

            });
        })->export('xlsx');
    }


    //旅行社信息
    public function TAMmanageInfo($id,Request $request)
    {
        $talist = TaBase::whereId($id)->first();
        if($talist->ta_province_id != 0){
            $provinces = ConfCity::whereId($talist->ta_province_id)->select('name')->get();
            $citys = ConfCity::whereId($talist->ta_city_id)->select('name')->get();
            $province = json_decode($provinces);
            $city = json_decode($citys);
            $talist->address = $province[0]->name . $city[0]->name;
        }else{
            $talist->address = "";
        }
        $talist->ta_guides_count = GuideBase::whereTaId($talist->id)->count();
        $talist->billingSum = self::sum($talist->id);
        $count = GuideBase::whereTaId($id)->count();
        $real_name = $request->input('real_name');
        $mobile = $request->input('mobile');
        $tabases = self::getTaGuides($id,$real_name,$mobile);
        return view("boss.ta.ta_list", ['talist' => $talist, 'tabases' => $tabases, 'count' => $count,'real_name'=>$real_name,'mobile'=>$mobile]);
    }

    /**
     * @param $id
     * @param Request $request
     */
    public function exportTaGuides($id,Request $request){
        $real_name = $request->input('real_name');
        $mobile = $request->input('mobile');
        $taGuides = self::getTaGuides($id,$real_name,$mobile);
        $field = ['导游姓名','手机号','导游绑定游客数','关注公众号游客数','导游累计收益','注册时间'];
        $items[] =$field;
        foreach($taGuides as $key=>$value){
            $items[$key+1] = [empty($value->real_name) ? 'GID.'.$value->id : $value->real_name,$value->mobile,$value->count,$value->count_user_follow_WX,$value->amount,$value->created_at];
        }
        Excel::create(date('YmdHis'),function($excel) use ($items){
            $excel->sheet('goods', function($sheet) use ($items){
                $sheet->setWidth(array(
                    'A'=>25,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                ));
                $sheet->rows($items);

            });
        })->export('xlsx');
    }

    /**
     * @param $id
     * @param $real_name
     * @param $mobile
     * @return $this|GuideBase|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Query\Builder
     */
    static private function getTaGuides($id,$real_name,$mobile){
        $guideBase = GuideBase::whereTaId($id);
        if($real_name){
            $tabase = $guideBase->where('real_name','like','%'.$real_name.'%');
        }
        if($mobile){
            $userId = UserBase::whereMobile($mobile)->pluck('id');
            $guideBase = $guideBase->where('uid',$userId);
        }
        $guideBase = $guideBase->orderBy('id','desc')->paginate(self::pageNum);

        foreach ($guideBase as $value) {
            $value->count = UserWx::whereGuideId($value->id)->count();
            $value->count_user_follow_WX = UserWx::whereGuideId($value->id)->whereRef('wx_qrcode')->count();
            $billingAmount = GuideBilling::whereGuideId($value->id)->whereInOut(self::inCome)->sum('amount');
            $billingReturn = GuideBilling::whereGuideId($value->id)->whereInOut(self::inCome)->sum('return_amount');
            $value->amount = floatval($billingAmount)-doubleval($billingReturn);
            $value->mobile = UserBase::whereId($value->uid)->pluck('mobile');
        }
        return $guideBase;
    }


    public function addTA(){
        return view('boss.ta.ta_add');
    }

    //添加旅行社
    public function taAddInfo(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            $validator = Validator::make([
                'ta_name' => $request->ta_name,
                'opt_name' => $request->opt_name,
                'mobile' =>$request->mobile,
                'opt_id_card' =>$request->opt_id_card,
            ], [
                'ta_name' => 'required',
                'opt_name' => 'required',
                'mobile' => 'required',
                'opt_id_card' => 'required',
            ]);
            if($validator->fails()){
                return Redirect::back()->withErrors($validator)->withInput();//输出错误信息
            }
            $mobileCheck = TaBase::whereMobile($data['mobile'])->first();
            if($mobileCheck){
                return response()->json(['ret'=>'no','msg'=>'该手机号已注册旅行社']);
            }

            $salt = $this->setSalt();
            $password = rand(100000,999999);
            $text = "恭喜你已成功入驻易游购商家后台，你的管理账号:".$request->mobile."，密码:".$password."，登入地址:http://".env('TRAVEL_DOMAIN');

            $mobile = $request->mobile;
            $ip = ip2long($request->getClientIp());
            $type = PlatformSm::travelAdd;
            parent::platformSendSms($mobile, $ip, $type,$text);

            $taBase = new TaBase();
            $taBase->salt = $salt;
            $taBase->password = $this->passwdEncode($password,$salt);
            $taBase->ta_name = $request->ta_name;
            $taBase->opt_name = $request->opt_name;
            $taBase->mobile = $request->mobile;
            $taBase->opt_id_card = $request->opt_id_card;
            $taBase->self_invite_code = self::generateInviteCode();
            $taBase->state = TaBase::state_check;
            $taBase->save();
            return response()->json(['ret'=>'yes','msg'=>'旅行社添加成功']);
        }

    }

    //旅行社实名未审核列表
    protected function TaUnauditedList($id)
    {
        $talists = TaBase::whereId($id)->first();
        $talists->provinces = $this->getCityName($talists->ta_province_id);
        $talists->citys = $this->getCityName($talists->ta_city_id);
        $talists->address = $talists->provinces . $talists->citys;
        switch($talists->state) {
            case '0':
                $talists->RET ="未实名" ;
                break;
            case '1':
                $talists->RET ="实名已通过" ;
                break;
            case '3':
                $talists->RET ="实名已驳回" ;
                break;
            case '2':
                $talists->RET ="实名未审核" ;
                break;
        }
        return view("boss.ta.taunauditer_list", compact('talists'));
    }

    public function checkPass(Request $request)
    {
        $id = $request->input('id','');
        $active = $request->input('active','');
        $taBases = TaBase::whereId($id)->first();
        $taLog = new TaLog();

        if($active == 1){
            TaBase::whereId($id)->update(['state'=>TaBase::state_check]);
            $content['结果'] = '旅行社通过审核';
            $content = json_encode($content,JSON_UNESCAPED_UNICODE);
            $taLog->content = $content;
            //$taLog->type = "审核通过";
            $taLog->uid = $id;
            $ret = $taLog->save();
            if($ret == 1){
                return response()->json(['ret'=>'yes']);
            }
            return response()->json(['ret'=>'no']);
        }else{
            $refute_des = $request->input('refute_des','');
            switch($refute_des) {
                case '0':
                    $content['ret'] ="身份证信息与身份证件信息不一致" ;
                    break;
                case '1':
                    $content['ret'] ="身份证件拍摄不清晰" ;
                    break;
                case '2':
                    $content['ret'] ="上传的是无效证件信息" ;
                    break;
            }
            //发短信
            $ip = ip2long($request->getClientIp());
            $text = "【易游购】抱歉，你提交的实名认证未通过审核，原因：".$content['ret'];
            $data = parent::platformSendSms($taBases->mobile,$ip,PlatformSm::travelCheck,$text);


            if ($data > 0){
                TaBase::whereId($id)->update(['state'=>3]);
                //taLog日志
                $content = json_encode($content,JSON_UNESCAPED_UNICODE);
                $taLog->content = $text;
                //$taLog->type = "审核驳回";
                $taLog->uid = $id;
                $ret = $taLog->save();
                if($ret == 1){
                    return response()->json(['ret'=>'yes']);
                }
                return response()->json(['ret'=>'no']);//未写入日志
            }else{
                return response()->json(['ret'=>'not']);//验证码失败
            }
        }
    }

    public function checkRefuse($id)
    {
        return view("boss.ta.refute", compact('id'));
    }

        //提交审核日志，更新user_base表state状态
        protected function GuideAudioLog($id)
        {
            $guideAudits = new GuideAudit();
            $userbases = new UserBase();
            $guideAudits->uid = $id;
            $guideAudits->active = Input::get('active');
            $data = array('state' => Input::get('active'));
            $guideAudits->content = Input::get('content');
            $guideAudits->save();
            $userbases->whereId($id)->update($data);
        }

    //计算旅行社累计收益
     static private function sum($id){
            $incomesum = TaBilling::whereTaId($id)->where('in_out',TaBilling::in_income)->sum('amount');
            $enpendsum = TaBilling::whereTaId($id)->where('in_out',TaBilling::in_income)->sum('return_amount');
            return number_format($incomesum-$enpendsum,2);
    }

    //改变旅行社状态
    public function taChangeState($id,$state){
        TaBase::whereId($id)->update(['state'=>$state]);
    }

    /**
     * @param $data
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    static private function getTasInfo($data){
        $currentMonthStartDate = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y")));
        $today = date('Y-m-d H:i:s');
        $tabases = new TaBase();
        if(!empty($data)){
            if (empty($data['datemax'])) {
                $datemax = date('Y-m-d H:i:s');
            }
            if(!empty($data['pavilion_id'])){
                $tabases = $tabases->where('ta_province_id',$data['pavilion_id']);
            }
            if (!empty($data['ta_name'])) {
                $tabases = $tabases->where('ta_name', 'like', '%' . $data['ta_name'] . '%');
            }
            if (!empty($data['mobile'])) {
                $tabases =  $tabases->where('mobile', 'like', '%' . $data['mobile'] . '%');
            }
            if (!empty($data['datemin'])) {
                $tabases = $tabases->whereBetween('created_at', [$data['datemin'], $data['datemax']]);
            }
        }

        $tabases = $tabases->orderBy('id','desc')->paginate(self::pageNum);
        foreach ($tabases as $tabase) {
            $tabase->guide_count = GuideBase::whereTaId($tabase->id)->count();
            $tabase->amount = self::sum($tabase->id);

            //旅行社累计营业额
            $taAmountGoods = OrderBase::whereTaId($tabase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_goods');
            $taAmountExpress = OrderBase::whereTaId($tabase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_express');
            $taAmountCoupon = OrderBase::whereTaId($tabase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->sum('amount_coupon');
            $taTurnover = $taAmountGoods + $taAmountExpress - $taAmountCoupon;
            //本月营业额
            $currentMonthAmountGoods = OrderBase::whereTaId($tabase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereBetween('created_at',[$currentMonthStartDate,$today])->sum('amount_goods');
            $currentMonthAmountExpress = OrderBase::whereTaId($tabase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereBetween('created_at',[$currentMonthStartDate,$today])->sum('amount_express');
            $currentMonthAmountCoupon = OrderBase::whereTaId($tabase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->whereBetween('created_at',[$currentMonthStartDate,$today])->sum('amount_coupon');
            $currentMonthTurnover = $currentMonthAmountGoods + $currentMonthAmountExpress - $currentMonthAmountCoupon;

            $tabase->taTurnover = number_format($taTurnover,2);
            $tabase->currentMonthTurnover = number_format($currentMonthTurnover,2);
            switch($tabase->state) {
                case '1':
                    $tabase->RET ="正常" ;
                    break;
                case '4':
                    $tabase->RET ="关停" ;
                    break;
            }
            if($tabase->ta_province_id !=0 && $tabase->ta_city_id != 0) {
                $provinces = ConfCity::whereId($tabase->ta_province_id)->select('name')->first();
                $citys = ConfCity::whereId($tabase->ta_city_id)->select('name')->first();
                $province = json_decode($provinces);
                $city = json_decode($citys);
                $tabase->address = $province->name . $city->name;
            }
        }
        return $tabases;
    }


}















































?>