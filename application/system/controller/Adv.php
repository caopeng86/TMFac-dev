<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 14:24
 */
namespace app\system\controller;

use app\api\model\AdvModel;
use think\Db;
use think\facade\Request;

class Adv extends Base
{
    protected $advModel;
    public function __construct()
    {
        parent::__construct();
        $this->advModel = new AdvModel();
    }

    /**
     *
     *  获取广告列表
     */
    public function advList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition = array(
            ['status','=',1]
        );
        $advList = $this->advModel->advList($condition,false,'','sort desc');
        if($advList === false){
            return reTmJsonObj(500,'获取数据失败', []);
        }
        return reTmJsonObj(200,'获取数据成功',$advList);
    }

    /**
     *  保存广告信息
     */
    public function saveAdv(){
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
            $inputData['update_time'] = time();
            $result = $this->advModel->advSave($condition,$inputData);
        }else{ //否则添加数据
            //判断广告个数 start
            $num = $this->advModel->countAdv([
                ['status','=',1]
            ]);
            if($num > 5)return reTmJsonObj(500,'最多支持5张轮播图');
            $inputData['add_time'] = time();
            $inputData['update_time'] = time();
            //end
           $result = $this->advModel->addAdv($inputData);
           $inputData['id'] = $result;
        }
        if($result){
            $advInfo = $this->advModel->getAdvInfo(['id'=>$inputData['id']]);
            return reTmJsonObj(200,'操作成功',$advInfo);
        }else{
            return reTmJsonObj(500,'操作失败');
        }
    }

    /**
     * 删除广告
     */
    public function deleteAdv(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!($inputData['id'] > 0)){
            return reTmJsonObj(500,'id丢失');
        }
        //判断广告个数
        $num = $this->advModel->countAdv([
            ['status','=',1]
        ]);
        if($num <= 1){
            return reTmJsonObj(500,'至少上传一张轮播图');
        }
        $condition = [
            ['id','=',$inputData['id']]
        ];
        $result = $this->advModel->deleteAdv($condition);
        if($result){
            return reTmJsonObj(200,'操作成功');
        }else{
            return reTmJsonObj(500,'操作失败');
        }
    }

    /**
     * 获取单条广告信息
     */
    public function getAdvInfo(){
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
            ['id','=',$inputData['id']],
            ['status','=',1]
        ];
        $advInfo = $this->advModel->getAdvInfo($condition);
        if($advInfo){
            return reTmJsonObj(200,'操作成功',$advInfo);
        }else{
            return reTmJsonObj(500,'获取失败');
        }
    }

}