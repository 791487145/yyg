<?php

namespace App\Http\Controllers\Wx;

use App\Models\CouponUser;
use App\Models\GuideBase;
use Log;
use Input;
use Cookie;
use Validator;
use App\Models\UserWx;
use App\Http\Requests;
use App\Models\UserBase;
use App\Models\ConfCity;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Models\UserFavorite;
use App\Http\Controllers\GenController;

class AddressController extends WxController
{
    public function addressList(Request $request)
    {
        if(env('APP_ENV') == 'local'){
            $open_id = 'oY1sE1F30sAlbsa1vnSlDU8Jhh6A';
        }else{
            $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
        }
        if($open_id != ''){
             Cookie::queue('openid',$open_id);
        }
        $User_wxs = UserWx::whereOpenId($open_id)->first();

        if($User_wxs->uid == 0){
            return redirect('/address');
        }
        $count = UserAddress::whereUid($User_wxs->uid)->count();
        if($count == 0){
            return redirect('/address');
        }

        $UserAddresses = '';
        if(isset($User_wxs->uid) && $User_wxs->uid != 0){
            $UserAddresses = UserAddress::whereUid($User_wxs->uid)->get();
            foreach($UserAddresses as $UserAddress){
                $UserAddress->province = self::getCityName($UserAddress->province_id);
                $UserAddress->city = self::getCityName($UserAddress->city_id);
                $UserAddress->district = self::getCityName($UserAddress->district_id);
            }
        }
        return view('wx.address.address',compact('UserAddresses'));
    }

    public function addressAdd(Request $request)
    {
        if(env('APP_ENV') == 'local'){
            $open_id = 'oY1sE1F30sAlbsa1vnSlDU8Jhh6A';
        }else{
            $open_id = Cookie::get('openid');
        }

        $uid = UserWx::whereOpenId($open_id)->first();
        if($request->isMethod('post')){
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required',
                'mobile' => 'required',
                'province' => 'required',
                'city' => 'required',
                'district' => 'required',
                'phone' => 'required',
                'address' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['ret'=>'not']); //输出错误信息;
            }
            $UserAddress = new UserAddress();
            $UserAddress->name = $data['name'];
            $UserAddress->mobile = $data['mobile'] ;
            $UserAddress->is_default = (isset($data['is_default']) ? $data['is_default'] : 0 ) ;
            $UserAddress->province_id = $data['province'] ;
            $UserAddress->city_id = $data['city'] ;
            $UserAddress->district_id = $data['district'] ;
            $UserAddress->address = $data['address'] ;

            if($uid->uid != 0){
                $num = $UserAddress->whereUid($uid->uid)->count();
                if($num == 0){//没有地址第一个默认
                    $UserAddress->is_default = 1;
                }
                if($UserAddress->is_default == 1){//此地址为默认地址时将其余改为非默认
                    $UserAddress->whereUid($uid->uid)->update(array('is_default'=>0));
                }

                if($data['tel'] != $data['phone']){//用户手机号码修改
                    $UserBase = UserBase::whereMobile($data['phone'])->first();
                    if(is_null($UserBase)){
                        $UserBase = new UserBase();
                        $UserBase->mobile = $data['phone'];
                        $UserBase->nick_name = $data['name'];
                        $UserBase->save();
                        UserFavorite::whereOpenId($open_id)->update(['uid'=>$UserBase->id]);
                        UserWx::whereOpenId($open_id)->update(['uid'=>$UserBase->id]);
                        $UserAddress->uid = $UserBase->id;
                        $UserAddress->is_default == 1;
                        Log::alert('44444444');
                    }else{
                        //导游
                        $GuideBase = GuideBase::whereUid($UserBase['id'])->first();
                        if(!is_null($GuideBase)){
                            UserWx::whereOpenId($open_id)->update(['guide_id'=>$GuideBase['id']]);
                            Log::alert('5555555555');
                        }
                        UserWx::whereOpenId($open_id)->update(['uid'=>$UserBase['id']]);
                        $UserAddress->uid = $UserBase->id;
                        Log::alert('666666666666');
                    }
                }else{
                    $UserAddress->uid = $uid->uid;
                }
            }else{//第一次添加
                if($data['phone'] == ''){
                    return response()->json(['ret'=>'no','msg'=>'请填写手机号']);
                }

                $UserBases = UserBase::whereMobile($data['phone'])->first();
                $UserWx = new UserWx();
                if(is_null($UserBases)){
                    $UserBase = new UserBase();
                    $UserBase->nick_name = $data['name'];
                    $UserBase->mobile = $data['phone'];
                    $UserBase->save();
                    UserFavorite::whereOpenId($open_id)->update(['uid'=>$UserBase->id]);
                    UserWx::whereOpenId($open_id)->update(['uid'=>$UserBase->id]);
                    $UserAddress->uid = $UserBase->id;
                    Log::alert('11111111111');
                }else{
                    $GuideBase = GuideBase::whereUid($UserBases->id)->first();
                    //导游
                    if(!is_null($GuideBase)){
                        $UserWx->whereOpenId($open_id)->update(['uid'=>$UserBases->id,'guide_id'=>$GuideBase['id']]);
                        Log::alert('2222222222222222');
                    }else{
                        $UserWx->whereOpenId($open_id)->update(['uid'=>$UserBases->id]);
                        Log::alert('333333333333');
                    }
                    $UserAddress->uid = $UserBases->id;
                }
                $UserAddress->is_default = 1;
            }

