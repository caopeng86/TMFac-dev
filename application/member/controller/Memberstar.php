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
        $params = ['member_code','title','app_id','article_id','extend','intro','type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
//        if(preg_match("/^(http:\/\/|https:\/\/).*$/",$inputData['url'])){
//            return reJson(500,'url不能带域名', []);
//        }
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

        return reJson(200, '收藏成功', ['star_id'=>$this->starModel->getLastInsID()]);
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
        if($Info === false){
            return reJson(500, '查询失败', []);
        }
        if(!empty($Info)){
            return reJson(200, '已被收藏', ['star_id'=>$Info['star_id']]);
        }
        return reJson(200, '未被收藏', []);
    }

    /**
     * 批量检查是否被收藏
     * @return \think\response\Json
     * 返回正常json样式 {"code":200,"data":[{"article_id":"1","star_id":false},{"article_id":"2","star_id":2},{"article_id":"3","star_id":false},{"article_id":"7","star_id":7},{"article_id":"10","star_id":false}],"msg":"查询成功"}
     */
    public function checkIsStarBatch(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code','app_id','article_ids'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(is_array($inputData['article_ids'])){
            $article_ids = $inputData['article_ids'];
        }else{
            $article_ids = explode(',',$inputData['article_ids']);
        }
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' =>$article_ids,
            'app_id' => $inputData['app_id']
        ];
        $starList = $this->starModel->starList($condition,'star_id,article_id');
        if($starList === false){
            return reJson(500, '查询失败', []);
        }
        $returnData = array();
        if(!empty($starList)){
            $starList = array_column($starList,'star_id','article_id');
            foreach ($article_ids as $val){
                $returnData[] = array(
                    'article_id'=>$val,
                    'star_id'=>$val > 0 && isset($starList[$val])?$starList[$val]:false
                );
            }
            return reJson(200,'查询成功', $returnData);
        }else{
            foreach ($article_ids as $val){
                $returnData[] = array(
                    'article_id'=>$val,
                    'star_id'=>false
                );
            }
            return reJson(200,'全未被收藏',$returnData);
        }
    }

    /**
     * 取消收藏
     */
    public function deleteStar(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['star_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
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
        //类型判断
        if(!empty($inputData['type']) && in_array($inputData['type'],array(1,2))){
            $condition['type'] = $inputData['type'];
        }
        $count = $this->starModel->countStar($condition);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $totalPage = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;
        $order = 'create_time desc';
        $field = 'star_id, member_code, app_id, article_id, title, intro, pic, create_time, extend,tag,type';

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