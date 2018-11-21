<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 14:16
 */

namespace app\extend\controller;
use app\api\model\ConfigModel;
use think\facade\Env;
include_once Env::get('root_path').'vendor/Jpush/Client.php';
include_once Env::get('root_path').'vendor/Jpush/Config.php';
include_once Env::get('root_path').'vendor/Jpush/DevicePayload.php';
include_once Env::get('root_path').'vendor/Jpush/Http.php';
include_once Env::get('root_path').'vendor/Jpush/PushPayload.php';
include_once Env::get('root_path').'vendor/Jpush/ReportPayload.php';
include_once Env::get('root_path').'vendor/Jpush/Exceptions/JPushException.php';
include_once Env::get('root_path').'vendor/Jpush/Exceptions/APIRequestException.php';
include_once Env::get('root_path').'vendor/Jpush/Exceptions/ServiceNotAvaliable.php';
include_once Env::get('root_path').'vendor/Jpush/Exceptions/APIConnectionException.php';
//vendor('Jpush.Client');
//vendor('Jpush.Config');
//vendor('Jpush.DevicePayload');
//vendor('Jpush.Http');
//vendor('Jpush.PushPayload');
//vendor('Jpush.Exceptions.JPushException');
//vendor('Jpush.Exceptions.APIRequestException');
//vendor('Jpush.Exceptions.ServiceNotAvaliable');
//vendor('Jpush.Exceptions.APIConnectionException');

use think\Controller;
use think\facade\Config;

class Jpush extends Controller
{
    private static $appKye;
    private static $masterSecret;
    private static $cilent;
    private static $push;
    private static $device;

    public function __construct()
    {
        parent::__construct();
        $ConfigModel = new ConfigModel();
        $condition = array(
            'key'=>array('Jpush_key','Jpush_secret')
        );
        $JpushConfig = $ConfigModel->getConfigList($condition);
        if(!$JpushConfig){
            return reJson(500, 'Jpush配置信息不存在', []);
        }
        $JpushConfig = $ConfigModel->ArrayToKey($JpushConfig);
//        $this::$appKye = Config::get('Jpush')['app_key'];
//        $this::$masterSecret = Config::get('Jpush')['master_secret'];
        $this::$appKye = $JpushConfig['Jpush_key'];
        $this::$masterSecret = $JpushConfig['Jpush_secret'];
        $this::$cilent = new \JPush\Client($this::$appKye, $this::$masterSecret);
        $this::$push = Jpush::$cilent->push();
        $this::$device = Jpush::$cilent->device();
    }

    /**
     * 推送给全部用户
     * @param $extras
     * @return mixed
     */
    public static function JPushAll($extras) {
        //推送平台
        $platform = ['ios', 'android'];
        //显示标题
        $alert = $extras['title'];
        //android
        $androidExt = $extras;
        if(isset($androidExt['iosInfo'])){
            unset($androidExt['iosInfo']);
        }
        $android_notification = [
            'alert'  => $alert,
            'extras' => $androidExt
        ];
        //ios
        $iosExt = $extras;
        if(isset($iosExt['androidInfo'])){
            unset($iosExt['androidInfo']);
        }
        $ios_notification = [
            'alert'  => $alert,
            'extras' => $iosExt
        ];
        //通知
//        $notification = array(
//            'alert'  => $alert,
//            'extras' => $extras
//        );
        //自定义消息
//        $message = [
//            'msg_content' => $extras['content'],
//            'title'  => $alert,
//            'extras' => $extras
//        ];
        //可选参数
        $options = [
            'apns_production' => false,  //true是生产环境false是开发环境
//            'override_msg_id' => $extras['msg_id']
        ];
        $res = Jpush::$push->setPlatform($platform)
            ->addAllAudience()
//            ->setNotificationAlert($alert,$notification)
            ->iosNotification($alert,$ios_notification)
            ->androidNotification($alert,$android_notification)
//            ->message($alert,$message)
            ->options($options)
            ->send();
        return $res;
    }

    /**
     * 推送给单个用户
     * @param $extras
     * @return mixed
     */
    public static function JPushOne($extras) {
        //推送平台
        $platform = ['ios', 'android'];
        //显示标题
        $alert = $extras["title"];
        //android
        $androidExt = $extras;
        if(isset($androidExt['iosInfo'])){
            unset($androidExt['iosInfo']);
        }
        $android_notification = [
            'alert'  => $alert,
            'extras' => $androidExt
        ];
        //ios
        $iosExt = $extras;
        if(isset($iosExt['androidInfo'])){
            unset($iosExt['androidInfo']);
        }
        $ios_notification = [
            'alert'  => $alert,
            'extras' => $iosExt
        ];
//        $notification = array(
//            'alert'  => $alert,
//            'extras' => $extras
//        );
        //自定义消息
//        $message = [
//            'msg_content' => $extras['content'],
//            'title'  => $alert,
//            'extras' => $extras
//        ];
        //可选参数
        $options = [
            'apns_production' => false  //true是生产环境false是开发环境
        ];

        $res = Jpush::$push->setPlatform($platform)
            ->addAlias($extras['alias'])
//            ->setNotificationAlert($alert,$notification)
            ->iosNotification($alert,$ios_notification)
            ->androidNotification($alert,$android_notification)
//            ->message($alert,$message)
            ->options($options)
            ->send();
        return $res;
    }

    /**
     * 推送给某个省份的用户
     * @param $extras
     * @return mixed
     */
    public static function JPushProvince($extras) {
        //推送平台
        $platform = ['ios', 'android'];
        //显示标题
        $alert = $extras["title"];
        //安卓
        $android_notification = [
            'alert'  => $alert,
            'extras' => $extras
        ];
        //ios
        $ios_notification = [
            'alert'  => $alert,
            'extras' => $extras
        ];
        //通知
//        $notification = array(
//            'alert'  => $alert,
//            'extras' => $extras
//        );
        //自定义消息
        $message = [
            'msg_content' => $extras['content'],
            'title'  => $alert,
            'extras' => $extras
        ];
        //可选参数
        $options = [
            'apns_production' => false  //true是生产环境false是开发环境
        ];

        $res = Jpush::$push->setPlatform($platform)
            ->addAlias($extras['alias'])
//            ->setNotificationAlert($alert,$notification)
            ->iosNotification($alert,$ios_notification)
            ->androidNotification($alert,$android_notification)
            ->message($alert,$message)
            ->options($options)
            ->send();
        return $res;
    }

    /**
     *  获取推送情况
     */
    public function getRes($id){
        return Jpush::$cilent->report()->getReceived($id);
    }
}