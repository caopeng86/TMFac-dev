<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/12
 * Time: 10:06
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberhistoryModel extends CommonModel
{
    /**
     * 新增历史记录
     * @param $data
     * @return int|string
     */
    public function addHistory($data){
        $re = Db::table($this->member_history_db)->insert($data);
        return $re;
    }

    /**
     * 修改历史记录
     * @param $condition
     * @param $data
     * @return int|string
     */
    public function updateHistory($condition, $data){
        $re = Db::table($this->member_history_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 取消历史记录
     * @param $condition
     * @return int
     */
    public function deleteHistory($condition){
        $re = Db::table($this->member_history_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 统计历史记录总条数
     * @param $condition
     * @return int|string
     */
    public function countHistory($condition){
        $re = Db::table($this->member_history_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取历史记录列表
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function historyList($condition, $field, $limit='', $order=''){
        $re =Db::table($this->member_history_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }
}