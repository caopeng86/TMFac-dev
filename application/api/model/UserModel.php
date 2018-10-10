<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 14:45
 */

namespace app\api\model;


use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;
use think\Model;

class UserModel extends CommonModel
{
    /**
     * 统计用户表中数据数量
     * @param $condition
     * @return int|string
     */
    public function getUserCount($condition){
        $re = Db::table($this->user_db)->where($condition)->count('user_id');
        return $re;
    }

    /**
     * 获取用户列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getUserList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->user_db)->where($condition)->field($field)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 获取用户详情
     * @param $condition
     * @param $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getUserInfo($condition, $field){
        $re = Db::table($this->user_db)->field($field)->where($condition)->find();
        return $re;
    }

    /**
     * 修改用户数据
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateUserInfo($condition, $data){
        $re = Db::table($this->user_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 新增用户
     * @param $data
     * @return int|string
     */
    public function addUser($data){
        $re = Db::table($this->user_db)->insert($data);
        return $re;
    }

    /**
     * 记录用户日志
     * @param $data
     * @return int|string
     */
    public function addUserLog($data){
        $re = Db::table($this->user_log_db)->insert($data);
        return $re;
    }

    /**
     * 检查是否登录
     * @param $pass array ['system\site\getsitelist'];
     */
    public function checkIsLogin($pass = []){
        $token = Request::header('token');
        $url = Request::module().'\\'.Request::controller().'\\'.Request::action();
        $url = strtolower($url);
        //跳过验证的方法
        if(in_array($url, $pass)){
            return ['code'=>200];
        }
        //判断是否传入token
        if(!$token){
            return ["code"=>500,"msg"=>"token未传入"];
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
            if($userInfo === false){
                return ["code"=>500,"msg"=>"获取失败"];
            }
            //没有这个用户说明token错误
            if(!$userInfo){
                return ["code"=>500,"msg"=>"token错误"];
            }
            //判断token是否已经超时
            $time = time() - $userInfo['access_key_create_time'];
            if($time > Config::get('token_time')){
                return ["code"=>501,"msg"=>"token超时"];
            }
            //保存根据token查询到的用户数据
            Cache::set($userInfo['access_key'], $userInfo, (Config::get('token_time') - $time));
        }else{
            //有缓存则直接从缓存中判断token是否正确
            if(Cache::get($token)['access_key'] !== $token){
                return ["code"=>501,"msg"=>"token错误"];
            }
        }
        return ['code'=>200];
    }
}