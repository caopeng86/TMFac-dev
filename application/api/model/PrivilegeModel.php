<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 18:34
 */

namespace app\api\model;


use think\Db;
use think\Model;

class PrivilegeModel extends CommonModel
{
    /**
     * 统计所有权限条数
     * @param $condition
     * @return int|string
     * @throws
     */
    public function countPrivilege($condition){
        $re = Db::table($this->privilege_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取权限列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getPrivilegeList($condition, $field='', $limit='', $order='privilege_id asc'){
        $re = Db::table($this->privilege_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 查找一条权限数据
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getPrivilegeInfo($condition, $field=''){
        $re = Db::table($this->privilege_db)->field($field)->where($condition)->find();
        return $re;
    }

    /**
     * 新增权限数据
     * @param $data
     * @return int|string
     */
    public function addPrivilege($data){
        $re = Db::table($this->privilege_db)->insert($data);
        return $re;
    }

    /**
     * 修改权限数据
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updatePrivilege($condition, $data){
        $re = Db::table($this->privilege_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 删除权限数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deletePrivilege($condition){
        $re = Db::table($this->privilege_db)->where($condition)->delete();
        return $re;
    }


}