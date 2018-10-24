<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Store\StoreController;
use App\Models\ConfCity;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\GoodsBase;
use App\Models\GoodsMaterialBase;
use App\Models\SupplierSm;
use Illuminate\Http\Request;
use App\Models\GoodsMaterialImage;
use App\Http\Requests;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
class SupplierController extends StoreController
{
    /* GET供应商设置 */
    function set(){
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        $province = ConfCity::whereParentId(1)->get();
        $city = [];
        $store_city = [];
        if($supplier->province_id > 0){
            $city = ConfCity::whereParentId($supplier->province_id)->get();
        }
        if($supplier->store_province_id > 0){
            $store_city = ConfCity::whereParentId($supplier->store_province_id)->get();
        }
        return view('store.supplier.set')->with([
            'province'   => $province,
            'supplier'   => $supplier,
            'city'       => $city,
            'store_city' => $store_city
        ]);
    }

    /* POST供应商设置 */
    function setting(){
        $data = [
            'avatar' => Input::get('avatar'),
            'province_id' => Input::get('province_id'),
            'city_id' => Input::get('city_id'),
            'store_name' => Input::get('store_name'),
            'store_logo' => Input::get('store_logo'),
            'store_province_id' => Input::get('store_province_id'),
            'store_city_id' => Input::get('store_city_id'),
        ];
        SupplierBase::whereId($this->user['id'])->update($data);
        return Redirect::back();
    }
    
    /* 弹出框供应商设置 */
    function AlertSetting(){
        $supplierinfo = Session::get(SupplierBase::SESSION_SUPPLIER);
        $supplier_id  = $supplierinfo['id'];
        $params = Input::all();
        
        $data = array();
        $data['store_name']        = $params['store_name'];
        $data['store_logo']        = $params['image_name'];
        $data['store_province_id'] = $params['store_province_id'];
        $data['store_city_id']     = $params['store_city_id'];
   
        $id = SupplierBase::where('id',$supplier_id)->update($data);
        
        return Redirect::back();
    }
    
    /* 获取城市 */
    function getCity($province){
        if($province == 0){
            return response()->json(false);
        }
        $city = ConfCity::whereParentId($province)->get();
        return response()->json($city);
    }
    /* 文件上传 */
    function upload(){
        return response()->json(['img' => $this->uploadToQiniu()]);
    }
    
    /* 供应商设置插件上传 */
    function uploadPlupLoad(){
        return response()->json(['img' => $this->uploadPlupLoadToQu()]);
    }
   
    function getPassword(){
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        return view('store.supplier.password')->with(['supplier'=>$supplier]);
    }

    /* 修改密码 */
    function postPassword(){
        $password = Input::get('password');
        $password_confirm = Input::get('password_confirm');
        if (!$password || !$password_confirm){
            return $this->getReturnResult('no','密码不能为空');
        }
        if($password != $password_confirm){
            return $this->getReturnResult('no','两次密码不一致');
        }
        $user = SupplierBase::whereId($this->user['id'])->first();
        $flag = $this->checkSupplierCode($user->mobile,Input::get('type'),Input::get('mobile_code'));
        if (!$flag){
            return $this->getReturnResult('no','验证码错误');
        }
        $user->password = $this->passwdEncode($password,$user->salt);
        $user->save();
        SupplierSm::whereCode(Input::get('mobile_code'))->whereMobile($user->mobile)->whereType(Input::get('type'))->update(['is_valid'=>-1]);
        return $this->getReturnResult('yes','密码修改成功');
    }



}
