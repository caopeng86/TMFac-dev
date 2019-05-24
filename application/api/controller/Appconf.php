<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 15:59
 */

namespace app\api\controller;


use think\Controller;
use think\facade\Env;
use think\facade\Request;

class Appconf extends Controller
{
    /**
     * 获取配置文件base64加密数据
     * @return \think\response\Json
     */
    public function getConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['file_name'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $path = Env::get('root_path').'appconf/'.$inputData['file_name'];

        if(isset($inputData['data'])){
            //如果传入base64编码数据则先执行覆盖操作
            if(is_file($path)){
                file_put_contents($path, $inputData['data']);
            }else{
                file_put_contents($path, $inputData['data']);
                chmod($path, 0777);
            }
        }

        if(!is_file($path)){
            return json([]);
        }
        $file = file_get_contents($path);
        if($file === false){
            die('{"code":500,"msg":"获取文件内容失败","data":""}');
        }

        $file = base64_decode($file);
        $file = json_decode($file, true);
        return json($file);
       // echo $file;die;
    }
}