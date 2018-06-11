<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/12
 * Time: 15:25
 */

namespace app\extend\controller;


use think\Controller;
use think\facade\Env;

class Uploads extends Controller
{
    /**
     * 获取文件全路径
     * @param $path
     * @return string
     */
    public static function getFilePath($path){
        $realPath = Env::get('root_path').'uploads/'.$path;
        return $realPath;
    }

    /**
     * 单文件上传
     * @param $file
     * @param string $path
     * @return bool|string
     */
    public static function fileUpload($file, $path=''){
        if(!is_object($file)){
            Logservice::writeArray(['err'=>'file参数需是一个对象'], '文件上传失败', 2);
            return false;
        }
        if(empty($path)){
            $path = 'default';
        }
        // 移动到目录下
        $info = $file->move(Env::get('root_path').'uploads/'.$path.'/');
        if($info){
            // 成功上传后返回保存路径
            return '/uploads/'.$path.'/'.$info->getSaveName();
        }else{
            // 上传失败获取错误信息
            Logservice::writeArray(['err'=>$file->getError()], '文件上传失败', 2);
            return false;
        }
    }

    /**
     * 多文件上传
     * @param $files
     * @param string $path
     * @return array|bool
     */
    public static function fileUploadAll($files, $path=''){
        if(!is_array($files)){
            Logservice::writeArray(['err'=>'files参数需是一个数组'], '文件上传失败', 2);
            return false;
        }
        if(empty($path)){
            $path = 'default';
        }
        $arr = [];
        foreach($files as $file){
            // 移动到框架应用根目录/uploads/目录下
            $info = $file->move(Env::get('root_path').'uploads/'.$path.'/');
            if($info){
                // 成功上传后返回保存路径
                $arr[] = '/uploads/'.$path.'/'.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                Logservice::writeArray(['err'=>$file->getError()], '文件上传失败', 2);
                return false;
            }
        }
        return $arr;
    }
}