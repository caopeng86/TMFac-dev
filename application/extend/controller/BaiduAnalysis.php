<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/8
 * Time: 10:07
 */
namespace app\extend\controller;

class BaiduAnalysis
{
    private $API_Key = 'FPYaPsW7vC1KlmL0bQ5ThcdIuOt7VcTe';
    private $Secret_Key = 'kiSp3yrE43qhAFhE7wOeOlSQmDn6H1GT';
    private $Key = '';

    public function __construct($key,$secret)
    {
        $this->API_Key = $key;
        $this->Secret_Key = $secret;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key){
        $this->Key = $key;
        return $this;
    }

    /**
     * 获取Code
     */
    public function getCodeUrl(){
        $redirect_uri = urlencode($this->getServerHost().'/api/baidu/resultCode.html');
        $url = 'http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&client_id='.$this->API_Key.'&redirect_uri='.$redirect_uri.'&scope=basic&display=popup';
        return $url;
    }


    /**
     * 获取百度token
     */
    public function getToken($code){
        $redirect_uri = urlencode($this->getServerHost().'/api/baidu/resultCode.html');
        $url = 'http://openapi.baidu.com/oauth/2.0/token?grant_type=authorization_code&code='.$code.'&client_id='.$this->API_Key.'&client_secret='.$this->Secret_Key.'&redirect_uri='.$redirect_uri;
        $result = curlGet($url);
        return $result;
    }

    /**
     * 获取域名
     */
    private function getServerHost(){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $http = $http_type.$_SERVER['SERVER_NAME'];
        if($_SERVER["SERVER_PORT"]){
            $http = $http . ':'.$_SERVER["SERVER_PORT"];
        }
        return $http;
    }

    /**
     * 更新Token
     */
    public function updateToken($refresh_token){
        $url = 'http://openapi.baidu.com/oauth/2.0/token?grant_type=refresh_token&refresh_token='.$refresh_token.'&client_id='.$this->API_Key.'&client_secret='.$this->Secret_Key;
        $result = curlGet($url);
        return $result;
    }

    /**
     * 获取报告级API接口
     */
    public function getDataByKey($token,$reportData){
        $reportData['access_token'] = $token;
        $reportData['key'] = $this->Key;
        $url = 'https://openapi.baidu.com/rest/2.0/mtj/svc/app/getDataByKey';
        $url = $this->packagingUrl($url,$reportData);
        $result = curlGet($url);
        return $result;
    }

    /**
     * 组装链接
     */
    private function packagingUrl($url,$data){
        $url .= '?';
        foreach ($data as $key => $val){
            $url .= $key.'='.$val.'&';
        }
        return $url;
    }

}