            $num = UserAddress::whereUid($UserAddress->uid)
                ->whereMobile($UserAddress->mobile)
                ->whereName($UserAddress->name)
                ->whereProvinceId($UserAddress->province_id)
                ->whereCityId($UserAddress->city_id)
                ->whereDistrictId($UserAddress->district_id)
                ->whereAddress($UserAddress->address)
                ->count();
            if($num == 0){
                Log::alert('$UserAddress'.print_r($UserAddress,true));

                $UserAddress->save();
                CouponUser::whereOpenId($open_id)->update(['uid'=>$UserAddress->uid]);
            }
            return response()->json(['ret'=>'yes']);
            
        }

        $mobile[0] = '';
        if(isset($uid->uid) && $uid->uid != 0){
            $mobile = UserBase::whereId($uid->uid)->lists('mobile');
        }
        return view('wx.address.address_add',compact('mobile'));
    }

    public function addressEdit($id)
    {
        $UserAddress = UserAddress::whereId($id)->first();
        $UserAddress->province = self::getCityName($UserAddress->province_id);
        $UserAddress->city = self::getCityName($UserAddress->city_id);
        $UserAddress->district = self::getCityName($UserAddress->district_id);
        return view('wx.address.address_edit',compact('UserAddress'));
    }

    public function editAddress()
    {
        $open_id = Cookie::get('openid');
        $data = Input::all();
        $data['is_default'] = (isset($data['is_default']) ? $data['is_default'] : 0 ) ;
        $UserAddress = new UserAddress();

        if($data['is_default'] != 0){
            $uid = UserWx::whereOpenId($open_id)->lists('uid');
            $UserAddress->whereUid($uid[0])->update(['is_default'=>0]);
        }

        $UserAddress->whereId($data['id'])->update($data);
        return response()->json(['ret'=>'yes']);
    }

    public function addressAction($action,Request $request)
    {
        $id = $request->input('id','');
        $UserAddress = new UserAddress();

        if($action == 'del'){
            $uid = $UserAddress->whereId($id)->lists('uid');
            $num = $UserAddress->whereUid($uid[0])->count();
            $is_default = $UserAddress->whereId($id)->lists('is_default');
            $UserAddress->whereId($id)->delete();

            if($is_default[0] == 1){
                if($num > 1){
                    $UserAddresses = $UserAddress->whereUid($uid[0])->first();
                    $UserAddress->whereId($UserAddresses->id)->update(['is_default'=> 1 ]);
                    return response()->json(['id'=>$UserAddresses->id]);
                }
            }
            return response()->json(['id'=>0]);
            
        }else{
            $is_default = $request->input('is_default');
            if(!isset($is_default)){
                return response()->json(['ret'=>'one']);
            }
            $uid = UserAddress::whereId($id)->lists('uid');
            $UserAddress->whereUid($uid[0])->update(['is_default'=>0]);
            $UserAddress->whereId($id)->update(['is_default'=>$is_default]);
            return response()->json(['ret'=>'two']);
        }
    }

}
