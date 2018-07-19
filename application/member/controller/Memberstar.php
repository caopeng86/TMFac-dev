<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11
 * Time: 15:51
 */

namespace app\member\controller;


use app\member\model\MemberstarModel;
use think\facade\Request;

class Memberstar extends Base
{
    protected $starModel;
    public function __construct()
    {
        parent::__construct();
        $this->starModel = new MemberstarModel();
    }

    /**
     * 添加收藏
     */
    public function addStar(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code','title','app_id','article_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //判断是否已收藏
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' => $inputData['article_id'],
            'app_id' => $inputData['app_id'],
            'title' => $inputData['title'],
        ];
        $list = $this->starModel->starList($condition, 'title');
        if($list === false){
            return reJson(500, '获取收藏列表失败', []);
        }
        if(!empty($list)){
            return reJson(500, '已被收藏', []);
        }

        //收藏数据
        $inputData['create_time'] = time();
        $re = $this->starModel->addStar($inputData);
        if($re === false){
            return reJson(500, '收藏失败', []);
        }

        return reJson(200, '收藏成功', ['star_id'=>$re]);
    }

    /**
     * 检验是否被收藏
     */
    public function checkIsStar(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code','app_id','article_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        //判断是否已收藏
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' => $inputData['article_id'],
            'app_id' => $inputData['app_id']
        ];
        $Info = $this->starModel->starFind($condition,'star_id');
        if(!empty($Info)){
            return reJson(200, '已被收藏', ['star_id'=>$list['star_id']]);
        }
        return reJson(200, '未被收藏', []);
    }


    /**
     * 取消收藏
     */
    public function deleteStar(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['star_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['star_id'] = $inputData['star_id'];
        $re = $this->starModel->deleteStar($condition);
        if($re === false){
            return reJson(500, '取消收藏失败', []);
        }

        return reJson(200, '取消收藏成功', []);
    }

    /**
     * 获取收藏列表
     */
    public function getStarList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //条件拼接
        $condition['member_code'] = $inputData['member_code'];

        //分页处理
        $pageSize = empty($inputData['page_size'])? 20 : $inputData['page_size'];
        $count = $this->starModel->countStar($condition);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $totalPage = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;
        $order = 'create_time desc';
        $field = 'star_id, member_code, app_id, article_id, title, intro, pic, create_time, extend';

        //获取列表数据
        $list = $this->starModel->starList($condition, $field, $limit, $order);
        if($list === false){
            return reJson(500, '获取列表失败', []);
        }

        $return = [
          'total' => $count,
          'totla_page' => $totalPage,
          'list' => $list
        ];

        return reJson(200, '获取列表成功', $return);
    }
}