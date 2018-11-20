<?php
/**
 * Created by PhpStorm.
 * User: wcc
 * Date: 2018/10/16
 * Time: 11:12
 */
namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberBehaviorLogModel extends CommonModel
{

    /**
     * 获取单条
     * @param $member_id
     * @return mixed
     */
    public function getMemberPoint($id = 0){
        return Db::table($this->user_behavior_log)->where(['id'=>$id])->find();
    }
    /**
     * 添加
     */
    public function addPointLog($member_id = 0,$content = "",$remarks = ""){
        $data = [
            'content'=>$content,
            'remarks'=>$remarks,
            'member_id'=>$member_id,
            'create_time'=>time(),
        ];
        return Db::table($this->user_behavior_log)->insertGetId($data);
    }

    /**
     * 获取列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getPointLogList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->user_behavior_log)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 获取用户积分变动情况列表数
     */
    public function getPointLogCount($condition){
        $re = Db::table($this->user_behavior_log)->where($condition)->count('id');
        return $re;
    }



}