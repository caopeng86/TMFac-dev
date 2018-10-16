<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 15:29
 */

namespace app\member\controller;


use app\api\model\ConfigModel;
use app\extend\controller\Logservice;
use app\member\model\MemberModel;
use app\member\model\MemberThirdPartyModel;
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
        $field = 'member_id, member_code, member_name,member_nickname,site_code, email, mobile, head_pic, create_time, status, deleted,
        birthday, sex,password,wx,qq,wb';
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
                    'head_pic' => '/uploads/default/head.jpg'
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
            "member_id" => $memberInfo['member_id'],
            "user_id" => $memberInfo['member_id'],
            "member_code" => $memberInfo['member_code'],
            "member_name" => $memberInfo['member_name'],
            "access_key" => $updateData['access_key'],
            "access_key_create_time" => $updateData['access_key_create_time'],
        ];
        Cache::set($token, $cacheData,Config::get('token_time'));
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
        $field = 'member_id, member_code, member_name,member_nickname,site_code, email, mobile, head_pic, create_time, status, deleted,
        birthday, sex,password,wx,qq,wb';
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
            $addData['head_pic'] = empty($inputData['head_pic'])?'/uploads/default/head.jpg':$inputData['head_pic'];
            $addData['member_nickname'] = empty($inputData['member_nickname'])?'':$inputData['member_nickname'];
            $addData['birthday'] = empty($inputData['birthday'])?'':$inputData['birthday'];
            $addData['sex'] = empty($inputData['sex'])?0:$inputData['sex'];
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
        $updateData['ip'] = Request::ip();
        //保存第3方登陆数据
        $ThirdPartyModel = new MemberThirdPartyModel();
        $ThirdPartyModel->updateOrAddThirdParty($inputData,$memberInfo,$updateData['ip']);
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
            "member_id" => $memberInfo['member_id'],
            "user_id" => $memberInfo['member_id'],
            "member_code" => $memberInfo['member_code'],
            "member_name" => $memberInfo['member_name'],
            "access_key" => $updateData['access_key'],
            "access_key_create_time" => $updateData['access_key_create_time'],
        ];
        Cache::set($token, $cacheData,Config::get('token_time'));
        Logservice::writeArray(['token'=>$token, 'data'=>$cacheData], '会员登录信息');

        $return = [
            'token' => $token,
            'member_info' => $memberInfo,
        ];
        return reJson(200, '登录成功', $return);
    }

    /**
     * 绑定接口
     */
    public function bindOtherLoginInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['uid','type','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $memberInfo = $this->memberModel->getMemberInfo(['member_code'=>$inputData['member_code']]);
        if(!$memberInfo){
            return reJson(500,'用户不存在',[]);
        }
        $bindData = [];
        //根据uid取出会员数据
        if($inputData['type'] == 1){
            $bindData['qq'] = $inputData['uid'];
        }elseif ($inputData['type'] == 2){
            $bindData['wx'] = $inputData['uid'];
        }elseif ($inputData['type'] == 3){
            $bindData['wb'] = $inputData['uid'];
        }
        //验证第3方登录账户是否绑定了其他用户
        $otherMember = $this->memberModel->getMemberInfo($bindData);
        if($otherMember){
            return reJson(500,'该账号已绑定',[]);
        }
        if(empty($memberInfo['member_nickname'])){
            $bindData['member_nickname'] = $memberInfo['member_nickname'] = $inputData['member_nickname'];
        }
        if(empty($memberInfo['head_pic'])){
            $bindData['head_pic'] = $memberInfo['head_pic'] = $inputData['head_pic'];
        }
        $result = $this->memberModel->updateMember(['member_id'=>$memberInfo['member_id']],$bindData);
        if(!$result){
            return reJson(500,'用户信息保存失败',[]);
        }
        $updateData['ip'] = Request::ip();
        //保存第3方登陆数据
        $ThirdPartyModel = new MemberThirdPartyModel();
        $ThirdPartyModel->updateOrAddThirdParty($inputData,$memberInfo,$updateData['ip']);
        return reJson(200,'绑定成功',[]);
    }

    /**
     * 取消绑定
     */
    public function cancelBindInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['type','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        //判断是否能够取消
        $memberInfo = $this->memberModel->getMemberInfo(['member_code'=>$inputData['member_code']]);
        if(!$memberInfo){
            return reJson(500,'用户不存在',[]);
        }
        $cancelInfo = array();
        if($inputData['type'] == 1){
            $memberInfo['qq'] = '';
            $cancelInfo['qq'] = '';
        }elseif ($inputData['type'] == 2){
            $memberInfo['wx'] = '';
            $cancelInfo['wx'] = '';
        }elseif ($inputData['type'] == 3){
            $memberInfo['wb'] = '';
            $cancelInfo['wb'] = '';
        }
        if(!$memberInfo['mobile'] && !$memberInfo['qq'] && !$memberInfo['wx'] && !$memberInfo['wb']){
            return reJson(500,'不能取消绑定', []);
        }
        $result = $this->memberModel->updateMember(['member_id'=>$memberInfo['member_id']],$cancelInfo);
        if($result){
            return reJson(200,'取消成功',[]);
        }
        return reJson(500,'取消失败');
    }

    /**
     * 检查版本
     */
    public function getVersion(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        if(!in_array($inputData['type'],['ios_version','android_version']))return reJson(500,'type参数异常');
        $ConfigModel = new ConfigModel();
        $condition = [];
        $condition['key'] = ['version','must_update'];
        $condition['type'] = $inputData['type'];
        $ConfigList = $ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reJson(500, '获取失败', []);
        }
        $ConfigList = $ConfigModel->ArrayToKey($ConfigList);
        return reJson(200, '获取成功', $ConfigList);
    }

    /**
     * 获取启动页配置
     */
    public function getStartConfig(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = [];
        $condition['key'] = ['app_start_image','app_start_image_m','app_start_image_s','app_start_url','app_start_title'];
        $condition['type'] = 'client';
        $ConfigModel = new ConfigModel();
        $ConfigList = $ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reJson(500, '获取失败', []);
        }
        $ConfigList = $ConfigModel->ArrayToKey($ConfigList);
        foreach ($ConfigList as $key => $val){
            if(!$val)unset($ConfigList[$key]);
        }
        return reJson(200, '获取成功', $ConfigList);
    }
}