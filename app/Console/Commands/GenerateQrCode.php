<?php

namespace App\Console\Commands;

use App\Models\GoodsBase;
use App\Models\GoodsImage;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderPay;
use App\Models\OrderReturn;
use App\Models\PlatformBilling;
use App\Models\PlatformSm;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\SupplierSm;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\TaGroup;
use App\Models\TaSm;
use App\Models\UBase;
use App\Models\UserBase;
use Log;
use QrCode;
use Illuminate\Console\Command;
use zgldh\QiniuStorage\QiniuStorage;
use App\Http\Controllers\Wx\WxLocationController;

class GenerateQrCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:qrcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成导游店铺二维码';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        self::updateGuideQrCode();
        self::guideQrCode();
    }

    static function updateGuideQrCode(){

        $GuideBase = GuideBase::whereQrcode('')->get();
        foreach($GuideBase as $v){
            $content = Qrcode::format('png')->size(200)->margin(1)->generate('http://'.env('H5_DOMAIN').'/guide/'.$v['id'].'?gid='.$v['id']);

            $file_name = 'qr_guide_'.$v['id'].'_'.date('YmdHis').'.png';

            $disk = QiniuStorage::disk('qiniu');
            $disk->put($file_name, $content);
            GuideBase::whereId($v['id'])->update(array('qrcode'=>$file_name));
        }

    }

    static function guideQrCode()
    {
        $GuideBases = GuideBase::whereWxQrcode('')->get();
        $access_token = WxLocationController::accessToken();
        foreach($GuideBases as $guideBase){
            $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
            $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$guideBase->id.'}}}';
            $res = json_decode(WxLocationController::postCurl($url,$data));
            $ticket = urlencode($res->ticket);
            //$qrUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
            GuideBase::whereId($guideBase->id)->update(['wx_qrcode'=>$ticket]);
        }
    }

}
