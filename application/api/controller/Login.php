<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 15:17
 */

namespace app\api\controller;


use app\api\model\BranchModel;
use app\api\model\RoleModel;
use app\api\model\UserModel;
use app\extend\controller\Logservice;
use think\captcha\Captcha;
use think\Controller;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;


class Login extends Controller
{
    protected $captcha;
    protected $userModel;
    protected $roleModel;
    protected $branchModel;
    protected $verifyConfig = [
        'fontSize' => 15,// 验证码字体大小
        'length' => 4,// 验证码位数
        'useNoise' => false,// 是否添加杂点
        'imageH' => 30,//高
        'imageW' => 100,//宽

    ];

    public function __construct()
    {
        parent::__construct();
        $this->captcha = new Captcha($this->verifyConfig);
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->branchModel = new BranchModel();
    }

    /**
     * 获取用户部门name,code
     * @param $branchId
     * @return array|bool
     */
    private function _getBranch($branchId){
        $branch = $this->branchModel->getBranchInfo(['branch_id' => $branchId], 'branch_code, branch_name');
        if($branch === false){
            Logservice::writeArray(['sql'=>$this->branchModel->getLastSql()], '获取用户部门失败', 2);
            return false;
        }
        return $branch;
    }

    /**
     * 获取角色name,code
     * @param $userCode
     * @return array|bool
     */
    private function _getRole($userCode){
        //查询用户角色表获取所有角色code
        $condition['tm_role_user.user_code'] = $userCode;
        $field = 'tm_role_user.role_code, tm_role.role_name';
        $role = $this->roleModel->getRoleUserListDetail($condition, $field);
        if($role === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取角色失败', 2);
            return false;
        }
        return $role;
    }

    /**
     * 获取权限name,code
     * @param $roleCodes
     * @return array|bool
     */
    private function _getPrivilege($roleCodes){
        //查询所有角色对应的所有权限
        $condition = [['tm_role_privilege.role_code', 'in', $roleCodes]];
        $field = 'tm_role_privilege.privilege_code, tm_privilege.privilege_name';
        $privilege = $this->roleModel->getRolePrivilegeListDetail($condition, $field);
        if($privilege === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取权限失败', 2);
            return false;
        }
        return $privilege;
    }

    /**
     * 获取站点code,name
     * @param $roleCodes
     * @return mixed
     */
    private function _getSite($roleCodes){
        //查询所有角色对应的所有站点
        $condition = [['tm_role_site.role_code', 'in', $roleCodes]];
        $field = 'tm_role_site.site_code, tm_site.site_name';
        $site = $this->roleModel->getRoleSiteListDetail($condition, $field);
        if($site === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取站点失败', 2);
            return false;
        }
        return $site;
    }

    /**
     * 获取所有应用code,name,app_code
     * @param $roleCodes
     * @return mixed
     */
    private function _getComponent($roleCodes){
        //查询所有角色对应的所有站点
        $condition = [['tm_role_component.role_code', 'in', $roleCodes]];
        $field = 'tm_component.component_code, tm_component.component_name, tm_component.component_key,
         tm_component.developer_code, tm_component.access_key, tm_component.secret_key, tm_component.index_version,
         tm_component.admin_version, tm_component.app_code, tm_component.create_time, tm_component.company_name,
         tm_component.address, tm_component.tel, tm_component.description, tm_component.linkman, tm_component.note,
         tm_component.component_pic';
        $site = $this->roleModel->getRoleComponentListDetail($condition, $field);
        if($site === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取应用失败', 2);
            return false;
        }
        return $site;
    }

    /**
     * 生成验证码
     */
    public function getVerify(){
        ob_clean();
        $this->captcha->codeSet = '1';
        $captchaData = $this->captcha->entry();
        return $captchaData;
    }

