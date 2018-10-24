<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Store\StoreController;
use App\Models\ConfCity;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\GoodsBase;
use App\Models\GoodsMaterialBase;
use App\Models\SupplierExpress;
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

class ExpressManageController extends Controller
{
    public function supplierExpressEdit(Request $request)
    {
        $action = $request->input('action');
        $state = $request->input('state',0);
        $supplierInfo = Session::get(SupplierBase::SESSION_SUPPLIER);
        if($action == "is_pick_up"){
            $ret = SupplierBase::whereId($supplierInfo['id'])->update(['is_pick_up'=>$state]);
        }
        return response()->json(['ret'=>'yes']);
    }

}
    


    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
