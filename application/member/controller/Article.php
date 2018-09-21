<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/21
 * Time: 16:45
 */
namespace app\member\controller;

use app\api\model\SystemArticleModel;
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
        $info['add_time'] = date('Y-m-d h:i:s',$info['add_time']);
        $info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return reJson(200,'获取成功',$info);
    }

    /**
     * 获取免责文章信息
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
        $info['add_time'] = date('Y-m-d h:i:s',$info['add_time']);
        $info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return reJson(200,'获取成功',$info);
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
        $info['add_time'] = date('Y-m-d h:i:s',$info['add_time']);
        $info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return reJson(200,'获取成功',$info);
    }
}