<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\GenController;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\SupplierBase;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Models\SupplierSm;
use Illuminate\Support\Facades\Log;
use App\Models\SupplierExpress;
class StoreController extends GenController
{
    public $user = array();
    function __construct(){
        $this->user = \Session::get(SupplierBase::SESSION_SUPPLIER);
        $supplier = SupplierBase::whereId($this->user['id'])->first();
        $route = Route::currentRouteAction();
        list($controller, $action) = explode('@', $route);
        $controller = explode('\\',$controller);
        $controller = end($controller);
        View::share('controller', $controller);
        View::share('action', $action);
        View::share('supplier', $supplier);
    }

    /* 验证手机验证码 */
    function checkSupplierCode($mobile,$type,$code){
        $sms = SupplierSm::whereMobile($mobile)->whereType($type)->whereIsValid(0)->orderBy('created_at','desc')->first();
        if (!$sms){
            return false;
        }else{
            if($sms->code != $code){
                return false;
            }
        }
        return true;
    }

    public function supplierInfo()
    {
        $supplierExpress = SupplierExpress::whereSupplierId($this->user['id'])->first();
        $supplierBase = SupplierBase::whereId($this->user['id'])->first();
        $data['is_pick_up'] = $supplierBase->is_pick_up;
        $data['state'] = 0;

        if(!is_null($supplierExpress)){
            $data['total_amount'] = $supplierExpress->total_amount;
            $data['express_amount'] = $supplierExpress->express_amount;
            $data['state'] = $supplierExpress->state;
        }
        return $data;
    }

}
