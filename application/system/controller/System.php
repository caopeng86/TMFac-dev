<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/22
 * Time: 13:54
 */
namespace app\system\controller;

use app\api\model\ConfigModel;
use app\api\model\UserModel;
use think\Db;
use think\Controller;
use think\facade\Request;
use think\facade\Env;

class System extends Controller
{
    protected  $ConfigModel = '';
    public function __construct()
    {
        parent::__construct();
        $this->ConfigModel = new ConfigModel();
    }
    /**
     * 获取系统配置
     */
    public function getSystem(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $ConfigType = $this->ConfigModel->getConfigType();
        if(!in_array($inputData['type'],$ConfigType)){
            return reTmJsonObj(500,'参数类型不存在');
        }
        $condition = array();
        $condition['type'] = $inputData['type'];
        $ConfigList = $this->ConfigModel->getConfigList($condition,'key,value,remarks');
        if($ConfigList){
            return reTmJsonObj(200, '更新成功', $ConfigList);
        }
        return reTmJsonObj(500, '更新失败', []);
    }


    /**
     * 保存系统配置
     */
    public function saveSystem(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['config','type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $ConfigType = $this->ConfigModel->getConfigType();
        if(!in_array($inputData['type'],$ConfigType)){
            return reTmJsonObj(500,'参数类型不存在');
        }
        $type = $inputData['type'];
        unset($inputData['type']);
        foreach ($inputData['config'] as $val){
            $this->ConfigModel->batchSaveConfig($val['key'],$val['value'],$val['remarks'],$type);
        }
        return reTmJsonObj(200,'保存完成');
    }

    /**
     * 获取门户配置信息
     */
    public function getGateway(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['site_name','site_logo'];
        $condition['type'] = 'base';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     *保存门户配置信息
     */
    public function setGateway(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['site_name','site_logo'];
        $remarks = ['site_name'=>'平台名称','site_logo'=>'平台logo'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'base');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取启动页配置
     */
    public function getStartConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['app_start_image','app_start_image_m','app_start_image_s','app_start_url','app_start_title'];
        $condition['type'] = 'client';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置启动页配置
     */
    public function setStartConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['app_start_image','app_start_image_m','app_start_image_s'];
        $remarks = ['app_start_image'=>'启动页图片','app_start_image_m'=>'中等启动页','app_start_image_s'=>'小启动页','app_start_url'=>'启动页跳转链接','app_start_title'=>'启动页标题'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        $params[] = 'app_start_url';
        $params[] = 'app_start_title';
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(empty($inputData[$val]))$inputData[$val] = ''; //未定义则赋予空值
            $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'client');
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 设置阿里短信服务配置
     */
    public function setAliSmsConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['ali_sms_key_id','ali_sign_name','ali_key_secret','ali_check_template_code'];
        $remarks = ['ali_sms_key_id'=>'阿里短信服务key','ali_sign_name'=>'阿里短信签名','ali_key_secret'=>'阿里短信服务secret','ali_check_template_code'=>'阿里验证短信模板code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'client');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取阿里短信服务配置
     */
    public function getAliSmsConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['ali_sms_key_id','ali_sign_name','ali_key_secret','ali_check_template_code'];
        $condition['type'] = 'client';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置极光推送配置
     */
    public function setJpushConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['Jpush_key','Jpush_secret'];
        $remarks = ['Jpush_key'=>'极光key','Jpush_secret'=>'极光secret'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'client');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取极光推送配置
     */
    public function getJpushConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['Jpush_key','Jpush_secret'];
        $condition['type'] = 'client';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }
    /**
     *设置版本信息
     */
    public function setVersion(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['type','version','must_update'];
        $remarks = ['version'=>'版本号','must_update'=>'强制更新'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!in_array($inputData['type'],['ios_version','android_version']))return reTmJsonObj(500,'更新失败');
        foreach ($remarks as $key => $val){
            if(!empty($inputData[$key])){
                $this->ConfigModel->batchSaveConfig($key,$inputData[$key],$val,$inputData['type']);
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 部署sql文件
     */
    public function installSystem(){
        $root_address = $_SERVER['DOCUMENT_ROOT'];
        $PCVersion = $this->ConfigModel->getPCVersion();
        if(empty($PCVersion['version'])){
            $PCVersion['version'] = 'V1';
        }
        $version = substr($PCVersion['version'],1);
        $update_data = array(
            '1'=>$root_address.'/db/V1.sql',
            '2'=>$root_address.'/db/V2.sql',
        );
        if(empty($update_data[$version])){
            return reTmJsonObj(500,'无更新',[]);
        }
        if(!file_exists($update_data[$version])){
            return reTmJsonObj(500,'无更新',[]);
        }
        $sql = file_get_contents($update_data[$version]);
        $result = Db::execute($sql);
        if($result){
            unlink($update_data[$version]);
        }
        return reTmJsonObj(200,'更新完成',[]);
    }

    /**
     * 验证登陆信息
     */

    public function checkToken(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['token'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [
            ['access_key','=',$inputData['token']]
        ];
        $userModel = new UserModel();
        $user = $userModel->getUserCount($condition);
        if($user >= 1){
            return reTmJsonObj(200,'有效', []);
        }
        return reTmJsonObj(500,'无效', []);
    }

    /**
     * 设置极光推送配置
     */
    public function setPullDataConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['PullDataKey'];
        $remarks = ['PullDataKey'=>'从平台拉取数据key'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'client');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取极光推送配置
     */
    public function getPullDataConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['PullDataKey'];
        $condition['type'] = 'client';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置支付宝支付配置
     */
    public function setAlipayConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['alipay_app_id','alipay_public_key','alipay_private_key'];
        $remarks = ['alipay_app_id'=>'支付宝支付appid','alipay_public_key'=>'支付宝支付公钥','alipay_private_key'=>'支付宝支付私钥'];
        /*$ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }*/
        foreach ($params as $val){
            if(isset($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'payment');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 设置微信支付配置
     */
    public function setWechatpayConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['wechat_app_id','wechat_mch_id','wechat_key'];
        $remarks = ['wechat_app_id'=>'微信appid','wechat_mch_id'=>'微信商户号','wechat_key'=>'微信PAI秘钥'];
        /*$ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }*/
       /* if(!file_exists(Env::get('root_path')."/Wechatpayfile/apiclient_cert.pem")){
            return reTmJsonObj(500, "客户端证书文件未上传", []);
        }
        if( !file_exists(Env::get('root_path')."/Wechatpayfile/apiclient_key.pem")){
            return reTmJsonObj(500, "客户端秘钥文件未上传", []);
        }*/
        foreach ($params as $val){
            if(isset($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'payment');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取支付宝支付配置
     */
    public function getAlipayConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['alipay_app_id','alipay_public_key','alipay_private_key'];
        $condition['type'] = 'payment';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $ConfigList['alipay_app_id'] = empty($ConfigList['alipay_app_id'])?"":$ConfigList['alipay_app_id'];
        $ConfigList['alipay_public_key'] = empty($ConfigList['alipay_public_key'])?"":$ConfigList['alipay_public_key'];
        $ConfigList['alipay_private_key'] = empty($ConfigList['alipay_private_key'])?"":$ConfigList['alipay_private_key'];
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 获取微信支付配置
     */
    public function getWecatpayConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['wechat_app_id','wechat_mch_id','wechat_key'];
        $condition['type'] = 'payment';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $ConfigList['wechat_app_id'] = empty($ConfigList['wechat_app_id'])?"":$ConfigList['wechat_app_id'];
        $ConfigList['wechat_mch_id'] = empty($ConfigList['wechat_mch_id'])?"":$ConfigList['wechat_mch_id'];
        $ConfigList['wechat_key'] = empty($ConfigList['wechat_key'])?"":$ConfigList['wechat_key'];
        $ConfigList['apiclient_cert'] = file_exists(Env::get('root_path')."Wechatpayfile/apiclient_cert.pem");
        $ConfigList['apiclient_key'] = file_exists(Env::get('root_path')."Wechatpayfile/apiclient_key.pem");
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /*上传微信支付配置文件*/
    public function uploadWecahtpayFile(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ["type"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!("apiclient_cert" == $inputData['type'] || "apiclient_key" == $inputData['type'])){
            return reTmJsonObj(500, "type值不对", []);
        }
        if(!isset($_FILES["file"])){
            return reTmJsonObj(500, "请选择文件上传", []);
        }
        $arr = $_FILES["file"];

        if(strpos(strrchr($arr['name'], '.'),'pem')){
            //  move_uploaded_file($arr["tmp_name"],$filename);
            if(is_uploaded_file($arr['tmp_name'])) {
                $uploaded_file=$arr['tmp_name'];
                $path=Env::get('root_path')."/Wechatpayfile";
                if(!file_exists($path)) {
                    mkdir($path);
                }
                $intofilename = "aa.txt";
                if("apiclient_cert" == $inputData['type']){
                    $intofilename = "apiclient_cert.pem";
                }
                if("apiclient_key" == $inputData['type']){
                    $intofilename = "apiclient_key.pem";
                }
                if(file_exists($path."/".$intofilename)){
                    unlink($path."/".$intofilename);
                }
                if(move_uploaded_file($uploaded_file,$path."/".$intofilename)) {
                    return reTmJsonObj(200, "上传成功", []);
                } else {
                    return reTmJsonObj(500, "保存失败，请重新上传", []);
                }
            } else {
                return reTmJsonObj(500, "上传失败", []);
            }

        }else{
            return reTmJsonObj(500, "文件格式不对", []);
        }
    }




    /**
     * 获取背景图片
     */
    public function getBackGroupPic(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['BackGroupPic'];
        $condition['type'] = 'client';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置背景图片
     */
    public function setBackGroupPic(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['BackGroupPic'];
        $remarks = ['BackGroupPic'=>'个人中心背景图'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'client');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取自动定位参数auto_location
     */
    public function getLocationConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['auto_location','set_default_province','set_default_city','set_default_area'];
        $condition['type'] = 'client';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功',$ConfigList);
    }

    /**
     * 设置自动定位参数
     */
    public function setLocationConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        if($inputData['auto_location'] == 1){
            $params = ['auto_location'];
        }else{
            $params = ['auto_location','set_default_province','set_default_city'];
        }
        $remarks = ['auto_location'=>'是否自动获取地址','set_default_province'=>'设置默认省','set_default_city'=>'设置默认市','set_default_area'=>'设置默认区县'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if($inputData['auto_location'] != 1){
            $params[] = 'set_default_area';
        }
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'client');
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取启动页播放方式
     * start_adv_type 1随机展示 2 顺序展示
     */
    public function getStartAdvType(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['start_adv_type'];
        $condition['type'] = 'app';
        $ConfigList = $this->ConfigModel->getOneConfig($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        return reTmJsonObj(200, '获取成功',[$ConfigList['key']=>$ConfigList['value']]);
    }

    /**
     *  保存启动页播放方式
     */
    public function setStartAdvType(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['start_adv_type'];
        $remarks = ['start_adv_type'=>'启动页播放方式'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'app');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 设置上传方式
     * local(本地),oss(阿里云对象存储),qn(七牛对象存储)
     */
    public function setUploadType(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['type'];
        $remarks = ['type'=>'设置上传方式'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'upload');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取上传方式
     */
    public function getUploadType(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['type'];
        $condition['type'] = 'upload';
        $ConfigList = $this->ConfigModel->getOneConfig($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        return reTmJsonObj(200, '获取成功',[$ConfigList['key']=>$ConfigList['value']]);
    }

    /**
     *  设置本地参数
     */
    public function setLocal(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['path'];
        $remarks = ['path'=>'路径','absolute_path'=>'扩展资源盘绝对路径','cdn'=>'资源盘域名'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $params[] = 'absolute_path';
        $params[] = 'cdn';
        foreach ($params as $val){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'LOCALupload');
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     *  获取本地参数
     */
    public function getLocal(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['path','absolute_path','cdn'];
        $condition['type'] = 'LOCALupload';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     *  设置oss参数
     */
    public function setFtp(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['host','username','password','port','path','cdn'];
        $remarks = ['host'=>'ip','username'=>'用户名','password'=>'密码','port'=>'端口号','path'=>'路径','cdn'=>'访问域名'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'FTPupload');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     *  获取oss参数
     */
    public function getFtp(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['host','username','password','port','path','cdn'];
        $condition['type'] = 'FTPupload';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     *  设置oss参数
     */
    public function setOss(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['accessKeyId','accessKeySecret','endpoint','bucket','cdn'];
        $remarks = ['accessKeyId'=>'OSS的key','accessKeySecret'=>'OSS的Secret','endpoint'=>'OSS的域名','bucket'=>'OSS的空间','cdn'=>'外网访问域名'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'OSSupload');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     *  获取oss参数
     */
    public function getOss(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['accessKeyId','accessKeySecret','endpoint','bucket','cdn'];
        $condition['type'] = 'OSSupload';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置七牛参数
     */
    public function setQiniu(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['accessKey','secretKey','bucket','upload','cdn'];
        $remarks = ['accessKey'=>'七牛key','secretKey'=>'七牛secret','bucket'=>'七牛空间','upload'=>'七牛上传地址','cdn'=>'七牛获取文件域名'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'QNupload');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取七牛参数
     */
    public function getQiniu(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['accessKey','secretKey','bucket','upload','cdn'];
        $condition['type'] = 'QNupload';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置百度统计参数
     */
    public function setBaiduAnalysis(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['Baidu_Api_Key','Baidu_Secret_Key','Baidu_android_App_Key','Baidu_iOS_App_Key'];
        $remarks = ['Baidu_Api_Key'=>'百度统计key','Baidu_Secret_Key'=>'百度统计密钥','Baidu_android_App_Key'=>'百度统计应用安卓key','Baidu_iOS_App_Key'=>'百度统计应用iOSkey'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(!empty($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'BaiduAnalysis');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取百度统计参数
     */
    public function getBaiduAnalysis(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['Baidu_Api_Key','Baidu_Secret_Key','Baidu_android_App_Key','Baidu_iOS_App_Key'];
        $condition['type'] = 'BaiduAnalysis';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置微信公众号配置
     */
    public function setWechatPublicConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['wechat_public_app_id','wechat_public_app_secret'];
        $remarks = ['wechat_public_app_id'=>'微信公众号appid','wechat_public_app_secret'=>'微信公众号AppSecret'];
        $ret = checkBeforeAction($inputData, [], $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        foreach ($params as $val){
            if(isset($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'wechat');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取微信公众号配置
     */
    public function getWechatPublicConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['wechat_public_app_id','wechat_public_app_secret'];
        $condition['type'] = 'wechat';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $ConfigList['wechat_public_app_id'] = empty($ConfigList['wechat_public_app_id'])?"":$ConfigList['wechat_public_app_id'];
        $ConfigList['wechat_public_app_secret'] = empty($ConfigList['wechat_public_app_secret'])?"":$ConfigList['wechat_public_app_secret'];
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }

    /**
     * 设置经纬度配置
     */
    public function setLongitudeAndLatitudeConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['longitude','latitude','is_open_position','poi_name','poi_adcode'];
        $remarks = ['longitude'=>'经度','latitude'=>'纬度','is_open_position'=>"是否开启定位",'poi_name'=>'定位','poi_adcode'=>'定位'];
        /*$ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }*/
        foreach ($params as $val){
            if(isset($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'position');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取经纬度配置 1
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
        $condition['key'] = ['longitude','latitude','is_open_position','poi_name','poi_adcode'];
        $condition['type'] = 'position';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        $ConfigList['longitude'] = empty($ConfigList['longitude'])?"":$ConfigList['longitude'];
        $ConfigList['latitude'] = empty($ConfigList['latitude'])?"":$ConfigList['latitude'];
        $ConfigList['is_open_position'] = empty($ConfigList['is_open_position'])?0:$ConfigList['is_open_position'];
        $ConfigList['poi_name'] = empty($ConfigList['poi_name'])?"":$ConfigList['poi_name'];
        $ConfigList['poi_adcode'] = empty($ConfigList['poi_adcode'])?"":$ConfigList['poi_adcode'];
        return reTmJsonObj(200, '获取成功', $ConfigList);
    }


    /**
     * 设置License
     */
    public function setLicense(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $params = ['license'];
        $remarks = ['license'=>'license值'];
        foreach ($params as $val){
            if(isset($inputData[$val])){
                $this->ConfigModel->batchSaveConfig($val,$inputData[$val],$remarks[$val],'license');
            }
        }
        return reTmJsonObj(200,'保存成功',[]);
    }

    /**
     * 获取License
     */
    public function getLicense(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['license'];
        $condition['type'] = 'license';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        if(empty($ConfigList['license'])){
            return reTmJsonObj(500, '获取失败', []);
        }
        $data = tmBaseHttp("https://www.360tianma.com/home/index/getLicense",['license'=>$ConfigList['license'],'domain'=>$_SERVER['SERVER_NAME']]);
        $data = (array)json_decode($data);
        if($data['status']!=1){
            return reTmJsonObj(500, '获取失败', []);
        }
        return reTmJsonObj(200, '成功', $data['data']);
    }

}