<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\GenController;
use App\Models\ConfExpress;
use App\Models\GoodsBase;
use App\Models\GoodsGift;
use App\Models\GoodsSpec;
use App\Models\OrderBase;
use App\Models\OrderExpress;
use App\Models\OrderLog;
use App\Models\OrderReturn;
use App\Models\SupplierSm;
use Illuminate\Http\Request;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Store\StoreController;
use Illuminate\Support\Facades\Input;
use PetstoreIO\Order;
use Maatwebsite\Excel\Facades\Excel;
class OrderController extends StoreController
{
    private $page = 20;

    public function export($state = 99){
        //接收参数
        $option = Input::all();
        //获取订单列表
        $orderBase = OrderBase::whereSupplierId($this->user['id']);
        $express_type = '';//订单支付状态
        //根据状态取结果
        if($state != 99){
            $param = substr($state,-4);
            if($param == 'abc1'){
                $express_type = substr($state,0,strlen($state)-4);
                $state = substr($state,-1);
            }
            if($state == 20){
                $orderBase = $orderBase->whereIn('state',[11,12,13]);
            }else{
                if($express_type > -1){
                    $orderBase = $orderBase->whereExpressType($express_type);
                }
                $orderBase = $orderBase->whereState($state);
            }

        }
        $orders = OrderBase::getList($orderBase,$option,99999);
        if($orders->isEmpty()){
            return redirect('/order/all');
        }
        /* 导出 */
        $this->exportOrders($orders);
        exit;
    }
    /* 导出订单 */
    function exportOrders($orders){
        if(!$orders->isEmpty()){
            $field = ['订单编号','商品名称','产品规格','付款金额','商品数量','运费','配送方式','买家留言','收货人姓名','收货地址-省市区',
                '收货地址-街道地址','收货人电话','物流公司（必填）','物流单号（必填）'];
        }
        if(!$orders->isEmpty()){
            Excel::create(date('YmdHis'),function($excel) use ($orders,$field){
                $excel->sheet('order', function($sheet) use ($orders,$field){
                    $sheet->setColumnFormat(array(
                        'A' => '0',
                        'K' => '0',
                        'M' => '0',
                        'N' => '0',
                    ));
                    $cellStart = 3;
                    foreach ($orders as $order){
                        //拼接数据
                        $title = '';
                        if(empty($order->goods)){
                            continue;
                        }else {
                            foreach ($order->goods as $goods) {
                                //取商品赠品
                                $name = '';
                                /*$giftBase = new GoodsGift();
                                $gifts = $giftBase->whereGoodsId($goods->goods_id)->get();
                                $name = '';
                                foreach ($gifts as $val) {
                                    $goodsBase = GoodsBase::whereId($val->gift_id)->first();
                                    $specBase = GoodsSpec::whereId($val->spec_id)->first();
                                    $name .= '赠品:' . $goodsBase->title . '----' . $specBase->name . "\r\n";
                                }*/
                                $title = $goods->title . "\r\n";
                                $goods->num;
                                $title = $title . $name;
                                $title = trim($title);

                                $data[] = [
                                    $order->order_no, $title, $goods->spec_name, $order->amount_real, $goods->num,
                                    $order->amount_express,$order->express_type, $order->buyer_message, $order->receiver_name,
                                    $order->receiver_info->province . $order->receiver_info->city, $order->receiver_info->district . $order->receiver_info->address,
                                    $order->receiver_mobile, $order->express_name, $order->express_no
                                ];
                                //dd($data);
                            }
                        }
                        //合并单元格
                        $mergeCells = count($order->goods);
                        $rangeArr = ['A','D','F','G','H','I','J','K','L','M','N'];
                        foreach ($rangeArr as $v){
                            $range = $v.$cellStart.':'.$v.($mergeCells + $cellStart -1);
                            $sheet->mergeCells($range);
                        }
                        $cellStart += $mergeCells;

                    }

                    //根据商品数量，合并单元格
                    $sheet->mergeCells('A1:M1');
                    $sheet->setHeight(1, 70);
                    $sheet->cell('A1', function($row){
                        $row->setBackground('#92d050');
                        $row->setFontWeight('bold');
                        $row->setFontSize(14);
                        $row->setFontColor('#e74430');
                        $row->setValue('注：为了更精准的完成批量发货，请填写完整的物流名称，如：韵达快递不能只写“韵达”，圆通快递不能只写“圆通”否则会导致批量发货不成功。您可以直接选择平台上已有的物流公司，如下：天天快递、全峰快递、百世汇通、韵达快递、圆通快递、中通快递、EMS、宅急送、顺丰快递、申通快递、优速快递、如风达、国通快递、联邦快递、邮政包裹/平邮、德邦物流、快捷快递');
                    });
                    $sheet->appendRow(2, $field);
                    $sheet->row(2,function($row){
                        $row->setFontWeight('bold');
                    });
                    $sheet->cell('L2', function($row){
                        $row->setBackground('#ffff00');
                        $row->setFontColor('#e74430');
                    });
                    $sheet->cell('M2', function($row){
                        $row->setBackground('#ffff00');
                        $row->setFontColor('#e74430');
                    });
                    $sheet->setWidth(array(
                        'A'=>20,
                        'B'=>60,
                        'C'=>15,
                        'D'=>10,
                        'E'=>15,
                        'F'=>10,
                        'G'=>30,
                        'H'=>15,
                        'I'=>20,
                        'J'=>20,
                        'K'=>20,
                        'L'=>20,
                        'M'=>20,
                        'N'=>20
                    ));
                    $i = 3;
                    foreach ($data as $val){
                        $sheet->row($i,$val);
                        $i++;
                    }
                });
            })->export('xlsx');
        }
    }
    /* 全部订单 */
    function all($state = 99){
        //接收参数
        $option = Input::all();
        //dd($option);
        //获取订单列表
        $orderBase = OrderBase::whereSupplierId($this->user['id']);
        //根据状态取结果
        if($state != 99){
            if($state == 20){
                $orderBase = $orderBase->whereIn('state',[11,12,13]);
            }else{
                $orderBase = $orderBase->whereState($state);
            }

        }
        $orders = OrderBase::getList($orderBase,$option,$this->page);
        //dd($orders);
        return view('store.order.all')->with(['orders'=>$orders,'state'=>$state,'option'=>$option]);
    }
    /* 发货订单 */
    function deliverys($express_type = 0)
    {
        //接收参数
        $option = \Input::all();
        //获取订单列表
        $state = OrderBase::STATE_PAYED;
        $orderBase = OrderBase::whereSupplierId($this->user['id'])->whereState($state)->whereExpressType($express_type);
        $orders = OrderBase::getList($orderBase,$option,$this->page);
        return view('store.order.deliverys')->with(['orders'=>$orders,'state'=>$state,'option'=>$option,'express_type' => $express_type]);
    }
    /* 发货 */
    function getDelivery($id){
        $orderBase = new OrderBase();
        $result = $orderBase->whereId($id)->whereSupplierId($this->user['id'])->whereState(OrderBase::STATE_PAYED)->first();
        if($result){
            $orderBase = OrderBase::whereSupplierId($this->user['id']);
            $order = OrderBase::getOrder($orderBase,$id);
            if($order){
                $order->express = ConfExpress::all();
                return view('store.order.delivery')->with(['order'=>$order]);
            }
        }
    }

