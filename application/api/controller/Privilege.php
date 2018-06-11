<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 18:33
 */

namespace app\api\controller;


use app\api\model\PrivilegeModel;
use app\extend\controller\Logservice;
use think\facade\Request;

class Privilege extends Base
{
    protected $privilegeModel;
    public function __construct()
    {
        parent::__construct();
        $this->privilegeModel = new PrivilegeModel();
    }

    /**
     * 获取权限列表
     */
    public function getPrivilegeList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition = [];
        $field = 'privilege_id, privilege_name, privilege_code, privilege_intro';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //获取权限总数
        $count = $this->privilegeModel->countPrivilege($condition);
        $totalPage = ceil($count / $pageSize);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow.','.$pageSize;
        $order = 'privilege_id desc';
        //取出列表分页处理
        $privilegeList = $this->privilegeModel->getPrivilegeList($condition, $field, $limit, $order);
        if($privilegeList === false){
            Logservice::writeArray(['sql'=>$this->privilegeModel->getLastSql()], '获取权限列表失败', 2);
            return reJson(500, '获取权限列表失败', []);
        }

        //拼接返回结果
        $re = [
            "list" => $privilegeList,
            "totalPage" => $totalPage,
            "total" => $count
        ];

        return reJson(200, '获取权限列表成功', $re);
    }
}