<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 18:33
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\PrivilegeModel;
use app\api\model\RoleModel;
use think\Db;
use think\facade\Request;

class Privilege extends Base
{
    protected $privilegeModel;
    protected $roleModel;
    public function __construct()
    {
        parent::__construct();
        $this->privilegeModel = new PrivilegeModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * 新增权限
     */
    public function addPrivilege(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['privilege_name','privilege_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        //判断权限code是否重复
        $privilege = $this->privilegeModel->getPrivilegeInfo(['privilege_code' => $inputData['privilege_code']], 'privilege_id');
        if($privilege){
            return reTmJsonObj(500,'权限代码不可重复',[]);
        }

        //不重复则新增
        Db::startTrans();
        $re = $this->privilegeModel->addPrivilege($inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->privilegeModel->getLastSql()], '新增权限失败', 2);
            Db::rollback();
            return reTmJsonObj(500,'新增权限失败',[]);
        }

        //将新增的权限与超级管理员关联
        $role = $this->roleModel->addRolePrivilegeAll([['role_code' => 1, 'privilege_code' => $inputData['privilege_code']]]);
        if($role === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '权限关联信息保存失败', 2);
            Db::rollback();
            return reTmJsonObj(500, '权限关联信息保存失败', []);
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '新增权限');
        return reTmJsonObj(200,'新增权限成功',[]);
    }

    /**
     * 修改权限
     */
    public function updatePrivilege(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['privilege_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        $condition = ['privilege_id' => $inputData['privilege_id']];
        $re = $this->privilegeModel->updatePrivilege($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->privilegeModel->getLastSql()], '修改权限失败', 2);
            return reTmJsonObj(500, '修改权限失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改权限');
        return reTmJsonObj(200, '修改权限成功', []);
    }

    /**
     * 删除权限
     */
    public function deletePrivilege(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['privilege_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        $condition = ['privilege_code' => $inputData['privilege_code']];
        Db::startTrans();
        $re = $this->privilegeModel->deletePrivilege($condition);
        //$re返回影响的行数
        if($re === false){
            Logservice::writeArray(['sql'=>$this->privilegeModel->getLastSql()], '删除权限失败', 2);
            Db::rollback();
            return reTmJsonObj(500, '删除权限失败', []);
        }

        //删除权限角色关联表数据
        $role = $this->roleModel->deleteRolePrivilege($condition);
        if($role === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '删除站点角色关联表数据失败', 2);
            Db::rollback();
            return reTmJsonObj(500, '删除权限关联信息失败', []);
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '删除权限');
        return reTmJsonObj(200, '删除权限成功', []);
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
            return reTmJsonObj(500,$msg,[]);
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
            return reTmJsonObj(500, '获取权限列表失败', []);
        }

        //拼接返回结果
        $re = [
            "list" => $privilegeList,
            "totalPage" => $totalPage,
            "total" => $count
        ];

        return reTmJsonObj(200, '获取权限列表成功', $re);
    }
}