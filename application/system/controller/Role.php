<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 13:01
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\RoleModel;
use think\facade\Request;

class Role extends Base
{
    protected $roleModel;
    public function __construct()
    {
        parent::__construct();
        $this->roleModel = new RoleModel();
    }

    /**
     * 保存用户角色数据
     * @param array $userCode
     * @param array $roleCode
     * @return bool
     */
    private function _saveRoleUser($userCode, $roleCode){
        $j=0;
        $addData = [];
        //处理用户角色数据放到一个数组中
        foreach ($userCode as $value){
            foreach ($roleCode as $v){
                $addData[$j]['user_code'] = $value;
                $addData[$j]['role_code'] = $v;
                $j++;
            }
        }

        $condition = [['user_code', 'in', $userCode]];
        //保存角色用户数据
        $re = $this->roleModel->saveRoleUser($condition, $addData);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '保存角色用户数据失败', 2);
            return false;
        }

        return true;
    }

    /**
     * 获取角色列表带组织机构
     */
    public function getRoleBranchList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //联表查询角色列表数据
        $field = 'role_id, role_code, role_name, role_intro, branch_name, tm_role.branch_code';
        $condition = [];
        if(!empty($inputData['branch_code'])){
            $condition['tm_role.branch_code'] = $inputData['branch_code'];
        }
        $roleList = $this->roleModel->getRoleBranchList($condition, $field);
        if($roleList === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色带组织机构列表失败', 2);
            return reJson(500, '获取角色列表失败', []);
        }

        return reJson(200, '获取角色列表成功', $roleList);
    }

    /**
     * 新增角色
     */
    public function addRole(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['role_name'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $inputData['role_code'] = createCode();
        $re = $this->roleModel->addRole($inputData);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '新增角色失败', 2);
            return reJson(500, '新增角色失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '新增角色');
        return reJson(200, '新增角色成功', []);
    }

    /**
     * 修改角色
     */
    public function updateRole(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['role_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['role_code'] = $inputData['role_code'];
        $re = $this->roleModel->updateRole($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '修改角色失败', 2);
            return reJson(500, '修改角色失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改角色');
        return reJson(200, '修改角色成功', []);
    }

    /**
     * 删除角色
     */
    public function deleteRole(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['role_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //删除角色表及其关联表数据
        $condition['role_code'] = $inputData['role_code'];
        $re = $this->roleModel->deleteAboutRole($condition);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '删除角色失败', 2);
            return reJson(500, '删除角色失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '删除角色');
        return reJson(200, '删除角色成功', []);
    }

    /**
     * 关联角色站点
     */
    public function saveRoleSite(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['role_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!isset($inputData['site_code_list'])){
            return reJson(500, 'site_code_list参数不存在', []);
        }
        //处理接收数据
        $siteCode = explode(',', $inputData['site_code_list']);
        $roleSite = [];
        foreach ($siteCode as $v){
            $roleSite[] = [
                'role_code' => $inputData['role_code'],
                'site_code' => $v
            ];
        }

        $condition['role_code'] = $inputData['role_code'];
        //保存角色站点数据
        $re = $this->roleModel->saveRoleSiteAll($condition, $roleSite);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '保存角色站点失败', 2);
            return reJson(500, '关联角色站点失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '关联角色站点');
        return reJson(200, '关联角色站点成功', []);
    }

    /**
     * 关联角色应用
     */
    public function saveRoleComponent(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['role_code','component_code_list'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //处理接收数据
        $siteCode = explode(',', $inputData['component_code_list']);
        $roleComponent = [];
        foreach ($siteCode as $v){
            $roleComponent[] = [
                'role_code' => $inputData['role_code'],
                'component_code' => $v
            ];
        }

        $condition['role_code'] = $inputData['role_code'];
        //保存角色应用数据
        $re = $this->roleModel->saveRoleComponent($condition, $roleComponent);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '保存角色应用失败', 2);
            return reJson(500, '关联角色应用失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '关联角色应用');
        return reJson(200, '关联角色应用成功', []);
    }

    /**
     * 关联角色权限
     */
    public function saveRolePrivilege(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['role_code','privilege_code_list'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //处理接收数据
        $siteCode = explode(',', $inputData['privilege_code_list']);
        $rolePrivilege = [];
        foreach ($siteCode as $v){
            $rolePrivilege[] = [
                'role_code' => $inputData['role_code'],
                'privilege_code' => $v
            ];
        }

        $condition['role_code'] = $inputData['role_code'];
        //保存角色权限数据
        $re = $this->roleModel->saveRolePrivilege($condition, $rolePrivilege);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '保存角色权限失败', 2);
            return reJson(500, '关联角色权限失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '关联角色权限');
        return reJson(200, '关联角色权限成功', []);
    }

    /**
     * 分配角色,可批量
     */
    public function saveRoleUser(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['user_codes','role_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //处理传入数据
        $userCode = explode(',', $inputData['user_codes']);
        $roleCode = explode(',', $inputData['role_codes']);

        //保存用户角色数据
        $re = $this->_saveRoleUser($userCode, $roleCode);
        if(!$re){
            return reJson(500, '关联角色权限失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '批量分配角色');
        return reJson(200, '分配角色成功', []);
    }

    /**
     * 复制用户权限
     */
    public function copyRoleUser(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['user_codes','copy_user_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //查找复制者的所有角色
        $condition['user_code'] = $inputData['copy_user_code'];
        $roleCodes = $this->roleModel->getRoleUserList($condition, 'role_code');
        if(empty($roleCodes)){
            return reJson(500, '复制的用户角色不存在', []);
        }
        if($roleCodes === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色用户失败', 2);
            return reJson(500, '复制的用户角色获取失败', []);
        }

        //处理数据
        $roleCode = array_column($roleCodes, 'role_code');
        $userCode = explode(',', $inputData['user_codes']);

        //保存用户角色数据
        $re = $this->_saveRoleUser($userCode, $roleCode);
        if(!$re){
            return reJson(500, '关联角色权限失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '复制用户权限');
        return reJson(200, '复制用户权限成功', []);
    }

    /**
     * 查找某角色所有的站点数据
     */
    public function getRoleSiteList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['role_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $roleCodes = explode(',', $inputData['role_codes']);
        $condition = [['role_code', 'in', $roleCodes]];
        $re = $this->roleModel->getRoleSiteList($condition, 'site_code');
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色站点失败', 2);
            return reJson(500, '查找站点数据失败', []);
        }

        return reJson(200, '查找站点数据成功', $re);
    }

    /**
     * 查找某角色所有的应用数据
     */
    public function getRoleComponentList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['role_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $roleCodes = explode(',', $inputData['role_codes']);
        $condition = [['role_code', 'in', $roleCodes]];
        $re = $this->roleModel->getRoleComponentList($condition, 'component_code');
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色应用失败', 2);
            return reJson(500, '查找应用数据失败', []);
        }

        return reJson(200, '查找应用数据成功', $re);
    }

    /**
     * 查找某角色所有的权限数据
     */
    public function getRolePrivilegeList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['role_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $roleCodes = explode(',', $inputData['role_codes']);
        $condition = [['role_code', 'in', $roleCodes]];
        $re = $this->roleModel->getRolePrivilegeList($condition, 'privilege_code');
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色权限失败', 2);
            return reJson(500, '查找权限数据失败', []);
        }

        return reJson(200, '查找权限数据成功', $re);
    }

    /**
     * 获取某个用户的所有角色
     */
    public function getRoleUserList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['user_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['tm_role_user.user_code'] = $inputData['user_code'];
        $field = 'tm_role_user.role_code, tm_role.role_name';
        $re = $this->roleModel->getRoleUserListDetail($condition, $field);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色用户失败', 2);
            return reJson(500, '查找角色数据失败', []);
        }

        return reJson(200, '查找角色数据成功', $re);
    }

    /**
     * 获取所有角色对应的站点,权限,机构
     */
    public function getRoleListDetail(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //获取所有角色列表
        $field = 'role_code, role_name, branch_name, tm_role.branch_code';
        $condition = [];
        if(!empty($inputData['branch_code'])){
            $condition['tm_role.branch_code'] = $inputData['branch_code'];
        }
        $roleList = $this->roleModel->getRoleBranchList($condition, $field);
        if($roleList === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色列表失败', 2);
            return reJson(500, '获取角色列表失败', []);
        }
        $roleCodes = array_column($roleList, 'role_code');

        //获取所有权限
        $conditionPrivilege = [['tm_role_privilege.role_code', 'in', $roleCodes]];
        $fieldPrivilege = 'tm_role_privilege.role_code, tm_role_privilege.privilege_code, tm_privilege.privilege_name';
        $privilege = $this->roleModel->getRolePrivilegeListDetail($conditionPrivilege, $fieldPrivilege);
        if($privilege === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取所有权限失败', 2);
            return reJson(500, '获取所有权限失败', []);
        }

        //获取所有站点
        $conditionSite = [['tm_role_site.role_code', 'in', $roleCodes]];
        $fieldSite = 'tm_role_site.role_code, tm_role_site.site_code, tm_site.site_name';
        $site = $this->roleModel->getRoleSiteListDetail($conditionSite, $fieldSite);
        if($site === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取所有站点失败', 2);
            return reJson(500, '获取所有站点失败', []);
        }

        //拼接数据
        foreach ($roleList as $key => $value){
            foreach ($privilege as $vo){
                if($value['role_code'] == $vo['role_code']){
                    $value['privilege'][] = $vo;
                }
            }
            foreach ($site as $v){
                if($value['role_code'] == $v['role_code']){
                    $value['site'][] = $v;
                }
            }
            $roleList[$key] = $value;
        }

        return reJson(200, '成功', $roleList);
    }

    /**
     * 查找角色可以访问Portal
     */
    public function getRolePortalList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['role_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $roleCodes = explode(',', $inputData['role_codes']);
        $condition = [['role_code', 'in', $roleCodes]];
        $re = $this->roleModel->getRolePortalList($condition, 'key');
        if($re === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色应用失败', 2);
            return reJson(500, '查找应用数据失败', []);
        }

        return reJson(200, '查找应用数据成功', $re);
    }

    /**
     * 关联角色应用
     */
    public function saveRolePortal(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['role_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!isset($inputData['portal_list'])){
            return reJson(500, 'portal_list参数不存在', []);
        }
        //处理接收数据
        $siteCode = explode(',', $inputData['portal_list']);
        $roleComponent = [];
        foreach ($siteCode as $v){
            $roleComponent[] = [
                'role_code' => $inputData['role_code'],
                'key' => $v
            ];
        }

        $condition['role_code'] = $inputData['role_code'];
        //保存角色应用数据
        $re = $this->roleModel->saveRolePortal($condition, $roleComponent);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '保存角色应用失败', 2);
            return reJson(500, '关联角色应用失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '关联角色应用');
        return reJson(200, '关联角色应用成功', []);
    }



}