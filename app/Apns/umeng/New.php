<?php
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/../../../vendor/vlucas/phpdotenv/src/Dotenv.php');


class Send {
    function sendAndroidBroadcast() {
        try {
            $brocast = new AndroidBroadcast();
            $brocast->setAppMasterSecret($_SERVER['Android_MasterSecret']);
            $brocast->setPredefinedKeyValue("appkey",           $_SERVER['Android_APPKEY']);
            $brocast->setPredefinedKeyValue("timestamp",        strval(time()));
            $brocast->setPredefinedKeyValue("ticker",           "你有新的一条平台公告未读");
            $brocast->setPredefinedKeyValue("title",            "你有新的一条平台公告未读");
            $brocast->setPredefinedKeyValue("text",             "你有新的一条平台公告未读，立即查看");
            $brocast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $brocast->setPredefinedKeyValue("production_mode", "false");
            // [optional]Set extra fields
            $brocast->setExtraField("category", "push_new");
            print("Sending broadcast notification, please wait...\r\n");
            $ret = $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSBroadcast() {
        try {
            $brocast = new IOSBroadcast();
            $brocast->setAppMasterSecret($_SERVER['IOS_MasterSecret']);
            $brocast->setPredefinedKeyValue("appkey",           $_SERVER['IOS_APPKEY']);
            $brocast->setPredefinedKeyValue("timestamp",        strval(time()));

            $brocast->setPredefinedKeyValue("alert", "你有新的一条平台公告未读，立即查看");
            $brocast->setPredefinedKeyValue("badge", 1);
            $brocast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $brocast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $brocast->setCustomizedField("category", "push_new");
            print("Sending broadcast notification, please wait...\r\n");
            $ret = $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

}




$dotenv = new Dotenv();
$dotenv->load(dirname(__FILE__) . '/../../../');
$Send = new Send();
$Send->sendIOSBroadcast();
$Send->sendAndroidBroadcast();
exit;
