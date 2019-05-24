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

class Wechatpubliclogin extends Controller
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
        $this->code = "";
    }


    public function getWechatInfo() {
        if(empty($this->appId) || empty($this->appSecret)){
            return reTmJsonObj(500, '未配置相关功能', []);
        }
        $inputData = Request::post();
        $method = Request::method();
        $params = ['code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $this->code = $inputData['code'];

        $dataCache = $this->getAccessToken();
        $access_token = $dataCache['access_token'];
        $openid = $dataCache['openid'];
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $res = json_decode($this->httpGet($url));
        if(!(is_object($res) && empty($res->unionid))){
            die('{"code":500,"msg":"登录失败","data":""}');
        }
        return reTmJsonObj(200, '登录成功', (array)$res);
    }


    private function getAccessToken() {
        $dataCache = Cache::get("wechat_login_access_token");
        if (!empty($dataCache['expire_time']) && $dataCache['expire_time'] > time()) {

        } else {
            // 如果是企业号用以下URL获取access_token
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appId&secret=$this->appSecret&code=$this->code&grant_type=authorization_code";
            $res = json_decode($this->httpGet($url));
            if(!(is_object($res) && empty($res->access_token))){
                die('{"code":500,"msg":"登录失败","data":""}');
            }
            $access_token = $res->access_token;
            if ($access_token) {
                $data = [
                    "expire_time"=>time() + 7000,
                    "access_token"=>$access_token,
                    "openid"=>$res->openid,
                    "scope"=>$res->scope
                ];
                Cache::set("wechat_login_access_token", $data);
                $dataCache = $data;
            }
        }
        return $dataCache;
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