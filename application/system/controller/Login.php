<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 15:17
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\BranchModel;
use app\api\model\RoleModel;
use app\api\model\UserModel;
use think\facade\Cache;
use think\captcha\Captcha;
use think\Controller;
use think\facade\Config;
use think\facade\Env;
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
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取用户角色失败', 2);
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
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取用户权限失败', 2);
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
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取用户站点失败', 2);
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
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '获取用户应用失败', 2);
            return false;
        }
        return $site;
    }

    /**
     * 检测缓存中的licensesToken
     * @param $licensesToken
     * @param $priKey
     * @return bool
     */
    private function _checkLicensesToken($licensesToken, $priKey){
        //私钥解密
        openssl_private_decrypt(base64_decode($licensesToken), $decrypt, $priKey);
        if(!$decrypt){
            Logservice::writeArray(['priKey'=>$priKey,'licenses'=>base64_decode($licensesToken)], 'licenses私钥解密失败', 2);
            return false;
        }
        $decrypt = json_decode($decrypt, true);
        $time = $decrypt['end_time'];

        if($time < time()){
            return false;
        }
        return $decrypt;
    }

    /**
     * 从云平台验证licenses有效性
     * @param $licensesKey
     * @param $msg
     * @return bool
     */
    private function _checkLicenses($licensesKey, &$msg){
        //请求云端验证
        $url = Config::get('licenses_chk');
        $postData = [
            'licenses_key' => $licensesKey,
            'url' => $_SERVER['SERVER_NAME']
        ];
        $output = curlPost($url, $postData);
        if($output === false){
            return false;
        }

        $output = json_decode($output, true);
        if($output['code'] == 500){
            $msg = $output['msg'];
            return false;
        }

        $outputData = $output['data'];
        //返回获得的数据
        return $outputData;
    }

    /**
     * 校验licenses
     * @param $userCode
     * @param $msg
     * @return bool|mixed
     */
    private function _licenses($userCode, &$msg){
        //检测licenses文件
        $file = Env::get('app_path')."priKey.pem";
        if(!is_file($file)){
            $msg = [501, '请联系平台授权'];
            return false;
        }
        //获取并验证licenses_key
        $data = json_decode(base64_decode(file_get_contents($file)), true);
        $licensesKey = $data['licenses_key'];
        $priKey = $data['pri_key'];
        //检查缓存中是否存在licenses_key数据
        $cacheLicenses = Cache::get($userCode);
        if(!isset($cacheLicenses['licenses_token'])){
            //丛云平台检查licenses
            $licenses = $this->_checkLicenses($licensesKey, $err);
            if($licenses === false){
                $msg = [502, $err];
                return false;
            }
            //私钥解密
            openssl_private_decrypt(base64_decode($licenses), $decrypt, $priKey);
            $decrypt = json_decode($decrypt, true);
            //保存入缓存7天
            Cache::set($userCode, ["licenses_token" => $licenses], 3600*24*7);
        }else{
            //存在则检查缓存中licensesToken是否过期
            $decrypt = $this->_checkLicensesToken($cacheLicenses['licenses_token'], $priKey);
            if($decrypt === false){
                //丛云平台检查licenses
                $licenses = $this->_checkLicenses($licensesKey, $err);
                if($licenses === false){
                    $msg = [502, $err];
                    return false;
                }
                //私钥解密
                openssl_private_decrypt(base64_decode($licenses), $decrypt, $priKey);
                $decrypt = json_decode($decrypt, true);
                //保存入缓存7天
                Cache::set($userCode, ["licenses_token" => $licenses], 3600*24*7);
            }
        }

        return $decrypt;
    }

    /**
     * 生成验证码
     */
    public function getVerify(){
//        var_dump('hello');exit();
        ob_clean();
        $this->captcha->codeSet = '1';
        $captchaData = $this->captcha->entry();
        return $captchaData;
    }
    public function getEnv(){
        $env=Env::get(SERVER_ENV.'HOSTNAME');

        var_dump($env);
        var_dump(SERVER_ENV.'HOSTNAME');
        var_dump(Env::get('EXTEND_PATH'));
        var_dump(Env::get());
//        var_dump($_SERVER);
    }

    /**
     * B端用户登录
     */
    public function userLogin(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['user_name', 'password', 'verify'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        //验证验证码
        $chkVerify = $this->captcha->check($inputData['verify']);
        if($chkVerify === false){
            return reJson(500,'验证码输入错误',[]);
        }

        //验证用户名
        $condition = ['user_name' => $inputData['user_name']];
        $field = 'user_id, user_code, user_name, real_name, password, head_pic, email,
         mobile, branch_id, status, deleted, access_key, access_key_create_time';
        $userInfo = $this->userModel->getUserInfo($condition, $field);
        if(!$userInfo || $userInfo['status']==1 || $userInfo['deleted']==1){
            return reJson(500,'没有该用户',[]);
        }

        //验证密码
        if(md5(md5($inputData['password'])) !== $userInfo['password']){
            return reJson(500,'密码错误',[]);
        }
        unset($userInfo['password']);

        //记录登录信息到数据库
        $token = createCode();
        $updateCondition = ['user_id' => $userInfo['user_id']];
        $updateData['access_key'] = $token;
        $updateData['access_key_create_time'] = time();
        $remember = $this->userModel->updateUserInfo($updateCondition, $updateData);
        if($remember === false){
            return reJson(500, '记录登录信息失败', []);
        }

        //校验licenses
//        $decrypt = $this->_licenses($userInfo['user_code'], $err);
//        if($decrypt === false){
//            Logservice::writeArray(['user_code'=>$userInfo['user_code'], 'err'=>$err], 'licenses校验未通过', 2);
//            return reJson($err[0], $err[1], []);
//        }

        //保存用户信息到缓存 7天
        $cacheData = [
            "user_id" => $userInfo['user_id'],
            "user_code" => $userInfo['user_code'],
            "user_name" => $userInfo['user_name'],
            "access_key_create_time" => $updateData['access_key_create_time'],
            "access_key" => $updateData['access_key'],
        ];
        Cache::set($updateData['access_key'], $cacheData, 5);
        Logservice::writeArray(['token'=>$updateData['access_key'], 'data'=>$cacheData], '记录登录缓存数据');

        //获取用户部门code,name
        $branch = $this->_getBranch($userInfo['branch_id']);
        if($branch === false){
            return reJson(500, '获取用户部门失败', []);
        }
        $userInfo['branch_code'] = $branch['branch_code'];
        $userInfo['branch_name'] = $branch['branch_name'];

        //获取用户所有角色code,name
        $role = $this->_getRole($userInfo['user_code']);
        if($role === false){
            return reJson(500, '获取用户所有角色失败', []);
        }
        $roleCodes = array_column($role, 'role_code');

        //获取用户权限code,name
        $privilege = $this->_getPrivilege($roleCodes);
        if($privilege === false){
            return reJson(500, '获取用户权限失败', []);
        }

        //获取用户所有站点code,name
        $site = $this->_getSite($roleCodes);
        if($site === false){
            return reJson(500, '获取用户站点失败', []);
        }

//        //获取所有应用code,name,app_code
//        $component = $this->_getComponent($roleCodes);
//        if($component === false){
//            return reJson(500, '获取用户站点失败', []);
//        }

        $return = [
//            'type' => $decrypt['type'],
            'token' => $token,
            'user_info' => $userInfo,
            'role' => $role,
            'site' => $site,
            'privilege' => $privilege,
            'component' => ''
        ];
        return reJson(200, '登录成功', $return);
    }

    /**
     * 获取图标列表
     */
    public function getIconList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //获取文件名数据
        $path = Env::get('root_path').'uploads'.DIRECTORY_SEPARATOR.'icon';
        $file = scandir($path);
        unset($file[0],$file[1]);
        $file = array_merge($file);

        //拼接文件路径输出
        foreach ($file as $k => $v){
            $v = 'uploads'.DIRECTORY_SEPARATOR.'icon'.DIRECTORY_SEPARATOR.$v;
            $file[$k] = $v;
        }

        //分页处理
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        $total = count($file);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $file = array_slice($file, $firstRow, $pageSize);
        $totalPage = ceil($total / $pageSize);

        //拼接数据输出
        $outPut = [
            'total' => $total,
            'total_page' => $totalPage,
            'list' => $file,
        ];

        return reJson(200, '获取成功', $outPut);
    }

    /**
     * 输入licenses验证
     */
    public function reCheckLicenses(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['licenses_key'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //验证输入的licenses
        //检测licenses文件
        $file = Env::get('app_path')."priKey.pem";
        if(!is_file($file)){
            return reJson(501, '请联系平台授权', []);
        }
        //获取并验证licenses_key
        $data = json_decode(base64_decode(file_get_contents($file)), true);
        $priKey = $data['pri_key'];

        //检查licenses文件,获取并验证licenses
        $licensesKey = $inputData['licenses_key'];
        $licenses = $this->_checkLicenses($licensesKey, $err);
        if($licenses === false){
            return reJson(502, $err, []);
        }

        //检测公钥加密的数据是否能被本地私钥解密
        $licensesToken = $this->_checkLicensesToken($licenses, $priKey);
        if(!$licensesToken){
            return reJson(502, '私钥错误', []);
        }

        //修改文本文件中licenses
        $reData = ['pri_key' => $priKey, 'licenses_key' => $inputData['licenses_key']];
        file_put_contents($file, base64_encode(json_encode($reData)));

        return reJson(500, '成功', []);
    }
}