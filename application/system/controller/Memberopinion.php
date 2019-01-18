<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/16
 * Time: 17:14
 */
namespace app\system\controller;

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
        $condition = array(
            [TM_PREFIX.'member_opinion.status','IN',[1,2]]
        );
        $OpinionTotal = $this->OpinionModel->getCount($condition); //获取总数
        $num = !empty($inputData['page_size'])?$inputData['page_size']:20; //默认获取20条数据
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
        if(!in_array($inputData['status'],array(0,1,2))){
            return reTmJsonObj(500, 'status参数异常', []);
        }
        $result = $this->OpinionModel->updateOpinion($condition,['status'=>$inputData['status']]);
        if($result){
            return reTmJsonObj(200, '成功', []);
        }else{
            return reTmJsonObj(500, '失败', []);
        }
    }
}