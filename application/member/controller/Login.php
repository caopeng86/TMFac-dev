<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 15:29
 */

namespace app\member\controller;


use app\extend\controller\Logservice;
use app\member\model\MemberModel;
use think\Controller;
use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Login extends Controller
{
    protected $memberModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
    }

    /**
     * 会员登录  state: 1:验证码登录 else:密码登录
     */
    public function memberLogin(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['mobile','state','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //验证手机号
        if(!preg_match("/^1[34578]\d{9}$/", $inputData['mobile'])){
            return reJson(500, '手机号错误', []);
        }

        //根据手机号取出会员数据
        $condition = ['mobile' => $inputData['mobile'], 'site_code' => $inputData['site_code']];
        $field = 'member_id, member_code, member_name, site_code, email, mobile, head_pic, create_time, status, deleted,
        birthday, sex';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
            return reJson(500, '获取会员数据失败', []);
        }
        if($memberInfo['status'] == 1 || $memberInfo['deleted'] == 1){
            return reJson(500, '该会员被删除或禁用', []);
        }

        Db::startTrans();
        if($inputData['state'] == 1){
            //选择验证码登录
            if(!isset($inputData['code'])){
                return reJson(500, '验证码参数错误', []);
            }
            //缓存中取出验证码,验证手机验证码
            $code = Cache::get(md5($inputData['mobile']));
            if($code != $inputData['code']){
                Logservice::writeArray(['code'=>$inputData['code'], 'cache'=>$code], '手机验证码错误', 2);
                return reJson(500, '验证失败', []);
            }
            if(empty($memberInfo)){
                //没有会员则新增该会员
                $addData = [
                    'member_name' => substr($inputData['mobile'],0,3)."****".substr($inputData['mobile'],7,4),
                    'member_code' => createCode(),
                    'create_time' => time(),
                    'password' => md5(md5(rand(100000,999999))),
                    'site_code' => $inputData['site_code'],
                    'mobile' => $inputData['mobile'],
                ];
                $add = $this->memberModel->addMember($addData);
                if(!$add){
                    Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
                    Db::rollback();
                    return reJson(500, '新增会员失败', []);
                }
                $addData['member_id'] = $add;
                unset($addData['password']);
                $memberInfo = $this->memberModel->getMemberInfo(['member_id' => $addData['member_id']], $field);
                if($memberInfo === false){
                    Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
                    return reJson(500, '获取会员数据失败', []);
                }
                Logservice::writeArray(['memberInfo'=>$addData], '短信注册会员数据');
            }
        }else{
            //选择密码登录
            if(!isset($inputData['password'])){
                return reJson(500, '密码参数错误', []);
            }
            if(empty($memberInfo)){
                return reJson(500, '没有该会员', []);
            }else {
                //验证密码
                if (md5(md5($inputData['password'])) !== $memberInfo['password']) {
                    return reJson(500, '密码错误', []);
                }
                unset($memberInfo['password']);
            }
        }

        //记录登录信息到数据库
        $token = createCode();
        $updateCondition = ['mobile' => $inputData['mobile']];
        $updateData['access_key'] = $token;
        $updateData['access_key_create_time'] = time();
        $remember = $this->memberModel->updateMember($updateCondition, $updateData);
        if($remember === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '记录登录信息失败', 2);
            Db::rollback();
            return reJson(500,'记录登录信息失败',[]);
        }
        Db::commit();

        //保存会员信息到缓存 7天
        $cacheData = [
            "user_id" => $memberInfo['member_id'],
            "member_code" => $memberInfo['member_code'],
            "member_name" => $memberInfo['member_name'],
            "access_key" => $updateData['access_key'],
            "access_key_create_time" => $updateData['access_key_create_time'],
        ];
        Cache::set($token, $cacheData, 3600*24*7);
        Logservice::writeArray(['token'=>$token, 'data'=>$cacheData], '会员登录信息');

        $return = [
            'token' => $token,
            'member_info' => $memberInfo,
        ];

        return reJson(200, '登录成功', $return);
    }

    /**
     * 第三方登录 type: 1:qq登录 / 2:微信登录 / 3:新浪微博
     */
    public function anotherLogin(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['uid','type','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //根据uid取出会员数据
        if($inputData['type'] == 1){
            $addData['qq'] = $inputData['uid'];
            $condition['qq'] = $inputData['uid'];
        }elseif ($inputData['type'] == 2){
            $addData['wx'] = $inputData['uid'];
            $condition['wx'] = $inputData['uid'];
        }elseif ($inputData['type'] == 3){
            $addData['wb'] = $inputData['uid'];
            $condition['wb'] = $inputData['uid'];
        }
        $condition['site_code'] = $inputData['site_code'];
        $field = 'member_id, member_code, member_name, site_code, email, mobile, head_pic, create_time, status, deleted,
        birthday, sex';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
            return reJson(500, '获取会员数据失败', []);
        }
        if($memberInfo['status'] == 1 || $memberInfo['deleted'] == 1){
            return reJson(500, '该会员被删除或禁用', []);
        }

        //没有会员则新增该会员
        Db::startTrans();
        if(empty($memberInfo)){
            $addData['member_name'] = empty($inputData['member_name'])?rand(100,999).'****'.rand(1000,9999):$inputData['member_name'];
            $addData['site_code'] = $inputData['site_code'];
            $addData['member_code'] = createCode();
            $addData['create_time'] = time();
            $addData['password'] = md5(md5(rand(100000,999999)));
            $addData['sex'] = empty($inputData['sex'])?'':$inputData['sex'];
            $addData['head_pic'] = empty($inputData['head_pic'])?'':$inputData['head_pic'];
            $add = $this->memberModel->addMember($addData);
            if(!$add){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
                Db::rollback();
                return reJson(500, '新增会员失败', []);
            }
            $addData['member_id'] = $add;
            unset($addData['password']);
            $memberInfo = $this->memberModel->getMemberInfo(['member_id' => $addData['member_id']], $field);
            if($memberInfo === false){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
                return reJson(500, '获取会员数据失败', []);
            }
            Logservice::writeArray(['memberInfo'=>$addData], '第三方登录新增会员数据');
        }

        //记录登录信息到数据库
        $token = createCode();
        $updateCondition = ['member_code' => $memberInfo['member_code']];
        $updateData['access_key'] = $token;
        $updateData['access_key_create_time'] = time();
        $remember = $this->memberModel->updateMember($updateCondition, $updateData);
        if($remember === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '记录登录信息失败', 2);
            Db::rollback();
            return reJson(500,'记录登录信息失败',[]);
        }
        Db::commit();

        //保存会员信息到缓存 7天
        $cacheData = [
            "user_id" => $memberInfo['member_id'],
            "member_code" => $memberInfo['member_code'],
            "member_name" => $memberInfo['member_name'],
            "access_key" => $updateData['access_key'],
            "access_key_create_time" => $updateData['access_key_create_time'],
        ];
        Cache::set($token, $cacheData, 3600*24*7);
        Logservice::writeArray(['token'=>$token, 'data'=>$cacheData], '会员登录信息');

        $return = [
            'token' => $token,
            'member_info' => $memberInfo,
        ];

        return reJson(200, '登录成功', $return);
    }
}