<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Controllers\GenController;
use App\Http\Requests;
use App\Models\ConfCity;
use App\Models\ConfPavilion;
use App\Models\OrderBase;
use App\Models\PlatformSm;
use App\Models\GoodsSpec;
use App\Models\GoodsBase;
use App\Models\GuideAudit;
use App\Models\GuideBase;
use App\Models\Role;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\SupplierExpress;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\User;
use App\Models\UserBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use Redirect;
use App\Models\UserWx;
use App\Models\GuideBilling;

class CuscomerController extends BaseController
{
    const salesRole = 4;
    const guideReject = 31;
    //供应商管理
    public function supplierList()
    {
        $name = Input::get("name");
        $tmp['name'] = $name;
        $SupplierBases = new SupplierBase();

        if($name != ''){
            $SupplierBases = $SupplierBases->where('name','like', '%' . $name . '%');
        }

        $SupplierBases = $SupplierBases->orderBy('id','desc');
        $SupplierBases = $this->sulists($SupplierBases);
        return view("boss.cuscomer.supplier_list",compact('SupplierBases','tmp'));
    }

    public function supplierAdd(Request $request)
    {
        if($request->isMethod('post')){

            $validator = Validator::make([
                'mobile' => $request->mobile,
                'card_id' => $request->card_id,
                'deposit' =>$request->deposit,
                'name' =>$request->name,
            ], [
                'mobile' => 'required',
                'card_id' => 'required',
                'deposit' => 'required',
                'name' => 'required',
            ]);
            if($validator->fails()){
                return Redirect::back()->withErrors($validator)->withInput();//输出错误信息
            }

            $num = SupplierBase::whereMobile($request->mobile)->count();
            if($num > 0){
                return response()->json(['ret'=>BaseController::RETFAIL,'msg'=>GenController::MOBILE_EXIST]);
            }

            $salt = $this->setSalt();
            $password = rand(100000,999999);
            $text = "恭喜你已成功入驻易游购商家后台，你的管理账号:".$request->mobile."，密码:".$password."，登入地址:http://".env('STORE_DOMAIN');

            $mobile = $request->mobile;
            $ip = ip2long($request->getClientIp());
            $type = PlatformSm::supplierAdd;
            $ret = parent::platformSendSms($mobile, $ip, $type,$text);

            $SupplierBase = new SupplierBase();
            $SupplierBase->name = $request->name;
            $SupplierBase->salt = $salt;
            $SupplierBase->password = $this->passwdEncode($password,$salt);
            $SupplierBase->card_id = $request->card_id;
            $SupplierBase->deposit = $request->deposit;
            $SupplierBase->mobile = $request->mobile;
            $SupplierBase->remark = $password;
            $SupplierBase->withdraw_name = $request->name;
            $SupplierBase->save();

            $supplierExpress = new SupplierExpress();
            $supplierExpress->title = "全国包邮";
            $supplierExpress->total_amount = 0;
            $supplierExpress->express_amount = 0;
            $supplierExpress->state = 1;
            $supplierExpress->supplier_id = $SupplierBase->id;
            $supplierExpress->save();
            return response()->json(['ret'=>'yes','msg'=>'添加成功']);
        }else{

            return view("boss.cuscomer.supplier_add");

        }
    }

    public function editSupplier($id)
    {
        $SupplierBase = SupplierBase::whereId($id)->first();
        $amountGoods = OrderBase::whereSupplierId($SupplierBase->id)->whereIn('state',[1,2,5])->sum('amount_goods');
        $amountExpress = OrderBase::whereSupplierId($SupplierBase->id)->whereIn('state',[1,2,5])->sum('amount_express');
        $SupplierBase->amount = number_format($amountGoods+$amountExpress,2);
        $GoodsBases = GoodsBase::whereSupplierId($id)->whereState(GoodsBase::state_online)->paginate($this->page);

        foreach($GoodsBases as $GoodsBase){
           $ConfPavilion = ConfPavilion::whereId($GoodsBase->pavilion)->first();
            $GoodsSpecs = GoodsSpec::whereGoodsId($GoodsBase->id)->get();
            $amount = 0;

            foreach($GoodsSpecs as $GoodsSpec){
                $amount = ($GoodsSpec->price)*($GoodsSpec->num_sold) + $amount;
            }
            $price = $this->getPrice($GoodsBase->id);//价格区间
            $GoodsBase->prices = $price['price'];
            $GoodsBase->prices_buying = $price['price_buying'];
            $GoodsBase->amount = $amount;
            $GoodsBase->pavilion_name = $ConfPavilion->name;

         }

        return view("boss.cuscomer.supplier_edit",['SupplierBase' => $SupplierBase,'GoodsBases' => $GoodsBases]);
    }

