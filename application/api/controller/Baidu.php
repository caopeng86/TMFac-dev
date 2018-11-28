<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/8
 * Time: 10:30
 */

namespace app\api\controller;

use app\api\model\ConfigModel;
use app\extend\controller\BaiduAnalysis;
use think\Controller;
use think\facade\Request;

class Baidu extends Controller
{
    private $API_Key = '';
    private $Secret_Key = '';
    private $android_App_Key = '';
    private $iOS_App_Key = '';
    public function __construct()
    {
        $ConfigModel = new ConfigModel();
        $condition['key'] = ['Baidu_Api_Key','Baidu_Secret_Key','Baidu_android_App_Key','Baidu_iOS_App_Key'];
        $condition['type'] = 'BaiduAnalysis';
        $BaiduConfig = $ConfigModel->getConfigList($condition);
        $BaiduConfig = $ConfigModel->ArrayToKey($BaiduConfig);
        $this->API_Key = $BaiduConfig['Baidu_Api_Key'];
        $this->Secret_Key = $BaiduConfig['Baidu_Secret_Key'];
        $this->android_App_Key = $BaiduConfig['Baidu_android_App_Key'];
        $this->iOS_App_Key = $BaiduConfig['Baidu_iOS_App_Key'];
        parent::__construct();
    }

    /**
     * getCode
     */
    public function resultCode(){
        $inputData = Request::get();
        $inputData['code'];
        $Analysis = new BaiduAnalysis($this->API_Key,$this->Secret_Key);
        $result = $Analysis->getToken($inputData['code']);
        $result = json_decode($result);
        if(!empty($result->access_token)){
            $this->saveToken($result);
            $this->success('授权成功','/application/member_info/html/dataShow.html');
        }
        $this->error($result->error_description,'/application/member_info/html/member-list.html');
    }

    /**
     * 获取Code
     */
    public function getCode(){
        $Analysis = new BaiduAnalysis($this->API_Key,$this->Secret_Key);
        $url = $Analysis->getCodeUrl();
        $this->redirect($url);
    }

    /**
     * 保存Token
     */
    private function saveToken($result){
        if(!empty($result->refresh_token)){
            $ConfigModel = new ConfigModel();
            $ConfigModel->batchSaveConfig('Baidu_refresh_token',$result->refresh_token,'长期refresh_token缓存','BaiduAnalysis');
        }
        session('baidu_token',$result);
    }

    /**
     * 清除token
     */
    public function clearToken(){
        $ConfigModel = new ConfigModel();
        $ConfigModel->batchSaveConfig('Baidu_refresh_token','','长期refresh_token缓存','BaiduAnalysis');
        session('baidu_token',' ');
    }

    /**
     * 获取access_token
     */
    private function getSessionToken($Analysis){
        $session_token = session('baidu_token');
        if(empty($session_token->access_token)){
            $ConfigModel = new ConfigModel();
            $condition['key'] = ['Baidu_refresh_token'];
            $condition['type'] = 'BaiduAnalysis';
            $refresh_token_config = $ConfigModel->getOneConfig($condition);
            if(!empty($refresh_token_config['value'])){
                $result = $Analysis->updateToken($refresh_token_config['value']);
                $result = json_decode($result);
                if(!empty($result->access_token)){
                    $this->saveToken($result);
                    $session_token = session('baidu_token');
                }
            }
        }
        return $session_token;
    }