    /**
     * B端用户登录
     */
    public function userLogin(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['user_name', 'password'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        //验证验证码
//        $chkVerify = $this->captcha->check($inputData['verify']);
//        if($chkVerify === false){
//            return reTmJsonObj(500,'验证码输入错误',[]);
//        }

        //验证用户名
        $condition = ['user_name' => $inputData['user_name']];
        $field = 'user_id, user_code, user_name, real_name, password, head_pic, email,
         mobile, branch_id, status, deleted, access_key, access_key_create_time';
        $userInfo = $this->userModel->getUserInfo($condition, $field);
        if(!$userInfo || $userInfo['status']==1 || $userInfo['deleted']==1){
            return reTmJsonObj(500,'没有该用户',[]);
        }

        //验证密码
        if(md5(md5($inputData['password'])) !== $userInfo['password']){
            return reTmJsonObj(500,'密码错误',[]);
        }
        unset($userInfo['password']);

        //记录登录信息到数据库
        $token = createCode();
        $updateCondition = ['user_id' => $userInfo['user_id']];
        $updateData['access_key'] = $token;
        $updateData['access_key_create_time'] = time();
        $remember = $this->userModel->updateUserInfo($updateCondition, $updateData);
        if($remember === false){
            return reTmJsonObj(500, '记录登录信息失败', []);
        }

        //保存用户信息到缓存 7天
        $cacheData = [
            "user_id" => $userInfo['user_id'],
            "user_code" => $userInfo['user_code'],
            "user_name" => $userInfo['user_name'],
            "access_key_create_time" => $updateData['access_key_create_time'],
            "access_key" => $updateData['access_key'],
        ];
        Cache::set($updateData['access_key'], $cacheData, Config::get('token_time'));
        Logservice::writeArray(['token'=>$updateData['access_key'], 'data'=>$cacheData], '用户登录缓存');

        //获取用户部门code,name
        $branch = $this->_getBranch($userInfo['branch_id']);
        if($branch === false){
            return reTmJsonObj(500, '获取用户部门失败', []);
        }
        $userInfo['branch_code'] = $branch['branch_code'];
        $userInfo['branch_name'] = $branch['branch_name'];

        //获取用户所有角色code,name
        $role = $this->_getRole($userInfo['user_code']);
        if($role === false){
            return reTmJsonObj(500, '获取用户所有角色失败', []);
        }
        $roleCodes = array_column($role, 'role_code');

        //获取用户权限code,name
        $privilege = $this->_getPrivilege($roleCodes);
        if($privilege === false){
            return reTmJsonObj(500, '获取用户权限失败', []);
        }

        //获取用户所有站点code,name
        $site = $this->_getSite($roleCodes);
        if($site === false){
            return reTmJsonObj(500, '获取用户站点失败', []);
        }

        //获取所有应用code,name,app_code
        $component = $this->_getComponent($roleCodes);
        if($component === false){
            return reTmJsonObj(500, '获取用户站点失败', []);
        }

        $return = [
            'token' => $token,
            'user_info' => $userInfo,
            'role' => $role,
            'site' => $site,
            'privilege' => $privilege,
            'component' => $component
        ];

        return reTmJsonObj(200, '登录成功', $return);
    }

    /**
     * 验证token
     */
    public function checkToken(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['token'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500,$msg,[]);
        }

        //检查缓存中token
        $cache = Cache::get($inputData['token']);
        $token = $cache['access_key'];
        if(!$cache || empty($cache['user_id'])){
            return reTmJsonObj(500, '无效的token', []);
        }

        $user_id = $cache['user_id'];
        //没有则查询数据库是否有这个token的用户存在
        $condition = [
            'user_id' => $user_id,
            'status' => 0,
            'deleted' => 0
        ];
        $field = 'user_id, user_code, user_name, real_name, password, head_pic,
         email, mobile, branch_id, access_key, access_key_create_time';
        $userInfo = $this->userModel->getUserInfo($condition, $field);
        //没有这个用户说明token错误
        if(!$userInfo){
            return reTmJsonObj(500, '用户不存在', []);
        }

        //判断token是否已经超时
        $time = time() - $userInfo['access_key_create_time'];
        if($time > 3600*24*7){
            return reTmJsonObj(500, 'token超时', []);
        }

        //获取用户部门code,name
        $branch = $this->_getBranch($userInfo['branch_id']);
        if($branch === false){
            return reTmJsonObj(500, '获取用户部门失败', []);
        }
        $userInfo['branch_code'] = $branch['branch_code'];
        $userInfo['branch_name'] = $branch['branch_name'];

        //获取用户所有角色code,name
        $role = $this->_getRole($userInfo['user_code']);
        if($role === false){
            return reTmJsonObj(500, '获取用户所有角色失败', []);
        }
        $roleCodes = array_column($role, 'role_code');

        //获取用户权限code,name
        $privilege = $this->_getPrivilege($roleCodes);
        if($privilege === false){
            return reTmJsonObj(500, '获取用户权限失败', []);
        }

        //获取用户所有站点code,name
        $site = $this->_getSite($roleCodes);
        if($site === false){
            return reTmJsonObj(500, '获取用户站点失败', []);
        }

        //获取所有应用code,name,app_code
        $component = $this->_getComponent($roleCodes);
        if($component === false){
            return reTmJsonObj(500, '获取用户站点失败', []);
        }

        $return = [
            'token' => $token,
            'user_info' => $userInfo,
            'role' => $role,
            'site' => $site,
            'privilege' => $privilege,
            'component' => $component
        ];

        return reTmJsonObj(200, '验证成功', $return);
    }
}