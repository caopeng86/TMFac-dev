<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 14:45
 */

namespace app\api\model;


use think\Db;
use think\facade\Config;
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
}