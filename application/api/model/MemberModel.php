<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\api\model;


use think\Db;
use think\Model;

class MemberModel extends CommonModel
{
    /**
     * 返回model对应的数据表名
     * @return string
     */
    public function getTableName(){
        return $this->member_db;
    }
    /**
     * 计算会员表总条数
     * @param $condition
     * @return int|string
     * @throws
     */
    public function getCount($condition){
        $join_exp= $this->member_db.'.site_code = '.$this->site_db.'.site_code';
        $re = Db::table($this->member_db)->where($condition)->join($this->site_db, $join_exp)->count('member_id');
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
        $join_exp= $this->member_db.'.site_code = '.$this->site_db.'.site_code';
        $re = Db::table($this->member_db)->where($condition)->join($this->site_db, $join_exp)
            ->field($field)->limit($limit)->order($order)->select();
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
        $re = Db::table($this->member_db)->insert($data);
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


    /**
     * 统计分组用户数据
     */
    public function countGroupMember($condition,$group,$field,$cache = false){
        $re = Db::table($this->member_db)->where($condition);
        if($cache > 0){ //缓存
            $re = $re->cache(true,$cache);
        }
        $re = $re->group($group)->field($field)->select();
        return $re;
    }

    /*获取会员注册渠道来源来源*/
    public function getChannelSourcesList(){
       return Db::table($this->member_db)->field("channel_sources")->group("channel_sources")->select();
    }
}