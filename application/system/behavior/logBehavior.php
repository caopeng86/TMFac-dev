<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/2
 * Time: 14:38
 */

namespace app\system\behavior;


use app\extend\controller\Logservice;
use app\api\model\UserModel;
use think\facade\Cache;
use think\facade\Request;

class logBehavior
{
    /**
     * 行为控制器:记录用户日志
     * @param $params
     * @return bool
     */
    public function run($params)
    {
//        Logservice::writeArray(['inputData'=>Request::param()], '');
        //不记录行为的方法
        $pass = [
            'system\login\getverify',
            'system\login\userlogin',
            'system\login\geticonlist',
            'system\login\rechecklicenses',
            'system\upload\imgupload',
            'system\site\getsitelist',
            'system\login\getenv',
        ];
        // 记录登录用户的日志
        $url = Request::module().'\\'.Request::controller().'\\'.Request::action();
        $url = strtolower($url);
        if(in_array($url, $pass)){
            return true;
        }
        $passController = [
            'system\system'
        ];
        $Controller = strtolower(Request::module().'\\'.Request::controller());
        if(in_array($Controller, $passController)){
            return true;
        }
        //获取需要记录的日志数据
        $token = Request::header('token');
        $userInfo = Cache::get($token);
        $ip = Request::ip();
        $logType = Request::method();
//        $input = Request::param();
//        $logMsg = json_encode(['url' => $url, 'input' => $input]);
        if(!empty($userInfo['user_code'])){
            //拼接数据
            $log = [
                'user_code' => $userInfo['user_code'],
                'ip' => $ip,
                'log_type' => $logType,
                'log_message' => $url,
                'log_time' => time()
            ];

            //记录日志
            $logObj = new UserModel();
            $re = $logObj->addUserLog($log);
            if(!$re){
                die('{"code":500,"msg":"记录用户日志错误","data":""}');
            }
        }
        return true;
    }
}