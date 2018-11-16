<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 11:19
 */

namespace app\api\controller;




use app\api\model\ClientVersionModel;
use think\facade\Request;

class System extends Base
{


    public function __construct()
    {
        parent::__construct();

    }

    /*是否包含微信配置和支付宝配置1*/
    public function haswechatalipayconfig(){
        $hasWechat = false;
        $hasAlipay = false;
        $wechatConfig = getWecatpayConfig();
        $alipayConfig = getAlipayConfig();
        if(!empty($wechatConfig['wechat_app_id']) && !empty($wechatConfig['wechat_mch_id']) && !empty($wechatConfig['wechat_key'])){
            $hasWechat = true;
        }
        if(!empty($alipayConfig['alipay_app_id']) && !empty($alipayConfig['alipay_public_key']) && !empty($alipayConfig['alipay_private_key'])){
            $hasAlipay = true;
        }
        return reJson(200, '成功', ["hasWechat"=>$hasWechat,"hasAlipay"=>$hasAlipay]);
    }


    /**
     *   检查客户端更新
     */
    public function checkUpdateClient(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['version','client_type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!((int)$inputData['version'] > 0)){
            return reJson(500,'版本号格式错误');
        }
        if(!in_array($inputData['client_type'],['iOS','Android'])){
            return reJson(500,'客户端类型错误');
        }
        $condition = [
            ['client_type','=',$inputData['client_type']],
            ['version','>',(int)$inputData['version']]
        ];
        $ClientVersionModel = new ClientVersionModel();
        $ClientList = $ClientVersionModel->versionList($condition,false,false,'version desc');
        if($ClientList === false){
            return reJson(500,'获取失败');
        }
        if(count($ClientList) > 0){
            $is_force = 0;
            foreach ($ClientList as $val){
                if($val['is_force'] == 1){ //如果版本中存在
                    $is_force = 1;
                    break;
                }
            }
            $ClientList[0]['add_time'] = date('Y-m-d h:i:s',$ClientList[0]['add_time']);
            $ClientList[0]['is_force'] = $is_force; //将最新的版本替换成强制更新
            $ClientList[0]['version'] = (int)$ClientList[0]['version'];
            return reJson(200,'发现最新版本',$ClientList[0]);
        }else{
            return reJson(200,'已经是最新版本');
        }
    }



}