    public function orderManySend(Request $request)
    {
        $id = $request->input('id');
        $time = date("Y-m-d H:i:s");
        $ret = OrderBase::whereIn('id',$id)->update(['state'=>OrderBase::STATE_SEND,'express_time'=>$time]);
        $orderBases = OrderBase::whereIn('id',$id)->get();
        foreach($orderBases as $orderBase){
            $orderLog = [
                'order_no' => $orderBase->order_no,
                'uid' => $this->user['id'],
                'action' => '订单发货',
                'content' => json_encode(['before_state'=>1,'after_state'=>2])
            ];
            OrderLog::create($orderLog);
        }
        return response()->json(['ret'=>$id]);
    }

    function postDelivery($id){
        $order = OrderBase::whereId($id)->whereSupplierId($this->user['id'])->whereState(OrderBase::STATE_PAYED)->first();
        $time = date("Y-m-d H:i:s");
        if ($order){
            if($order->express_type == 0){
                $order->express_name = Input::get('express_name','');
                $order->express_no = Input::get('express_no','');
            }
            $order->express_time = $time;
            $order->state = OrderBase::STATE_SEND;
            $result = $order->save();
            if($result){
                //发货提示短信
                self::sendDeliverySms($order->order_no);
            }
            //订单操作日志
            $orderLog = [
                'order_no' => $order->order_no,
                'uid' => $this->user['id'],
                'action' => '订单发货',
                'content' => json_encode(['before_state'=>1,'after_state'=>2])
            ];
            OrderLog::create($orderLog);
            return $this->getReturnResult('yes','发货成功');
        }
    }
    function getImport(){
        return view('store.order.import');
    }

