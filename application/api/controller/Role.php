<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 13:01
 */

namespace app\api\controller;


use app\api\model\RoleModel;
use app\extend\controller\Logservice;
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
     * 获取所有角色对应的站点,权限,机构
     */
    public function getRoleListDetail(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
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
            return reTmJsonObj(500, '获取角色列表失败', []);
        }
        $roleCodes = array_column($roleList, 'role_code');

        //获取所有权限
        $conditionPrivilege = [['tm_role_privilege.role_code', 'in', $roleCodes]];
        $fieldPrivilege = 'tm_role_privilege.role_code, tm_role_privilege.privilege_code, tm_privilege.privilege_name';
        $privilege = $this->roleModel->getRolePrivilegeListDetail($conditionPrivilege, $fieldPrivilege);
        if($privilege === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取所有权限失败', 2);
            return reTmJsonObj(500, '获取所有权限失败', []);
        }

        //获取所有站点
        $conditionSite = [['tm_role_site.role_code', 'in', $roleCodes]];
        $fieldSite = 'tm_role_site.role_code, tm_role_site.site_code, tm_site.site_name';
        $site = $this->roleModel->getRoleSiteListDetail($conditionSite, $fieldSite);
        if($site === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取所有站点失败', 2);
            return reTmJsonObj(500, '获取所有站点失败', []);
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

        return reTmJsonObj(200, '成功', $roleList);
    }
}