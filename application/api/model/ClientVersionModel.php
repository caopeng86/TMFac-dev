<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/14
 * Time: 15:16
 */

namespace app\api\model;

use think\Db;
use think\Model;

class ClientVersionModel extends CommonModel
{
    /**
     * 新增版本号
     * @param $data
     * @return int|string
     */
    public function addVersion($data){
        $data['add_time'] = time();
        $re = Db::table($this->client_version_db)->insertGetId($data);
        return $re;
    }

    /**
     * 删除版本
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteVersion($condition){
        $re = Db::table($this->client_version_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 统计版本总数
     * @param $condition
     * @return int|string
     */
    public function countVersion($condition){
        $re = Db::table($this->client_version_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取版本列表
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function versionList($condition, $field = false, $limit='', $order=''){
        $re = Db::table($this->client_version_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 保存客户端版本号
     * @param $condition
     * @param $data
     */
    public function versionSave($condition,$data){
        $re = Db::table($this->client_version_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 获取单条版本号信息
     * @param $condition
     * @param bool $field
     */
    public function getVersionInfo($condition,$field = false,$order = false){
        $re = Db::table($this->client_version_db)->where($condition)->order($order)->field($field)->find();
        return $re;
    }

}