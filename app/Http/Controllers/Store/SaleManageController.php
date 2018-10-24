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

class SaleManageController extends StoreController{
    
    /* 商品素材 */
    function goodmaterial(){
        //获取当前登录供应商的id
        $supplierinfo = Session::get(SupplierBase::SESSION_SUPPLIER);
        $supplier_id  = $supplierinfo['id'];
        //取出添加图片的关联商品
        $goodsinfo    = GoodsBase::where('supplier_id',$supplier_id)->whereIn('state',[GoodsBase::state_online,GoodsBase::state_down,GoodsBase::state_finish])->get();
        //取出每个供应商对应的素材
        $pagesize = 6;
        $num = GoodsMaterialBase::where('supplier_id',$supplier_id)->count();
        $totalpage = ceil($num/$pagesize);
        $materialinfo = GoodsMaterialBase::where('supplier_id',$supplier_id)->orderBy('created_at','desc')->paginate($pagesize);
        foreach($materialinfo as $key=>$val){
            //添加图片的代码
            $GoodsImages = GoodsMaterialImage::whereMaterialId($val['id'])->get();
            //封面图
            $goodbase    = GoodsBase::where('id',$val['goods_id'])->first();
            if(!is_null($goodbase)){
                $materialinfo[$key]['title'] = $goodbase['title'];
                $materialinfo[$key]['cover'] = $goodbase['cover'];
            }
            $img = array();
            foreach($GoodsImages as $images){
                $img[] = $images['image_name'];
            }
            $materialinfo[$key]['img'] = $img;
        }
        return view('store.material.goodmaterial',['goodsinfo'=>$goodsinfo,'materialinfo'=>$materialinfo,'supplier_id'=>$supplier_id,'pagesize'=>$pagesize,'num'=>$num,'totalpage'=>$totalpage]);
    }
    
    /* 添加素材 */
    function addMaterial()
    {
        $data = Input::all();
        //保存素材
        $goodsMaterialBase = new GoodsMaterialBase();
        $goodsMaterialBase->goods_id    = $data['goods_id'];
        $goodsMaterialBase->supplier_id = $data['supplier_id'];
        $goodsMaterialBase->content     = $data['content'];
        $goodsMaterialBase->save();
        //保存素材对应的照片
        if(isset($data['image_name'])){
            foreach($data['image_name'] as $img){
                $goodsMaterialImage = new GoodsMaterialImage();
                $goodsMaterialImage->image_name  = $img;
                $goodsMaterialImage->material_id = $goodsMaterialBase->id;
                $goodsMaterialImage->save();
            }
        }
        return redirect('/material/goodmaterial');
    
    }
    
    /* 删除素材 */
    function delmaterial($id)
    {
        GoodsMaterialBase::where('id',$id)->delete();
        $res = GoodsMaterialImage::where('material_id',$id)->delete();
        if($res){
            return response()->json(['ret'=>'yes']);
        }else{
            return response()->json(['ret'=>'no']);
        }
    }

    /* 新插件图片上传 */
    function uploadPlupLoad()
    {
        return response()->json(['img' => $this->uploadPlupLoadToQu()]);
    }

    public function supplierExpressList(Request $request)
    {
        if($request->isMethod('post')){
            $supplierExpress = SupplierExpress::whereSupplierId($this->user['id'])->first();
            if(is_null($supplierExpress)){
                $supplierExpress = new SupplierExpress();
            }
            $supplierExpress->title = $request->title;
            $supplierExpress->supplier_id = $request->supplier_id;
            $supplierExpress->total_amount = sprintf("%.2f", $request->total_amount);
            $supplierExpress->express_amount = sprintf("%.2f", $request->express_amount);
            $supplierExpress->save();
            return response()->json(['content'=>"操作成功"]);
        }

        $supplierBase = SupplierBase::whereId($this->user['id'])->first();
        $express = SupplierExpress::whereSupplierId($this->user['id'])->first();
        return view('store.material.supplier_express',compact('express','supplierBase'));
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}