    public function supplierEdit($id)
    {
        $state = Input::get('state');
        $SupplierBase = new SupplierBase();
        $SupplierBase->whereId($id)->update(['state' => $state]);

        return response()->json(['state'=>$state]);
    }

    //导游管理
    public function guiderList($state = UserBase::state_upload_2cert)
    {
        $name = Input::get("real_name",'');
        $tmp['real_name'] = $name;

        $UserBases = UserBase::whereState($state)->whereIsGuide(UserBase::is_guide_yes)->orderBy('id','desc')->paginate($this->page);
        if($name != ''){
            $state = Input::get("state");
            $guideBases = GuideBase::where( 'real_name','like', '%' . $name . '%')->lists('uid');
            $UserBases = UserBase::whereState($state)->whereIn('id',$guideBases)->orderBy('id','desc')->paginate($this->page);
        }
        foreach($UserBases as $UserBase){
            $GuideBase = GuideBase::where('uid',$UserBase->id)->first();
            $UserBase->id = $GuideBase->id;
            $UserBase->real_name = (empty($GuideBase->real_name) ? '' : $GuideBase->real_name) ;
            $UserBase->guide_no = (empty($GuideBase->guide_no) ? '' : $GuideBase->guide_no) ;
            $UserBase->guide_photo_1 = (empty($GuideBase->guide_photo_1) ? '' : $GuideBase->guide_photo_1);
            $UserBase->guide_photo_2 = (empty($GuideBase->guide_photo_2) ? '' : $GuideBase->guide_photo_2);
        }
        return view("boss.cuscomer.guider_list",compact('UserBases','state','tmp'));
    }

    //导游审核通过
    public function guiderEdit($id)
    {
        $guideBaseUid = GuideBase::whereId($id)->pluck('uid');
        UserBase::whereId($guideBaseUid)->update(array('state'=>UserBase::state_check));
        $GuiderAudit = new GuideAudit();
        $content['结果'] = '导游通过审核';
        $content = json_encode($content,JSON_UNESCAPED_UNICODE);
        $GuiderAudit->content = $content;
        $GuiderAudit->active = '导游审核';
        $GuiderAudit->uid = $id;
        $GuiderAudit->save();
        return $id;
    }

    //导游审核驳回
    public function guiderCheck($id)
    {
        $guideBaseUid = GuideBase::whereId($id)->pluck('uid');
        $users = UserBase::whereId($guideBaseUid)->first();
        $users->state = UserBase::state_no_check;
        $users->save();
        return view("boss.cuscomer.guider_check",['id'=>$id]);
    }


    /*
     * 导游邮件审核
     * id  guide_id
     * */
    public function guideMailAudit($id){
        $guideInfo = GuideBase::whereId($id)->first();
        $userInfo = UserBase::whereId($guideInfo->uid)->first();
        if($userInfo->state == UserBase::state_check){
            return '已审核通过';
        }
        $condition = $this->guiderEdit($id);
        if($condition){
            return '导游审核通过';
        }else{
            return '导游审核通过失败';
        }
    }


    public function supplierSms($id,Request $request)
    {
        $SupplierBase = SupplierBase::whereId($id)->first();
        $text = "【易游购】".$SupplierBase->name."店长，你好，你在易游购平台上的保证金已不足￥2000.00元，为了保证您的商品在平台上正常售卖，请尽快联系我们平台工作人员，多有不便，敬请谅解~";
        $mobile = $SupplierBase->mobile;
        $ip = ip2long($request->getClientIp());
        $type = PlatformSm::supplierMoney;
        $ret = parent::platformSendSms($mobile, $ip, $type,$text);
        if ($ret > 0){
            return response()->json(['ret'=>'yes']);
        }
        return response()->json(['ret'=>'no']);
    }

    //发送短信
    public function guideCheckSms($id,Request $request)
    {
        $data = Input::get('content');
        $text = "【易游购】抱歉，你提交的导游证认证未通过审核，原因：".$data;
        $userInfo = UserBase::whereId($id)->first();
        $mobile = $userInfo->mobile;
        $ip = ip2long($request->getClientIp());
        $type = self::guideReject;
        parent::platformSendSms($mobile, $ip, $type,$text);
        return redirect("/cuscomer/guiders/11");
    }

