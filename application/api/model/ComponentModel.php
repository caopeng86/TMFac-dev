<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 18:48
 */

namespace app\api\model;


use think\Db;
use think\Model;

class ComponentModel extends CommonModel
{
    /**
     * 统计所有应用条数
     * @param $condition
     * @return int|string
     */
    public function countComponent($condition){
        $re = Db::table($this->component_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取应用列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getComponentList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->component_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 查找一条应用数据
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getComponentInfo($condition, $field=''){
        $re = Db::table($this->component_db)->field($field)->where($condition)->find();
        return $re;
    }
}