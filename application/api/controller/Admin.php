<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 11:05
 */

namespace app\api\controller;


use app\api\model\BranchModel;
use app\api\model\UserModel;
use app\extend\controller\Logservice;
use app\member\model\MemberModel;
use think\Controller;
use think\facade\Request;

class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据id获取用户详情
     * @return \think\response\Json
     */
    public function getUserInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['user_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $userModel = new UserModel();
        $field = 'user_id, user_code, user_name, real_name, branch_id, is_branch_admin, status, email, tel, mobile, deleted,
         create_time, head_pic, extend';
        $info = $userModel->getUserInfo(['user_id' => $inputData['user_id']], $field);
        if($info === false){
            Logservice::writeArray(['sql'=>$userModel->getLastSql()], '获取用户详情失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }

        return reTmJsonObj(200, '成功', $info);
    }

    /**
     * 获取所有用户列表
     * @return \think\response\Json
     */
    public function getUserList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //获取用户列表
        $userModel = new UserModel();
        $field = 'user_id, user_code, user_name, real_name, branch_id, is_branch_admin, status, email, tel, mobile, deleted,
         create_time, head_pic, extend';
        $userList = $userModel->getUserList([], $field);
        if($userList === false){
            Logservice::writeArray(['sql'=>$userModel->getLastSql()], '获取用户列表失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }

        //获取组织机构列表
        $branchModel = new BranchModel();
        $branchList = $branchModel->getBranchList([], 'branch_id, branch_name');
        if($branchList === false){
            Logservice::writeArray(['sql'=>$branchModel->getLastSql()], '获取组织机构列表失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }

        //拼接数据
        foreach ($userList as $key => $value){
            foreach ($branchList as $k => $v) {
                $value['branch_name'] = "";
                if($value['branch_id'] == $v['branch_id']){
                    $value['branch_name'] = $v['branch_name'];
                }
            }
            $userList[$key] = $value;
        }

        return reTmJsonObj(200, '成功', $userList);
    }

    /**
     * 根据id获取分支机构详情
     * @return \think\response\Json
     */
    public function getBranchInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['department_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $branchModel = new BranchModel();
        $field = 'branch_name, branch_tel, branch_fax, branch_code';
        $info = $branchModel->getBranchInfo(['branch_id' => $inputData['department_id']], $field);
        if($info === false){
            Logservice::writeArray(['sql'=>$branchModel->getLastSql()], '获取组织机构详情失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }

        return reTmJsonObj(200, '成功', $info);
    }

    /**
     * 获取会员详情
     * @return \think\response\Json
     */
    public function getMemberInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['member_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $memberModel = new MemberModel();
        $field = 'member_id, member_code, member_name, member_nickname, member_real_name, site_code, email, mobile,
        head_pic, birthday, sex, status, deleted, create_time,access_key';
        $info = $memberModel->getMemberInfo(['member_id' => $inputData['member_id']], $field);
        if($info === false){
            Logservice::writeArray(['sql'=>$memberModel->getLastSql()], '获取会员详情失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }

        return reTmJsonObj(200, '成功', $info);
    }

    /**
     * 获取会员详情
     * @return \think\response\Json
     */
    public function getMemberInfoByMobile(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['mobile'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $memberModel = new MemberModel();
        $field = 'member_id, member_code, member_name, member_nickname, member_real_name, site_code, email, mobile,
        head_pic, birthday, sex, status, deleted, create_time';
        $info = $memberModel->getMemberInfo(['mobile' => $inputData['mobile']], $field);
        if($info === false){
            Logservice::writeArray(['sql'=>$memberModel->getLastSql()], '获取会员详情失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }

        return reTmJsonObj(200, '成功', $info);
    }

    /**
     * 修改用户扩展字段
     * @return \think\response\Json
     */
    public function updateUserExtend(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['user_id', 'extend'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $userModel = new UserModel();
        $re = $userModel->updateUserInfo(['user_id' => $inputData['user_id']], ['extend' => $inputData['extend']]);
        if($re === false){
            Logservice::writeArray(['sql'=>$userModel->getLastSql()], '修改用户扩展字段失败', 2);
            return reTmJsonObj(500, '修改失败', []);
        }

        return reTmJsonObj(200, '成功', []);
    }

    /**
     * 推送全部
     */
    public function pushAll(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['title', 'content'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $extras = $inputData;
        $JPush = new \app\extend\controller\Jpush();
        $re = $JPush::JPushAll($extras);
        if($re['http_code'] != 200){
            Logservice::writeArray(['err'=>$re], '推送失败', 2);
            return reTmJsonObj(500, '推送失败', $re);
        }

        return reTmJsonObj(200, '推送成功', []);
    }

    /**
     * 推送单个
     */
    public function pushOne(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['title', 'content', 'member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $extras = $inputData;
        $extras['alias'] = $inputData['member_code'];
        unset($extras['member_code']);
        $JPush = new \app\extend\controller\Jpush();
        $re = $JPush::JPushOne($extras);
        if($re['http_code'] != 200){
            Logservice::writeArray(['err'=>$re], '推送失败', 2);
            return reTmJsonObj(500, '推送失败', $re);
        }

        return reTmJsonObj(200, '推送成功', []);
    }
}