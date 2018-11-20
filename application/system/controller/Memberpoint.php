<?php
/**
 * Created by PhpStorm.
 * User: wcc
 * Date: 2018/10/17
 * Time: 11:07
 */

namespace app\system\controller;

use app\api\model\ConfigModel;
use app\api\model\SystemArticleModel;
use think\facade\Request;

class Memberpoint extends Base
{
    protected  $ConfigModel;
    protected  $SystemArticleModel;
    public function __construct()
    {
        parent::__construct();
        $this->ConfigModel = new ConfigModel();
        $this->SystemArticleModel = new SystemArticleModel();
    }

    /*编辑或者新建积分调整*/
    public function editPoint(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['key'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $Condition = ['key'=>$inputData['key'],'type'=>'point'];
        $getOneConfig = $this->ConfigModel->getOneConfig($Condition);
        if(empty($getOneConfig)){
            $addConfigData = [
                'key'=>$inputData['key'],
                'type'=>'point'
            ];
            if(isset($inputData['remarks'])){
                $addConfigData['remarks'] = $inputData['remarks'];
            }
            if(isset($inputData['value'])){
                $addConfigData['value'] = $inputData['value'];
            }
            $result = $this->ConfigModel->addConfig($addConfigData);
            if(false === $result){
                return reJson(500, '失败', []);
            }
        }else{
            if(isset($inputData['remarks'])){
                $addConfigData['remarks'] = $inputData['remarks'];
            }
            if(isset($inputData['value'])){
                $addConfigData['value'] = $inputData['value'];
            }
            $result = $this->ConfigModel->saveConfig($Condition,$addConfigData);
            if(false === $result){
                return reJson(500, '失败', []);
            }
        }
        return reJson(200, '成功', []);
    }

    /*获取积分调整列表*/
    public function getPoints(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = ['type'=>'point'];
        $List = $this->ConfigModel->getConfigList($condition,'id,key,value,remarks');
        $List = array_column($List,null,'key');
        return reJson(200,'获取成功',['list'=>$List]);
    }

    /*编辑积分规则*/
    public function editPointRule(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['content'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $Condition = ['id'=>4];
        $getOneConfig = $this->SystemArticleModel->getArticleInfo($Condition);
        if(empty($getOneConfig)){
            return reJson(500, '失败', []);
        }else{
            $addData = [
                'content'=>$inputData['content'],
            ];
            $result = $this->SystemArticleModel->updateArticleInfo($Condition,$addData);
            if(false === $result){
                return reJson(500, '失败', []);
            }
        }
        return reJson(200, '成功', []);
    }


    /*获取积分规则*/

    public function getPointRule(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $Condition = ['id'=>4];
        $data = $this->SystemArticleModel->getArticleInfo($Condition);
        return reJson(200,'获取成功',$data);
    }

}