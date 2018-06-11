<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/20
 * Time: 14:40
 */

namespace app\api\model;


use think\Db;
use think\Model;

class BranchModel extends CommonModel
{
    /**
     * 获取组织机构列表
     * @param $condition
     * @param string $field
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getBranchList($condition, $field='', $order=''){
        $re = Db::table($this->branch_db)->where($condition)->field($field)->order($order)->select();
        return $re;
    }

    /**
     * 查找一条部门信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getBranchInfo($condition, $field=''){
        $re = Db::table($this->branch_db)->field($field)->where($condition)->find();
        return $re;
    }

    /**
     * 新增部门
     * @param $data
     * @return int|string
     * @throws
     */
    public function addBranch($data){
        $re = Db::table($this->branch_db)->insertGetId($data);
        return $re;
    }

    /**
     * 修改部门
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateBranch($condition, $data){
        $re = Db::table($this->branch_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 删除部门
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteBranch($condition){
        $re = Db::table($this->branch_db)->where($condition)->delete();
        return $re;
    }
}