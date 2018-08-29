<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/16
 * Time: 17:16
 */
namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberOpinionModel extends CommonModel
{
    /**
     * 获取留言数量
     */
    public function getCount($condition){
        $re = Db::table($this->member_opinion_db)->where($condition)->count('id');
        return $re;
    }

    /**
     * 获取留言列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getOpinionList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->member_opinion_db)->join($this->member_db, $this->member_db.'.member_id = '.$this->member_opinion_db.'.member_id')
            ->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     *更新留言
     */
    public function updateOpinion($condition,$opinion){
        $re = Db::table($this->member_opinion_db)->where($condition)->update($opinion);
        return $re;
    }

    /**
     * 添加留言
     */
    public function addOpinion($opinion){
        return Db::table($this->member_opinion_db)->insert($opinion);
    }

}