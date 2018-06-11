<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 9:50
 */

namespace app\api\model;



use think\Db;
use think\Model;

class PushModel extends CommonModel
{
    /**
     * 获取推送列表数据
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getPushList($condition, $field, $limit='', $order=''){
        $re = Db::table($this->push_db)->where($condition)->field($field)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 统计推送总条数
     * @param $condition
     * @return int|string
     * @throws
     */
    public function countPush($condition){
        $re = Db::table($this->push_db)->where($condition)->count('push_id');
        return $re;
    }

    /**
     * 新增推送数据
     * @param $data
     * @return int|string
     * @throws
     */
    public function addPush($data){
        $re = Db::table($this->push_db)->insert($data);
        return $re;
    }

    /**
     * 删除推送数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deletePush($condition){
        $re = Db::table($this->push_db)->where($condition)->delete();
        return $re;
    }
}