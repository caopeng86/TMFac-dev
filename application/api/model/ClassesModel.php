<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18
 * Time: 15:32
 */

namespace app\api\model;


use think\Db;
use think\Model;

class ClassesModel extends CommonModel
{
    /**
     * 查询分类列表
     * @param $condition
     * @param string $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getClassesList($condition, $field='', $order='classes_id asc'){
        $re = Db::table($this->classes_db)->field($field)->where($condition)->order($order)->select();
        return $re;
    }

    /**
     * 查找一条分类信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getClassesInfo($condition, $field=''){
        $re = Db::table($this->classes_db)->field($field)->where($condition)->find();
        return $re;
    }

    /**
     * 新增分类
     * @param $data
     * @return int|string
     * @throws
     */
    public function addClasses($data){
        $re = Db::table($this->classes_db)->insertGetId($data);
        return $re;
    }

    /**
     * 修改分类
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateClasses($condition, $data){
        $re = Db::table($this->classes_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 删除分类
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteClasses($condition){
        $re = Db::table($this->classes_db)->where($condition)->delete();
        return $re;
    }
}