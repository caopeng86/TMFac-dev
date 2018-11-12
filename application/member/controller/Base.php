<?php
/**
 * Created by PhpStorm.
 * Member: Administrator
 * Date: 2017/12/13
 * Time: 12:17
 */

namespace app\member\controller;



use app\member\model\MemberModel;
use think\Controller;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Base extends Controller
{
    public $memberInfo;

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
            'member\member\addmember',
            'member\member\sendmsg',
            'member\member\checkcode',
            'member\article\getreliefarticle',
            'member\article\getprivacyarticle',
            'member\article\getaboutusarticle',
            'member\membermessagepush\messagelist',//系统消息
            'member\member\updatepass',
            'member\adv\getadvlist',
        ];
        if(in_array($url, $pass)){
            return true;
        }

        //判断是否传入token
        if(!$token){
            die('{"code":500,"msg":"token未传入","data":""}');
        }

        //判断缓存中是否有存用户信息
        if(!Cache::get($token)['access_key'] || !isset(Cache::get($token)['status'])){
            //没有则查询数据库是否有这个token的用户存在
            $memberModel = new MemberModel();
            $condition = ['access_key' => $token];
         //   $condition['status'] = 0;
            $condition['deleted'] = 0;
            $field = 'member_id, member_code, member_name, access_key_create_time, access_key,status,close_start_time,close_end_time';
            $memberInfo = $memberModel->getMemberInfo($condition, $field);
            //没有这个用户说明token错误
            if(!$memberInfo){
                die('{"code":501,"msg":"token错误,用户不存在","data":""}');
            }
            if(1 == $memberInfo['status']){
                die('{"code":502,"msg":"用户已经拉黑","data":""}');
            }
            if(time()>$memberInfo['close_start_time'] && time()<$memberInfo['close_end_time']){
                die('{"code":503,"msg":"已经被封号，封号时间'.date('Y-m-d H:i:s', $memberInfo['close_start_time']).'到'.date('Y-m-d H:i:s', $memberInfo['close_end_time']).'","data":""}');
            }
            //判断token是否已经超时
            $time = time() - $memberInfo['access_key_create_time'];
            if($time > Config::get('token_time')){
                die('{"code":501,"msg":"token超时","data":""}');
            }
            $this->memberInfo = $memberInfo;
            //保存根据token查询到的用户数据
            Cache::set($memberInfo['access_key'], $memberInfo, (Config::get('token_time')- $time));
        }else{
            //有缓存则直接从缓存中判断token是否正确
            if(Cache::get($token)['access_key'] !== $token){
                die('{"code":501,"msg":"token错误","data":""}');
            }
            if(1 == Cache::get($token)['status']){
                die('{"code":502,"msg":"很遗憾，该账户已被列入企业黑名单","data":""}');
            }
            if(time()>Cache::get($token)['close_start_time'] && time()<Cache::get($token)['close_end_time']){
                die('{"code":503,"msg":"已经被封号，封号时间"'.date('Y-m-d H:i:s', Cache::get($token)['close_start_time']).'到'.date('Y-m-d H:i:s', Cache::get($token)['close_end_time']).',"data":""}');
            }
            $this->memberInfo = Cache::get($token);
        }
        return true;
    }
}