    /**
     *  月统计用户数量
     */
    public function getMember(){
        $Analysis = new BaiduAnalysis($this->API_Key,$this->Secret_Key);
        $session_token = $this->getSessionToken($Analysis);
        if(empty($session_token->access_token)){
            return reJson(502,'token失效');
        }
        $data['method'] = 'newuser/a';
        $data['metrics'] = 'user_count,new_user_count,old_user_count';
        $data['start-date'] = date("YmdHis",strtotime(date('Y-m-01',time())));
        $data['end-date'] = date("YmdHis",time());
        $data['gran'] = 'month';
        $android = $Analysis->setKey($this->android_App_Key)->getDataByKey($session_token->access_token,$data);
        $android = json_decode($android);
        if(empty($android->result)){
            $this->clearToken();
            return reJson(500,'账号没有访问权限');
        }
        $info['metrics'] = $data['metrics'];
        $info['date'] =  $android->result->items[0];
        $info['android'] =  $android->result->items[1];
        $iOS  = $Analysis->setKey($this->iOS_App_Key)->getDataByKey($session_token->access_token,$data);
        $iOS  = json_decode($iOS);
        if(empty($iOS->result)){
            $this->clearToken();
            return reJson(500,'账号没有访问权限');
        }
        if($iOS->result->items[1]){
            $info['iOS'] =  $iOS->result->items[1];
        }
        return reJson(200,'获取成功',$info);
    }

    /**
     * 获取新增用户
     */
    public function getNewMember(){
        $Analysis = new BaiduAnalysis($this->API_Key,$this->Secret_Key);
        $session_token = $this->getSessionToken($Analysis);
        if(empty($session_token->access_token)){
            return reJson(502,'token失效');
        }
        $data['method'] = 'newuser/a';
        $data['metrics'] = 'new_user_count';
        $data['start-date'] = date("YmdHis",strtotime('-1 month'));
        $data['end-date'] = date("YmdHis",time());
        $data['max-results'] = 31;
        $data['gran'] = 'day';
        $info['metrics'] = $data['metrics'];
        $android = $Analysis->setKey($this->android_App_Key)->getDataByKey($session_token->access_token,$data);
        $android = json_decode($android);
        if(empty($android->result)){
            $this->clearToken();
            return reJson(500,'账号没有访问权限');
        }
        $info['date'] =  array_reverse($android->result->items[0]);
        $info['android'] =  array_reverse($android->result->items[1]);
        $iOS  = $Analysis->setKey($this->iOS_App_Key)->getDataByKey($session_token->access_token,$data);
        $iOS  = json_decode($iOS);
        if(empty($iOS->result)){
            $this->clearToken();
            return reJson(500,'账号没有访问权限');
        }
        if($iOS->result->items[1]){
            $info['iOS'] =  array_reverse($iOS->result->items[1]);
        }
        return reJson(200,'获取成功',$info);
    }

    /**
     * 获取用户活跃度
     */
    public function getMemberActivity(){
        $Analysis = new BaiduAnalysis($this->API_Key,$this->Secret_Key);
        $session_token = $this->getSessionToken($Analysis);
        if(empty($session_token->access_token)){
            return reJson(502,'token失效');
        }
        $data['method'] = 'activitydegree/a';
        $data['metrics'] = 'user_count';
        $data['start-date'] = date("YmdHis",strtotime('-1 month'));
        $data['end-date'] = date("YmdHis",time());
        $data['max-results'] = 31;
        $data['gran'] = 'day';
        $info['metrics'] = $data['metrics'];
        $android = $Analysis->setKey($this->android_App_Key)->getDataByKey($session_token->access_token,$data);
        $android = json_decode($android);
        if(empty($android->result)){
            $this->clearToken();
            return reJson(500,'账号没有访问权限');
        }
        $info['date'] =  array_reverse($android->result->items[0]);
        $info['android'] =  array_reverse($android->result->items[1]);
        $iOS  = $Analysis->setKey($this->iOS_App_Key)->getDataByKey($session_token->access_token,$data);
        $iOS  = json_decode($iOS);
        if(empty($iOS->result)){
            $this->clearToken();
            return reJson(500,'账号没有访问权限');
        }
        if($iOS->result->items[1]){
            $info['iOS'] =  array_reverse($iOS->result->items[1]);
        }
        return reJson(200,'获取成功',$info);
    }

}