<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\api\controller;

use app\api\model\ConfigModel;
use app\api\model\SiteModel;
use app\api\model\SystemArticleModel;
use app\extend\controller\Logservice;
use app\member\model\MemberModel;
use app\member\model\MemberThirdPartyModel;
use app\member\model\MemberpointModel;
use app\member\model\MemberBehaviorLogModel;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Config;

class Member extends Base
{
    protected $memberModel;
    protected  $ConfigModel;
    protected $MemberpointModel;
    protected $MemberBehaviorLogModel;
    protected  $SystemArticleModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
        $this->ConfigModel = new ConfigModel();
        $this->MemberpointModel = new MemberpointModel();
        $this->MemberBehaviorLogModel = new MemberBehaviorLogModel();
        $this->SystemArticleModel = new SystemArticleModel();

    }

    /**
     * 获取搜索条件
     * @param $inputData
     * @return array
     */
    private function _getCondition($inputData){
        //昵称/姓名/手机号码/邮箱/注册时间/站点
        $condition = [];
        $condition['deleted'] = 0;
        empty($inputData['site_code']) ? : $condition['site_code'] = $inputData['site_code'];
        empty($inputData['status']) ? : $condition['status'] = $inputData['status'];
        if(!empty($inputData['input'])){
            $condition['member_name|member_nickname|member_real_name|email|mobile'] = ['like', '%'.$inputData['input'].'%'];
        }
        if(!empty($inputData['start_time']) && !empty($inputData['end_time'])){
            $inputData['start_time'] = strtotime($inputData['start_time']);
            $inputData['end_time'] = strtotime($inputData['end_time']);
            $condition['create_time'] = ['between', [$inputData['start_time'], $inputData['end_time']]];
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
            return reTmJsonObj(500, $msg, []);
        }

        //获取搜索条件
        $condition = $this->_getCondition($inputData);
        $field = 'member_code, member_name, member_nickname, member_real_name, site_name, email, mobile, create_time';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->memberModel->getCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'member_id desc';
        //获取会员列表数据
        $memberList = $this->memberModel->getMemberList($condition, $field, $limit, $order);
        if($memberList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员列表失败', 2);
            return reTmJsonObj(500, '获取会员列表失败', []);
        }

        //时间转时间戳
        foreach ($memberList as $k => $v){
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $memberList[$k] = $v;
        }

        $return = [
            'list' => $memberList,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reTmJsonObj(200, '获取会员列表成功', $return);
    }

    /**
     * 获取会员信息
     */
    public function getMemberInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $token = Request::header('token');
		$params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret || !$token || strlen($token)<20){
            return reTmJsonObj(500, $msg, []);
        }
		
		$userId = getUserIByToken($token);
		if(empty($userId)){
            return reTmJsonObj(500, "token验证失效", []);
        }

		$condition['member_code'] = $inputData['member_code'];

        $field = 'member_id,member_code, member_name,member_sn,member_nickname, member_real_name,site_code,email,deleted,sex_edit_time,birthday_edit_time,mobile_edit_time,wb_edit_time,wx_edit_time,qq_edit_time,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,password,receive_notice,wifi_show_image,list_auto_play,login_type';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reTmJsonObj(500, '获取会员信息失败', []);
        }
		
        $memberInfo['login_type'] = empty($memberInfo['login_type'])?'mobile':$memberInfo['login_type'];
        $siteModel = new SiteModel();
        $siteName = $siteModel->getSiteInfo(['site_code' => $memberInfo['site_code']], 'site_name')['site_name'];
        $memberInfo['site_name'] = $siteName;

        $memberInfo['create_time'] = date('Y-m-d H:i:s', $memberInfo['create_time']);

        $memberInfo['close'] = 0;
        if(time()>$memberInfo['close_start_time'] && time()<$memberInfo['close_end_time']){
            $memberInfo['close'] = 1;
        }
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value');
        $ConfigList = array_column($ConfigList,'value','key');
        //第3方信息获取
        $memberThirdPartyModel = new MemberThirdPartyModel();
        $memberInfo['other_info'] = $memberThirdPartyModel->getThirdPartyListById($memberInfo['member_id']);
        $memberInfo['other_info'] = $memberThirdPartyModel->ArrayToType($memberInfo['other_info']);
        $ConfigList['sex'] = empty($memberInfo['sex_edit_time'])?$ConfigList['sex']:0;
        $ConfigList['birthday'] = empty($memberInfo['birthday_edit_time'])?$ConfigList['birthday']:0;
        $ConfigList['mobile'] = empty($memberInfo['mobile_edit_time'])?$ConfigList['mobile']:0;
        $ConfigList['wb'] = empty($memberInfo['wb_edit_time'])?$ConfigList['wb']:0;
        $ConfigList['wx'] = empty($memberInfo['wx_edit_time'])?$ConfigList['wx']:0;
        $ConfigList['qq'] = empty($memberInfo['qq_edit_time'])?$ConfigList['qq']:0;
        $memberInfo['point_config'] = $ConfigList;
        $memberInfo['is_first_login'] = false;

		unset($memberInfo["password"]);

        return reTmJsonObj(200, '获取会员信息成功', $memberInfo);
    }

    /*
	* 获取会员积分签到情况*/
    public function getMemberPointSign(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ["member_code"];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $return = [];

        $condition['member_code'] = $inputData['member_code'];
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value,remarks');
        $memberInfo = $this->memberModel->getMemberInfo($condition);
        if($memberInfo === false || $ConfigList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = array_column($ConfigList,null,'key');
        $loop = [];
        $sign_time_date = empty($memberInfo['sign_time'])?0:$this->diffBetweenTwoDays(date('Y-m-d', time()),date('Y-m-d', $memberInfo['sign_time']));
        $sign_num = 0;
        $today_is_sign = false;
        $today_num = 1;
        if($sign_time_date <2){
            $sign_num = empty($memberInfo['sign_num'])?0:$memberInfo['sign_num'];
        }
        $today_num = $sign_num%7 + 1;
        if($sign_time_date>0){
            $today_is_sign = true;
        }
        for ($x=0; $x<7; $x++) {
            array_push($loop,(int)$ConfigList['sign']['value']);
        }

        if(!empty($ConfigList['sign_cycle_first']['value']) && !empty($ConfigList['sign_cycle_first']['remarks'])
            && (int)$ConfigList['sign_cycle_first']['remarks']<8 && (int)$ConfigList['sign_cycle_first']['remarks']>0){
            $loop[$ConfigList['sign_cycle_first']['remarks']-1] = $loop[$ConfigList['sign_cycle_first']['remarks']-1] + $ConfigList['sign_cycle_first']['value'];
        }
        if(!empty($ConfigList['sign_cycle_two']['value']) && !empty($ConfigList['sign_cycle_two']['remarks'])
            && $ConfigList['sign_cycle_two']['remarks']<8 && $ConfigList['sign_cycle_two']['remarks']>0){
            $loop[$ConfigList['sign_cycle_two']['remarks']-1] = $loop[$ConfigList['sign_cycle_two']['remarks']-1] + $ConfigList['sign_cycle_two']['value'];
        }


       /* if(!empty($ConfigList['sign_extra_first']['value']) && !empty($ConfigList['sign_extra_first']['remarks'])){
            if(($sign_num-$sign_num%7+1)<=$ConfigList['sign_extra_first']['remarks'] && ($sign_num+(7-$sign_num%7))>=$ConfigList['sign_extra_first']['remarks']){
                $loop[$ConfigList['sign_extra_first']['remarks']%7-1] = $loop[$ConfigList['sign_extra_first']['remarks']%7-1] + $ConfigList['sign_extra_first']['value'];
            }
        }

        if(!empty($ConfigList['sign_extra_two']['value']) && !empty($ConfigList['sign_extra_two']['remarks'])){
            if(($sign_num-$sign_num%7+1)<=$ConfigList['sign_extra_two']['remarks'] && ($sign_num+(7-$sign_num%7))>=$ConfigList['sign_extra_two']['remarks']){
                $loop[$ConfigList['sign_extra_two']['remarks']%7-1] = $loop[$ConfigList['sign_extra_two']['remarks']%7-1] + $ConfigList['sign_extra_two']['value'];
            }
        }*/

       $return=[
           'loop'=>$loop,
           'sign_num'=>$sign_num,
           'today_is_sign'=>$today_is_sign,
           'today_num'=>$today_num,
           'sign_cycle_first_num'=>(int)$ConfigList['sign_cycle_first']['remarks'],
           'sign_cycle_two_num'=>(int)$ConfigList['sign_cycle_two']['remarks'],
           'sign_extra_two_num'=>(int)$ConfigList['sign_extra_two']['remarks'],
           'sign_extra_first_num'=>(int)$ConfigList['sign_extra_first']['remarks']
       ];

        return reTmJsonObj(200, '获取成功', $return);
    }

    /**
     * 会员积分签到
     */
    public function memberPointSign(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value,remarks');
        $memberInfo = $this->memberModel->getMemberInfo($condition);
        if($memberInfo === false || $ConfigList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = array_column($ConfigList,null,'key');

        if(!empty($ConfigList['sign_switch']['value']) && (1 == $ConfigList['sign_switch']['value'] || '1' == $ConfigList['sign_switch']['value'])){
            $loop = [];
            $sign_time_date = empty($memberInfo['sign_time'])?0:$this->diffBetweenTwoDays(date('Y-m-d', time()),date('Y-m-d', $memberInfo['sign_time']));
            $sign_num = 0;
            $today_is_sign = false;
            $today_num = 1;
            if($sign_time_date <2){
                $sign_num = empty($memberInfo['sign_num'])?0:$memberInfo['sign_num'];
            }
            $today_num = $sign_num%7 + 1;
            if($sign_time_date>0){
                $today_is_sign = true;
            }
            for ($x=0; $x<7; $x++) {
                array_push($loop,(int)$ConfigList['sign']['value']);
            }

            if(!empty($ConfigList['sign_cycle_first']['value']) && !empty($ConfigList['sign_cycle_first']['remarks'])
                && (int)$ConfigList['sign_cycle_first']['remarks']<8 && (int)$ConfigList['sign_cycle_first']['remarks']>0){
                $loop[$ConfigList['sign_cycle_first']['remarks']-1] = $loop[$ConfigList['sign_cycle_first']['remarks']-1] + $ConfigList['sign_cycle_first']['value'];
            }
            if(!empty($ConfigList['sign_cycle_two']['value']) && !empty($ConfigList['sign_cycle_two']['remarks'])
                && $ConfigList['sign_cycle_two']['remarks']<8 && $ConfigList['sign_cycle_two']['remarks']>0){
                $loop[$ConfigList['sign_cycle_two']['remarks']-1] = $loop[$ConfigList['sign_cycle_two']['remarks']-1] + $ConfigList['sign_cycle_two']['value'];
            }


            if(!empty($ConfigList['sign_extra_first']['value']) && !empty($ConfigList['sign_extra_first']['remarks'])){
                if(($sign_num-$sign_num%7+1)<=$ConfigList['sign_extra_first']['remarks'] && ($sign_num+(7-$sign_num%7))>=$ConfigList['sign_extra_first']['remarks']){
                    $loop[$ConfigList['sign_extra_first']['remarks']%7-1] = $loop[$ConfigList['sign_extra_first']['remarks']%7-1] + $ConfigList['sign_extra_first']['value'];
                }
            }

            if(!empty($ConfigList['sign_extra_two']['value']) && !empty($ConfigList['sign_extra_two']['remarks'])){
                if(($sign_num-$sign_num%7+1)<=$ConfigList['sign_extra_two']['remarks'] && ($sign_num+(7-$sign_num%7))>=$ConfigList['sign_extra_two']['remarks']){
                    $loop[$ConfigList['sign_extra_two']['remarks']%7-1] = $loop[$ConfigList['sign_extra_two']['remarks']%7-1] + $ConfigList['sign_extra_two']['value'];
                }
            }
            $re1 = false;
            if($today_is_sign){
                $re1 = $this->updatePoint($memberInfo,$loop[$today_num-1],['member_id'=>$memberInfo['member_id']],"sign_time","签到");
            }
            if(false === $re1){
                return reTmJsonObj(500, '今天已经签到', []);
            }
            return reTmJsonObj(200, '签到成功', []);

        }
        return reTmJsonObj(500, '签到失败', []);
    }

    /*获取积分规则*/

    public function getPointRule(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $Condition = ['id'=>4];
        $data = $this->SystemArticleModel->getArticleInfo($Condition);
        return reTmJsonObj(200,'获取成功',$data);
    }

    public function updatePoint($getMemberInfo,$putpoint,$condition,$memberKey,$remark = "修改用户信息"){
        $point = $getMemberInfo['point'] + $putpoint;
        $pointchange = $putpoint;
        $updateMemberData = [
            $memberKey=>time(),
            "point"=>$point,
            'sign_num'=>$getMemberInfo['sign_num']+1,
            'sign_time'=>time()
        ];
        $re = $this->memberModel->updateMember($condition, $updateMemberData);
        if(false === $re){
            return false;
        }
        $re = $this->MemberpointModel->addPointLog($getMemberInfo['member_id'],$pointchange,$remark,$point,'center');
        if(false === $re){
            return false;
        }
        $this->MemberBehaviorLogModel->addPointLog($getMemberInfo['member_id'],$remark);
        return true;

    }



    public function diffBetweenTwoDays($day1, $day2){
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }
}