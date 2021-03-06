<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11
 * Time: 15:51
 */

namespace app\member\controller;


use app\member\model\MemberfootprintModel;
use app\member\model\MemberstarModel;
use think\Db;
use think\facade\Request;

class Memberstar extends Base
{
    protected $starModel;
    public function __construct()
    {
        parent::__construct();
        $this->starModel = new MemberstarModel();
    }

    /**
     * 添加收藏
     */
    public function addStar(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code','title','app_id','article_id','extend','type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
//        if(preg_match("/^(http:\/\/|https:\/\/).*$/",$inputData['url'])){
//            return reTmJsonObj(500,'url不能带域名', []);
//        }
        //判断是否已收藏
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' => $inputData['article_id'],
            'app_id' => $inputData['app_id'],
            'title' => $inputData['title'],
        ];
        $list = $this->starModel->starList($condition, 'title');
        if($list === false){
            return reTmJsonObj(500, '获取收藏列表失败', []);
        }
        if(!empty($list)){
            return reTmJsonObj(500, '已被收藏', []);
        }

        //收藏数据
        $inputData['create_time'] = time();
        $re = $this->starModel->addStar($inputData);
        if($re === false){
            return reTmJsonObj(500, '收藏失败', []);
        }

        return reEncryptJson(200, '收藏成功', ['star_id'=>$this->starModel->getLastInsID()]);
    }

    /**
     * 检验是否被收藏
     */
    public function checkIsStar(){
        //判断请求方式以及请求参数
       // $inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code','app_id','article_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        //判断是否已收藏
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' => $inputData['article_id'],
            'app_id' => $inputData['app_id']
        ];
        $Info = $this->starModel->starFind($condition,'star_id');
        if($Info === false){
            return reTmJsonObj(500, '查询失败', []);
        }
        if(!empty($Info)){
            return reEncryptJson(200, '已被收藏', ['star_id'=>$Info['star_id']]);
        }
        return reEncryptJson(200, '未被收藏', []);
    }

    /**
     * 批量检查是否被收藏
     * @return \think\response\Json
     * 返回正常json样式 {"code":200,"data":[{"article_id":"1","star_id":false},{"article_id":"2","star_id":2},{"article_id":"3","star_id":false},{"article_id":"7","star_id":7},{"article_id":"10","star_id":false}],"msg":"查询成功"}
     */
    public function checkIsStarBatch(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['member_code','app_id','article_ids'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if(is_array($inputData['article_ids'])){
            $article_ids = $inputData['article_ids'];
        }else{
            $article_ids = explode(',',$inputData['article_ids']);
        }
        $condition = [
            'member_code' => $inputData['member_code'],
            'article_id' =>$article_ids,
            'app_id' => $inputData['app_id']
        ];
        $starList = $this->starModel->starList($condition,'star_id,article_id');
        if($starList === false){
            return reTmJsonObj(500, '查询失败', []);
        }
        $returnData = array();
        if(!empty($starList)){
            $starList = array_column($starList,'star_id','article_id');
            foreach ($article_ids as $val){
                $returnData[] = array(
                    'article_id'=>$val,
                    'star_id'=>$val > 0 && isset($starList[$val])?$starList[$val]:false
                );
            }
            return reEncryptJson(200,'查询成功', $returnData);
        }else{
            foreach ($article_ids as $val){
                $returnData[] = array(
                    'article_id'=>$val,
                    'star_id'=>false
                );
            }
            return reEncryptJson(200,'全未被收藏',$returnData);
        }
    }

    /**
     * 取消收藏
     */
    public function deleteStar(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['star_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

//        $condition['star_id'] = $inputData['star_id'];
//        $re = $this->starModel->deleteStar($condition);
        $re = Db::table(TM_PREFIX.'member_star')
            ->whereIn('star_id',$inputData['star_id'])
            ->delete();
        if($re === false){
            return reTmJsonObj(500, '取消收藏失败', []);
        }

        return reEncryptJson(200, '取消收藏成功', [],false);
    }

	/**
     * 清空收藏
     */
    public function clearStar(){
        //判断请求方式以及请求参数
        //$inputData = Request::post();
        $inputData = getEncryptPostData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
		$params = ['member_code','type'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $re = Db::table(TM_PREFIX.'member_star')
            ->whereIn('member_code',$inputData['member_code'])
			->whereIn('type',$inputData['type'])
            ->delete();
        if($re === false){
            return reTmJsonObj(500, '清空收藏失败', []);
        }

        return reEncryptJson(200, '清空收藏成功', [],false);
    }

    /**
     * 获取收藏列表
     */
    public function getStarList(){
        //判断请求方式以及请求参数
       // $inputData = Request::get();
        $inputData = getEncryptGetData();
        if(!$inputData){
            return reTmJsonObj(552,"解密数据失败",[]);
        }
        $method = Request::method();
        $params = ['index','member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //条件拼接
        $condition['member_code'] = $inputData['member_code'];
        //分页处理
        $pageSize = empty($inputData['page_size'])? 20 : $inputData['page_size'];
        //类型判断
        if(!empty($inputData['type']) && in_array($inputData['type'],array(1,2))){
            $condition['type'] = $inputData['type'];
        }
        $count = $this->starModel->countStar($condition);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $totalPage = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;
        $order = 'create_time desc';
        $field = 'star_id, member_code, app_id, article_id, title, intro, pic, create_time, extend,tag,type';

        //获取列表数据
        $list = $this->starModel->starList($condition, $field, $limit, $order);
        if($list === false){
            return reTmJsonObj(500, '获取列表失败', []);
        }

        $return = [
            'total' => $count,
            'totla_page' => $totalPage,
            'list' => $list
        ];

        return reEncryptJson(200, '获取列表成功', $return);
    }

    /**
     * 统计收藏条数和历史记录条数
     */
    public function countStarAndFootprint(){
        //判断请求方式以及请求参数
        //$inputData = Request::get();
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
        $condition = [
            ['member_code','=',$inputData['member_code']]
        ];
        //收藏条数
        $starNum = $this->starModel->countStar($condition);
        $MemberfootprintModel = new MemberfootprintModel();
      //  $condition[] = ['create_time','>=',time() - 7*24*3600];
        $condition[] = ['member_code','=',$inputData['member_code']];
        $condition[] = ['status','=',1];
        $condition[] = ['create_time','>=',time() - 7*24*3600];
        $footprintNum = $MemberfootprintModel->countFootprint($condition);
        return reEncryptJson(200,'成功',['starNum'=>$starNum,'footprintNum'=>$footprintNum]);
    }
}