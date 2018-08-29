<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\MemberModel;
use app\api\model\SiteModel;
use app\member\model\MemberThirdPartyModel;
use think\Db;
use think\facade\Request;

class Member extends Base
{
    protected $memberModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
    }

    /**
     * 获取搜索条件
     * @param $inputData
     * @return array
     */
    private function _getCondition($inputData){
        //昵称/姓名/手机号码/邮箱/注册时间/站点
        $condition = [];
        array_push($condition,['deleted','=',0]);
        empty($inputData['site_code']) ? : array_push($condition,[$this->memberModel->getTableName().'.site_code','=',$inputData['site_code']]);
        empty($inputData['status']) ? : array_push($condition,['status','=',$inputData['status']]);
        if(!empty($inputData['input'])){
            array_push($condition,['member_name|member_nickname|member_real_name|email|mobile','like','%'.$inputData['input'].'%']);
        }
        if(!empty($inputData['start_time']) && !empty($inputData['end_time'])){
            $inputData['start_time'] = strtotime($inputData['start_time']);
            $inputData['end_time'] = strtotime($inputData['end_time']);
            array_push($condition,['create_time','between',[$inputData['start_time'], $inputData['end_time']]]);
        }
        return $condition;
    }

    /**
     * 获取会员列表
     */
    public function getMemberList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        //获取搜索条件
        $condition = $this->_getCondition($inputData);
        $field = 'member_id,member_code, member_name, member_nickname, member_real_name, email,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->memberModel->getCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'create_time desc';
        //获取会员列表数据
        $memberList = $this->memberModel->getMemberList($condition, $field, $limit, $order);
        if($memberList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员列表数据失败', 2);
            return reJson(500, '获取会员列表失败', []);
        }
        foreach ($memberList as $k => $v){
            if(empty($v['head_pic'])){ //头像处理
                $v['head_pic'] = '/images/member_default_head.png';
            }else{
                if(substr($v['head_pic'],0,1) != '/' && !preg_match("/^(http:\/\/|https:\/\/).*$/",$v['head_pic'])){
                    $v['head_pic'] = 'http://'.$v['head_pic'];
                }
            }
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']); //时间转时间戳
            //第3方信息获取
            $memberThirdPartyModel = new MemberThirdPartyModel();
            $v['other_info'] = $memberThirdPartyModel->getThirdPartyList(['member_id'=>$v['member_id']],'uid,nick_name,member_id,head_url,address,ip,type');
            $v['other_info'] = $memberThirdPartyModel->ArrayToType($v['other_info']);
            $memberList[$k] = $v;
        }

        $return = [
            'list' => $memberList,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reJson(200, '获取会员列表成功', $return);
    }

    /**
     * 获取会员信息
     */
    public function getMemberInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $field = 'member_code, member_name, member_nickname, member_real_name, site_code, email,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情数据失败', 2);
            return reJson(500, '获取会员信息失败', []);
        }

        $siteModel = new SiteModel();
        $siteName = $siteModel->getSiteInfo(['site_code' => $memberInfo['site_code']], 'site_name')['site_name'];
        $memberInfo['site_name'] = $siteName;

        $memberInfo['create_time'] = date('Y-m-d H:i:s', $memberInfo['create_time']);

        return reJson(200, '获取会员信息成功', $memberInfo);
    }

    /**
     * 新增会员
     */
    public function addMember(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_name','password','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $inputData['member_code'] = createCode();
        $inputData['create_time'] = time();
        $inputData['password'] = md5(md5($inputData['password']));

        $re = $this->memberModel->addMember($inputData);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
            return reJson(500, '新增会员失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '新增会员');
        return reJson(200, '新增会员成功', []);
    }

    /**
     * 修改会员
     */
    public function updateMember(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $re = $this->memberModel->updateMember($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员数据失败', 2);
            return reJson(500, '编辑会员失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改会员');
        return reJson(200, '编辑会员成功', []);
    }

    /**
     * 更改用户密码
     */
    public function changePassword(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['member_code', 'old_pass', 'new_pass'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition = ['member_code' => $inputData['member_code']];
        //比对旧密码
        $userInfo = $this->memberModel->updateMember($condition, 'password');
        if($userInfo['password'] !== md5(md5($inputData['old_pass']))){
            return reJson(500, '原密码输入错误', []);
        }

        //更改密码
        $re = $this->memberModel->updateMember($condition, ['password' => md5(md5($inputData['new_pass']))]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员密码失败', 2);
            return reJson(500, '修改失败', []);
        }
        Logservice::writeArray([], '修改会员密码');
        return reJson(200, '修改成功', []);
    }

    /**
     * 删除会员
     */
    public function deleteMember(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['member_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //支持批量删除
        $memberCode = explode(',', $inputData['member_codes']);
        Db::startTrans();
        foreach ($memberCode as $value){
            $condition['member_code'] = $value;
            //删除用户即修改用户deleted字段值
            $re = $this->memberModel->updateMember($condition, ['deleted' => 1]);
            if($re === false){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql(), 'condition'=>$condition], '删除会员数据失败', 2);
                Db::rollback();
                return reJson(500, '删除用户失败', []);
            }
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '删除会员');
        return reJson(200, '删除会员成功', []);
    }

    /**
     * 获取会员总数,今日新增会员,昨日新增会员
     */
    public function getCount(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //总数
        $totalConf['deleted'] = 0;

        $total = $this->memberModel->getCount($totalConf);
        if($total === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '统计总数失败', 2);
            return reJson(500, '获取全部统计失败', []);
        }

        //今日统计
        $todayStart = strtotime(date('Y-m-d'));
        $todayEnd = $todayStart + 24*3600 - 1;
        $todayConf = [
            'deleted' => 0,
            'create_time'=> array('between',"$todayStart,$todayEnd")
        ];
        $today = $this->memberModel->getCount($todayConf);
        if($today === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '统计今日数据失败', 2);
            return reJson(500, '获取今日统计失败', []);
        }

        //昨日统计
        $yesterdayStart = strtotime(date('Y-m-d', strtotime("-1 day")));
        $yesterdayEnd = $yesterdayStart + 24*3600 - 1;
        $yesterdayConf = [
            'deleted' => 0,
            'create_time'=> array('between',"$yesterdayStart,$yesterdayEnd")
        ];
        $yesterday = $this->memberModel->getCount($yesterdayConf);
        if($yesterday === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '统计昨日数据失败', 2);
            return reJson(500, '获取昨日统计失败', []);
        }

        $return = [
            'total' => $total,
            'today' => $today,
            'yesterday' => $yesterday
        ];
        return reJson(200, '获取统计成功', $return);
    }

    /**
     * 更新会员信息
     */
    public function updateMemberInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $MemberModel = new MemberModel();
        $condition = array(
            'member_id'=>$inputData['member_id']
        );
        $MemberInfo = $MemberModel->getMemberInfo($condition);
        if(!$MemberInfo){
            return reJson(500,'用户不存在',[]);
        }
        $data = array();
        $allowFiled = array('member_name','member_nickname','member_real_name','email','mobile','head_pic','sex','birthday','receive_notice','wifi_show_image','list_auto_play');
        foreach ($allowFiled as $val){
            if(!empty($inputData[$val])){
                $data[$val] = $inputData[$val];
            }
        }
        if(count($data) < 0){
            return reJson(500,'更新信息不存在',[]);
        }
        $result = $MemberModel->updateMember($condition,$data);
        if($result){
            $MemberInfo = $MemberModel->getMemberInfo($condition);
            return reJson(200,'更新成功',$MemberInfo);
        }
        return reJson(500,'更新失败',[]);
    }

    /**
     * 禁用或开启
     */
    public function forbiddenOrStartMember(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_id','status'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $MemberModel = new MemberModel();
        $condition = array(
            'member_id'=>$inputData['member_id']
        );
        $MemberInfo = $MemberModel->getMemberInfo($condition);
        if(!$MemberInfo){
            return reJson(500,'用户不存在',[]);
        }
        if(!in_array($inputData['status'],[0,1])){
            return reJson(500,'状态参数异常',[]);
        }
        $result = $MemberModel->updateMember($condition,['status'=>$inputData['status']]);
        if($result){
            return reJson(200,'成功',[]);
        }
        return reJson(500,'失败',[]);
    }

}