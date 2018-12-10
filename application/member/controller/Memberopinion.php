<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/16
 * Time: 17:14
 */
namespace app\member\controller;

use app\member\model\MemberModel;
use app\member\model\MemberOpinionModel;
use think\facade\Cache;
use think\facade\Request;

class Memberopinion extends Base
{
    protected $OpinionModel;
    public function __construct()
    {
        parent::__construct();
        $this->OpinionModel = new MemberOpinionModel();
    }

    /**
     * 获取意见列表
     */
    public function getOpinionList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = array();
        $OpinionTotal = $this->OpinionModel->getCount($condition); //获取总数
        $num = 20; //获取20条会员数据
        $totalPage = ceil($OpinionTotal/$num); //总页数
        if(!empty($inputData['page']) && $inputData['page'] > 0 && $inputData['page'] <= $totalPage){
            $start_num = ($num * ($inputData['page']-1));
        }else{
            $start_num = 0;
        }
        $OpinionList = $this->OpinionModel->getOpinionList($condition,'',$start_num .','.$num,'add_time desc');
        if($OpinionList === false){
            return reTmJsonObj(500,'获取数据失败', []);
        }
        foreach ($OpinionList as $key => $val){
            $OpinionList[$key]['add_time'] = date('Y-m-d h:i:s',$OpinionList[$key]['add_time']);
        }
        return reTmJsonObj(200,'获取成功',['total_page'=>$totalPage,'now_page'=>$start_num + 1,'list'=>$OpinionList]);
    }

    /**
     * 更新意见状态
     */
    public function updateOpinionStatus(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['id','status'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = array();
        if(!($inputData['id'] > 0)){
            return reTmJsonObj(500, '参数异常', []);
        }
        $condition['id'] = $inputData['id'];
        if(!in_array($inputData['status'],array(0,1))){
            return reTmJsonObj(500, 'status参数异常', []);
        }
        $result = $this->OpinionModel->updateOpinion($condition,['status'=>$inputData['status']]);
        if($result){
            return reTmJsonObj(200, '成功', []);
        }else{
            return reTmJsonObj(500, '失败', []);
        }
    }

    /**
     * 提交意见
     */
    public function addOpinionInfo(){
        $inputData = Request::post();
        $method = Request::method();
        $token = Request::header('token');
        $member_info = Cache::get($token);
        if(Cache::get($token.'addOpinionInfo') == 1){
            return reTmJsonObj(500,'已提交过意见，请在3分钟后再提交');
        }
        $params = ['message'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(empty($member_info['member_code'])){
            return reTmJsonObj(500, 'token异常', []);
        }
        $memberModel = new MemberModel();
        $member = $memberModel->getMemberInfo(['member_code'=>$member_info['member_code']],'member_id');
        if($member === false){
            return reTmJsonObj(500,'获取用户信息失败');
        }
        $member_info['member_id'] = $member['member_id'];
        if(strlen($inputData['message']) > 500){
            return reTmJsonObj(500, '字符数超过500', []);
        }
        $data['member_id'] = $member_info['member_id'];
        $data['message'] = $inputData['message'];
        $data['add_time'] = time();
        $data['status'] = 1;
        $result = $this->OpinionModel->addOpinion($data);
        if($result){
            Cache::set($token.'addOpinionInfo',1,180);
            return reTmJsonObj(200, '成功', []);
        }else{
            return reTmJsonObj(500, '失败', []);
        }
    }
}