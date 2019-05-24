<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 15:29
 */

namespace app\member\controller;


use app\api\model\ConfigModel;
use app\api\model\StartadvModel;
use app\extend\controller\Logservice;
use app\member\model\MemberModel;
use app\member\model\MemberThirdPartyModel;
use app\member\model\MemberpointModel;
use app\member\model\MemberBehaviorLogModel;
use think\Controller;
use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Login extends Controller
{
    protected $memberModel;
    protected $MemberpointModel;
    protected $MemberBehaviorLogModel;
    protected  $ConfigModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
        $this->MemberpointModel = new MemberpointModel();
        $this->MemberBehaviorLogModel = new MemberBehaviorLogModel();
        $this->ConfigModel = new ConfigModel();
    }

    /**
     * 会员登录  state: 1:验证码登录 else:密码登录
     */
    public function memberLogin(){
        //判断请求方式以及请求参数
        // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['mobile','state','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

		$mobile = $inputData['mobile'];
		$site_code = $inputData['site_code'];
        //验证手机号
        if(!preg_match("/^1[3456789]\d{9}$/", $mobile)){
            return reTmJsonObj(500, '手机号错误', []);
        }

		//选择验证码登录
		if($inputData['state'] == 1){
			if(!isset($inputData['code'])){
                return reTmJsonObj(500, '验证码参数错误', []);
            }
            //缓存中取出验证码,验证手机验证码
            $code = Cache::get(md5($mobile));
            if($code != $inputData['code']){
                Logservice::writeArray(['code'=>$inputData['code'], 'cache'=>$code], '手机验证码错误', 2);
                return reTmJsonObj(500, '验证失败', []);// 
            }
		//选择密码登录
		}else{
            if(!isset($inputData['password']) && strlen($inputData['password']) < 6){
                return reTmJsonObj(500, '密码参数格式错误（最低长度6位）', []);
            }
		}

		$ctime = time();
 
        $memberInfo = $this->memberModel->getMemberInfoByMobile($mobile,$site_code);
        if(!empty($memberInfo)){
            if($memberInfo['status'] == 1 || $memberInfo['deleted'] == 1){
                return reTmJsonObj(500, '很遗憾，该账户已被列入企业黑名单', []);
            }
            if($ctime>$memberInfo['close_start_time'] && $ctime<$memberInfo['close_end_time']){
                return reTmJsonObj(500, '很遗憾，该账户已被封号，封号时间'.date('Y-m-d H:i:s', $memberInfo['close_start_time']).'到'.date('Y-m-d H:i:s', $memberInfo['close_end_time']), []);
            }
        }
        $is_first_login = false;
		
		//用户信息验证
		Db::startTrans();
        if($inputData['state'] == 1){
            if(empty($memberInfo)){
                $is_first_login = true;
                //没有会员则新增该会员
                $memberName=substr($mobile,0,3)."****".substr($mobile,7,4);
                $addData = [
                    'member_name' => $memberName,
                    'member_nickname' => $memberName,
                    'member_code' => createCode(),
                    'create_time' => $ctime,
                    'password' => md5(md5(rand(100000,999999))),
                    'site_code' => $inputData['site_code'],
                    'mobile' => $mobile,
					'status' => 0,
					'login_type' => "mobile",
                    'head_pic' => '/uploads/default/head.jpg',
					'register_source'=> 'APP',
                    'member_sn'=>$this->createMemberSn(),
                ];
                empty($inputData['channel_sources'])?$addData['channel_sources'] = "":$addData['channel_sources'] = $inputData['channel_sources'] ?? "";
                $addData['member_nickname']=$addData['member_name'];
                $add = $this->memberModel->addMember($addData);
                if(!$add){
                    Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
                    Db::rollback();
                    return reTmJsonObj(500, '新增会员失败', []);
                }
                $addData['member_id'] = $add;
                unset($addData['password']);
                $memberInfo = $this->memberModel->getMemberInfoById($addData['member_id']);
                if($memberInfo === false){
                    Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
                    return reTmJsonObj(500, '获取会员数据失败', []);
                }
                Logservice::writeArray(['memberInfo'=>$addData], '短信注册会员数据');
            }
        }else{
            if(empty($memberInfo)){
                return reTmJsonObj(500, '没有该会员', []);
            }else {
                //验证密码
                if (md5(md5($inputData['password'])) !== $memberInfo['password']) {
                    return reTmJsonObj(500, '密码错误', []);
                }
                unset($memberInfo['password']);
            }
        }
		Db::commit();

		$tokenInfo = array();
		$tokenInfo['user_id'] = $memberInfo['member_id'];
		$tokenInfo['member_id'] = $memberInfo['member_id'];
		$tokenInfo['mobile'] = $memberInfo['mobile'];
		$tokenInfo['ip'] = $_SERVER['REMOTE_ADDR'];
		$token = createTokenCode($tokenInfo);
		
		$updateData = array();
		if($memberInfo['status'] == 2){ //如果是未登陆激活的用户，将用户进行激活处理
			$updateData['status'] = 0;
        }
		$updateData['login_type'] = "mobile";
		$updateData['access_key'] = $token;
		$updateData['access_key_create_time'] = $ctime;
		$this->memberModel->updateMember(['member_id' => $memberInfo['member_id']], $updateData);
		
		$memberInfo['status'] = 0;

		$Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value');
        $ConfigList1 = array_column($ConfigList,null,'key');
        $ConfigList = array_column($ConfigList,'value','key');
		
		//首次登陆增加积分
		if(empty($memberInfo['mobile_edit_time'])){
			$this->updatePoint($memberInfo,$ConfigList1,"mobile_edit_time","first_login","手机号码首次注册");
			$memberInfo = $this->memberModel->getMemberInfoById($memberInfo['member_id'],false);
		}

        $memberInfo['close'] = 0;
        if($ctime>$memberInfo['close_start_time'] && $ctime<$memberInfo['close_end_time']){
            $memberInfo['close'] = 1;
        }
		
        //第3方信息获取
        $memberThirdPartyModel = new MemberThirdPartyModel();
        $memberInfo['other_info'] = $memberThirdPartyModel->getThirdPartyListById($memberInfo['member_id']);
        $ConfigList['sex'] = empty($memberInfo['sex_edit_time'])?$ConfigList['sex']??0:0;
        $ConfigList['birthday'] = empty($memberInfo['birthday_edit_time'])?$ConfigList['birthday']??0:0;
        $ConfigList['mobile'] = empty($memberInfo['mobile_edit_time'])?$ConfigList['mobile']??0:0;
        $ConfigList['wb'] = empty($memberInfo['wb_edit_time'])?$ConfigList['wb']??0:0;
        $ConfigList['wx'] = empty($memberInfo['wx_edit_time'])?$ConfigList['wx']??0:0;
        $ConfigList['qq'] = empty($memberInfo['qq_edit_time'])?$ConfigList['qq']??0:0;
        $memberInfo['point_config'] = $ConfigList;
        $memberInfo['is_first_login'] = $is_first_login;

		$memberInfo['user_id'] = $memberInfo['member_id'];
		$memberInfo['access_key'] = $token;
        $memberInfo['access_key_create_time'] = $ctime;
		$memberInfo['create_token_time'] = $ctime;

		Cache::set($token,$memberInfo,Config::get('token_time'));
        Logservice::writeArray(['token'=>$token, 'data'=>$memberInfo], '会员登录信息');

		unset($memberInfo["password"]);	

        $return = [
            'token' => $token,
            'member_info' => $memberInfo,
			'appname' => Config::get('app_name'),
        ];

        return reEncryptJson(200, '登录成功', $return);
    }

    /**
     * 第三方登录 type: 1:qq登录 / 2:微信登录 / 3:新浪微博
     */
    public function anotherLogin(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['uid','type','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

		$uid = $inputData['uid'];
		$site_code = $inputData['site_code'];
        $register_source = "qq";
        //根据uid取出会员数据
        if($inputData['type'] == 1){
            $register_source = "qq";
        }elseif ($inputData['type'] == 2){
            $register_source = "wx";
        }elseif ($inputData['type'] == 3){
            $register_source = "wb";
        }
		$ctime = time();
        
        $memberInfo = $this->memberModel->getMemberInfoByThird($uid, $register_source, $site_code);
        if(!empty($memberInfo)){
            if($memberInfo['status'] == 1 || $memberInfo['deleted'] == 1){
                return reTmJsonObj(500, '很遗憾，该账户已被列入企业黑名单', []);
            }
            if($ctime>$memberInfo['close_start_time'] && $ctime<$memberInfo['close_end_time']){
                return reTmJsonObj(500, '很遗憾，该账户已被封号，封号时间'.date('Y-m-d H:i:s', $memberInfo['close_start_time']).'到'.date('Y-m-d H:i:s', $memberInfo['close_end_time']), []);
            }
        }

        $is_first_login = false;

        //没有会员则新增该会员
        Db::startTrans();
        if(empty($memberInfo)){
            $is_first_login = true;
            $addData['member_name'] = empty($inputData['member_name'])?rand(100,999).'****'.rand(1000,9999):$inputData['member_name'];
            $addData['site_code'] = $inputData['site_code'];
            $addData['member_code'] = createCode();
            $addData['status'] = 0;
			$addData['create_time'] = $ctime;
			$addData[$register_source] = $inputData['uid'];
			$addData['register_source'] = $register_source;
            $addData['password'] = md5(md5(rand(100000,999999)));
            $addData['sex'] = empty($inputData['sex'])?'':$inputData['sex'];
            $addData['head_pic'] = empty($inputData['head_pic'])?'/uploads/default/head.jpg':$inputData['head_pic'];
            $addData['member_nickname'] = empty($inputData['member_nickname'])?$addData['member_name']:$inputData['member_nickname'];
            $addData['birthday'] = empty($inputData['birthday'])?'':$inputData['birthday'];
            $addData['sex'] = empty($inputData['sex'])?0:$inputData['sex'];
            $addData['member_sn'] = $this->createMemberSn();
            empty($inputData['channel_sources'])?$addData['channel_sources'] = "":$addData['channel_sources'] = $inputData['channel_sources'];

            $add = $this->memberModel->addMember($addData);
            if(!$add){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
                Db::rollback();
                return reTmJsonObj(500, '新增会员失败', []);
            }
            $addData['member_id'] = $add;
            unset($addData['password']);
            $memberInfo = $this->memberModel->getMemberInfoById($addData['member_id']);
            if($memberInfo === false){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
                return reTmJsonObj(500, '获取会员数据失败', []);
            }
            Logservice::writeArray(['memberInfo'=>$addData], '第三方登录新增会员数据');

			//保存第3方登陆数据
			$RequestIp = Request::ip();
			$ThirdPartyModel = new MemberThirdPartyModel();
			$ThirdPartyModel->updateOrAddThirdParty($inputData,$memberInfo,$RequestIp);
        }
		Db::commit();

		$tokenInfo = array();
		$tokenInfo['user_id'] = $memberInfo['member_id'];
		$tokenInfo['member_id'] = $memberInfo['member_id'];
		$tokenInfo['mobile'] = $memberInfo['mobile'];
		$tokenInfo['ip'] = $_SERVER['REMOTE_ADDR'];
		$token = createTokenCode($tokenInfo);

		$updateData = array();
		if($memberInfo['status'] == 2){ //如果是未登陆激活的用户，将用户进行激活处理
			$updateData['status'] = 0;
        }
		$updateData['login_type'] = $register_source;
		$updateData['access_key'] = $token;
		$updateData['access_key_create_time'] = $ctime;
		$this->memberModel->updateMember(['member_id' => $memberInfo['member_id']], $updateData);
		
		$memberInfo['status'] = 0;

        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value');
        $ConfigList1 = array_column($ConfigList,null,'key');
        $ConfigList = array_column($ConfigList,'value','key');
		
		//首次登陆增加积分
		if($inputData['type'] == 1 && empty($memberInfo['qq_edit_time'])){
			$this->updatePoint($memberInfo,$ConfigList1,"qq_edit_time","first_login","QQ首次登陆");
			$memberInfo = $this->memberModel->getMemberInfoById($memberInfo['member_id'], false);
		}elseif ($inputData['type'] == 2 && empty($memberInfo['wx_edit_time'])){
			$this->updatePoint($memberInfo,$ConfigList1,"wx_edit_time","first_login","微信首次登陆");
			$memberInfo = $this->memberModel->getMemberInfoById($memberInfo['member_id'], false);
		}elseif ($inputData['type'] == 3 && empty($memberInfo['wb_edit_time'])){
			$this->updatePoint($memberInfo,$ConfigList1,"wb_edit_time","first_login","微博首次登陆");
			$memberInfo = $this->memberModel->getMemberInfoById($memberInfo['member_id'], false);
		}

        $memberInfo['close'] = 0;
        if(time()>$memberInfo['close_start_time'] && time()<$memberInfo['close_end_time']){
            $memberInfo['close'] = 1;
        }

        //第3方信息获取
        $memberThirdPartyModel = new MemberThirdPartyModel();
        $memberInfo['other_info'] = $memberThirdPartyModel->getThirdPartyListById($memberInfo['member_id']);
        $memberInfo['other_info'] = $memberThirdPartyModel->ArrayToType($memberInfo['other_info']);
        $ConfigList['sex'] = empty($memberInfo['sex_edit_time'])?$ConfigList['sex']??0:0;
        $ConfigList['birthday'] = empty($memberInfo['birthday_edit_time'])?$ConfigList['birthday']??0:0;
        $ConfigList['mobile'] = empty($memberInfo['mobile_edit_time'])?$ConfigList['mobile']??0:0;
        $ConfigList['wb'] = empty($memberInfo['wb_edit_time'])?$ConfigList['wb']??0:0;
        $ConfigList['wx'] = empty($memberInfo['wx_edit_time'])?$ConfigList['wx']??0:0;
        $ConfigList['qq'] = empty($memberInfo['qq_edit_time'])?$ConfigList['qq']??0:0;
        $memberInfo['point_config'] = $ConfigList;
        $memberInfo['is_first_login'] = $is_first_login;

		$memberInfo['user_id'] = $memberInfo['member_id'];
		$memberInfo['access_key'] = $token;
        $memberInfo['access_key_create_time'] = $ctime;
		$memberInfo['create_token_time'] = $ctime;

        Cache::set($token, $memberInfo,Config::get('token_time'));
        Logservice::writeArray(['token'=>$token, 'data'=>$memberInfo], '会员登录信息');

		unset($memberInfo["password"]);

        $return = [
            'token' => $token,
            'member_info' => $memberInfo,
			'appname' => Config::get('app_name'),
        ];

        return reEncryptJson(200, '登录成功', $return);
    }

    /**
     * 绑定接口
     */
    public function bindOtherLoginInfo(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['uid','type','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $memberInfo = $this->memberModel->getMemberInfo(['member_code'=>$inputData['member_code']]);
        if(!$memberInfo){
            return reTmJsonObj(500,'用户不存在',[]);
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
            return reTmJsonObj(500,'该账号已绑定',[]);
        }
        if(empty($memberInfo['member_nickname'])){
            $bindData['member_nickname'] = $memberInfo['member_nickname'] = $inputData['member_nickname'];
        }
        if(empty($memberInfo['head_pic'])){
            $bindData['head_pic'] = $memberInfo['head_pic'] = $inputData['head_pic'];
        }

        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value');
        $ConfigList = array_column($ConfigList,null,'key');
        $result = $this->memberModel->updateMember(['member_id'=>$memberInfo['member_id']],$bindData);
        $re1 = false;
        if($inputData['type'] == 1){
            $re1 = $this->updatePoint($memberInfo,$ConfigList,"qq_edit_time","qq","绑定QQ");
        }elseif ($inputData['type'] == 2){
            $re1 = $this->updatePoint($memberInfo,$ConfigList,"wx_edit_time","wx","绑定微信");
        }elseif ($inputData['type'] == 3){
            $re1 = $this->updatePoint($memberInfo,$ConfigList,"wb_edit_time","wb","绑定微博");
        }
        if(!$result ||  $re1 === false){
            return reTmJsonObj(500,'用户信息保存失败',[]);
        }
        $updateData['ip'] = Request::ip();
        //保存第3方登陆数据
        $ThirdPartyModel = new MemberThirdPartyModel();
        $ThirdPartyModel->updateOrAddThirdParty($inputData,$memberInfo,$updateData['ip']);
        return reEncryptJson(200,'绑定成功',[]);
    }

    public function updatePoint($memberInfo,$ConfigList,$memberKey,$configKey,$remark = "修改用户信息"){
        if(empty($memberInfo[$memberKey])){
            $point = $memberInfo['point'];
            $pointchange = 0;
			
            if(!empty($ConfigList['first_login_switch']['value']) && (1 == $ConfigList['first_login_switch']['value'] || '1' == $ConfigList['first_login_switch']['value'])){
                $point = $memberInfo['point'] + $ConfigList[$configKey]['value'];
                $pointchange = $ConfigList[$configKey]['value'];
            }

            $updateMemberData = [
                $memberKey=>time(),
                "point"=>$point
            ];

            $re = $this->memberModel->updateMember(['member_id'=>$memberInfo['member_id']], $updateMemberData);
            if(false === $re){
                return false;
            }
            $re = $this->MemberpointModel->addPointLog($memberInfo['member_id'],$pointchange,$remark,$point,'center');
            if(false === $re){
                return false;
            }
            $this->MemberBehaviorLogModel->addPointLog($memberInfo['member_id'],$remark);
            return true;
        }
        return true;
    }

    /**
     * 取消绑定
     */
    public function cancelBindInfo(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['type','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //判断是否能够取消
        $memberInfo = $this->memberModel->getMemberInfo(['member_code'=>$inputData['member_code']]);
        if(!$memberInfo){
            return reTmJsonObj(500,'用户不存在',[]);
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
            return reTmJsonObj(500,'不能取消绑定', []);
        }
        $result = $this->memberModel->updateMember(['member_id'=>$memberInfo['member_id']],$cancelInfo);
        if($result){
            return reEncryptJson(200,'取消成功',[]);
        }
        return reTmJsonObj(500,'取消失败');
    }

    /**
     * 检查版本
     */
    public function getVersion(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(!in_array($inputData['type'],['ios_version','android_version']))return reTmJsonObj(500,'type参数异常');
        $ConfigModel = new ConfigModel();
        $condition = [];
        $condition['key'] = ['version','must_update'];
        $condition['type'] = $inputData['type'];
        $ConfigList = $ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $ConfigList = $ConfigModel->ArrayToKey($ConfigList);
        return reEncryptJson(200, '获取成功', $ConfigList);
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
            return reTmJsonObj(500, $msg, []);
        }
        $StartAdvModel = new StartadvModel();
        $condition = [];
        $condition['key'] = ['start_adv_type'];
        $condition['type'] = 'app';
        $ConfigModel = new ConfigModel();
        $Config = $ConfigModel->getOneConfig($condition);
        if($Config === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $field = 'id,app_start_image,app_start_image_m,app_start_image_s,url,show_duration';
        $condition  = [
            ['status','=',1],
            ['start_time','<',time()],
            ['app_start_image','<>',''],
            ['app_start_image_s','<>',''],
            ['app_start_image_m','<>','']
        ];
        $startAdvList = $StartAdvModel->advList($condition,$field,false,'sort desc');
        if($startAdvList === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        if(!(count($startAdvList) > 0)){ //如果没有启动图则获取配置项中的启动图
            $condition = [];
            $condition['key'] = ['app_start_image','app_start_image_m','app_start_image_s','app_start_url','app_start_title'];
            $condition['type'] = 'client';
            $ConfigModel = new ConfigModel();
            $ConfigList = $ConfigModel->getConfigList($condition);
            if($ConfigList === false){
                return reTmJsonObj(500, '获取失败', []);
            }
            $ConfigList = $ConfigModel->ArrayToKey($ConfigList);
            $ConfigList['id']    = 0;
            $ConfigList['start_id'] = 0;
            return reTmJsonObj(200, '获取成功',$ConfigList);
        }
        if($Config['value'] == 1){ //随机
            $startAdvInfo = $startAdvList[rand(0,count($startAdvList) - 1)];
        }else{ //默认从大到小
            $now_id = !empty($inputData['id']) ? $inputData['id']:0;
            if($now_id > 0){
                $key = 0;
                foreach ($startAdvList as $k => $v){
                    if($v['id'] == $now_id){
                        if(!empty($startAdvList[$k+1])){ //如果不为空,则将$key传入
                            $key = $k+1;break;
                        }
                    }
                }
                $startAdvInfo = $startAdvList[$key];
            }else{
                $startAdvInfo = $startAdvList[0];
            }
        }
        $startAdvInfo['app_start_url'] = $startAdvInfo['url'];
        $condition = [];
        $condition['key'] = ['site_name'];
        $condition['type'] = 'base';
        $ConfigSiteName = $ConfigModel->getOneConfig($condition);
        if($ConfigSiteName === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        $startAdvInfo['app_start_title'] = $ConfigSiteName['value'];
        $startAdvInfo['start_id'] = $startAdvInfo['id'];
        return reTmJsonObj(200, '获取成功', $startAdvInfo);
    }

    /**
     * 创建用户唯一的sn号 提供给前端显示
     */
    private function createMemberSn(){
        $sn = $this->createSn();
        if($this->memberModel->getMemberInfo(['member_sn'=>$sn],'member_id')){ //如果存在则重新生成sn
            $sn = $this->createMemberSn();
        }
        return $sn;
    }

    /**
     * 生成sn号
     */
    private function createSn(){
        $chars='abcdefghijklmnopqrstuvwxyz';
        $len=strlen($chars);
        $randStr='';
        for ($i=0;$i<4;$i++){
            $randStr.=$chars[rand(0,$len-1)];
        }
        return $randStr .= rand(100000,999999);
    }

    /**
     * 注册接口
     */
    public function register(){
        //判断请求方式以及请求参数
      //  $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['mobile','code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        Db::startTrans();
//        //选择验证码登录
        if(!isset($inputData['code'])){
            return reTmJsonObj(500, '验证码参数错误', []);
        }
//        缓存中取出验证码,验证手机验证码
        $code = Cache::get(md5($inputData['mobile']));
        if($code != $inputData['code']){
            Logservice::writeArray(['code'=>$inputData['code'], 'cache'=>$code], '手机验证码错误', 2);
            return reTmJsonObj(500, '验证失败', []);
        }
        $memberInfo = $this->memberModel->getMemberInfo(['mobile'=>$inputData['mobile']]);
        if($memberInfo){
            return reTmJsonObj(500,'此手机号已被注册',[]);
        }
        $field = 'member_id,member_code, member_name, member_nickname, member_real_name,site_code,email,deleted,sex_edit_time,birthday_edit_time,mobile_edit_time,wb_edit_time,wx_edit_time,qq_edit_time,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,password,receive_notice,wifi_show_image,list_auto_play,login_type,member_sn,register_source';
        $addData = [
            'member_name' => substr($inputData['mobile'],0,3)."****".substr($inputData['mobile'],7,4),
            'member_code' => createCode(),
            'create_time' => time(),
            'password' => md5(md5(rand(100000,999999))),
            'site_code' => '00000000000000000000000000000000',
            'mobile' => $inputData['mobile'],
            'head_pic' => '/uploads/default/head.jpg',
            'member_sn'=>$this->createMemberSn(),
            'status' => 2, //未激活状态
            'register_source'=> 'web',//注册方式
        ];
        $add = $this->memberModel->addMember($addData);
        if(!$add){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
            Db::rollback();
            return reTmJsonObj(500, '新增会员失败', []);
        }
        $addData['member_id'] = $add;
        unset($addData['password']);
        $memberInfo = $this->memberModel->getMemberInfo(['member_id' => $addData['member_id']], $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
            return reTmJsonObj(500, '获取会员数据失败', []);
        }
        Logservice::writeArray(['memberInfo'=>$addData], '短信注册会员数据');
        Db::commit();
        return reEncryptJson(200, '注册成功',$addData);
    }

    /**
     * 检查用户的激活状态
     */
    public function checkMemberStatus(){
        //判断请求方式以及请求参数
      //  $inputData = Request::get();
        $inputData = getEncryptGetData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['mobile'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $field = 'member_id,mobile,status';
        $memberInfo = $this->memberModel->getMemberInfo(['mobile' => $inputData['mobile']],$field);
        if($memberInfo === false){
            return reTmJsonObj(500, '查询会员数据失败', []);
        }
        $memberInfo['is_activation'] = $memberInfo['status'] === 0?1:0; //1表示激活 0表示未激活
        return reEncryptJson(200, '获取成功',$memberInfo);
    }

}