    //销售
    public function salerList()
    {
        $name = Input::get("name");
        $tmp['name'] = $name;
        $Users = User::whereRoleId(self::salesRole);

        if($name != ''){
            $Users = $Users->where( 'name','like', '%' . $name . '%');
        }

        $Users = $this->salists($Users);
        return view("boss.cuscomer.saler_list",compact('Users','tmp'));
    }


    public function salerLook($action,$id)
    {
        $name = Input::get("name");
        $tmp['name'] = $name;
        $Users = User::whereId($id)->first();
        $Users->ta_num = TaBase::whereSaleId($id)->count();
        $Users->guide_num = GuideBase::whereSaleId($id)->count();

        if($action == 'ta'){
            $TaBases = TaBase::whereSaleId($id);
            if($name != null){
                $TaBases = $TaBases->where('ta_name','like', '%' . $name . '%');
            }
            $TaBases = $this->taSaList($TaBases);
        }else{
            $TaBases = GuideBase::whereSaleId($id);
            if($name != null){
                $TaBases = $TaBases->where('real_name','like', '%' . $name . '%');
            }
            $TaBases = $this->guSaList($TaBases);
        }
        return view("boss.cuscomer.saler_detail",compact('Users','TaBases','tmp'));
    }

    function salerAdd(Request $request)
    {
        if($request->isMethod('post')){
            $User = new User();
            $data = $User->whereEmail($request->email)->first();

            if(empty($data)){
               $User->name = $request->name;
               $User->email = $request->email;
               $User->role_id = $request->role;
               $User->password = bcrypt($request->password);
               $User->invite_code = 's'.self::generateInviteCode(4);
               $User->save();
            }else{
               return 0;
            }

        }else{
            $Roles = Role::whereId(4)->select('id','name')->first();
            return view("boss.cuscomer.saler_add",['Roles' => $Roles]);
        }
    }

    function salerEdit($id)
    {
        $Users = User::whereId($id)->first();
        return view("boss.cuscomer.saler_edit",['Users' => $Users]);

    }

    private function sulists($SupplierBases)
    {
        $SupplierBases = $SupplierBases->paginate($this->page);

        foreach($SupplierBases as $SupplierBase){
            $OrderBase = OrderBase::whereSupplierId($SupplierBase->id)->whereIn('state',[OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED])->get();
            $SupplierBase->amount = 0;
            foreach($OrderBase as $v){
                $SupplierBase->amount = $SupplierBase->amount + $v['amount_goods'] + $v['amount_express'] ;
            }
            $SupplierBase->goods_num = GoodsBase::whereSupplierId($SupplierBase->id)->whereState(GoodsBase::state_online)->count();
        }

        return $SupplierBases;
    }

    private function salists($Users)
    {
        $Users = $Users->orderby('id','desc')->paginate($this->page);

        foreach($Users as $User){
            $User->ta_num = TaBase::whereSaleId($User->id)->count();
            $User->guide_num = GuideBase::whereSaleId($User->id)->count();
        }
        return $Users;
    }

    private function taSaList($TaBases)
    {
        $TaBases = $TaBases->orderby('id','desc')->paginate($this->page);

        foreach($TaBases as $TaBase){
            if(!empty($TaBase->ta_province_id)){
                $TaBase->province = ConfCity::where('id',$TaBase->ta_province_id)->select('name')->first();
            }
            if(!empty($TaBase->ta_city_id)){
                $TaBase->city = ConfCity::where('id',$TaBase->ta_city_id)->select('name')->first();
            }

            $TaBase->guide_num = GuideBase::whereTaId($TaBase->id)->count();

            $total_amount = TaBilling::whereTaId($TaBase->id)->whereState(TaBilling::state_fund)->whereInOut(TaBilling::in_income)->sum('amount');
            $return_amount = TaBilling::whereTaId($TaBase->id)->whereState(TaBilling::state_fund)->whereInOut(TaBilling::in_income)->sum('return_amount');

            $TaBase->amount = number_format($total_amount - $return_amount,2);//累计收益
        }

        $TaBases->action = 0;
        return $TaBases;
    }

    private function guSaList($TaBases)
    {
        $TaBases = $TaBases->paginate($this->page);
        foreach($TaBases as $TaBase){
            $TaBase->tour_num = UserWx::whereGuideId($TaBase->id)->count();
            if(empty($TaBase->real_name)){
                $TaBase->real_name = 'GID.'.$TaBase->id;
            }
            $TaBase->amount = UserBase::whereId($TaBase->uid)->pluck('amount');
        }
        $TaBases->action = 1;
        return $TaBases;
    }

}