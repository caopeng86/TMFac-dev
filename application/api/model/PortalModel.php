<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 9:45
 */

namespace app\api\model;


use think\Db;
use think\Model;

class PortalModel extends CommonModel
{
    /**
     * 新增应用列表
     * @param $data
     * @return int|string
     */
    public function addPortal($data){
        $re = Db::table($this->portal_db)->insert($data);
        return $re;
    }

    /**
     * 修改应用列表
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updatePortal($condition, $data){
        $re = Db::table($this->portal_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 查找一条应用配置
     * @param $condition
     * @param $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getPortal($condition, $field=''){
        $re = Db::table($this->portal_db)->field($field)->where($condition)->find();
        return $re;
    }
}