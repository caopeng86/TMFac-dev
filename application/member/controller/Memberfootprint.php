<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/23
 * Time: 17:22
 */
namespace app\member\controller;

use app\member\model\MemberfootprintModel;
use think\Db;
use think\facade\Request;

class Memberfootprint extends Base
{
    protected $footprintModel;
    public function __construct()
    {
        parent::__construct();
        $this->footprintModel = new MemberfootprintModel();
    }

    /**
     * 添加浏览记录
     */
    public function addFootprint(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code','title','app_id','article_id','extend','type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition[] = ['article_id','=',$inputData['article_id']];
        $condition[] = ['member_code','=',$inputData['member_code']];
        $condition[] = ['title','=',$inputData['title']];
        $condition[] = ['intro','=',$inputData['intro']];
        $this->footprintModel->startTrans();
        $footprint_list = $this->footprintModel->footprintList($condition,'footprint_id');
        if($footprint_list){
            $footprint_ids = array_column($footprint_list,'footprint_id');
            $this->footprintModel->deleteFootprint([['footprint_id','in',$footprint_ids]]);
        }
        //添加历史记录
        $inputData['create_time'] = time();
        $re = $this->footprintModel->addFootprint($inputData);
        if($re === false){
            $this->footprintModel->rollback();
            return reTmJsonObj(500, '历史记录添加失败', []);
        }
        $this->footprintModel->commit();
        return reEncryptJson(200, '历史记录添加成功', ['footprint_id'=>$re]);
    }

    /**
     * 删除历史记录
     */
    public function deleteFootprint(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['footprint_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $re = Db::table(TM_PREFIX.'member_footprint')
            ->whereIn('footprint_id',$inputData['footprint_id'])
            ->update(['status'=>0]);
        if($re === false){
            return reTmJsonObj(500, '删除失败', []);
        }

        return reEncryptJson(200, '删除成功', []);
    }

    /**
     * 清空历史记录
     */
    public function clearFootprint(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition['member_code'] = $inputData['member_code'];
        $re = $this->footprintModel->deleteFootprint($condition);
        if($re === false){
            return reTmJsonObj(500, '失败', []);
        }

        return reEncryptJson(200, '清空历史记录成功', []);
    }

    /**
     * 获取历史记录列表
     */
    public function getFootprintList(){
        //判断请求方式以及请求参数
        //$inputData = Request::get();
        $inputData = getEncryptGetData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['index','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //条件拼接
        $condition[] = ['member_code','=',$inputData['member_code']];
        $condition[] = ['status','=',1];
        $condition[] = ['create_time','>=',time() - 7*24*3600];
        //根据时间排序统计条数
        if($inputData['get_time_list'] == 1){
            $time_list = $this->footprintModel->footprintTimeList($condition);
            if(!empty($inputData['returnType']) && $inputData['returnType'] == 1){

            }else{
                $time_list = array_column($time_list,'num','days');
            }
        }

        //分页处理
        $pageSize = empty($inputData['page_size'])? 20 : $inputData['page_size'];
        //类型判断
        if(!empty($inputData['type']) && in_array($inputData['type'],array(1,2))){
            $condition['type'] = $inputData['type'];
        }
        $count = $this->footprintModel->countFootprint($condition);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $totalPage = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;
        $order = 'create_time desc';
        $field = 'footprint_id, member_code, app_id, article_id, title, intro, pic, create_time, extend,tag,type';
        //获取列表数据
        $list = $this->footprintModel->footprintList($condition, $field, $limit, $order);
        if($list === false){
            return reTmJsonObj(500, '获取列表失败', []);
        }

        $return = [
            'total' => $count,
            'totla_page' => $totalPage,
            'list' => $list
        ];
        if(!empty($time_list)){
            $return['time_list'] = $time_list;
        }
        return reEncryptJson(200, '获取列表成功', $return);
    }
}