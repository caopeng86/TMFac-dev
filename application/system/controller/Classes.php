<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18
 * Time: 15:21
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\ClassesModel;
use think\Db;
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

    /**
     * 新增分类
     */
    public function addClasses(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['classes_name', 'site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }
        $inputData['classes_code'] = createCode();

        //保存分类数据
        Db::startTrans();
        $id = $this->classModel->addClasses($inputData);
        if(!$id){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '新增分类列表失败', 2);
            Db::rollback();
            return reJson(500, '新增失败', []);
        }
        //保存排序字段
        $re = $this->classModel->updateClasses(['classes_id' => $id], ['sort' => $id]);
        if(!$re){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '保存排序字段失败', 2);
            Db::rollback();
            return reJson(500, '新增排序失败', []);
        }

        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '新增分类');
        return reJson(200, '新增成功', []);
    }

    /**
     * 修改分类
     */
    public function updateClasses(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['classes_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition['classes_code'] = $inputData['classes_code'];
        //保存分类数据
        $re = $this->classModel->updateClasses($condition, $inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '保存分类数据失败', 2);
            return reJson(500, '修改失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改分类');
        return reJson(200, '修改成功', []);
    }

    /**
     * 删除分类
     */
    public function deleteClasses(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['classes_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        //判断该分类下是否有子分类
        $son = $this->classModel->getClassesInfo(['parent_classes_id' => $inputData['classes_id']], 'classes_id');
        if($son){
            return reJson(500, '该分类下有子分类,不能删除', []);
        }

        $condition['classes_id'] = $inputData['classes_id'];
        //删除分类数据
        $re = $this->classModel->deleteClasses($condition);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '删除分类数据失败', 2);
            return reJson(500, '删除失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '删除分类');
        return reJson(200, '删除成功', []);
    }

    /**
     * 改变分类排序
     */
    public function changeClassSort(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['sort_top_code','sort_bottom_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        //获取排序值
        $sortTop = $this->classModel->getClassesInfo(['classes_code' => $inputData['sort_top_code']], 'sort')['sort'];
        if(!$sortTop){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '获取排序值失败', 2);
            return reJson(500, '获取排序失败', []);
        }

        $sortBottom = $this->classModel->getClassesInfo(['classes_code' => $inputData['sort_bottom_code']], 'sort')['sort'];
        if(!$sortBottom){
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '获取排序值失败', 2);
            return reJson(500, '获取排序失败', []);
        }

        //修改排序,交换上下的排序值
        Db::startTrans();
        $reTop = $this->classModel->updateClasses(['classes_code' => $inputData['sort_top_code']], ['sort' => $sortBottom]);
        if(!$reTop){
            Db::rollback();
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '修改排序值失败', 2);
            return reJson(500, '修改排序失败', []);
        }
        $reBottom = $this->classModel->updateClasses(['classes_code' => $inputData['sort_bottom_code']], ['sort' => $sortTop]);
        if(!$reBottom){
            Db::rollback();
            Logservice::writeArray(['sql'=>$this->classModel->getLastSql()], '修改排序值失败', 2);
            return reJson(500, '修改排序失败', []);
        }

        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '改变分类排序');
        return reJson(200, '修改排序成功', []);
    }
}