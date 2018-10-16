<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/10/16
 * Time: 11:12
 */
namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberpointModel extends CommonModel
{
    /**
     * 修改用户积分
     */
    public function editPoint($member_id,$point,$remarks,$from_component){
        $now_point = $this->getMemberPoint($member_id);
        if($point + $now_point<0){
            return false;
        }
         Db::startTrans();
        $result = Db::table($this->member_db)->where(['member_id'=>$member_id])->setInc('point',$point);
        if($result === false){
            Db::rollback();
            return false;
        }
        $result = $this->addPointLog($member_id,$point,$remarks,$point + $now_point,$from_component);
        if($result === false){
            Db::rollback();
            return false;
        }
        Db::commit();
        return true;
    }

    /**
     * 获取当前用户积分
     * @param $member_id
     * @return mixed
     */
    public function getMemberPoint($member_id){
        return Db::table($this->member_db)->where(['member_id'=>$member_id])->value('point');
    }
    /**
     * 添加用户积分日志
     */
    private function addPointLog($member_id,$change_point,$remarks,$now_point,$from_component){
        $data = [
            'change_point'=>$change_point,
            'now_point'=>$now_point,
            'remark'=>$remarks,
            'member_id'=>$member_id,
            'add_time'=>time(),
            'from_component'=>$from_component
        ];
        return Db::table($this->member_point_log)->insertGetId($data);
    }

    /**
     * 获取用户积分变动情况列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getPointLogList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->member_point_log)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 获取用户积分变动情况列表数
     */
    public function getPointLogCount($condition){
        $re = Db::table($this->member_point_log)->where($condition)->count('id');
        return $re;
    }
}