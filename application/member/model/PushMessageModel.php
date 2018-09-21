<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/20
 * Time: 18:10
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class PushMessageModel extends CommonModel
{
    /**
     * 获取推送信息
     */
    public function getCount($condition){
        $re = Db::table($this->push_message_db)->where($condition)->count('id');
        return $re;
    }

    /**
     * 获取推送信息列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->push_message_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     *更新推送信息
     */
    public function updateInfo($condition,$opinion){
        $re = Db::table($this->push_message_db)->where($condition)->update($opinion);
        return $re;
    }

    /**
     * 添加推送信息
     */
    public function addInfo($opinion){
        $opinion['add_time'] = time();
        return Db::table($this->push_message_db)->insertGetId($opinion);
    }

    /**
     * 获取单条信息
     */
    public function getInfo($condition){
        return Db::table($this->push_message_db)->where($condition)->find();
    }
}