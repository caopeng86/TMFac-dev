<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\MemberModel;
use app\api\model\SiteModel;
use app\member\model\MemberThirdPartyModel;
use app\member\model\MemberpointModel;
use app\member\model\MemberBehaviorLogModel;
use think\Db;
use think\facade\Request;
use think\Controller;

class Member extends Controller
{
    protected $memberModel;
    protected $MemberpointModel;
    protected $MemberBehaviorLogModel;
    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new MemberModel();
        $this->MemberpointModel = new MemberpointModel();
        $this->MemberBehaviorLogModel = new MemberBehaviorLogModel();
    }

    /**
     * 获取搜索条件
     * @param $inputData
     * @return array
     */
    private function _getCondition($inputData){
        //昵称/姓名/手机号码/邮箱/注册时间/站点
        $condition = [];
        array_push($condition,['deleted','=',0]);
        empty($inputData['site_code']) ? : array_push($condition,[$this->memberModel->getTableName().'.site_code','=',$inputData['site_code']]);
     //   empty($inputData['status']) ? : array_push($condition,['status','=',$inputData['status']]);
        empty($inputData['sex']) ? : array_push($condition,['sex','=',$inputData['sex']]);
        if(isset($inputData['status'])){
            if($inputData['status']!=10){
                array_push($condition,['status','=',$inputData['status']]);
            }else{
                array_push($condition,['close_start_time','<',time()]);
                array_push($condition,['close_end_time','>',time()]);
            }
        }
        if(!empty($inputData['input'])){
            array_push($condition,['member_name|member_nickname|member_real_name|email|mobile','like','%'.$inputData['input'].'%']);
        }
        if(!empty($inputData['account'])){
            array_push($condition,['mobile|wx|qq|wb','like','%'.$inputData['account'].'%']);
        }
        if(!empty($inputData['member_nickname'])){
            array_push($condition,['member_nickname','like','%'.$inputData['member_nickname'].'%']);
        }
        if(!empty($inputData['mobile'])){
            array_push($condition,['mobile','like','%'.$inputData['mobile'].'%']);
        }
        if(!empty($inputData['start_time']) && !empty($inputData['end_time'])){
            $inputData['start_time'] = strtotime($inputData['start_time']);
            $inputData['end_time'] = strtotime($inputData['end_time']);
            array_push($condition,['create_time','between',[$inputData['start_time'], $inputData['end_time']]]);
        }

        empty($inputData['member_id']) ? : array_push($condition,['member_id','in',explode(",",$inputData['member_id'])]);
        empty($inputData['not_member_id']) ? : array_push($condition,['member_id','not in',explode(",",$inputData['not_member_id'])]);
        if (!empty($inputData['channel_sources'])){
            array_push($condition,['channel_sources','=',$inputData['channel_sources']]);
        }
        return $condition;
    }

    /**
     * 修改会员积分，支持批量修改
     */
    public function updateMemberPoint(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ["point"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //获取搜索条件
        $condition = $this->_getCondition($inputData);
        $result = $this->MemberpointModel->editPoints($condition,$inputData['point']);
        if($result){
            return reEncryptJson(200,'更新成功',[]);
        }
        return reTmJsonObj(500,'更新失败',[]);
    }

    /*
     * 导出会员信息到exel
     * */
    public function exportMemberToExcel(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //获取搜索条件
        $condition = $this->_getCondition($inputData);
        $field = 'member_id,member_code, member_name, member_nickname, member_real_name, email,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,member_sn';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        $order = 'create_time desc';
        if(!empty($inputData['sort']) && in_array($inputData['sort'],['create_time','access_key_create_time']) && !empty($inputData['order']) && in_array($inputData['order'],['desc','asc'])){
            $order = $inputData['sort']." ".$inputData['order'];
        }
        //获取会员列表数据
        $memberList = $this->memberModel->getMemberList($condition, $field, "", $order);
        if($memberList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员列表数据失败', 2);
            return reTmJsonObj(500, '获取会员列表失败', []);
        }
        $exportToExcelAllArr = [];
        foreach ($memberList as $k => $v){
//            $accout = "";
//            if(!empty($v['wb'])){
//                $accout = $v['wb'];
//            }
//            if(!empty($v['qq'])){
//                $accout = $v['qq'];
//            }
//            if(!empty($v['wx'])){
//                $accout = $v['wx'];
//            }
//            if(!empty($v['mobile'])){
//                $accout = $v['mobile'];
//            }
            if(empty($v['head_pic'])){ //头像处理
                $v['head_pic'] = '/images/member_default_head.png';
            }else{
                if(substr($v['head_pic'],0,1) != '/' && !preg_match("/^(http:\/\/|https:\/\/).*$/",$v['head_pic'])){
                    $v['head_pic'] = 'http://'.$v['head_pic'];
                }
            }
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']); //时间转时间戳
            $v['access_key_create_time'] = date('Y-m-d H:i:s', $v['access_key_create_time']); //时间转时间戳
            $sex = "";
            if(1 == $v['sex']){
                $sex = "男";
            }
            if(2 == $v['sex']){
                $sex = "女";
            }
            $v['close'] = 0;
            if(time()>$v['close_start_time'] && time()<$v['close_end_time']){
                $v['close'] = 1;
            }
            $status = "正常";
            if(1 == $v['close']){
                $status = "封号中";
            }
            if(1 == $v['status']){
                $status = "拉黑";
            }
            $exportToExcelArr = [$v['member_id'],$v['member_sn'],$v['head_pic'],$v['member_nickname'],$v['point'],
                $v['birthday'],$sex,$v['mobile'],$status,$v['create_time'],$v['access_key_create_time']
            ];
            $exportToExcelAllArr[] = $exportToExcelArr;
        }
        $this->exportToExcel("会员名单.xls",["ID","账号","头像","昵称","积分","生日","性别","手机号","状态","注册时间","最近登录时间"],$exportToExcelAllArr);
        exit();
    }


    /**
     * @data 2018/1/05
     * @desc 数据导出到excel(csv文件)
     * @param $filename 导出的csv文件名称 如date("Y年m月j日").'-test.csv'
     * @param array $tileArray 所有列名称
     * @param array $dataArray 所有列数据
     */
    protected function exportToExcel($filename, $tileArray=[], $dataArray=[]){
        ini_set('memory_limit','512M');
        ini_set('max_execution_time',0);
        ob_end_clean();
        ob_start();
        header("Content-Type: text/csv");
        header("Content-Disposition:filename=".$filename);
        $fp=fopen('php://output','w');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
        fputcsv($fp,$tileArray);
        $index = 0;
        foreach ($dataArray as $item) {
            if($index==1000){
                $index=0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp,$item);
        }

        ob_flush();
        flush();
        ob_end_clean();
    }

    /**
     * 获取会员列表
     */
    public function getMemberList(){

        //判断请求方式以及请求参数
      //  $inputData = Request::get();
        $inputData = getEncryptGetData();
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
        $field = 'member_id,member_code, member_name, member_nickname, member_real_name, email,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,login_type,member_sn,channel_sources';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->memberModel->getCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'create_time desc';
        if(!empty($inputData['sort']) && in_array($inputData['sort'],['create_time','access_key_create_time']) && !empty($inputData['order']) && in_array($inputData['order'],['desc','asc'])){
            $order = $inputData['sort']." ".$inputData['order'];
        }
        //获取会员列表数据
        $memberList = $this->memberModel->getMemberList($condition, $field, $limit, $order);
        if($memberList === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员列表数据失败', 2);
            return reTmJsonObj(500, '获取会员列表失败', []);
        }
        foreach ($memberList as $k => $v){
            if(empty($v['head_pic'])){ //头像处理
                $v['head_pic'] = '/images/member_default_head.png';
            }else{
                if(substr($v['head_pic'],0,1) != '/' && !preg_match("/^(http:\/\/|https:\/\/).*$/",$v['head_pic'])){
                    $v['head_pic'] = 'http://'.$v['head_pic'];
                }
            }
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']); //时间转时间戳
            $v['access_key_create_time'] = date('Y-m-d H:i:s', $v['access_key_create_time']); //时间转时间戳
            $v['close'] = 0;
            if(time()>$v['close_start_time'] && time()<$v['close_end_time']){
                $v['close'] = 1;
            }
            //第3方信息获取
            $memberThirdPartyModel = new MemberThirdPartyModel();
            $v['other_info'] = $memberThirdPartyModel->getThirdPartyList(['member_id'=>$v['member_id']],'uid,nick_name,member_id,head_url,address,ip,type');
            $v['other_info'] = $memberThirdPartyModel->ArrayToType($v['other_info']);
            $v['channel_sources'] = empty($v['channel_sources'])?"无":$v['channel_sources'];
            $memberList[$k] = $v;
        }

        $return = [
            'list' => $memberList,
            'totalPage' => $totalPage,
            'total' => $count
          //  'total' => $this->memberModel->getLastSql()
        ];

        return reEncryptJson(200, '获取会员列表成功', $return);
    }

    /*获取会员注册渠道来源来源*/
    public function getChannelSourcesList(){
        $list = $this->memberModel->getChannelSourcesList();
        $list_arr = [];
        foreach ($list as $value){
            if(!empty($value['channel_sources'])){
                $list_arr[] = $value;
            }
        }
        return reTmJsonObj(200, '成功', $list_arr);
    }
    /**
     * 获取会员信息
     */
    public function getMemberInfo(){
        //判断请求方式以及请求参数
       // $inputData = Request::get();
        $inputData = getEncryptGetData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $field = 'member_id,member_code, member_name, member_nickname, member_real_name,site_code,email,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,member_sn';
        $memberInfo = $this->memberModel->getMemberInfo($condition, $field);
        if($memberInfo === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '获取会员详情数据失败', 2);
            return reTmJsonObj(500, '获取会员信息失败', []);
        }

        if(empty($memberInfo['head_pic'])){ //头像处理
            $memberInfo['head_pic'] = '/images/member_default_head.png';
        }else{
            if(substr($memberInfo['head_pic'],0,1) != '/' && !preg_match("/^(http:\/\/|https:\/\/).*$/",$memberInfo['head_pic'])){
                $memberInfo['head_pic'] = 'http://'.$memberInfo['head_pic'];
            }
        }
        $memberInfo['create_time'] = date('Y-m-d H:i:s', $memberInfo['create_time']); //时间转时间戳
        $memberInfo['access_key_create_time'] = date('Y-m-d H:i:s', $memberInfo['access_key_create_time']); //时间转时间戳
        $memberInfo['close'] = 0;
        if(time()>$memberInfo['close_start_time'] && time()<$memberInfo['close_end_time']){
            $memberInfo['close'] = 1;
        }
        $memberInfo['close_start_time'] = date('Y-m-d H:i:s', $memberInfo['close_start_time']); //时间转时间戳
        $memberInfo['close_end_time'] = date('Y-m-d H:i:s', $memberInfo['close_end_time']); //时间转时间戳
        //第3方信息获取
        $memberThirdPartyModel = new MemberThirdPartyModel();
        $memberInfo['other_info'] = $memberThirdPartyModel->getThirdPartyList(['member_id'=>$memberInfo['member_id']],'uid,nick_name,member_id,head_url,address,ip,type');
        $memberInfo['other_info'] = $memberThirdPartyModel->ArrayToType($memberInfo['other_info']);

        $siteModel = new SiteModel();
        $siteName = $siteModel->getSiteInfo(['site_code' => $memberInfo['site_code']], 'site_name')['site_name'];
        $memberInfo['site_name'] = $siteName;


        return reEncryptJson(200, '获取会员信息成功', $memberInfo);
    }

    /*
     * 获取会员积分变动列表*/
    public function getMemberPointList(){
        //判断请求方式以及请求参数
       // $inputData = Request::get();
        $inputData = getEncryptGetData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ["member_id"];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //获取搜索条件
        $condition = ["member_id"=>$inputData['member_id']];
        $field = '*';
        empty($inputData['page_size']) ? $pageSize = 200000000 : $pageSize = $inputData['page_size'];
        empty($inputData['index']) ? $index = 1 : $index = $inputData['index'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->MemberpointModel->getPointLogCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($index - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'add_time desc';
        //获取会员列表数据
        $List = $this->MemberpointModel->getPointLogList($condition, $field, $limit, $order);
        if($List === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        foreach ($List as $k => $v){
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']); //时间转时间戳
            $List[$k] = $v;
        }
        $return = [
            'list' => $List,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reEncryptJson(200, '获取会员列表成功', $return);
    }


    /*
 * 获取会员积分变动列表*/
    public function getMemberBehaviorList(){
        //判断请求方式以及请求参数
        //$inputData = Request::get();
        $inputData = getEncryptGetData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ["member_id"];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //获取搜索条件
        $condition = ["member_id"=>$inputData['member_id']];
        $field = '*';
        empty($inputData['page_size']) ? $pageSize = 200000000 : $pageSize = $inputData['page_size'];
        empty($inputData['index']) ? $index = 1 : $index = $inputData['index'];
        //根据条件计算总会员数,计算分页总页数
        $count = $this->MemberBehaviorLogModel->getPointLogCount($condition);
        $totalPage = ceil($count / $pageSize);
        //分页处理
        $firstRow = ($index - 1) * $pageSize;
        $limit = $firstRow . ',' . $pageSize;
        $order = 'create_time desc';
        //获取会员列表数据
        $List = $this->MemberBehaviorLogModel->getPointLogList($condition, $field, $limit, $order);
        if($List === false){
            return reTmJsonObj(500, '获取失败', []);
        }
        foreach ($List as $k => $v){
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']); //时间转时间戳
            $List[$k] = $v;
        }
        $return = [
            'list' => $List,
            'totalPage' => $totalPage,
            'total' => $count
        ];

        return reEncryptJson(200, '获取会员列表成功', $return);
    }

    /**
     * 新增会员
     */
    public function addMember(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_name','password','site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $inputData['member_code'] = createCode();
        $inputData['create_time'] = time();
        $inputData['password'] = md5(md5($inputData['password']));

        $re = $this->memberModel->addMember($inputData);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '新增会员数据失败', 2);
            return reTmJsonObj(500, '新增会员失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '新增会员');
        return reEncryptJson(200, '新增会员成功', [],false);
    }

    /**
     * 修改会员
     */
    public function updateMember(){
        //判断请求方式以及请求参数
        //$inputData = Request::put();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition['member_code'] = $inputData['member_code'];
        $re = $this->memberModel->updateMember($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员数据失败', 2);
            return reTmJsonObj(500, '编辑会员失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改会员');
        return reEncryptJson(200, '编辑会员成功', [],false);
    }

    /**
     * 更改用户密码
     */
    public function changePassword(){
        //判断请求方式以及请求参数
        //$inputData = Request::put();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code', 'old_pass', 'new_pass'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition = ['member_code' => $inputData['member_code']];
        //比对旧密码
        $userInfo = $this->memberModel->updateMember($condition, 'password');
        if($userInfo['password'] !== md5(md5($inputData['old_pass']))){
            return reTmJsonObj(500, '原密码输入错误', []);
        }

        //更改密码
        $re = $this->memberModel->updateMember($condition, ['password' => md5(md5($inputData['new_pass']))]);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '修改会员密码失败', 2);
            return reTmJsonObj(500, '修改失败', []);
        }
        Logservice::writeArray([], '修改会员密码');
        return reEncryptJson(200, '修改成功', [],false);
    }

    /**
     * 删除会员
     */
    public function deleteMember(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['member_codes'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //支持批量删除
        $memberCode = explode(',', $inputData['member_codes']);
        Db::startTrans();
        foreach ($memberCode as $value){
            $condition['member_code'] = $value;
            //删除用户即修改用户deleted字段值
            $re = $this->memberModel->updateMember($condition, ['deleted' => 1]);
            if($re === false){
                Logservice::writeArray(['sql'=>$this->memberModel->getLastSql(), 'condition'=>$condition], '删除会员数据失败', 2);
                Db::rollback();
                return reTmJsonObj(500, '删除用户失败', []);
            }
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '删除会员');
        return reTmJsonObj(200, '删除会员成功', []);
    }

    /**
     * 获取会员总数,今日新增会员,昨日新增会员
     */
    public function getCount(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //总数
        $totalConf['deleted'] = 0;

        $total = $this->memberModel->getCount($totalConf);
        if($total === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '统计总数失败', 2);
            return reTmJsonObj(500, '获取全部统计失败', []);
        }

        //今日统计
        $todayStart = strtotime(date('Y-m-d'));
        $todayEnd = $todayStart + 24*3600 - 1;
        $todayConf = [
            'deleted' => 0,
            'create_time'=> array('between',"$todayStart,$todayEnd")
        ];
        $today = $this->memberModel->getCount($todayConf);
        if($today === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '统计今日数据失败', 2);
            return reTmJsonObj(500, '获取今日统计失败', []);
        }

        //昨日统计
        $yesterdayStart = strtotime(date('Y-m-d', strtotime("-1 day")));
        $yesterdayEnd = $yesterdayStart + 24*3600 - 1;
        $yesterdayConf = [
            'deleted' => 0,
            'create_time'=> array('between',"$yesterdayStart,$yesterdayEnd")
        ];
        $yesterday = $this->memberModel->getCount($yesterdayConf);
        if($yesterday === false){
            Logservice::writeArray(['sql'=>$this->memberModel->getLastSql()], '统计昨日数据失败', 2);
            return reTmJsonObj(500, '获取昨日统计失败', []);
        }

        $return = [
            'total' => $total,
            'today' => $today,
            'yesterday' => $yesterday
        ];
        return reTmJsonObj(200, '获取统计成功', $return);
    }

    /**
     * 更新会员信息
     */
    public function updateMemberInfo(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $MemberModel = new MemberModel();
        $condition = array(
            'member_id'=>$inputData['member_id']
        );
        $MemberInfo = $MemberModel->getMemberInfo($condition);
        if(!$MemberInfo){
            return reTmJsonObj(500,'用户不存在',[]);
        }
        $data = array();
        $allowFiled = array('member_name','member_nickname','member_real_name','email','mobile','head_pic','sex','birthday','receive_notice','wifi_show_image','list_auto_play');
        foreach ($allowFiled as $val){
            if(!empty($inputData[$val])){
                $data[$val] = $inputData[$val];
            }
        }
        if(count($data) < 0){
            return reTmJsonObj(500,'更新信息不存在',[]);
        }
        $result = $MemberModel->updateMember($condition,$data);
        $MemberBehaviorLog = "修改用户信息";
        if($result){
            $this->MemberBehaviorLogModel->addPointLog($inputData['member_id'],$MemberBehaviorLog);
            $MemberInfo = $MemberModel->getMemberInfo($condition);
            return reEncryptJson(200,'更新成功',$MemberInfo);
        }
        return reTmJsonObj(500,'更新失败',[]);
    }

    /**
     * 禁用或开启
     */
    public function forbiddenOrStartMember(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $MemberModel = new MemberModel();
        $condition = array(
            'member_id'=>$inputData['member_id']
        );
        $MemberInfo = $MemberModel->getMemberInfo($condition);
        if(!$MemberInfo){
            return reTmJsonObj(500,'用户不存在',[]);
        }
        if(!in_array($inputData['status'],[0,1])){
            return reTmJsonObj(500,'状态参数异常',[]);
        }
        if(!empty($inputData['member_status'])){
            $inputData['status'] = $inputData['member_status'] ;
        }
        $result = $MemberModel->updateMember($condition,['status'=>$inputData['status']]);
        $MemberBehaviorLog = "";
        if(1 == $inputData['status']){
            $MemberBehaviorLog = "拉黑";
        }else{
            $MemberBehaviorLog = "解除拉黑";
        }
        if($result){
            $this->MemberBehaviorLogModel->addPointLog($inputData['member_id'],$MemberBehaviorLog);
            return reEncryptJson(200,'成功',[],false);
        }
        return reTmJsonObj(500,'失败',[]);
    }

    /**
     * 封号
     */
    public function closeMember(){
        //判断请求方式以及请求参数1
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_id','close_start_time','close_end_time','close_down_point','close_reason'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $MemberModel = new MemberModel();
        $condition = array(
            'member_id'=>$inputData['member_id']
        );
        $MemberInfo = $MemberModel->getMemberInfo($condition);
        if(!$MemberInfo){
            return reTmJsonObj(500,'用户不存在',[]);
        }
        $point = $MemberInfo['point']-$inputData['close_down_point']<0?0:$MemberInfo['point']-$inputData['close_down_point'];
        $result = $MemberModel->updateMember($condition,['close_reason'=>$inputData['close_reason'],'close_down_point'=>$inputData['close_down_point'],
            'close_start_time'=>strtotime($inputData['close_start_time']),'close_end_time'=>strtotime($inputData['close_end_time']),'point'=>$point]);
        $MemberBehaviorLog = "管理员封号，从".$inputData['close_start_time']."到".$inputData['close_end_time']."封号原因：".$inputData['close_reason'];
        if(false === $result){
            return reTmJsonObj(500,'失败',[]);
        }else{
            $this->MemberBehaviorLogModel->addPointLog($inputData['member_id'],$MemberBehaviorLog);
            $this->MemberpointModel->addPointLog($inputData['member_id'],0-$inputData['close_down_point'],$MemberBehaviorLog,$point,'admin');
            return reEncryptJson(200,'成功',[],false);
        }
    }

    /**
     * 解除封号
     */
    public function startMember(){
        //判断请求方式以及请求参数1
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $MemberModel = new MemberModel();
        $condition = array(
            'member_id'=>$inputData['member_id']
        );
        $MemberInfo = $MemberModel->getMemberInfo($condition);
        if(!$MemberInfo){
            return reTmJsonObj(500,'用户不存在',[]);
        }
        $result = $MemberModel->updateMember($condition,['close_start_time'=>0,'close_end_time'=>0]);
        $MemberBehaviorLog = "管理员解除封号";
        if(false === $result){
            return reTmJsonObj(500,'失败',[]);
        }else{
            $this->MemberBehaviorLogModel->addPointLog($inputData['member_id'],$MemberBehaviorLog);
            return reEncryptJson(200,'成功',[],false);
        }
    }

    /**
     * 统计会员性别
     */
    public function memberAnalysisSex(){
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $memberModel = new MemberModel();
        $condition = [
            ['status','=',0]
        ];
        $group = 'sex';
        $field = 'sex,count(*) as num';
        $memberCount = $memberModel->countGroupMember($condition,$group,$field,10);
        if($memberCount){
            $sexData = [];
            foreach ($memberCount as $val) {
                if($val['sex'] === null){
                    $sexData['key'][] = '未知';
                }elseif($val['sex'] === 0){
                    $sexData['key'][] = '保密';
                }elseif($val['sex'] === 1){
                    $sexData['key'][] = '男';
                }elseif($val['sex'] === 2){
                    $sexData['key'][] = '女';
                }
                $sexData['value'][] = $val['num'];
            }
            return reTmJsonObj(200,'获取成功',$sexData);
        }
        return reTmJsonObj(500,'获取失败', []);
    }

    /**
     * 统计用户注册方式
     */
    public function memberAnalysisLoginType(){
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        $memberModel = new MemberModel();
        $condition = [
            ['status','=',0]
        ];
        $group = 'login_type';
        $field = 'login_type,count(*) as num';
        $memberCount = $memberModel->countGroupMember($condition,$group,$field,10);
        $type_name = [
            'mobile'=>'手机号码',
            'qq'=>'QQ',
            'wx'=>'微信',
            'wb'=>'微博'
        ];
        if($memberCount){
            $login_typeData = [];
            foreach ($memberCount as $val) {
                $login_typeData['key'][] = $type_name[$val['login_type']];
                $login_typeData['value'][] = $val['num'];
            }
            return reTmJsonObj(200,'获取成功',$login_typeData);
        }
        return reTmJsonObj(500,'获取失败', []);
    }



}