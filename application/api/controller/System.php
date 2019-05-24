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
use app\api\model\ConfigModel;
use think\Controller;

class System extends Controller
{

    protected  $ConfigModel = '';
    public function __construct()
    {
        parent::__construct();
        $this->ConfigModel = new ConfigModel();

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
        return reTmJsonObj(200, '成功', ["hasWechat"=>$hasWechat,"hasAlipay"=>$hasAlipay]);
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
            return reTmJsonObj(500, $msg, []);
        }
        if(!((int)$inputData['version'] > 0)){
            return reTmJsonObj(500,'版本号格式错误');
        }
        if(!in_array($inputData['client_type'],['iOS','Android'])){
            return reTmJsonObj(500,'客户端类型错误');
        }
        $condition = [
            ['client_type','=',$inputData['client_type']],
            ['version','>',(int)$inputData['version']]
        ];
        $ClientVersionModel = new ClientVersionModel();
        $ClientList = $ClientVersionModel->versionList($condition,false,false,'version desc');
        if($ClientList === false){
            return reTmJsonObj(500,'获取失败');
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
            return reTmJsonObj(200,'发现最新版本',$ClientList[0]);
        }else{
            return reTmJsonObj(200,'已经是最新版本');
        }
    }

    /*是否开启签到*/
    public function hasopensign(){
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
       // $condition['key'] = ['wechat_app_id','wechat_mch_id','wechat_key'];
        $condition['type'] = 'point';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if(empty($ConfigList)){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $retList = [];
        $arr = [
            "first_login", "sex", "birthday", "mobile", "wb", "wx", "qq", "sign", "sign_cycle_first", "sign_cycle_two", "sign_extra_two",
        "sign_extra_first", "first_login_switch", "perfect_information_switch", "sign_switch"];
        foreach ($arr as $value){
            $retList[$value] = empty($ConfigList[$value])?0:(int)$ConfigList[$value];
        }
        return reTmJsonObj(200, '获取成功', $retList);
    }

    /**
     * 获取经纬度配置
     */
    public function getLongitudeAndLatitudeConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['longitude','latitude','is_open_position'];
        $condition['type'] = 'position';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $ConfigList['longitude'] = empty($ConfigList['longitude'])?"":$ConfigList['longitude'];
        $ConfigList['latitude'] = empty($ConfigList['latitude'])?"":$ConfigList['latitude'];
        $ConfigList['is_open_position'] = empty($ConfigList['is_open_position'])?0:$ConfigList['is_open_position'];
        $ConfigList['distance'] = 3; //默认三千米
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 获取app唯一标示
     */
    public function getTmAppKey(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['android_app_key','ios_app_key'];
        $condition['type'] = 'tm_app_key';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $ConfigList['android_app_key'] = empty($ConfigList['android_app_key'])?"":$ConfigList['android_app_key'];
        $ConfigList['ios_app_key'] = empty($ConfigList['ios_app_key'])?"":$ConfigList['ios_app_key'];
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }
}