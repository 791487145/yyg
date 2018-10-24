<?php

namespace App\Http\Controllers\Activity;

use Cookie;
use App\Models\UserWx;
use Illuminate\Http\Request;
use App\Http\Controllers\Wx\WxController;

class ActivityController extends WxController
{
    public function index(Request $request)
    {
        $gid = $request->input('gid',0);
        if($gid > 0) {
            if (env('APP_ENV') == 'local') {
                $open_id = 'oY1sE1F30sAlbsa1vnSlDU8Jhh6A';
            } else {
                $open_id = self::setWxAuth($request, $_SERVER['REQUEST_URI']);
            }
            if ($open_id != '') {
                Cookie::queue('openid', $open_id);
            }
            $ref = 'Theme';
            self::bindWxGuideId($open_id, $gid, $ref);
        }
        $fileName = $request->input('filename');
        $fileName = 'special'.$fileName;
        return view('activity.'.$fileName);
    }
}
