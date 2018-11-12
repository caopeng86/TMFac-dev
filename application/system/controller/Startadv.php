<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 14:24
 */
namespace app\system\controller;

use app\api\model\StartadvModel;
use think\Db;
use think\facade\Request;

class Startadv extends Base
{
    protected $StartAdvModel;
    public function __construct()
    {
        parent::__construct();
        $this->StartAdvModel = new StartadvModel();
    }

    /**
     *
     *  获取广告列表
     */
    public function startAdvList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = array();
        $advList = $this->StartAdvModel->advList($condition,false,'','sort desc');
        if($advList === false){
            return reJson(500,'获取数据失败', []);
        }
        foreach ($advList as $key => $val){
            $advList[$key]['start_time'] = date('Y-m-d h:i:s',$val['start_time']);
            $advList[$key]['update_time'] = date('Y-m-d h:i:s',$val['update_time']);
            $advList[$key]['add_time'] = date('Y-m-d h:i:s',$val['add_time']);
        }
        return reJson(200,'获取数据成功',$advList);
    }

    /**
     *  保存广告信息
     */
    public function saveStartAdv(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!empty($inputData['start_time'])){ //转时间戳
            $inputData['start_time'] = strtotime($inputData['start_time']);
        }
        if(!empty($inputData['id']) && $inputData['id'] > 0){ //如果存在id则更新
            $condition = [
                ['id','=',$inputData['id']]
            ];
            $inputData['update_time'] = time();
            $result = $this->StartAdvModel->advSave($condition,$inputData);
        }else{ //否则添加数据
            //判断广告个数 start
            $num = $this->StartAdvModel->countAdv([]);
            if($num > 5)return reJson(500,'最多支持5张图');
            $inputData['add_time'] = time();
            $inputData['update_time'] = time();
            //end
           $result = $this->StartAdvModel->addAdv($inputData);
           $inputData['id'] = $result;
        }
        if($result){
            $advInfo = $this->StartAdvModel->getAdvInfo(['id'=>$inputData['id']]);
            $advInfo['start_time'] = date('Y-m-d h:i:s',$advInfo['start_time']);
            $advInfo['update_time'] = date('Y-m-d h:i:s',$advInfo['update_time']);
            $advInfo['add_time'] = date('Y-m-d h:i:s',$advInfo['add_time']);
            return reJson(200,'操作成功',$advInfo);
        }else{
            return reJson(500,'操作失败');
        }
    }

    /**
     * 删除广告
     */
    public function deleteStartAdv(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!($inputData['id'] > 0)){
            return reJson(500,'id丢失');
        }
        //判断广告个数A
        $num = $this->StartAdvModel->countAdv([]);
        if($num <= 1){
            return reJson(500,'至少上传一张图');
        }
        $condition = [
            ['id','=',$inputData['id']]
        ];
        $result = $this->StartAdvModel->deleteAdv($condition);
        if($result){
            return reJson(200,'操作成功');
        }else{
            return reJson(500,'操作失败');
        }
    }

    /**
     * 获取单条广告信息
     */
    public function getStartAdvInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!($inputData['id'] > 0)){
            return reJson(500,'id丢失');
        }
        $condition = [
            ['id','=',$inputData['id']]
        ];
        $advInfo = $this->StartAdvModel->getAdvInfo($condition);
        if($advInfo){
            $advInfo['start_time'] = date('Y-m-d h:i:s',$advInfo['start_time']);
            $advInfo['update_time'] = date('Y-m-d h:i:s',$advInfo['update_time']);
            $advInfo['add_time'] = date('Y-m-d h:i:s',$advInfo['add_time']);
            return reJson(200,'操作成功',$advInfo);
        }else{
            return reJson(500,'获取失败');
        }
    }

}