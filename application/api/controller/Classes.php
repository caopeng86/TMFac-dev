<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18
 * Time: 15:21
 */

namespace app\api\controller;


use app\api\model\ClassesModel;
use app\extend\controller\Logservice;
use think\facade\Request;

class Classes extends Base
{
    protected $classModel;
    public function __construct()
    {
        parent::__construct();
        $this->classModel = new ClassesModel();
    }

    /**
     * 获取分类列表
     */
    public function getClassesList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition['site_code'] = $inputData['site_code'];
        $field = 'classes_id, classes_code, classes_name, parent_classes_id, classes_intro';
        $order = 'sort asc';
        $classesList = $this->classModel->getClassesList($condition, $field, $order);
        if($classesList === false){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '获取分类列表失败', 2);
            return reJson(500, '获取列表失败', []);
        }
        if(empty($classesList)){
            return reJson(200, '查询成功,列表为空', []);
        }
        //无限极分类处理列表
        $reList = getAttr($classesList, 0, 'parent_classes_id', 'classes_id');

        return reJson(200, '获取列表成功', $reList);
    }
}