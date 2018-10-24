<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;

class ImageController extends Controller
{
    function upload(Request $request){
//    	include '/lib/umeditor/php/Uploader.class.php';
//    	 //上传配置
//	    $config = array(
//	        "savePath" => "upload/" ,             //存储文件夹
//	        "maxSize" => 1000 ,                   //允许的文件最大尺寸，单位KB
//	        "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
//	    );
//	    //上传文件目录
//	    $Path = "upload/";
//
//	    //背景保存在临时目录中
//	    $config[ "savePath" ] = $Path;
//	    $up = new Uploader( "upfile" , $config );
//	    $type = $_REQUEST['type'];
//	    $callback=$_GET['callback'];
//
//	    $info = $up->getFileInfo();
//	    /**
//	     * 返回数据
//	     */
//	    if($callback) {
//	        echo '<script>'.$callback.'('.json_encode($info).')</script>';
//	    } else {
//	        echo json_encode($info);
//	    }
//	    exit;



    	if($request->editorid || $request->type == 'ajax'){
    		//ext name
            $image_info = getimagesize($_FILES['upfile']['tmp_name']);
            $file_name_arr = explode("/", $image_info['mime']);
            $file_name_ext = isset($file_name_arr[1]) ? $file_name_arr[1] : '';


            $disk = QiniuStorage::disk('qiniu');
            $contents = file_get_contents($_FILES['upfile']['tmp_name']);

            //name
            $file_name = 'ud_' . substr(md5_file($_FILES['upfile']['tmp_name']), 12) . date('His') . '.' . $file_name_ext;
            $disk->put($file_name, $contents);

            $url = $disk->downloadUrl($file_name);
            /**
             * 得到上传文件所对应的各个参数,数组结构
             * array(
             *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
             *     "url" => "",            //返回的地址
             *     "name" => "",          //新文件名
             *     "originalName" => "",   //原始文件名
             *     "type" => ""            //文件类型
             *     "size" => "",           //文件大小
             * )
             */
            $json = [
                'name'  =>  $file_name,
                'originalName'  =>  $_FILES['upfile']['name'],
                'size'  =>  $_FILES['upfile']['size'],
                'state' =>  'SUCCESS',
                'type'  =>  strtolower( strrchr( $_FILES['upfile']['name'] , '.' ) ),
                'url'   =>  $url,
            ];
            echo json_encode($json);
            //echo '--------------------------------';
            //echo  response()->json($json,200, [], JSON_UNESCAPED_UNICODE);
            //return response()->json($json);
    	}
        
    }
//name:"14915333026340.png"
//originalName:"icon_right_s.png"
//size:1930
//state:"SUCCESS"
//type:".png"
//url:"upload/20170407/14915333026340.png"



//     name:
// "1491535413177.jpg"
// originalName
// :
// "admin-login-bg.jpg"
// size
// :
// 54018
// state
// :
// "SUCCESS"
// type
// :
// ".jpg"
// url
// :
// "upload/20170407/1491535413177.jpg"
}
