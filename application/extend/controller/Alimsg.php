<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 10:08
 */

namespace app\extend\controller;
//vendor('alimsg.vendor.autoload');
use think\facade\Env;
include_once Env::get('root_path').'vendor/alimsg/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

use think\Controller;

// 加载区域结点配置
Config::load();

class Alimsg extends Controller
{
    private static $acsClient = null;
    private static $accessKeyId;
    private static $accessKeySecret;
    private static $phoneNumbers;
    private static $templateCode;
    private static $signName;
    private static $code;

    public function __construct($config=array())
    {
        parent::__construct();
        $this::$accessKeyId = \think\facade\Config::get('alimsg')['access_key_id'];
        $this::$accessKeySecret = \think\facade\Config::get('alimsg')['access_key_secret'];
        $this::$signName = \think\facade\Config::get('alimsg')['sign_name'];
        $this::$phoneNumbers = $config['phone_numbers'];
        $this::$templateCode = $config['template_code'];
        $this::$code = $config['code'];
    }

    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    private static function getAcsClient() {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        $accessKeyId = Alimsg::$accessKeyId; // AccessKeyId

        $accessKeySecret = Alimsg::$accessKeySecret; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    /**
     * 发送短信
     * @return mixed|\SimpleXMLElement
     */
    public static function sendSms() {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置短信接收号码
        $request->setPhoneNumbers(Alimsg::$phoneNumbers);
        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName(Alimsg::$signName);
        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode(Alimsg::$templateCode);
        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(Array(  // 短信模板中字段的值
            "code"=>Alimsg::$code,
        )));
        // 可选，设置流水号
//        $request->setOutId("");
        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
//        $request->setSmsUpExtendCode("");
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        return $acsResponse;
    }

}