<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 15:40
 */

namespace app\member\controller;

use app\extend\controller\Alimsg;
use app\extend\controller\Logservice;
use app\member\model\MemberModel;
use app\member\model\MemberpointModel;
use app\member\model\MemberBehaviorLogModel;
use app\member\model\MemberThirdPartyModel;
use app\api\model\SystemArticleModel;
use app\api\model\ConfigModel;
use app\api\model\SiteModel;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Request;
require_once '../vendor/tmdatapull/GetData.php';


class Member extends Base
{
    protected $memberModel;
    protected $MemberpointModel;
    protected $MemberBehaviorLogModel;
    protected  $ConfigModel;
    protected  $SystemArticleModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
        $this->MemberpointModel = new MemberpointModel();
        $this->MemberBehaviorLogModel = new MemberBehaviorLogModel();
        $this->ConfigModel = new ConfigModel();
        $this->SystemArticleModel = new SystemArticleModel();
    }

    /**
     * 多图片数据处理,返回多图保存路径
     * @param $imgFiles
     * @param $mobile
     * @return array|bool
     */
    private function _handelImg($imgFiles, $mobile){
        //获取图片(base64编码)
        $img_path = [];
        foreach ($imgFiles as $value){
            $value = str_ireplace(' ','+',$value);//安卓中有空格需替换成加号
            $matches = [];
            //获取文件后缀
            $preg_match = preg_match('/^(data:\s*image\/(\w+);base64,)/', $value, $matches);
            $type = $matches[2];
            //获取图片base64编码数据
            $avatar = preg_replace('/data:.*;base64,/i', '', $value);
            if(!$avatar || !$preg_match){
                Logservice::writeArray(['imgData'=>$value], '获取文件后缀失败,获取图片base64编码数据失败', 2);
                return false;//身份证图片处理失败
            }
            //解码获取图片
            $byte = base64_decode($avatar);
            if($byte === false){
                Logservice::writeArray(['imgData'=>$byte], '解码获取图片失败', 2);
                return false;
            }

            //保存图片到本地服务器
            $img_name = time().rand(1000, 9999);
            $temppath = Env::get('root_path').'uploads/'.'member/'.$mobile;
            if(!file_exists($temppath)){
                mkdir($temppath, 0777, true);
            }
            $temppath = $temppath.'/'.$img_name.'.'.$type;
            $re = file_put_contents($temppath, $byte);
            if( !$re ){
                Logservice::writeArray(['temppath'=>$temppath], 'file_put_contents图片保存失败', 2);
                return false;
            }
            $path = '/uploads/member'.'/'.$mobile.'/'.$img_name.'.'.$type;
            //拼接图片保存路径数据为一个字符串
            $img_path[] = $path;
        }
        return $img_path;
    }

    /**
     * 上传图片到188服务器
     * @param $base64Img
     * @param $path
     * @return bool|mixed
     */
    private function _base64ImgUpload($base64Img, $path){
        $url = Config::get('base64_upload_url');
        $postData = [
            'base64_img' => $base64Img,
            'path' => $path
        ];

        $output = curlPost($url, $postData);
        if($output === false){
            return false;
        }

        //返回获得的数据
        return json_decode($output, true);
    }

    /**
     * 发送短息
     */
    public function sendMsg(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['mobile','state'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //验证手机号
        if(!preg_match("/^1[34578]\d{9}$/", $inputData['mobile'])){
            return reJson(500, '手机号错误', []);
        }

        //state 1: 登录 2: 密码找回 3: 修改密码 4: 原手机号验证 5: 新手机号验证
//        $arr = [1,2,3,4,5];
//        $template_code = 'SMS_125026751';
//        if(in_array($inputData['state'], $arr)){
//            $template_code = 'SMS_125026751';
//        }

        //配置发送短信的配置
        $config = [
            'phone_numbers' => $inputData['mobile'],
            'code' => rand(100000, 999999),
        ];

        //发送短信接收回执
        $msgObj = new Alimsg($config);
        $re = $msgObj::sendSms();
        if($re->Message != 'OK'){
            return reJson(500, '发送短信失败', $re);
        }
        //短信发送成功后将验证码保存到缓存中
        Cache::set(md5($inputData['mobile']), $config['code'], 10*60);
        return reJson(200, '发送短信成功', []);
    }

    /**
     * 验证手机短信验证码
     */
    public function checkCode(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['mobile','code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //缓存中取出验证码,验证手机验证码
        $code = Cache::get(md5($inputData['mobile']));
        if($code != $inputData['code']){
            Logservice::writeArray(['code'=>$inputData['code'], 'cache'=>$code], '手机验证码错误', 2);
            return reJson(500, '验证失败', []);
        }

        return reJson(200, '验证通过', []);
    }

    /**
     * 修改会员信息
     */
    public function updateMember(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code', 'site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        if(isset($inputData['mobile'])){
            //验证手机号是否存在
            //, 'site_code' => $inputData['site_code']
            $mobile = $this->memberModel->getMemberInfo(['mobile' => $inputData['mobile']], 'mobile');
            if($mobile === false){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员手机号失败', 2);
                return reJson(500, '修改失败', []);
            }
            if(!empty($mobile)){
                return reJson(500, '该手机号已绑定', []);
            }
        }

        $condition['member_code'] = $inputData['member_code'];
        $condition['site_code'] = $inputData['site_code'];
        $condition['status'] = 0;
        $getMemberInfo = $this->memberModel->getMemberInfo($condition);
        if (empty($getMemberInfo)){
            return reJson(500, '账号异常', []);
        }
        if(isset($inputData['password'])){
            $inputData['password'] = md5(md5($inputData['password']));
        }
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value');
        $ConfigList = array_column($ConfigList,null,'key');
        $re = $this->memberModel->updateMember($condition, $inputData);
        $re1 = true;
        if(isset($inputData['sex'])){
            $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"sex_edit_time","sex","完善性别");
        }
        if(!empty($inputData['birthday'])){
            $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"birthday_edit_time","birthday","完善生日");
        }
        if(!empty($inputData['mobile'])){
            $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"mobile_edit_time","mobile","绑定手机");
        }
        if(!empty($inputData['wb'])){
            $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"wb_edit_time","wb","绑定微博");
        }
        if(!empty($inputData['wx'])){
            $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"wx_edit_time","wx","绑定微信");
        }
        if(!empty($inputData['qq'])){
            $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"qq_edit_time","qq","绑定QQ");
        }
        if($re === false || $re1 === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员数据失败', 2);
            return reJson(500, '修改失败', []);
        }

        Logservice::writeArray(['inputData'=>$inputData], '修改会员信息');
        return reJson(200, '修改成功', []);
    }

    public function updatePoint($getMemberInfo,$ConfigList,$condition,$memberKey,$configKey,$remark = "修改用户信息"){
        if(empty($getMemberInfo[$memberKey])){
            $point = $getMemberInfo['point'];
            $pointchange = 0;
            if(!empty($ConfigList['perfect_information_switch']['value']) && (1 == $ConfigList['perfect_information_switch']['value'] || '1' == $ConfigList['perfect_information_switch']['value'])){
                $point = $getMemberInfo['point'] + $ConfigList[$configKey]['value'];
                $pointchange = $ConfigList[$configKey]['value'];
            }
            $updateMemberData = [
                $memberKey=>time(),
                "point"=>$point
            ];
            $re = $this->memberModel->updateMember($condition, $updateMemberData);
            if(false === $re){
                return false;
            }
            $re = $this->MemberpointModel->addPointLog($getMemberInfo['member_id'],$pointchange,$remark,$point,'center');
            if(false === $re){
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * 通过手机号修改密码
     */
    public function updatePass(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['mobile','password','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['mobile'] = $inputData['mobile'];
        $condition['site_code'] = $inputData['site_code'];
        $inputData['password'] = md5(md5($inputData['password']));
        $re = $this->memberModel->updateMember($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员密码失败', 2);
            return reJson(500, '修改失败', []);
        }
        Logservice::writeArray(['mobile'=>$inputData['mobile']], '修改密码');
        return reJson(200, '修改成功', []);
    }

    /**
     * 密码验证
     */
    public function checkPass(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code', 'password'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $password = $this->memberModel->getMemberInfo(['member_code' => $inputData['member_code']], 'password')['password'];
        if(!$password){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员数据失败', 2);
            return reJson(500, '会员数据获取失败', []);
        }
        if($password != md5(md5($inputData['password']))){
            return reJson(500, '密码错误', []);
        }

        return reJson(200, '验证通过', []);
    }

    /**
     * 修改用户头像
     */
    public function changeHeadPic(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['head_pic', 'member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //处理图片数据
        $path = $this->_handelImg([$inputData['head_pic']], $inputData['member_code']);
        if($path === false){
            return reJson(500, '图片数据处理失败', []);
        }
//        $path = $this->_base64ImgUpload($inputData['head_pic'], $inputData['mobile']);
//        if($path === false){
//            return reJson(500, '上传服务器失败', []);
//        }
        //保存路径到数据库
        $re = $this->memberModel->updateMember(['member_code' => $inputData['member_code']], ['head_pic' => $path[0]]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员头像失败', 2);
            return reJson(500, '保存失败', []);
        }

        Logservice::writeArray(['path'=>$path[0], 'member_code'=>$inputData['member_code']], '修改用户头像');
        return reJson(200, '成功', [$path[0]]);
    }

    /**
     * 修改手机号码
     */
    public function changeMobile(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['member_code','mobile', 'code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //缓存中取出验证码,验证手机验证码
        $code = Cache::get(md5($inputData['mobile']));
        if($code != $inputData['code']){
            Logservice::writeArray(['code'=>$inputData['code'], 'cache'=>$code], '手机验证码错误', 2);
            return reJson(500, '验证失败', []);
        }

        //验证手机号是否存在
        //, 'site_code' => $inputData['site_code']
        $mobile = $this->memberModel->getMemberInfo(['mobile' => $inputData['mobile']], 'mobile');
       if($mobile === false){     //1.没看懂为啥要加这段，注释了，矛盾啊 2.d0wop : 不矛盾啊！
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员手机号失败', 2);
            return reJson(500, '修改失败', []);
        }
        if(!empty($mobile)){
            return reJson(500, '该手机号已绑定', []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value');
        $ConfigList = array_column($ConfigList,null,'key');
        $getMemberInfo = $this->memberModel->getMemberInfo($condition);
        $re1 = $this->updatePoint($getMemberInfo,$ConfigList,$condition,"mobile_edit_time","mobile","绑定手机号");
        $re = $this->memberModel->updateMember($condition,['mobile'=>$inputData['mobile']]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '手机号更换失败', 2);
            return reJson(500, '更换失败', []);
        }

        Logservice::writeArray(['inputData'=>$inputData], '修改会员信息');
        return reJson(200, '更换成功', []);
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
        //  $field = 'member_id,member_code, member_name, member_nickname, member_real_name, site_code, email, mobile, head_pic, create_time, status, wx, qq, zfb, wb,receive_notice,wifi_show_image,list_auto_play';

        $field = 'member_id,member_code, member_name, member_nickname,member_sn,member_real_name,site_code,email,deleted,sex_edit_time,birthday_edit_time,mobile_edit_time,wb_edit_time,wx_edit_time,qq_edit_time,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,password,receive_notice,wifi_show_image,list_auto_play,login_type';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reJson(500, '获取会员信息失败', []);
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
        $memberInfo['other_info'] = $memberThirdPartyModel->getThirdPartyList(['member_id'=>$memberInfo['member_id']],'uid,nick_name,member_id,head_url,address,ip,type');
        $memberInfo['other_info'] = $memberThirdPartyModel->ArrayToType($memberInfo['other_info']);
        $ConfigList['sex'] = empty($memberInfo['sex_edit_time'])?$ConfigList['sex']:0;
        $ConfigList['birthday'] = empty($memberInfo['birthday_edit_time'])?$ConfigList['birthday']:0;
        $ConfigList['mobile'] = empty($memberInfo['mobile_edit_time'])?$ConfigList['mobile']:0;
        $ConfigList['wb'] = empty($memberInfo['wb_edit_time'])?$ConfigList['wb']:0;
        $ConfigList['wx'] = empty($memberInfo['wx_edit_time'])?$ConfigList['wx']:0;
        $ConfigList['qq'] = empty($memberInfo['qq_edit_time'])?$ConfigList['qq']:0;
        $memberInfo['point_config'] = $ConfigList;
        $memberInfo['is_first_login'] = false;
        return reJson(200, '获取会员信息成功', $memberInfo);
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
            return reJson(500, $msg, []);
        }
        $return = [];

        $condition['member_code'] = $inputData['member_code'];
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value,remarks');
        $memberInfo = $this->memberModel->getMemberInfo($condition);
        if($memberInfo === false || $ConfigList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reJson(500, '获取失败', []);
        }
        $ConfigList = array_column($ConfigList,null,'key');
        $loop = [];
        $sign_time_date = empty($memberInfo['sign_time'])?2:$this->diffBetweenTwoDays(date('Y-m-d', time()),date('Y-m-d', $memberInfo['sign_time']));
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
//a
        if(!empty($ConfigList['sign_cycle_first']['value']) && !empty($ConfigList['sign_cycle_first']['remarks'])
            && (int)$ConfigList['sign_cycle_first']['remarks']<8 && (int)$ConfigList['sign_cycle_first']['remarks']>0){
            $loop[$ConfigList['sign_cycle_first']['remarks']-1] = $loop[$ConfigList['sign_cycle_first']['remarks']-1] + $ConfigList['sign_cycle_first']['value'];
        }
        if(!empty($ConfigList['sign_cycle_two']['value']) && !empty($ConfigList['sign_cycle_two']['remarks'])
            && $ConfigList['sign_cycle_two']['remarks']<8 && $ConfigList['sign_cycle_two']['remarks']>0){
            $loop[$ConfigList['sign_cycle_two']['remarks']-1] = $loop[$ConfigList['sign_cycle_two']['remarks']-1] + $ConfigList['sign_cycle_two']['value'];
        }

        $loop_copy = $loop;
         if(!empty($ConfigList['sign_extra_first']['value']) && !empty($ConfigList['sign_extra_first']['remarks'])){
             if(($sign_num-$sign_num%7+1)<=$ConfigList['sign_extra_first']['remarks'] && ($sign_num+(7-$sign_num%7))>=$ConfigList['sign_extra_first']['remarks']){
                 $loop_copy[$ConfigList['sign_extra_first']['remarks']%7-1] = $loop_copy[$ConfigList['sign_extra_first']['remarks']%7-1] + $ConfigList['sign_extra_first']['value'];
             }
         }

         if(!empty($ConfigList['sign_extra_two']['value']) && !empty($ConfigList['sign_extra_two']['remarks'])){
             if(($sign_num-$sign_num%7+1)<=$ConfigList['sign_extra_two']['remarks'] && ($sign_num+(7-$sign_num%7))>=$ConfigList['sign_extra_two']['remarks']){
                 $loop_copy[$ConfigList['sign_extra_two']['remarks']%7-1] = $loop_copy[$ConfigList['sign_extra_two']['remarks']%7-1] + $ConfigList['sign_extra_two']['value'];
             }
         }
         $rule = "";
        $getArticleInfo = $this->SystemArticleModel->getArticleInfo(['id'=>4]);
         if($getArticleInfo)
             $rule = $getArticleInfo['content'];

        $return=[
            'loop'=>$loop,
            'sign_num'=>$sign_num,
            'today_is_sign'=>$today_is_sign,
            'today_num'=>$today_num,
            'sign_cycle_first_num'=>(int)$ConfigList['sign_cycle_first']['remarks'],
            'sign_cycle_two_num'=>(int)$ConfigList['sign_cycle_two']['remarks'],
            'sign_extra_two_num'=>(int)$ConfigList['sign_extra_two']['remarks'],
            'sign_extra_first_num'=>(int)$ConfigList['sign_extra_first']['remarks'],
            "sign"=>(int)$ConfigList['sign']['value'],
            "all_sign_point"=>$loop_copy[$today_num-1],
            'sign_cycle'=>null,
            'sign_extra'=>null,
            'cycle_point'=>0,
            'extra_point'=>0,
            'rule'=>$rule
        ];
         if($today_num == (int)$ConfigList['sign_cycle_first']['remarks']){
             $return['sign_cycle'] = [
                 'num'=>(int)$ConfigList['sign_cycle_first']['remarks'],
                 'point'=>(int)$ConfigList['sign_cycle_first']['value']
             ];
             $return['cycle_point']=(int)$ConfigList['sign_cycle_first']['value'];
         }
        if($today_num == (int)$ConfigList['sign_cycle_two']['remarks']){
            $return['sign_cycle'] = [
                'num'=>(int)$ConfigList['sign_cycle_two']['remarks'],
                'point'=>(int)$ConfigList['sign_cycle_two']['value']
            ];
            $return['cycle_point']=(int)$ConfigList['sign_cycle_two']['value'];
        }
        if($sign_num+1 == (int)$ConfigList['sign_extra_first']['remarks']){
            $return['sign_extra'] = [
                'num'=>(int)$ConfigList['sign_extra_first']['remarks'],
                'point'=>(int)$ConfigList['sign_extra_first']['value']
            ];
            $return['extra_point']=(int)$ConfigList['sign_extra_first']['value'];
        }
        if($sign_num+1 == (int)$ConfigList['sign_extra_two']['remarks']){
            $return['sign_extra'] = [
                'num'=>(int)$ConfigList['sign_extra_two']['remarks'],
                'point'=>(int)$ConfigList['sign_extra_two']['value']
            ];
            $return['extra_point']=(int)$ConfigList['sign_extra_two']['value'];
        }

        return reJson(200, '获取成功', $return);
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
            return reJson(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $Configcondition = ['type'=>'point'];
        $ConfigList = $this->ConfigModel->getConfigList($Configcondition,'key,value,remarks');
        $memberInfo = $this->memberModel->getMemberInfo($condition);
        if($memberInfo === false || $ConfigList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reJson(500, '获取失败', []);
        }
        $ConfigList = array_column($ConfigList,null,'key');

        if(!empty($ConfigList['sign_switch']['value']) && (1 == $ConfigList['sign_switch']['value'] || '1' == $ConfigList['sign_switch']['value'])){
            $loop = [];
            $sign_time_date = empty($memberInfo['sign_time'])?2:$this->diffBetweenTwoDays(date('Y-m-d', time()),date('Y-m-d', $memberInfo['sign_time']));
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
                $re1 = $this->updatePoint1($memberInfo,$loop[$today_num-1],['member_id'=>$memberInfo['member_id']],"sign_time","签到");
            }
            if(false === $re1){
                return reJson(500, '签到失败', []);
            }
            if($sign_time_date >=2){
                $this->memberModel->updateMember(['member_id'=>$memberInfo['member_id']], ['sign_num' => 1]);
            }
            return reJson(200, '签到成功', []);

        }
        return reJson(500, '签到失败', []);
    }

    /*获取积分规则*/

    public function getPointRule(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $Condition = ['id'=>4];
        $data = $this->SystemArticleModel->getArticleInfo($Condition);
        return reJson(200,'获取成功',$data);
    }

    public function updatePoint1($getMemberInfo,$putpoint,$condition,$memberKey,$remark = "修改用户信息"){
        $point = $getMemberInfo['point'] + $putpoint;
        $pointchange = $putpoint;
        $updateMemberData = [
            $memberKey=>time(),
            "point"=>$point,
            'sign_num'=>$getMemberInfo['sign_num']+1
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