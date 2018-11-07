<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 14:56
 */

namespace app\api\model;

use think\Db;
use think\Model;

class AdvModel extends CommonModel
{
    /**
     * 新增广告
     * @param $data
     * @return int|string
     */
    public function addAdv($data){
        $re = Db::table($this->adv_db)->insertGetId($data);
        return $re;
    }

    /**
     * 删除广告
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteAdv($condition){
//        $re = Db::table($this->adv_db)->where($condition)->delete();
        $re = Db::table($this->adv_db)->where($condition)->update(['status'=>0]);
        return $re;
    }

    /**
     * 统计广告总条数
     * @param $condition
     * @return int|string
     */
    public function countAdv($condition){
        $re = Db::table($this->adv_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取广告列表
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function advList($condition, $field = false, $limit='', $order=''){
        $re = Db::table($this->adv_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 保存广告信息
     * @param $condition
     * @param $data
     */
    public function advSave($condition,$data){
        $re = Db::table($this->adv_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 获取单条广告信息
     * @param $condition
     * @param bool $field
     */
    public function getAdvInfo($condition,$field = false){
       $re = Db::table($this->adv_db)->where($condition)->field($field)->find();
       return $re;
    }

}