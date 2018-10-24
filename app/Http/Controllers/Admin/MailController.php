<?php
namespace App\Http\Controllers\Admin;

use App\Models\GuideAudit;
use Mail;
use App\Http\Requests;
use App\Models\GuideBase;
use App\Models\UserBase;
use App\Http\Controllers\Controller;

class MailController extends Controller{


    /*
     * 通过邮件的方式审核导游
     * id  guide_id
     * */
    static public function sendMailQueueMethod($id){

        $guideBaseInfo = GuideBase::whereId($id)->first();
        $userBaseInfo = UserBase::whereId($guideBaseInfo->uid)->first();
        $guideBaseInfo->mobile = $userBaseInfo->mobile;

        if($userBaseInfo->state == UserBase::state_upload_2cert){
            $subject = '导游审核';
            $mailList = explode(',',env('MAIL_LIST'));
            $template = 'guideMailAudit';
            $templateData = $guideBaseInfo;
            self::sendMail($template,$templateData,$subject,$mailList);
        }

    }

    static private function sendMail($template,$templateData,$subject,$mailList){
        $flag = Mail::send($template,['data'=>$templateData],function($message)use ($subject,$mailList){
            foreach($mailList as $value){
                $message ->to($value)->subject($subject);
            }
        });
        return $flag;
    }

}
?>