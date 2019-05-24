<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\queue\model;


use think\Db;
use think\Model;
use think\facade\Cache;
use think\facade\Config;

class QueueModel extends Model
{
	private $queue_db = "cmqueue_task_info";
	private $filed = 'id,task_name,task_queue,task_vhost,task_param,execute_type,duty,alarm_type,mobiles,emails,req_url,rsp_param,create_time,update_time,status';

	public function getCount($condition,$orwhere = []){
        if(empty($orwhere)){
            $re = Db::table($this->queue_db)->where($condition)->count('id');
        }else{
            $re = Db::table($this->queue_db)->where($condition)->whereOr($orwhere)->count('id');
        }
        return $re;
    }

	public function getQueueList($condition,$limit,$orwhere = [])
	{
		if(empty($orwhere)){
			$re = Db::table($this->queue_db)->field($this->field)->where($condition)->limit($limit)->order("id desc")->select();
		}else{
			$re = Db::table($this->queue_db)->field($this->field)->where($condition)->whereOr($orwhere)->limit($limit)->order("id desc")->select();
		}

		return $re;
	}

	public function getQueueInfo($condition)
	{
		$re = Db::table($this->queue_db)->field($this->field)->where($condition)->find();

		return $re;
	}

	public function addQueue($data){
        $re = Db::table($this->queue_db)->insertGetId($data);
        return $re;
    }

	public function updateQueue($condition, $data){

		$updateData = [];

		$filed = explode(",",$this->filed);
		foreach($filed as $column){
			if(isset($data[$column])){
				$updateData[$column] = $data[$column];
			}
		}

		$re = Db::table($this->queue_db)->where($condition)->update($updateData);

        return $re;
    }
}