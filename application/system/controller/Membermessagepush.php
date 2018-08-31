<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/20
 * Time: 17:57
 */
namespace app\system\controller;

use app\member\model\PushMessageModel;
use think\facade\Request;

class Membermessagepush extends Base
{
    protected $PushMessageModel;
    public function __construct()
    {
        parent::__construct();
        $this->PushMessageModel = new PushMessageModel();
    }

    /**
     * 消息列表
     */
    public function messageList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = array();
        $Total = $this->PushMessageModel->getCount($condition); //获取总数
        $num = !empty($inputData['page_size'])?$inputData['page_size']:20; //默认获取20条数据
        $totalPage = ceil($Total/$num); //总页数
        if(!empty($inputData['page']) && $inputData['page'] > 0 && $inputData['page'] <= $totalPage){
            $start_num = ($num * ($inputData['page']-1));
        }else{
            $start_num = 0;
        }
        $PushMessageList = $this->PushMessageModel->getList($condition,'',$start_num .','.$num,'add_time desc');
        if($PushMessageList === false){
            return reJson(500,'获取数据失败', []);
        }
        foreach ($PushMessageList as $key => $val){
            $PushMessageList[$key]['add_time'] = date('Y-m-d h:i:s',$PushMessageList[$key]['add_time']);
            $PushMessageList[$key]['push_time'] = date('Y-m-d h:i:s',$PushMessageList[$key]['push_time']);
            if(!empty($val['push_situation'])){
                $push_situation = json_decode($val['push_situation'],true);
                $PushMessageList[$key]['android_received'] = $push_situation['android_received'];
                $PushMessageList[$key]['ios_apns_sent'] = $push_situation['ios_apns_sent'];
                unset($PushMessageList[$key]['push_situation']);
            }else{
                $PushMessageList[$key]['android_received'] = 0;
                $PushMessageList[$key]['ios_apns_sent'] = 0;
            }
        }
        return reJson(200,'获取成功',['total_page'=>$totalPage,'now_page'=>$start_num + 1,'list'=>$PushMessageList]);
    }

    /**
     * 获取推送信息详情
     */
    public function getMessageInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!($inputData['id'] > 0)){
            return reJson(500, '参数异常', []);
        }
        $messageInfo = $this->PushMessageModel->getInfo(['id'=>$inputData['id']]);
        if($messageInfo === false){
            return reJson(500,'获取数据失败', []);
        }
        $messageInfo['add_time'] = date('Y-m-d h:i:s',$messageInfo['add_time']);
        $messageInfo['push_time'] = date('Y-m-d h:i:s',$messageInfo['push_time']);
        return reJson(200,'获取成功',$messageInfo);
    }

    /**
     * 保存推送信息
     */
    public function saveMessageInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = array();
        if(!empty($inputData['push_time'])){
            $inputData['push_time'] = strtotime($inputData['push_time']);//转时间戳
        }
        if(!empty($inputData['id']) && $inputData['id'] > 0){
            $result = $this->PushMessageModel->updateInfo($condition,$inputData);
        }else{
            $result = $this->PushMessageModel->addInfo($inputData);
        }
        if($result){
            return reJson(200,'保存成功', []);
        }
        return reJson(500,'失败',[]);
    }

    /**
     * 改变消息的状态
     */
    public function changeMessageStatus(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['id','status'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = array();
        if(!($inputData['id'] > 0)){
            return reJson(500, '参数异常', []);
        }
        $condition['id'] = $inputData['id'];
        if(!in_array($inputData['status'],array(0,1))){
            return reJson(500, 'status参数异常', []);
        }
        $result = $this->PushMessageModel->updateInfo($condition,['status'=>$inputData['status']]);
        if($result){
            return reJson(200, '成功', []);
        }else{
            return reJson(500, '失败', []);
        }
    }

}