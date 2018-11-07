<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 10:32
 */
namespace app\extend\controller;


use OSS\OssClient;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\facade\Config;

class Upload
{
    private $ConfigUploadInfo = array();

    public function __construct()
    {
        $this->ConfigUploadInfo = Config::get('upload_info');
        if(!in_array($this->ConfigUploadInfo['type'],['local','qn','oss'])){ // 默认file
            $this->ConfigUploadInfo['type'] = 'local';
        }
    }

    /**
     * 获取文件域名信息
     */
    public static function getHostInfo(){
        $Upload = new Upload();
    }

    /**
     * 上传文件
     */
    public static function uploadFile($file,$path = '',$name = ''){
        $Upload = new Upload();
        $type = $Upload->ConfigUploadInfo['type'];
        return $Upload->$type($file,$path,$name);
    }

    /**
     *  获取链接
     */
    public static function getUrl($url,$name,$type){
        $Upload = new Upload();
        if(!in_array($type,['local','qn','oss'])){ // 默认file
            $type = 'local';
        }
        $type = $type.'Url';
        return $Upload->$type($url,$name);
    }

    /**
     * 本地上传
     */
    public function local($file,$path = '',$name = ''){
        $re = Uploads::fileUpload($file,$path,$name);
        if(!$re){
            return false;
        }
        return ['path'=>$re,'type'=>$this->ConfigUploadInfo['type']];
    }

    /**
     * 七牛上传
     * 必要配置参数
     *      'accessKey'=>'',
     *      'secretKey'=>'',
     *      'bucket'=>'',
     *      'upload'=>'',
     *      'cdn'=>'',
     */
    public function qn($file,$path = '',$name = ''){
        require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/qiniu/autoload.php';
        $QiNiu = new Auth($this->ConfigUploadInfo['qn_param']['accessKey'],$this->ConfigUploadInfo['qn_param']['secretKey']);
        // 生成上传 Token
        $token = $QiNiu->uploadToken($this->ConfigUploadInfo['qn_param']['bucket']);
        // 上传到七牛后保存的文件名
        $key = $name ? $path.$name : $path.$file->getInfo()['name'];
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token,$key,$file->getInfo()['tmp_name']);
        if ($err !== null) {
            return false;
        } else {
            return ['path'=>$ret['key'],'type'=>$this->ConfigUploadInfo['type']];
        }
    }

    /**
     * 获取阿里云oss类
     * @return OssClient
     */
    public function getOssClient(){
        require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/aliyuncs/oss-sdk-php/autoload.php';
        return new OssClient($this->ConfigUploadInfo['oss_param']['accessKeyId'], $this->ConfigUploadInfo['oss_param']['accessKeySecret'], $this->ConfigUploadInfo['oss_param']['endpoint']);
    }

    /**
     * oss上传
     * 必要配置参数
     * accessKeyId
     * accessKeySecret
     * endpoint
     * bucket
     */

    public function oss($file,$path = '',$name = ''){
        $name = $name?$path.$name : $path.$file->getInfo()['name'];
        $ossClient = $this->getOssClient();
        if (!$ossClient) {
            return false;
        }
        $result = $ossClient->uploadFile($this->ConfigUploadInfo['oss_param']['bucket'], $name, $file->getInfo()['tmp_name']);
        if($result){
            return ['path'=>$name,'type'=>$this->ConfigUploadInfo['type']];
        }
        return false;
    }

    /**
     * 生成ossUrl链接
     * @param $upload_url
     * @param string $file_name
     * @return bool|string
     */
    public function ossUrl($upload_url,$file_name = ''){
        $ossClient = $this->getOssClient();
        if (!$ossClient) {
            return false;
        }
        $options = [];
        if($file_name){
            $options['response-content-disposition'] = "attachment;filename=".$file_name;
        }
        $url = $ossClient -> signUrl($this->ConfigUploadInfo['oss_param']['bucket'],$upload_url,3600,'GET',$options);
        return $url;
    }

    /**
     * 获取七牛链接
     * @param $upload_url
     * @param string $file_name
     * @return string
     */
    public function qnUrl($upload_url,$file_name = ''){
        return $this->ConfigUploadInfo['qn_param']['cdn'].'/'.$upload_url;
    }

    /**
     * 获取本地链接
     * @param $upload_url
     * @param string $file_name
     */
    public function localUrl($upload_url,$file_name = ''){
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $http = $http_type.$_SERVER['SERVER_NAME'];
        if($_SERVER["SERVER_PORT"]){
            $http = $http . ':'.$_SERVER["SERVER_PORT"];
        }
        return $http.$upload_url;
    }
}