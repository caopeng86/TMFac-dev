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
     * 修改用户积分
     */
    public function editPointNew($member_id,$point,$remarks,$from_component,$article_id = 0,$extend = ""){
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
        $result = $this->addPointLogNew($member_id,$point,$remarks,$point + $now_point,$from_component,$article_id,$extend);
        if($result === false){
            Db::rollback();
            return false;
        }
        Db::commit();
        return true;
    }

    /*
 *  批量修改积分,
     * member_id是一个以,分隔的字符串。
     * 如果修改积分后积分小于0，默认设置为0
 * */
    public function editPoints($condition,$point=0){

        $member_db_re = Db::table($this->member_db)->field("member_id,point")->where($condition)->select();
        if(false === $member_db_re)
            return false;
        Db::startTrans();
        $result = Db::table($this->member_db)->where($condition)->setInc('point',$point);
        $result1 = Db::table($this->member_db)->where("point<0")->update(["point"=>0]);
        if($result === false || $result1 === false){
            Db::rollback();
            return false;
        }
        $addPointLogArr = [];
        foreach ($member_db_re as $ke=>$val){
            $addPointLog = [
                "member_id"=>$val['member_id'],
                'add_time'=>time(),
                'from_component'=>"admin",
                "remark"=>"管理员调整积分",
                "change_point"=>$point
            ];
            $addPointLog['now_point'] = $point+$val['point']<0?0:$point+$val['point'];
            $addPointLogArr[] = $addPointLog;
        }
        $result = Db::table($this->member_point_log)->insertAll($addPointLogArr);
        if($result === false ){
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
    public function addPointLog($member_id,$change_point,$remarks,$now_point,$from_component){
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
     * 添加用户积分日志
     */
    public function addPointLogNew($member_id,$change_point,$remarks,$now_point,$from_component,$article_id = 0,$extend = ""){
        $data = [
            'change_point'=>$change_point,
            'now_point'=>$now_point,
            'remark'=>$remarks,
            'member_id'=>$member_id,
            'add_time'=>time(),
            'from_component'=>$from_component,
            'article_id'=>$article_id,
            'extend'=>$extend
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