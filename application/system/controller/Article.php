<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/16
 * Time: 16:18
 */
namespace app\system\controller;

use app\api\model\SystemArticleModel;
use think\Db;
use think\facade\Request;

class Article extends Base
{
    protected  $systemArticleModel = '';
    public function __construct()
    {
        parent::__construct();
        $this->systemArticleModel = new SystemArticleModel();
    }

    /**
     * 获取免责文章信息
     */
    public function getReliefArticle(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $info = $this->systemArticleModel->getArticleInfo(['id'=>1]);
        if(!empty($info['add_time']))$info['add_time'] = date('Y-m-d h:i:s',$info['add_time']);
        if(!empty($info['update_time']))$info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return reJson(200,'获取成功',$info);
    }

    /**
     * 更新免责文章
     */
    public function updateReliefArticle(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $result = $this->systemArticleModel->updateArticleInfo(['id'=>1],$inputData);
        if($result){
            return reJson(200, '更新成功', []);
        }
        return reJson(500, '更新失败', []);
    }

    /**
     * 获取隐私协议
     */
    public function getPrivacyArticle(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $info = $this->systemArticleModel->getArticleInfo(['id'=>2]);
        if(!empty($info['add_time']))$info['add_time'] = date('Y-m-d h:i:s',$info['add_time']);
        if(!empty($info['update_time']))$info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return reJson(200,'获取成功',$info);
    }

    /**
     * 更新隐私协议
     */
    public function updatePrivacyArticle(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $result = $this->systemArticleModel->updateArticleInfo(['id'=>2],$inputData);
        if($result){
            return reJson(200, '更新成功', []);
        }
        return reJson(500, '更新失败', []);
    }

    /**
     * 获取关于我们
     */
    public function getAboutUsArticle(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $info = $this->systemArticleModel->getArticleInfo(['id'=>3]);
        if(!empty($info['add_time']))$info['add_time'] = date('Y-m-d h:i:s',$info['add_time']);
        if(!empty($info['update_time']))$info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return reJson(200,'获取成功',$info);
    }

    /**
     * 更新关于我们
     */
    public function updateAboutUsArticle(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $result = $this->systemArticleModel->updateArticleInfo(['id'=>3],$inputData);
        if($result){
            return reJson(200, '更新成功', []);
        }
        return reJson(500, '更新失败', []);
    }

}