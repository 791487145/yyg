<?php namespace App\Http\Controllers\Api;

use App\Models\GoodsBase;
use Log;
use Lang;
use App\Models\StoreUser;
use Illuminate\Http\Request;
use App\Http\Controllers\GenController;
use zgldh\QiniuStorage\QiniuStorage;


ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 90);
ini_set('max_input_time', 90);
class ImageController extends GenController
{

    /**
     *
     * @SWG\Post(path="/v1/upload/image",
     *   tags={"image"},
     *   summary="上传图片/声音",
     *   description="",
     *   operationId="uploadImage",
     *   @SWG\Parameter(
     *     name="body",
     *     in="formData",
     *     description="上传图片",
     *     required=true,
     *     type="file",
     *     @SWG\Schema(ref="#/definitions/file")
     *   ),
     * @SWG\Response(response=200,description="successful operation"),
     * )
     *
     */

    public function upload(Request $request)
    {


        $result = array();
        $result['key'] = '';
        Log::alert('$_FILE数据:' . print_r($_FILES, true));
        if (isset($_FILES['body']['tmp_name'])) {

            //ext name
            $image_info = getimagesize($_FILES['body']['tmp_name']);
            $file_name_arr = explode("/", $image_info['mime']);
            $file_name_ext = $file_name_arr[1];


            $disk = QiniuStorage::disk('qiniu');

            $contents = file_get_contents($_FILES['body']['tmp_name']);

            //name
            $file_name = substr(md5_file($_FILES['body']['tmp_name']), 12) . '.' . $file_name_ext;
            $disk->put($file_name, $contents);

        }

        return response()->json(array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('url' => $disk->downloadUrl($file_name))));
    }

}

