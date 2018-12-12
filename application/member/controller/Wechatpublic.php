<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 11:19
 */

namespace app\member\controller;


use app\api\model\ConfigModel;
use think\Controller;
use think\facade\Cache;
use think\facade\Request;

class Wechatpublic extends Controller
{

    private $appId;
    private $appSecret;
    protected  $ConfigModel = '';
    public function __construct()
    {
        parent::__construct();
        $this->ConfigModel = new ConfigModel();
        $condition = [];
        $condition['key'] = ['wechat_public_app_id','wechat_public_app_secret'];
        $condition['type'] = 'wechat';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $this->appId = empty($ConfigList['wechat_public_app_id'])?"":$ConfigList['wechat_public_app_id'];
        $this->appSecret = empty($ConfigList['wechat_public_app_secret'])?"":$ConfigList['wechat_public_app_secret'];

    }


    public function getSignPackage() {
        if(empty($this->appId) || empty($this->appSecret)){
            return reTmJsonObj(500, '未配置相关功能', []);
        }
        $inputData = Request::post();
        $method = Request::method();
        $params = ['url'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $url = $inputData['url'];
        $jsapiTicket = $this->getJsApiTicket();
       /* $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";*/

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return reTmJsonObj(200, '登录成功', $signPackage);
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        $dataCache = Cache::get("wechat_ajsapi_ticket");
        if (!empty($dataCache['expire_time']) && $dataCache['expire_time'] > time()) {
            $ticket = $dataCache['jsapi_ticket'];
        } else {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data = [
                    "expire_time"=>time() + 7000,
                    "jsapi_ticket"=>$ticket
                ];
                Cache::set("wechat_ajsapi_ticket", $data);
            }
        }
        return $ticket;
    }

    private function getAccessToken() {
        $dataCache = Cache::get("wechat_access_token");
        if (!empty($dataCache['expire_time']) && $dataCache['expire_time'] > time()) {
            $access_token = $dataCache['access_token'];
        } else {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data = [
                    "expire_time"=>time() + 7000,
                    "access_token"=>$access_token
                ];
                Cache::set("wechat_access_token", $data);
            }
        }
        return $access_token;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}