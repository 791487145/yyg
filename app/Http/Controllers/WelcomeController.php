<?php namespace App\Http\Controllers;


use App\Http\Controllers\GenController;
use zgldh\QiniuStorage\QiniuStorage;
use Lang;


class WelcomeController extends GenController{


    function index(){
        echo 'hello';exit;
    }


    public function ueditor_upload(Request $request)
    {
        $action = $request->get('action');
        switch ($action) {

            case 'uploadimage':

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
                 *     "title" => "",          //新文件名
                 *     "original" => "",       //原始文件名
                 *     "type" => ""            //文件类型
                 *     "size" => "",           //文件大小
                 * )
                 */
                return response()->json(array('state' => 'SUCCESS', 'url' => $url, 'title' => $file_name), 200, [], JSON_UNESCAPED_UNICODE);

            case 'config':

                $config = config('ueditor.upload');
                return response()->json($config, 200, [], JSON_UNESCAPED_UNICODE);

        }


    }


}
