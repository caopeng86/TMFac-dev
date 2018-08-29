<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/23
 * Time: 17:24
 */
namespace app\member\model;

use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberfootprintModel extends CommonModel
{
    /**
     * 新增浏览记录
     * @param $data
     * @return int|string
     */
    public function addFootprint($data){
        $re = Db::table($this->member_footprint_db)->insertGetId($data);
        return $re;
    }

    /**
     * 取消浏览记录
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteFootprint($condition){
//        $re = Db::table($this->member_footprint_db)->where($condition)->delete();
//        return $re;
        return Db::table($this->member_footprint_db)->where($condition)->update(['status'=>0]);
    }

    /**
     * 统计浏览记录总条数
     * @param $condition
     * @return int|string
     */
    public function countFootprint($condition){
        $re = Db::table($this->member_footprint_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取浏览记录
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function footprintList($condition, $field, $limit='', $order=''){
        $re = Db::table($this->member_footprint_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 获取单个浏览记录
     * @param $condition
     * @param $field
     * @return array|null|\PDOStatement|string|Model
     */
    public function footprintFind($condition, $field){
        $re = Db::table($this->member_footprint_db)->field($field)->where($condition)->find();
        return $re;
    }

    /**
     * 时间统计列表
     */
    public function footprintTimeList($condition){
        return Db::table($this->member_footprint_db)->field('DATE_FORMAT(FROM_UNIXTIME(create_time),\'%Y-%m-%d\') days,count(footprint_id) num')->where($condition)->group('days')->order('days desc')->select();
    }
}