    /* 导入文件 */
    function postImport(){
        $targetFolder = '/uploads';
        $verifyToken = md5('unique_salt' . Input::get('timestamp'));
        //补充逻辑
        $path = public_path();
        if(!file_exists($path.$targetFolder)){
            mkdir($path.$targetFolder,777);
        }
        Log::alert('$_FILE数据:' . print_r($_FILES, true));
        if (!empty($_FILES) && Input::get('token') == $verifyToken) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
            $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];

            // Validate the file type
            $fileTypes = array('xls','csv','xlsx'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array($fileParts['extension'],$fileTypes)) {
                move_uploaded_file($tempFile,$targetFile);
                if(file_exists($targetFile)){
                    return $data = ['ret'=>'yes','msg'=>'订单成功','filename'=>$_FILES['Filedata']['name']];
                }
            }
        }
        return response()->json($this->getReturnResult('no','订单失败'));
    }

    /* 读取文件并批量修改订单 */
    public function importExcel(Request $request){
        $filename = $request->input('filename');
        $targetFolder = '/uploads';
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
        $filename = rtrim($targetPath,'/') . '/' . $filename;
        $error = array();
        Excel::load($filename, function($reader) use(&$error) {
            $data = $reader->all();
            $data = json_encode($data);
            $data = json_decode($data,true);
            array_shift($data);
            foreach ($data as $val){
                //获取所有的物流单号
                $express_no[] = $val[14];
            }
            $unique = array_unique($express_no);
            $repeat = array_diff_assoc ( $express_no, $unique );
            if ($repeat){
                foreach ($repeat as $key=>$v){
                    if ($v){
                        $error[$data[$key][1]] = '订单编号'.$data[$key][1].',运单编号'.$v.'重复，发货不成功';
                    }
                }
            }
            Log::alert('$error:' . print_r($error, true));
            foreach ($data as $key=>$val){
                //取待发货订单
                $param = 0;
                if(!is_null($error)){
                    foreach($error as $k=>$value){
                        if($val[1] == $k){
                            $param = 1;
                            break;
                        }
                    }
                }

                if($param == 1){
                    continue;
                }

                $order = OrderBase::whereOrderNo($val[1])->whereSupplierId($this->user['id'])->first();
                if (!$order){
                    continue;
                }

                if ($order->state == OrderBase::STATE_PAYED){
                    //未填写物流单号或物流公司
                    if (empty($val[14]) || empty($val[13])){
                        $error[] = '订单编号'.$val[1].',未填写物流公司或物流单号，发货不成功';
                        continue;
                    }
                    if(in_array($val[10],$repeat)){
                        continue;
                    }
                    $orderDeliveryResult = OrderBase::whereSupplierId($this->user['id'])->whereOrderNo($val[1])->update([
                        'express_name'=>$val[13],
                        'express_no'=>$val[14],
                        'state'=>OrderBase::STATE_SEND,
                        'express_time'=>date("Y-m-d H:i:s"),
                    ]);
                    if($orderDeliveryResult){
                        self::sendDeliverySms($val[1]);
                    }
                }
            }
        });
        if(empty($error)){
            return response()->json(['ret'=>'yes']);
        }
        return response()->json(['ret'=>'no','msg'=>$error]);
    }

    static private function sendDeliverySms($order_no){
        $orderBaseInfo = OrderBase::whereOrderNo($order_no)->first();
        if($orderBaseInfo){
            $mobile = $orderBaseInfo->receiver_mobile;
            $text = '【易游购】主人，您购买的美食已发货，'.$orderBaseInfo->express_name.'：'.$orderBaseInfo->express_no.'，您可以关注易游购微信公众号自助查询快递信息，客服电话400-915-8971';
            $result = GenController::sendSms($text,$mobile);
            $result = json_decode($result,true);
            if($result['msg'] == 'OK'){
                $sid = urlencode($text);
            }else{
                $result['msg'] = urlencode($result['msg']);
                $result['detail'] = urlencode($result['detail']);
                $sid = $result;
            }
            $suppliersSms = new SupplierSm();
            $suppliersSms->type = SupplierSm::SEND_DELIVERY_SMS;
            $suppliersSms->mobile = $mobile;
            $suppliersSms->code = '000000';
            $suppliersSms->sid = urldecode(json_encode($sid));
            $suppliersSms->ip = '0.0.0.0';
            $suppliersSms->save();
        }
    }
    /* 售后订单 */
    function afterSales($state = 0){
        $option = Input::all();
        $afterSales = OrderReturn::whereSupplierId($this->user['id'])->whereState($state)->orderBy('id','desc')->paginate($this->page);
        $orders = [];
        if (!$afterSales->isEmpty()){
            $orderNos = [];
            foreach ($afterSales as $order){
                $orderNos[] = $order->order_no;
            }
            $option['after_sale'] = $orderNos;
            $orderBase = OrderBase::whereSupplierId($this->user['id']);
            $orders = OrderBase::getList($orderBase,$option,$this->page);
        }
        return view('store.order.aftersales')->with(['orders'=>$orders,'state'=>$state,'option'=>$option]);
    }

    /* 售后订单详情 */
    public function afterSale($id){
        $orderBase = OrderBase::whereSupplierId($this->user['id']);
        $order = OrderBase::getOrder($orderBase,$id);
        if($order){
            //dd($order);
            return view('store.order.aftersale')->with(['order'=>$order,'id'=>$id]);
        }
    }

    public function afterSaleUpdate(Request $request,$id){
        $express_no = $request->input('express_no');
        $express_name = $request->input('express_name');
        $orderBase = new OrderBase();
        $orderBase->whereId($id)->update(['express_no'=>$express_no,'express_name'=>$express_name]);
    }

    /* 订单详情 */
    function show($id){
        $orderBase = OrderBase::whereSupplierId($this->user['id']);
        $order = OrderBase::getOrder($orderBase,$id);
        
        //用来获取当前订单的其他物流单号
        $order_no    = $order->order_no;
        $expressInfo = OrderExpress::whereOrderNo($order_no)->get();
        if($order){
            return view('store.order.show')->with(['order'=>$order,'expressInfo'=>$expressInfo]);
        }
    }
    
    /**
     * 添加物流单号的函数
     * @param string $orderno 订单号
     * @param string $expressno 物流单号
     * @return \Illuminate\Http\JsonResponse
     */
    function addexpress($orderno,$expressno){
        $orderBaseInfo = OrderBase::whereOrderNo($orderno)->first();
        $express_name  = $orderBaseInfo->express_name;
        $pattern = '/^[0-9]{1,}+$/';
        if(!preg_match($pattern, $expressno)){
            return response()->json(['ret'=>'fail','msg'=>'请用纯数字作为快递单号']);
        }
        $num  = OrderExpress::create(['order_no'=>$orderno,'express_name'=>$express_name,'express_no'=>$expressno]);
        if($num){
            $data = array(
                'express_name'=>$express_name,
                'express_no'=>$expressno,
            );
            return response()->json(['ret'=>'yes','content'=>$data]);
        }else{
            return response()->json(['ret'=>'no','content'=>'快递单号添加失败']);
        }
    }

    public function showList(Request $request)
    {
        $order_no = $request->input("order_no");
        $express_no = $request->input("express_no");
        $ret = OrderBase::whereOrderNo($order_no)->update(['express_no'=>$express_no]);
        if($ret == 1){
            return response()->json(['ret'=>'yes','content'=>$express_no]);
        }
        return response()->json(['ret'=>'no','content'=>$express_no]);
    }

}
