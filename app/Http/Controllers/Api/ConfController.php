<?php namespace App\Http\Controllers\Api;

use App\Models\AppVersion;
use App\Models\ConfBank;
use App\Models\ConfBanner;
use App\Models\ConfCategory;
use App\Models\ConfCity;
use App\Models\ConfHotWord;
use App\Models\ConfNews;
use App\Models\ConfPavilion;
use App\Models\ConfPavilionTag;
use App\Models\PlatformNews;
use App\Models\UserNews;
use Log;
use Lang;
use Redis;
use Illuminate\Http\Request;
use App\Http\Controllers\GenController;


class ConfController extends GenController
{

    /**
     * @SWG\Get(path="/v1/conf/banner",
     *   tags={"conf"},
     *   summary="获取banner图",
     *   description="",
     *   operationId="conf",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pavilion_id",
     *     in="query",
     *     description="地方錧id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion_id")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getBanner(Request $request)
    {

        $pavilion_id = $request->input('pavilion_id',0);
        if($pavilion_id == 0 && env('APP_ENV') == 'production'){
            $pavilion_id = 24;
        }
        $ConfBanner = ConfBanner::whereState(ConfBanner::state_online);

        if($pavilion_id > 0){
            $ConfBanner = $ConfBanner->wherePavilionId($pavilion_id);
        }
        $ConfBanner = $ConfBanner->orderBy('display_order','desc')->get();

        $result = array();
        foreach($ConfBanner as $banner){
            $tmp = array();
            $tmp['name'] = $banner['name'];
            $tmp['type'] = strval($banner['url_type']);
            $tmp['url'] = $banner['url_content'];
            $tmp['cover'] = $banner['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$banner['cover'] : '';
            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/conf/search_word",
     *   tags={"conf"},
     *   summary="获取搜索词",
     *   description="",
     *   operationId="conf",
     *   produces={"application/json"},
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getSearchWord()
    {

        $ConfHotWord = ConfHotWord::orderBy('display_order','desc')->get();
        $result = array();
        foreach($ConfHotWord as $v){
            $tmp = array();
            $tmp['name'] = $v['name'];
            $tmp['goods_id'] = strval($v['url']);
            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/conf/pavilion",
     *   tags={"conf"},
     *   summary="获取地方馆",
     *   description="",
     *   operationId="conf",
     *  produces={"application/json"},
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getPavilion()
    {

        $ConfPavilion = ConfPavilion::whereState(ConfPavilion::state_online)->orderBy('display_order','desc')->get();
        $result = array();
        foreach($ConfPavilion as $pavilion){
            $tmp = array();
            $tmp['id'] = strval($pavilion['id']);
            $tmp['name'] = $pavilion['name'];
            $tmp['logo'] = $pavilion['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['cover'] : '';
            $tmp['cover'] = $pavilion['background'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['background'] : '';

            $tmp['newCover'] = $pavilion['new_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['new_cover'] : '';
            $tmp['background'] = $pavilion['background'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['background'] : '';
            $tmp['description'] = $pavilion['description'];

            $ConfPavilionTag = ConfPavilionTag::wherePavilionId($pavilion['id'])->orderBy('display_order','desc')->get();

            $pavilionTag = array();
            foreach ($ConfPavilionTag as $tag) {
                $tagArray = array();
                $tagArray['name'] = $tag['name'];
                $tagArray['goods_id'] = strval($tag['goods_id']);
                $pavilionTag[] = $tagArray;
            }

            $tmp['tags'] = $pavilionTag;
            $result[] = $tmp;
        }

        $list = array();

        $list['banner'] = array();
        $ConfBanner = ConfBanner::whereState(ConfBanner::state_online)->wherePavilionId(ConfBanner::pavilion_id_9999)->orderBy('display_order','desc')->get();
        foreach($ConfBanner as $banner){
            $tmp = array();
            $tmp['name'] = $banner['name'];
            $tmp['type'] = strval($banner['url_type']);
            $tmp['url'] = $banner['url_content'];
            $tmp['cover'] = $banner['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$banner['cover'] : '';
            $list['banner'][] = $tmp;
        }

        $list['list'] = $result;

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $list);
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/conf/news",
     *   tags={"conf"},
     *   summary="获取公告",
     *   description="",
     *   operationId="conf",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="用户id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *    @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getNews(Request $request)
    {
        $uid = intval($request->input('uid', 0));
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $PlatformNews = PlatformNews::whereState(PlatformNews::state_online)->orderBy('created_at','desc')->offset($offset)->limit($limit)->get();
        $result = array();
        foreach($PlatformNews as $new){
            $tmp = array();
            $tmp['title'] = $new['title'];
            $tmp['url'] = strval($new['url']);
            $tmp['image'] = $new['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$new['cover'] : '';
            $tmp['content'] = $new['content'];
            $tmp['created_at'] = date('Y.m.d H:i',strtotime($new['created_at']));
            $result[] = $tmp;
        }
        UserNews::whereUid($uid)->update(array('is_read'=>UserNews::is_read_yes));
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/conf/category",
     *   tags={"conf"},
     *   summary="获取商品分类",
     *   description="",
     *   operationId="conf",
     *  produces={"application/json"},
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getCategory(Request $request)
    {

        $ConfCategory = ConfCategory::orderBy('display_order','desc')->get();
        $result = array();
        foreach($ConfCategory as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['name'] = $v['name'];
            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/conf/timestamp",
     *   tags={"conf"},
     *   summary="获取时间戳",
     *   description="",
     *   operationId="conf",
     *  produces={"application/json"},
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function timestamp()
    {
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('timestamp' => strval(time())));
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/conf/city",
     *   tags={"conf"},
     *   summary="获取省市区",
     *   description="",
     *   operationId="conf",
     *  produces={"application/json"},
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getCity()
    {
        $result = json_decode(Redis::get('city'),true);

        if(empty($result)){

            $cities = ConfCity::whereState(1)->get(array('id', 'name', 'parent_id'));

            $result = array();
            foreach ($cities as $k => $city) {
                if ($city['parent_id'] == 1) {
                    $result[] = array('id' => strval($city['id']), 'name' => $city['name']);
                }
            }

            foreach ($result as $k => $city) {
                $tmp = array();
                foreach ($cities as $kk => $vv) {
                    if ($vv['parent_id'] == $city['id']) {
                        $tmp[] = array('id' => strval($vv['id']), 'name' => $vv['name']);
                    }
                }
                $result[$k]['data'] = $tmp;
            }

            foreach ($result as $k => $city) {
                foreach ($city['data'] as $kk => $vv) {
                    $tmp = array();
                    foreach ($cities as $kkk => $vvv) {
                        if ($vv['id'] == $vvv['parent_id']) {
                            $tmp[] = array('id' => strval($vvv['id']), 'name' => $vvv['name']);
                        }
                    }
                    $result[$k]['data'][$kk]['data'] = $tmp;
                }
            }
            Redis::set('city', json_encode($result));
        }


        return response()->json(array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result));
    }

    /**
     * @SWG\Get(path="/v1/conf/bank",
     *   tags={"conf"},
     *   summary="获取提现银行",
     *   description="",
     *   operationId="conf",
     *   produces={"application/json"},
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getBank()
    {
        $ConfBank = ConfBank::orderBy('display_order','asc')->get();
        $result = array();
        foreach($ConfBank as $v){
            $result[] = $v['name'];
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    /**
     *
     * @SWG\Get(path="/v1/conf/ios_version",
     *   tags={"conf"},
     *   summary="当前版本",
     *   description="",
     *   operationId="iOSVersion",
     *   @SWG\Parameter(
     *     name="version",
     *     in="query",
     *     description="当前版本号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     *
     */
    public function iOSVersion(Request $request)
    {

        $version = $request->input('version');
        $current_version = AppVersion::whereName('ios')->whereVersion($version)->first();
        $result = array();
        $result['version_number'] = isset($current_version['version']) ? strval($current_version['version']) : '';
        $result['version_url'] = isset($current_version['url']) ? $current_version['url'] : '';
        $result['change_log'] = isset($current_version['content']) ?$current_version['content'] : '';
        $result['is_force_update'] = isset($current_version['is_force']) ? strval($current_version['is_force']) : '0';
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    /**
     *
     * @SWG\Get(path="/v1/conf/android_version",
     *   tags={"conf"},
     *   summary="当前版本",
     *   description="",
     *   operationId="androidVersion",
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     *
     */
    public function androidVersion()
    {
        $current_version = AppVersion::whereName('android')->whereIsSelected(AppVersion::IS_SELECTED)->first();
        $result = array();
        $result['version_number'] = isset($current_version['version']) ? strval($current_version['version']) : '';
        $result['version_url'] = isset($current_version['url']) ? $current_version['url'] : '';
        $result['change_log'] = isset($current_version['content']) ?$current_version['content'] : '';
        $result['is_force_update'] = isset($current_version['is_force']) ? strval($current_version['is_force']) : '0';
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

}

