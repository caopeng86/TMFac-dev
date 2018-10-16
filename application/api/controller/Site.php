<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 11:19
 */

namespace app\api\controller;


use app\api\model\RoleModel;
use app\api\model\SiteModel;
use app\extend\controller\Logservice;
use think\facade\Request;

class Site extends Base
{
    protected $siteModel;
    protected $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->siteModel = new SiteModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * 获取站点列表
     */
    public function getSiteList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition = [];
        $field = 'site_id, site_name, site_code, site_intro, is_work, add_time';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        if(!isset($inputData['is_work'])){
            $condition['is_work'] = 1;
        }else{
            if($inputData['is_work'] == 0){
                $condition['is_work'] = 1;
            }else{
                $condition = [];
            }
        }
        //获取站点总数
        $count = $this->siteModel->countSite($condition);
        $totalPage = ceil($count / $pageSize);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow.','.$pageSize;
        $order = 'site_id desc';
        //取出列表分页处理
        $siteList = $this->siteModel->getSiteList($condition, $field, $limit, $order);
        if($siteList === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '获取站点列表失败', 2);
            return reJson(500, '获取站点列表失败', []);
        }
        //转换时间戳
        foreach ($siteList as $key => $value){
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $siteList[$key] = $value;
        }

        //拼接返回结果
        $re = [
            "list" => $siteList,
            "totalPage" => $totalPage,
            "total" => $count
        ];

        return reJson(200, '获取站点列表成功', $re);
    }

    /**
     * 获取一条站点信息
     */
    public function getSiteInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition['site_code'] = $inputData['site_code'];
        $site_info = $this->siteModel->getSiteInfo($condition);
        if($site_info === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '获取站点信息失败', 2);
            reJson(500, '获取站点信息失败', []);
        }

        return reJson(200, '获取站点信息成功', $site_info);
    }

    /**
     *  保存投诉信息
     */
    public function saveComplain(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['id','message'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        return reJson(200,'投诉成功',[]);
    }

}