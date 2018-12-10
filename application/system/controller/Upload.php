<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/12
 * Time: 16:18
 */

namespace app\system\controller;


use app\extend\controller\TmUpload;
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
            return reTmJsonObj(500,$msg,[]);
        }
        $file = Request::file('file');
        if(!$file){
            return reTmJsonObj(500,'请检查服务器磁盘空间是否不足',[]);
        }
        $image = getimagesize($file->getInfo()['tmp_name']);
        if(!empty($inputData['width']) && $image[0] != $inputData['width']){
            return reTmJsonObj(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        if(!empty($inputData['height']) && $image[1] != $inputData['height']){
            return reTmJsonObj(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        $upload = new TmUpload($file->getInfo());
        $re = $upload->uploadFile();
        if ($re==false)return reTmJsonObj(500,$upload->getErrorMessage(),[]);
        $data = TmUpload::getUrl($re['type']) . $re['path'];
        return reTmJsonObj(200, '图片上传成功',$data);
    }

    /**
     * 文件上传,返回保存到服务器的路径,并未存入数据库
     */
    public function fileUpload(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }
        $file = Request::file('file');
        if(!$file){
            return reTmJsonObj(500, '上传失败');
        }
        $upload = new TmUpload($file->getInfo());
        $re = $upload->uploadFile();
        if ($re==false)return reTmJsonObj(500,$upload->getErrorMessage(),[]);
        $data = TmUpload::getUrl($re['type']) . $re['path'];
        return reTmJsonObj(200, '上传成功',$data);
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
            return reTmJsonObj(500,$msg,[]);
        }
        $file = Request::file('file');
        $re = \app\extend\controller\Upload::uploadFile($file);
        if(!$re){
            return reTmJsonObj(500,'上传失败',[]);
        }
        $image = getimagesize(Env::get('root_path').$re['path']);
        if(!empty($inputData['width']) && $image[0] != $inputData['width']){
            return reTmJsonObj(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        if(!empty($inputData['height']) && $image[1] != $inputData['height']){
            return reTmJsonObj(503,'图片尺寸要求为'.$inputData['width'].'px*'.$inputData['height'].'px!',[]);
        }
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $http = $http_type.$_SERVER['SERVER_NAME'];
        if($_SERVER["SERVER_PORT"]){
            $http = $http . ':'.$_SERVER["SERVER_PORT"];
        }
        $re = $http.$re;
       return reTmJsonObj(200, '图片上传成功', $re);
    }

    /**
     *  文件上传
     */
    public function uploadExternal(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }
        $file = Request::file('file');
        $data = \app\extend\controller\Upload::uploadFile($file);
        if($data){
            return reTmJsonObj(200, '上传成功',$data);
        }else{
            return reTmJsonObj(500, '上传失败');
        }

    }

    /**
     * 返回文件完整链接
     */
    public function returnFileUrl(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['path','type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }
        $data = \app\extend\controller\Upload::getUrl($inputData['path'],'',$inputData['type']);
        if($data){
            return reTmJsonObj(200,'成功',$data);
        }else{
            return reTmJsonObj(500,'失败');
        }

    }

}