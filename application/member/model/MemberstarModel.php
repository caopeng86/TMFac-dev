<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11
 * Time: 15:54
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberstarModel extends CommonModel
{
    /**
     * 新增收藏
     * @param $data
     * @return int|string
     */
    public function addStar($data){
        $re = Db::table($this->member_star_db)->insert($data);
        return $re;
    }

    /**
     * 取消收藏
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteStar($condition){
        $re = Db::table($this->member_star_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 统计收藏总条数
     * @param $condition
     * @return int|string
     */
    public function countStar($condition){
        $re = Db::table($this->member_star_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取收藏列表
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function starList($condition, $field, $limit='', $order=''){
        $re = Db::table($this->member_star_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }
}