<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/12
 * Time: 16:18
 */

namespace app\system\controller;


use app\extend\controller\Uploads;
use think\Controller;
use think\facade\Env;
use think\facade\Request;

class Upload extends Controller
{
    /**
     * 图片上传,返回保存到服务器的路径,并未存入数据库
     */
    public function imgUpload(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }
        $file = Request::file('file');
        $path = '';
        if(!empty($inputData['path'])){
            $path = $inputData['path'];
        }
        $re = Uploads::fileUpload($file,$path);
        if(!$re){
            return reJson(500,'上传失败',[]);
        }
        $image = getimagesize(Env::get('root_path').$re);
        if(!empty($inputData['width']) && $image[0] != $inputData['width']){
            return reJson(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        if(!empty($inputData['height']) && $image[1] != $inputData['height']){
            return reJson(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        return reJson(200, '图片上传成功', $re);
    }


    /**
     * 图片上传,返回保存到服务器的路径,并未存入数据库 返回包含域名
     */
    public function imgUploadInHost(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }
        $file = Request::file('file');
        $re = Uploads::fileUpload($file);
        if(!$re){
            return reJson(500,'上传失败',[]);
        }
        $image = getimagesize(Env::get('root_path').$re);
        if(!empty($inputData['width']) && $image[0] != $inputData['width']){
            return reJson(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        if(!empty($inputData['height']) && $image[1] != $inputData['height']){
            return reJson(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $http = $http_type.$_SERVER['SERVER_NAME'];
        if($_SERVER["SERVER_PORT"]){
            $http = $http . ':'.$_SERVER["SERVER_PORT"];
        }
        $re = $http.$re;
       return reJson(200, '图片上传成功', $re);
    }
}