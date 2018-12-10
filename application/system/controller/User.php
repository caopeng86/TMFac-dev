<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 14:43
 */

namespace app\system\controller;



use app\extend\controller\Logservice;
use app\api\model\UserModel;
use think\Db;
use think\facade\Request;

class User extends Base
{

    protected $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    /**
     * 判断用户名是否重复
     * @param $userName
     * @return bool
     */
    private function _checkUserName($userName){
        $re = $this->userModel->getUserInfo(['user_name' => $userName], 'user_code');
        if($re){
           return false;
        }
        return true;
    }

    /**
     * 修改用户基本信息
     */
    public function updateUserInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['user_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //修改基本信息
        $condition['user_code'] = $inputData['user_code'];
        $re = $this->userModel->updateUserInfo($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->userModel->getLastSql()], '修改用户数据失败', 2);
            return reTmJsonObj(500, '修改失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改用户基本信息');
        return reTmJsonObj(200, '修改成功', []);
    }

    /**
     * 更改用户密码
     */
    public function changePassword(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['user_code', 'old_pass', 'new_pass'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition = ['user_code' => $inputData['user_code']];
        //比对旧密码
        $userInfo = $this->userModel->getUserInfo($condition, 'password');
        if($userInfo['password'] !== md5(md5($inputData['old_pass']))){
            return reTmJsonObj(500, '原密码输入错误', []);
        }

        //更改密码
        $re = $this->userModel->updateUserInfo($condition, ['password' => md5(md5($inputData['new_pass']))]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->userModel->getLastSql()], '修改用户密码失败', 2);
            return reTmJsonObj(500, '修改失败', []);
        }
        Logservice::writeArray([], '修改用户密码');
        return reTmJsonObj(200, '修改成功', []);
    }

    /**
     * 新增用户
     */
    public function addUser(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['user_name', 'password', 'branch_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $inputData['create_time'] = time();
        $inputData['user_code'] = createCode();
        $inputData['password'] = md5(md5($inputData['password']));

        //判断用户名是否重复
        $name = $this->_checkUserName($inputData['user_name']);
        if(!$name){
            return reTmJsonObj(500, '用户名重复', []);
        }

        //新增角色数据
        $re = $this->userModel->addUser($inputData);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->userModel->getLastSql()], '新增用户数据失败', 2);
            return reTmJsonObj(500, '新增用户失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '新增用户');
        return reTmJsonObj(200, '新增用户成功', []);
    }

    /**
     * 删除用户数据,可批量
     */
    public function deleteUser(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['user_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //支持批量删除
        $userCode = explode(',', $inputData['user_codes']);
        Db::startTrans();
        foreach ($userCode as $value){
            $condition['user_code'] = $value;
            //删除用户即修改用户deleted字段值
            $re = $this->userModel->updateUserInfo($condition, ['deleted' => 1]);
            if($re === false){
                Logservice::writeArray(['sql'=>$this->userModel->getLastSql(), 'condition'=>$condition], '删除用户数据失败', 2);
                Db::rollback();
                return reTmJsonObj(500, '删除用户失败', []);
            }
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '删除用户');
        return reTmJsonObj(200, '删除用户成功', []);
    }

    /**
     * 重置用户密码
     */
    public function resetPassword(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['user_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition = ['user_code' => $inputData['user_code']];
        //重置密码
        $re = $this->userModel->updateUserInfo($condition, ['password' => md5(md5(123456))]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->userModel->getLastSql()], '重置用户密码失败', 2);
            return reTmJsonObj(500, '重置失败', []);
        }
        Logservice::writeArray([], '重置用户密码');
        return reTmJsonObj(200, '重置成功', []);
    }

}