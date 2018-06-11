<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/12
 * Time: 14:19
 */

namespace app\member\controller;



use app\member\model\MembercommentModel;
use think\facade\Request;

class Membercomment extends Base
{
    protected $commentModel;
    public function __construct()
    {
        parent::__construct();
        $this->commentModel = new MembercommentModel();
    }
    
    /**
     * 添加评论
     */
    public function addComment(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code','app_id','article_id','article_content','comment_content'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //评论
        $inputData['create_time'] = time();
        $re = $this->commentModel->addComment($inputData);
        if($re === false){
            return reJson(500, '评论失败', []);
        }

        return reJson(200, '评论成功', []);
    }

    /**
     * 删除评论
     */
    public function deleteComment(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['comment_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['comment_id'] = $inputData['comment_id'];
        $re = $this->commentModel->deleteComment($condition);
        if($re === false){
            return reJson(500, '删除评论失败', []);
        }

        return reJson(200, '删除评论成功', []);
    }

    /**
     * 获取评论列表
     */
    public function getCommentList(){
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
        $count = $this->commentModel->countComment($condition);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $totalPage = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;
        $order = 'create_time desc';
        $field = 'comment_id, member_code, app_id, article_id, article_content, comment_content, create_time, extend';

        //获取列表数据
        $list = $this->commentModel->commentList($condition, $field, $limit, $order);
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