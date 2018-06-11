<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/12
 * Time: 14:19
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MembercommentModel extends CommonModel
{
    /**
     * 新增评论
     * @param $data
     * @return int|string
     */
    public function addComment($data){
        $re = Db::table($this->member_comment_db)->insert($data);
        return $re;
    }

    /**
     * 删除评论
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteComment($condition){
        $re = Db::table($this->member_comment_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 统计评论总条数
     * @param $condition
     * @return int|string
     */
    public function countComment($condition){
        $re = Db::table($this->member_comment_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取评论列表
     * @param $condition
     * @param $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function commentList($condition, $field, $limit='', $order=''){
        $re = Db::table($this->member_comment_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }
}