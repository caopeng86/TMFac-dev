<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\api\controller;


use app\api\model\MemberModel;
use app\api\model\SiteModel;
use app\extend\controller\Logservice;
use think\facade\Request;

class Member extends Base
{
    protected $memberModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
    }

    /**
     * 获取搜索条件
     * @param $inputData
     * @return array
     */
    private function _getCondition($inputData){
        //昵称/姓名/手机号码/邮箱/注册时间/站点
        $condition = [];
        $condition['deleted'] = 0;
        empty($inputData['site_code']) ? : $condition['site_code'] = $inputData['site_code'];
        empty($inputData['status']) ? : $condition['status'] = $inputData['status'];
        if(!empty($inputData['input'])){
            $condition['member_name|member_nickname|member_real_name|email|mobile'] = ['like', '%'.$inputData['input'].'%'];
        }
        if(!empty($inputData['start_time']) && !empty($inputData['end_time'])){
            $inputData['start_time'] = strtotime($inputData['start_time']);
            $inputData['end_time'] = strtotime($inputData['end_time']);
            $condition['create_time'] = ['between', [$inputData['start_time'], $inputData['end_time']]];
        }
        return $condition;
    }

    /**
     * 获取会员列表
     */
    public function getMemberList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        //获取搜索条件
        $condition = $this->_getCondition($inputData);
        $field = 'member_code, member_name, member_nickname, member_real_name, site_name, email, mobile, create_time';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->memberModel->getCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'member_id desc';
        //获取会员列表数据
        $memberList = $this->memberModel->getMemberList($condition, $field, $limit, $order);
        if($memberList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员列表失败', 2);
            return reJson(500, '获取会员列表失败', []);
        }

        //时间转时间戳
        foreach ($memberList as $k => $v){
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $memberList[$k] = $v;
        }

        $return = [
            'list' => $memberList,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reJson(200, '获取会员列表成功', $return);
    }

    /**
     * 获取会员信息
     */
    public function getMemberInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $field = 'member_id,member_code, member_name, member_nickname, member_real_name, site_code, email, mobile, head_pic, create_time, status, wx, qq, zfb, wb,receive_notice,wifi_show_image,list_auto_play';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情失败', 2);
            return reJson(500, '获取会员信息失败', []);
        }

        $siteModel = new SiteModel();
        $siteName = $siteModel->getSiteInfo(['site_code' => $memberInfo['site_code']], 'site_name')['site_name'];
        $memberInfo['site_name'] = $siteName;

        $memberInfo['create_time'] = date('Y-m-d H:i:s', $memberInfo['create_time']);

        return reJson(200, '获取会员信息成功', $memberInfo);
    }
}