<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/14
 * Time: 15:10
 */
namespace app\system\controller;

use app\api\model\ClientVersionModel;
use app\api\model\UserModel;
use think\Db;
use think\facade\Request;

class Clientversion extends Base
{
    protected $ClientVersionModel;

    public function __construct()
    {
        parent::__construct();
        $this->ClientVersionModel = new ClientVersionModel();
    }

    /**
     *   更新或新增客户端版本
     */
    public function save(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!empty($inputData['id']) && $inputData['id'] > 0){ //如果存在id则更新
            $condition = [
                ['id','=',$inputData['id']]
            ];
            $result = $this->ClientVersionModel->versionSave($condition,$inputData);
        }else{ //否则添加数据
            $inputData['user_id'] = $this->userInfo['user_id'];
            $result = $this->ClientVersionModel->addVersion($inputData);
            $inputData['id'] = $result;
        }
        if($result){
            $ClientInfo = $this->ClientVersionModel->getVersionInfo(['id'=>$inputData['id']]);
            return reTmJsonObj(200,'操作成功', $ClientInfo );
        }else{
            return reTmJsonObj(500,'操作失败');
        }
    }

    /**
     *  获取客户端版本信息
     */
    public function getInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!($inputData['id'] > 0)){
            return reTmJsonObj(500,'id丢失');
        }
        $condition = [
            ['id','=',$inputData['id']]
        ];
        $ClientInfo = $this->ClientVersionModel->getVersionInfo($condition);
        if($ClientInfo){
            $ClientInfo['add_time'] = date('Y-m-d h:i:s',$ClientInfo['add_time']);
            if($ClientInfo['user_id'] > 0){ //显示操作管理员名称
               $UserModel = new UserModel();
               $userInfo = $UserModel->getUserInfo(['user_id'=>$ClientInfo['user_id']],'real_name');
               $ClientInfo['real_name'] = $userInfo['real_name'];
               unset($ClientInfo['user_id']);
            }
            return reTmJsonObj(200,'操作成功',$ClientInfo);
        }else{
            return reTmJsonObj(500,'获取失败');
        }
    }

    /**
     * 获取客户端版本列表
     */
    public function getList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = array();
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->ClientVersionModel->countVersion($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        if(empty($inputData['now_page']) || !($inputData['now_page'] > 0)){
           $inputData['now_page'] = 1;
        }
        $firstRow = ($inputData['now_page'] - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $ClientList = $this->ClientVersionModel->versionList($condition,false,$limit,'add_time desc');
        if($ClientList === false){
            return reTmJsonObj(500,'获取数据失败', []);
        }
        foreach ($ClientList as $key => $val){
            $ClientList[$key]['add_time'] = date('Y-m-d h:i:s',$val['add_time']);
            if($val['user_id'] > 0){ //显示操作管理员名称
                $UserModel = new UserModel();
                $userInfo = $UserModel->getUserInfo(['user_id'=>$val['user_id']],'real_name');
                $ClientList[$key]['real_name'] = $userInfo['real_name'];
                unset($ClientList[$key]['user_id']);
            }
        }
        return reTmJsonObj(200,'获取数据成功',['total_page'=>$totalPage,'now_page'=>$inputData['now_page'],'list'=>$ClientList]);
    }

    /**
     * 删除客户端版本
     */
    public function del(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!($inputData['id'] > 0)){
            return reTmJsonObj(500,'传入id格式无效');
        }
        $result = $this->ClientVersionModel->deleteVersion(['id'=>$inputData['id']]);
        if($result){
            return reTmJsonObj(200,'操作成功');
        }else{
            return reTmJsonObj(500,'操作失败');
        }
    }

    /**
     * 获取最新版本
     */
    public function getUpToDateVersion(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = [
            'client_type'=>'iOS',
        ];
        $data = [
            'iOS'=>[
                'version'=>'无',
                'display_version'=>'无'
            ],
            'Android'=>[
                'version'=>'无',
                'display_version'=>'无'
            ],
        ];
        $iOS = $this->ClientVersionModel->getVersionInfo($condition,false,'version desc');
        if($iOS){
            $data['iOS'] = [
                'version'=>$iOS['version'],
                'display_version'=>$iOS['display_version']
            ];
        }
        $condition['client_type'] = 'Android';
        $Android = $this->ClientVersionModel->getVersionInfo($condition,false,'version desc');
        if($Android){
            $data['Android'] = [
                'version'=>$Android['version'],
                'display_version'=>$Android['display_version']
            ];
        }
        return reTmJsonObj(200,'获取成功',$data);
    }

}