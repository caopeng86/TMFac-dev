<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\system\controller;



use think\facade\Request;
use app\api\model\CommonArticleModel;

class Managecommonarticle extends Base
{
    protected  $CommonArticleModel;
    public function __construct()
    {
        parent::__construct();
        $this->CommonArticleModel = new CommonArticleModel();
    }
    /*
 * 获取列表*/
    public function getList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //获取搜索条件
        $condition = [];
        $resultKey = ["website_name","title","content","keyword","abstract","from_source",
            "from_source_url","url","author","organization","column","publish_time","comment_num",
            "read_num", "collect_time", "img_url1", "img_url2","img_url3","column_id","from_id"];//定义返回的字段
        foreach ($resultKey as $va){
            if(!empty($inputData[$va])){
                $condition[$va] = $inputData[$va];
            }
        }
        $field = '*';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        empty($inputData['index']) ? $index = 1 : $index = $inputData['index'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->CommonArticleModel->getCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($index - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'article_id desc';
        //获取会员列表数据
        $List = $this->CommonArticleModel->getList($condition, $field, $limit, $order);
        if($List === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        foreach ($List as $k => &$v){
            unset($v['content']);
        }
        $return = [
            'list' => $List,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reTmJsonObj(200, '获取列表成功', $return);
    }


    /**
     * 获取信息
     */
    public function getInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['aid'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition['aid'] = $inputData['aid'];
        $field = '*';
        $memberInfo = $this->CommonArticleModel->getInfo($condition, $field);
        if(empty($memberInfo)){
            return reTmJsonObj(500, '获取信息失败', []);
        }

        return reTmJsonObj(200, '获取信息成功', $memberInfo);
    }

    /**
     * 修改
     */
    public function updateInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ["aid"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition['aid'] = $inputData['aid'];
        $resultKey = ["website_name","title","content","keyword","abstract","from_source",
            "from_source_url","url","author","organization","column","publish_time","comment_num",
            "read_num", "collect_time", "img_url1", "img_url2","img_url3","column_id","from_id"];//定义返回的字段
        $updateData = [];
        foreach ($resultKey as $va){
            if(isset($inputData[$va])){
                $updateData[$va] = $inputData[$va];
            }
        }
        $result = $this->CommonArticleModel->updateInfo($condition,$updateData);
        if($result){
            return reTmJsonObj(200,'更新成功',[]);
        }
        return reTmJsonObj(500,'更新失败',[]);
    }

    /**
     * 修改
     */
    public function addInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $resultKey = ["website_name","title","content","keyword","abstract","from_source",
            "from_source_url","url","author","organization","column","publish_time","comment_num",
            "read_num", "collect_time", "img_url1", "img_url2","img_url3","column_id","from_id"];//定义返回的字段
        $updateData = [];
        foreach ($resultKey as $va){
            if(isset($inputData[$va])){
                $updateData[$va] = $inputData[$va];
            }
        }
        $result = $this->CommonArticleModel->addInfo($updateData);
        if($result){
            return reTmJsonObj(200,'成功',[]);
        }
        return reTmJsonObj(500,'失败',[]);
    }

    /**
     * 删除
     */
    public function deleteInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ["aid"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $condition['aid'] = $inputData['aid'];
        $result = $this->CommonArticleModel->deleteInfo($condition);
        if($result){
            return reTmJsonObj(200,'成功',[]);
        }
        return reTmJsonObj(500,'失败',[]);
    }


}