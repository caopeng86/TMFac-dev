<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/10/16
 * Time: 11:32
 */

namespace app\member\controller;

use app\member\model\MemberpointModel;
use think\facade\Request;

class Memberpoint extends Base
{

    protected $MemberpointModel;
    public function __construct()
    {
        parent::__construct();
        $this->MemberpointModel = new MemberpointModel();
    }

    /**
     * 修改积分
     */
    public function editPoint(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['point','remarks','from_component'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $point = $this->MemberpointModel->getMemberPoint($this->memberInfo['member_id']);
        if($inputData['point'] + $point < 0){
            return reTmJsonObj(500, '用户积分不足', []);
        }
        $result = $this->MemberpointModel->editPoint($this->memberInfo['member_id'],$inputData['point'],$inputData['remarks'],$inputData['from_component']);
        if($result === false){
            return reTmJsonObj(500, '扣除用户积分失败，请重试', []);
        }
        return reTmJsonObj(200,'修改成功',['member_id'=>$this->memberInfo['member_id']]);
    }


    /*
     * 获取当前用户积分
     * */

    public function getPoint(){
        $point = $this->MemberpointModel->getMemberPoint($this->memberInfo['member_id']);
        return reTmJsonObj(200, '获取成功', ['point'=>$point,'member_id'=>$this->memberInfo['member_id']]);
    }


    /*
     * 获取用户积分变动情况列表
     * */

    public function getPointLogList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = ['member_id'=>$this->memberInfo['member_id']];
        if(!empty($inputData['from_component'])){
            $condition['from_component'] = $inputData['from_component'];
        }
        $Total = $this->MemberpointModel->getPointLogCount($condition); //获取总数
        $num = !empty($inputData['page_size'])?$inputData['page_size']:20; //默认获取20条数据
        $totalPage = ceil($Total/$num); //总页数
        if(!empty($inputData['page']) && $inputData['page'] > 0 && $inputData['page'] <= $totalPage){
            $start_num = ($num * ($inputData['page']-1));
        }else{
            $start_num = 0;
        }
        $field = 'id,change_point,now_point,remark,add_time';
        $pointLogList = $this->MemberpointModel->getPointLogList($condition,$field,$start_num .','.$num,'add_time desc');
        if($pointLogList === false){
            return reTmJsonObj(500,'获取数据失败', []);
        }
        foreach ($pointLogList as $key => $val){
            $pointLogList[$key]['add_time'] = date('Y-m-d h:i:s',$pointLogList[$key]['add_time']);
        }
        return reTmJsonObj(200,'获取成功',['total_page'=>$totalPage,'now_page'=>$start_num + 1,'list'=>$pointLogList]);
    }

}