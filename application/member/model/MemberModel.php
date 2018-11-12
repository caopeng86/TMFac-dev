<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class MemberModel extends CommonModel
{
    /**
     * 计算会员表总条数
     * @param $condition
     * @return int|string
     */
    public function getCount($condition){
        $re = Db::table($this->member_db)->where($condition)->count('member_id');
        return $re;
    }

    /**
     * 获取会员列表数据
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getMemberList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->member_db)->join($this->site_db, $this->member_db.'.site_code = '.$this->site_db.'.site_code')
            ->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 获取一条会员信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getMemberInfo($condition, $field=''){
        $re = Db::table($this->member_db)->field($field)->where($condition)->find();
        return $re;
    }

    /**
     * 新增会员
     * @param $data
     * @return int|string
     */
    public function addMember($data){
        $re = Db::table($this->member_db)->insertGetId($data);
        return $re;
    }

    /**
     * 修改会员信息
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateMember($condition, $data){
        $re = Db::table($this->member_db)->where($condition)->update($data);
        return $re;
    }

}