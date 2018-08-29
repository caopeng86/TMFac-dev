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
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
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
        $arr = [1,2,3,4,5];
        $template_code = 'SMS_125026751';
        if(in_array($inputData['state'], $arr)){
            $template_code = 'SMS_125026751';
        }

        //配置发送短信的配置
        $config = [
            'phone_numbers' => $inputData['mobile'],
            'template_code' => $template_code,
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
            $mobile = $this->memberModel->getMemberInfo(['mobile' => $inputData['mobile'], 'site_code' => $inputData['site_code']], 'mobile');
            if($mobile === false){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员手机号失败', 2);
                return reJson(500, '修改失败', []);
            }
            if(!empty($mobile)){
                return reJson(500, '手机号已存在', []);
            }
        }

        $condition['member_code'] = $inputData['member_code'];
        $condition['site_code'] = $inputData['site_code'];
        if(isset($inputData['password'])){
            $inputData['password'] = md5(md5($inputData['password']));
        }
        $re = $this->memberModel->updateMember($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员数据失败', 2);
            return reJson(500, '修改失败', []);
        }

        Logservice::writeArray(['inputData'=>$inputData], '修改会员信息');
        return reJson(200, '修改成功', []);
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
        $mobile = $this->memberModel->getMemberInfo(['mobile' => $inputData['mobile'], 'site_code' => $inputData['site_code']], 'mobile');
        if($mobile === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员手机号失败', 2);
            return reJson(500, '修改失败', []);
        }
        if(!empty($mobile)){
            return reJson(500, '手机号已存在', []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $re = $this->memberModel->updateMember($condition,['mobile'=>$inputData['mobile']]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '手机号更换失败', 2);
            return reJson(500, '更换失败', []);
        }

        Logservice::writeArray(['inputData'=>$inputData], '修改会员信息');
        return reJson(200, '更换成功', []);
    }
}