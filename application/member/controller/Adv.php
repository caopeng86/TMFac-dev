<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 15:22
 */
namespace app\member\controller;

use app\api\model\AdvModel;

use think\facade\Request;

class Adv extends Base
{
    protected $advModel;
    public function __construct()
    {
        parent::__construct();
        $this->advModel = new AdvModel();
    }

    /**
     * 获取广告界面
     */
    public function getAdvList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $condition = array(
            ['status','=',1]
        );
        $advList = $this->advModel->advList($condition,false,'','sort desc');
        if($advList){
            return reJson(200,'获取数据成功',$advList);
        }
        return reJson(500,'获取数据失败', []);
    }

}