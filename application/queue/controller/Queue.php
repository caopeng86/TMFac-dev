<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\queue\controller;


use app\queue\model\QueueModel;
use app\extend\controller\Logservice;

use think\Db;
use think\Controller;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Queue extends Controller
{
	protected $queueModel;
    public function __construct()
    {
        parent::__construct();
        $this->queueModel = new QueueModel();
    }

	public function getQueueList()
	{
		// $inputData = Request::post();
        $inputData = getEncryptData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
		$params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

		//获取搜索条件
        $condition = $this->_getCondition($inputData);
		empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
		$count = $this->queueModel->getCount($condition['where'],$condition['orwhere']);
		$totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
		//获取队列列表数据
        $queueList = $this->queueModel->getQueueList($condition['where'], $limit,$condition['orwhere']);
		if($queueList === false){
            Logservice::writeArray(['sql'=>$this->queueModel->getLastSql()], '获取队列列表失败', 2);
            return reTmJsonObj(500, '获取队列列表失败', []);
        }

		$return = [
            'list' => $queueList,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reTmJsonObj(200, '获取会员列表成功', $return);
	}

	public function addQueue()
	{
		// $inputData = Request::post();
        $inputData = getEncryptData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
		$params = ['task_name','task_queue','execute_type','duty','mobiles','emails','req_url'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
		if (!preg_match("/^(http|https|ftp)/", $inputData['req_url'])) {
			return reTmJsonObj(500, 'req_url错误');
		}

		$condition = [["task_queue","=",$inputData['task_queue']]];
		$info = $this->queueModel->getQueueInfo($condition);
		if(!empty($info)){
			return reTmJsonObj(500, '任务已存在');
		}
		$addData = [];
		$addData['task_name'] = $inputData['task_name'];
		$addData['task_queue'] = $inputData['task_queue'];
		$addData['execute_type'] = $inputData['execute_type'];
		$addData['duty'] = $inputData['duty'];
		$addData['mobiles'] = $inputData['mobiles'];
		$addData['emails'] = $inputData['emails'];
		$addData['req_url'] = $inputData['req_url'];
		$addData['task_vhost'] = "/";
		$addData['create_time'] = $addData['update_time'] = time();
		$addData['status'] = 1;
		$return = $this->queueModel->addQueue($addData);
		if($return === false){
			Logservice::writeArray(['sql'=>$this->queueModel->getLastSql()], '添加任务失败', 2);
			return reTmJsonObj(500, '添加任务失败');
		}

        return reTmJsonObj(200, '添加任务成功', $return);
	}

	public function updateQueue()
	{
		// $inputData = Request::post();
        $inputData = getEncryptData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
		$params = ['id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
		if (isset($inputData['req_url']) && !preg_match("/^(http|https|ftp)/", $inputData['req_url'])) {
			return reTmJsonObj(500, 'req_url错误');
		}

		$condition = [['id','=',$inputData['id']]];
		unset($inputData['id']);
		$inputData['update_time'] = time();
		$return = $this->queueModel->updateQueue($condition,$inputData);
		if($return === false){
			Logservice::writeArray(['sql'=>$this->queueModel->getLastSql()], '修改任务失败', 2);
			return reTmJsonObj(500, '修改任务失败', $return);
		}

        return reTmJsonObj(200, '修改任务成功', $return);
	}

	/**
     * 获取搜索条件
     * @param $inputData
     * @return array
     */
    private function _getCondition($inputData){
        //昵称/姓名/手机号码/邮箱/注册时间/站点
        $condition = ['where'=>[],'orwhere'=>[]];

		if(isset($inputData['search_key'])){
			$condition['orwhere'][] = ["task_queue","like","%".$inputData['search_key']."%"];
			$condition['orwhere'][] = ["task_name","like","%".$inputData['search_key']."%"];
			$condition['orwhere'][] = ["duty","like","%".$inputData['search_key']."%"];
		}
		if(isset($inputData['status'])){
			$condition['where'][] = ["status",'=',$inputData['status']];
		}
        return $condition;
    }

}