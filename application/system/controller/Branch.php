<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/20
 * Time: 14:39
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\BranchModel;
use app\api\model\RoleModel;
use app\api\model\UserModel;
use think\Db;
use think\facade\Request;

class Branch extends Base
{
    protected $branchModel;
    public function __construct()
    {
        parent::__construct();
        $this->branchModel = new BranchModel();
    }

    /**
     * 获取组织机构列表
     */
    public function getBranchList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //获取组织机构列表数据
        $condition = [];
        $field = 'branch_id, parent_id, branch_name, branch_code';
        $branchList = $this->branchModel->getBranchList($condition, $field);
        if($branchList === false){
            Logservice::writeArray(['sql'=>$this->branchModel->getLastSql()], '获取组织机构列表数据失败', 2);
            return reTmJsonObj(500, '获取组织机构列表失败', []);
        }

        //获取角色列表数据
        $uField = 'user_id, user_code, user_name, branch_id as user_branch_id, status, email, mobile, real_name';
        $uCondition['deleted'] = 0;
        $userModel = new UserModel();
        $userList = $userModel->getUserList($uCondition, $uField);
        if($userList === false){
            Logservice::writeArray(['sql'=>$userModel->getLastSql()], '获取角色列表数据失败', 2);
            return reTmJsonObj(500, '获取用户列表失败', []);
        }

        //组装数据
        foreach ($branchList as $key => $value){
            foreach ($userList as $k => $v){
                if($value['branch_id'] == $v['user_branch_id']){
                    $value['user_list'][] = $v;
                }
            }
            $branchList[$key] = $value;
        }

        //处理数据结构为无限极分类
        $branchList = getAttr($branchList, 0, 'parent_id', 'branch_id');
        return reTmJsonObj(200, '获取组织机构列表成功', $branchList);
    }

    /**
     * 移动部门,可批量
     */
    public function updateUserBranch(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['user_codes','branch_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //批量修改用户表中branch_id
        $userModel = new UserModel();
        $userCode = explode(',', $inputData['user_codes']);
        Db::startTrans();
        foreach ($userCode as $value){
            $condition['user_code'] = $value;
            $re = $userModel->updateUserInfo($condition, ['branch_id' => $inputData['branch_id']]);
            if($re === false){
                Logservice::writeArray(['sql'=>$userModel->getLastSql(), 'condition'=>$condition, 'branch_id'=>$inputData['branch_id']], '修改用户表失败', 2);
                Db::rollback();
                return reTmJsonObj(500, '移动部门失败', []);
            }
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '批量移动部门');
        return reTmJsonObj(200, '移动部门成功', []);
    }

    /**
     * 新增部门
     */
    public function addBranch(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['branch_name'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }
        $inputData['branch_code'] = createCode();

        //保存部门数据
        $id = $this->branchModel->addBranch($inputData);
        if(!$id){
            Logservice::writeArray(['sql'=>$this->branchModel->getLastSql()], '保存部门数据失败', 2);
            return reTmJsonObj(500, '新增失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '新增部门');
        return reTmJsonObj(200, '新增成功', []);
    }

    /**
     * 修改部门
     */
    public function updateBranch(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['branch_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        $condition['branch_code'] = $inputData['branch_code'];
        //保存部门数据
        $re = $this->branchModel->updateBranch($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->branchModel->getLastSql()], '修改部门数据失败', 2);
            return reTmJsonObj(500, '修改失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改部门');
        return reTmJsonObj(200, '修改成功', []);
    }

    /**
     * 删除部门
     */
    public function deleteBranch(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['branch_id', 'branch_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        //判断该分类下是否有子分类
        $son = $this->branchModel->getBranchInfo(['parent_id' => $inputData['branch_id']], 'branch_id');
        if($son){
            return reTmJsonObj(500, '该部门下有子部门,不能删除', []);
        }

        $userModel = new UserModel();
        $roleModel = new RoleModel();
        //判断部门下是否有用户
        $user = $userModel->getUserCount(['branch_id' => $inputData['branch_id']]);
        if($user > 0){
            return reTmJsonObj(500, '该部门下有用户,不能删除', []);
        }

        Db::startTrans();
        //删除角色表中的相关部门
        $update = $roleModel->updateRole(['branch_code' => $inputData['branch_code']], ['branch_code' => '']);
        if($update === false){
            Logservice::writeArray(['sql'=>$roleModel->getLastSql()], '删除角色表中的相关部门失败', 2);
            Db::rollback();
            return reTmJsonObj(500, '更新角色表失败', []);
        }

        $condition['branch_id'] = $inputData['branch_id'];
        //删除部门数据
        $re = $this->branchModel->deleteBranch($condition);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->branchModel->getLastSql()], '删除部门数据失败', 2);
            Db::rollback();
            return reTmJsonObj(500, '删除失败', []);
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '删除部门');
        return reTmJsonObj(200, '删除成功', []);
    }
}