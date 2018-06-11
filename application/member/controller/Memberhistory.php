<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/12
 * Time: 10:05
 */

namespace app\member\controller;


use app\member\model\MemberhistoryModel;
use think\facade\Request;

class Memberhistory extends Base
{
    protected $historyModel;
    public function __construct()
    {
        parent::__construct();
        $this->historyModel = new MemberhistoryModel();
    }

    /**
     * 添加历史
     */
    public function addHistory(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code','title','app_id','article_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //判断是否已历史
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' => $inputData['article_id'],
            'app_id' => $inputData['app_id'],
            'title' => $inputData['title'],
        ];
        $list = $this->historyModel->historyList($condition, 'title');
        if($list === false){
            return reJson(500, '获取历史列表失败', []);
        }

        //修改历史记录时间
        if(!empty($list)){
            $res = $this->historyModel->updateHistory($condition, ['create_time' => time()]);
            if($res === false){
                return reJson(500, '获取历史列表失败', []);
            }

            return reJson(200, '历史记录已更新', []);
        }

        //新增历史数据
        $inputData['create_time'] = time();
        $re = $this->historyModel->addHistory($inputData);
        if($re === false){
            return reJson(500, '历史记录添加失败', []);
        }

        return reJson(200, '历史记录已添加', []);
    }

    /**
     * 删除历史 type: 1:清除一条历史记录  else:清空历史记录
     */
    public function deleteHistory(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['history_id','type','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //拼接条件
        if($inputData['type'] == 1){
            $condition['history_id'] = $inputData['history_id'];
        }else{
            $condition['member_code'] = $inputData['member_code'];
        }

        //删除历史记录
        $re = $this->historyModel->deleteHistory($condition);
        if($re === false){
            return reJson(500, '删除历史失败', []);
        }

        return reJson(200, '删除历史成功', []);
    }

    /**
     * 获取历史列表
     */
    public function getHistoryList(){
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
        $count = $this->historyModel->countHistory($condition);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $totalPage = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;
        $order = 'create_time desc';
        $field = 'history_id, member_code, app_id, article_id, title, create_time, extend';

        //获取列表数据
        $list = $this->historyModel->historyList($condition, $field, $limit, $order);
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