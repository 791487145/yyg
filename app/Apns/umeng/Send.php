<?php
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');
require_once(dirname(__FILE__) . '/../../../vendor/vlucas/phpdotenv/src/Dotenv.php');


class Send {

    function sendAndroidUnicast($device_token,$uid,$title,$msg,$category,$origin_id) {
        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret($_SERVER['Android_MasterSecret']);
            $unicast->setPredefinedKeyValue("appkey",           $_SERVER['Android_APPKEY']);
            $unicast->setPredefinedKeyValue("timestamp",        strval(time()));
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens",    $device_token);
            $unicast->setPredefinedKeyValue("ticker",           $title);
            $unicast->setPredefinedKeyValue("title",            $title);
            $unicast->setPredefinedKeyValue("text",             $msg);
            $unicast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");
            // Set extra fields
            $unicast->setExtraField("uid", $uid);
            $unicast->setExtraField("category", $category);
            $unicast->setExtraField("id", $origin_id);

            error_log("\r\n".PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sending android unicast notification, please wait...\r\n",3,"/var/log/push_debug.log");
            error_log("Params:.$device_token.'||'.$uid.'||'.$title.'||'.$msg".PHP_EOL,3,"/var/log/push_debug.log");
            $result = $unicast->send();
            error_log('Result:'.print_r($result,true),3,"/var/log/push_debug.log");
            error_log("Sent SUCCESS\r\n",3,"/var/log/push_debug.log");
            error_log("--------------------------------------".PHP_EOL,3,"/var/log/push_debug.log");

        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSUnicast($device_token,$uid,$title,$category,$origin_id) {
        try {
            $unicast = new IOSUnicast();
            $unicast->setAppMasterSecret($_SERVER['IOS_MasterSecret']);
            $unicast->setPredefinedKeyValue("appkey",           $_SERVER['IOS_APPKEY']);
            $unicast->setPredefinedKeyValue("timestamp",        strval(time()));
            // Set your device tokens here
            //40207f1357d3e879103fa147741da4cde6bf98da1dcf9583322b612e0952fadf
            $unicast->setPredefinedKeyValue("device_tokens",    $device_token);
            $unicast->setPredefinedKeyValue("alert", $title);
            $unicast->setPredefinedKeyValue("badge", 0);
            $unicast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $unicast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $unicast->setCustomizedField("uid", $uid);
            $unicast->setCustomizedField("category", $category);
            $unicast->setCustomizedField("id", $origin_id);


            error_log("\r\n".PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sending IOS unicast notification, please wait...\r\n",3,"/var/log/push_debug.log");
            error_log("Params:.$device_token.'||'.$uid.'||'.$title".PHP_EOL,3,"/var/log/push_debug.log");
            $result = $unicast->send();
            error_log('Result'.print_r($result,true).PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sent SUCCESS\r\n",3,"/var/log/push_debug.log");
            error_log("--------------------------------------".PHP_EOL,3,"/var/log/push_debug.log");


        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
}




$dotenv = new Dotenv();
$dotenv->load(dirname(__FILE__) . '/../../../');
$Send = new Send();
$Send->sendIOSUnicast('c4da1e857e7c55535a91371660ea33a748f5e022ae884b48cda5f96f47d27017','25','测试公告推送标题','push_group','26');
exit;



$dotenv = new Dotenv();
$dotenv->load(dirname(__FILE__) . '/../../../');
$link    = mysql_connect($_SERVER['DB_HOST'], $_SERVER['DB_USERNAME'], $_SERVER['DB_PASSWORD']);
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $link);
$select_db = mysql_select_db($_SERVER['DB_DATABASE'], $link);

$Send = new Send();

$Send->sendAndroidUnicast('Aijhj4Zhzbd4F398UDfSIOS26L0rk8VIZnWfFGyL641d','25','测试公告推送标题','测试公告推送内容','push_new','26');
exit;




$sql = "select * from u_message where is_send=0";



$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
    $sql = "select * from devices where uid=".$row['uid'];
    $device = mysql_fetch_assoc(mysql_query($sql));
    $Send = new Send();
    if($device['app_name'] == 'ios'){
        $Send->sendIOSUnicast($device['device_token'],$row['uid'],$row['msg'],$row['category'],$row['origin_id']);
    }else{
        $Send->sendAndroidUnicast($device['device_token'],$row['uid'],$row['title'],$row['msg'],$row['category'],$row['origin_id']);
    }
    mysql_query("update u_message set is_send=1 where id={$row['id']}");
}
