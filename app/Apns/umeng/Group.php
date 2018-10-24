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

    function sendAndroidUnicast($device_token,$title,$msg,$category,$origin_id) {
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
            $unicast->setExtraField("category", $category);
            $unicast->setExtraField("id", $origin_id);

            error_log("\r\n".PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sending android unicast notification, please wait...\r\n",3,"/var/log/push_debug.log");
            $result = $unicast->send();
            error_log('Result:'.print_r($result,true),3,"/var/log/push_debug.log");
            error_log("Sent SUCCESS\r\n",3,"/var/log/push_debug.log");
            error_log("--------------------------------------".PHP_EOL,3,"/var/log/push_debug.log");

        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSUnicast($device_token,$title,$category,$origin_id) {
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
            $unicast->setPredefinedKeyValue("production_mode", "true");
            // Set customized fields
            $unicast->setCustomizedField("category", $category);
            $unicast->setCustomizedField("id", $origin_id);

            error_log("\r\n".PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sending IOS unicast notification, please wait...\r\n",3,"/var/log/push_debug.log");
            $result = $unicast->send();
            error_log('Result'.print_r($result,true).PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sent SUCCESS\r\n",3,"/var/log/push_debug.log");
            error_log("--------------------------------------".PHP_EOL,3,"/var/log/push_debug.log");
            return $result;


        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }


    function sendIOSCustomizedcast($uid,$title,$category,$origin_id,$production_mode) {
        try {
            $customizedcast = new IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($_SERVER['IOS_MasterSecret']);
            $customizedcast->setPredefinedKeyValue("appkey",           $_SERVER['IOS_APPKEY']);
            $customizedcast->setPredefinedKeyValue("timestamp",        strval(time()));

            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", $uid);
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", "iOS");
            $customizedcast->setPredefinedKeyValue("alert", $title);
            $customizedcast->setPredefinedKeyValue("badge", 0);
            $customizedcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedcast->setPredefinedKeyValue("production_mode", $production_mode);
            $customizedcast->setCustomizedField("category", $category);
            $customizedcast->setCustomizedField("id", $origin_id);


            error_log("\r\n".PHP_EOL,3,"/var/log/push_debug.log");
            $result = $customizedcast->send();
            error_log('Result'.print_r($result,true).PHP_EOL,3,"/var/log/push_debug.log");
            error_log("Sent SUCCESS\r\n",3,"/var/log/push_debug.log");
            error_log("--------------------------------------".PHP_EOL,3,"/var/log/push_debug.log");
            return $result;


        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
}




$dotenv = new Dotenv();
$dotenv->load(dirname(__FILE__) . '/../../../');
$link    = mysql_connect($_SERVER['DB_HOST'], $_SERVER['DB_USERNAME'], $_SERVER['DB_PASSWORD']);
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $link);
$select_db = mysql_select_db($_SERVER['DB_DATABASE'], $link);

$Send = new Send();
$msg = '你有一个新的旅游团来了，赶紧去接团';
$sql = "select * from ta_group where id=".$_SERVER["argv"][1];
$group = mysql_fetch_assoc(mysql_query($sql));

$production_mode = 'true';
if($_SERVER['APP_ENV'] == 'dev'){
    $production_mode = 'false';
}
if(!empty($group)){
    $sql = "select * from guide_base where id=".$group['guide_id'];
    $guide = mysql_fetch_assoc(mysql_query($sql));

    $sql = "select * from devices where uid=".$guide['uid'];
    $device = mysql_fetch_assoc(mysql_query($sql));

    if(isset($device['app_name']) && $device['app_name'] == 'android'){
        $ret = $Send->sendAndroidUnicast($device['device_token'],$msg,$msg,'push_group',$group['id']);
    }else{
        $ret = $Send->sendIOSCustomizedcast($guide['uid'],$msg,'push_group',$group['id'],$production_mode);
    }

    mysql_query("update ta_group set is_sent_msg=1 where id={$group['id']}");
}




//$Send->sendIOSCustomizedcast(10,'你有一个新的旅游团来了，赶紧去接团','push_group','26');
//$Send->sendAndroidUnicast('Aijhj4Zhzbd4F398UDfSIOS26L0rk8VIZnWfFGyL641d','你有一个新的旅游团来了','你有一个新的旅游团来了，赶紧去接团','push_group','26');
exit;

