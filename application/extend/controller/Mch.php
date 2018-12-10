<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/7
 * Time: 13:54
 */

namespace app\extend\controller;


use think\Controller;
use think\facade\Request;

class Mch extends Controller
{
    private $ftpUrl = 'http://39.107.76.66/mch';
    private $wxUrl = 'http://39.107.76.66:8080/index.php?g=Sobey&m=Mch&a=mchToWeiWeb';
    public function pushMchData(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        $data = json_decode($inputData,true);

        $info = array();
        $info['pic'] = $ftpUrl.$data['logo'];
        $info['info'] = $data['content'];
        $info['text'] = $data['summary'];
        $info['title'] = $data['title'];
        $info['author'] = $data['author'];

        curlPost($wxUrl,$data);

        Logservice::writeArray($data, 'Mch接收数据测试');

        return reTmJsonObj(200, '接收数据成功', $inputData);
    }
}