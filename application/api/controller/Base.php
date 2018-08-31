<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/13
 * Time: 12:17
 */

namespace app\api\controller;



use app\api\model\UserModel;
use think\Controller;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_checkToken();
    }

    /**
     * 登录令牌验证
     * @return bool
     */
    private function _checkToken(){
        $token = Request::header('token');
        $url = Request::module().'\\'.Request::controller().'\\'.Request::action();
        $url = strtolower($url);
        //跳过验证的方法
        $pass = [
            'api\jpush\pushinfo',
            'api\jpush\getres'
        ];
        if(in_array($url, $pass)){
            return true;
        }
        //判断是否传入token
        if(!$token){
            die('{"code":500,"msg":"token未传入","data":""}');
        }

        //判断缓存中是否有存用户信息
        if(!Cache::get($token)['access_key']){
            //没有则查询数据库是否有这个token的用户存在
            $userModel = new UserModel();
            $condition = ['access_key' => $token];
            $condition['status'] = 0;
            $condition['deleted'] = 0;
            $field = 'user_id, user_code, user_name, access_key_create_time, access_key';
            $userInfo = $userModel->getUserInfo($condition, $field);
            //没有这个用户说明token错误
            if(!$userInfo){
                die('{"code":501,"msg":"token错误","data":""}');

            }
            //判断token是否已经超时
            $time = time() - $userInfo['access_key_create_time'];
            if($time > Config::get('token_time')){
                die('{"code":501,"msg":"token超时","data":""}');
            }
            //保存根据token查询到的用户数据
            Cache::set($userInfo['access_key'], $userInfo, (Config::get('token_time') - $time));
        }else{
            //有缓存则直接从缓存中判断token是否正确
            if(Cache::get($token)['access_key'] !== $token){
                die('{"code":501,"msg":"token错误","data":""}');
            }
        }
        return true;
    }
}