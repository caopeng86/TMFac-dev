<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 11:20
 */

namespace app\api\model;


use think\Db;
use think\facade\Config;
use think\facade\Env;
use think\Model;

class SiteModel extends CommonModel
{
    /**
     * 计算数据库尺寸
     * @return string
     */
    public function getDatabaseSize(){
        $sql = "SHOW TABLE STATUS FROM ".Env::get(SERVER_ENV.'DATABASE_NAME');
        $tblPrefix = TM_PREFIX;
        if($tblPrefix != null) {
            $sql .= " LIKE '{$tblPrefix}%'";
        }
        $row = Db::query($sql);
        $size = 0;
        foreach($row as $value) {
            $size += $value["Data_length"] + $value["Index_length"];
        }
        $size = round(($size/1048576),2).'M';
        return $size;
    }


    /**
     * 获取数据库版本
     * @return mixed
     */
    public function getMysqlVersion(){
        $version = Db::query('select version() as ver')[0]['ver'];
        return $version;
    }

    /**
     * 统计所有站点条数
     * @param $condition
     * @return int|string
     * @throws
     */
    public function countSite($condition){
        $re = Db::table($this->site_db)->where($condition)->count();
        return $re;
    }

    /**
     * 获取站点列表
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getSiteList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->site_db)->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 查找一条站点数据
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getSiteInfo($condition, $field=''){
        $re = Db::table($this->site_db)->field($field)->where($condition)->find();
        return $re;
    }
    
    /**
     * 新增一条站点信息
     * @param $data
     * @return int|string
     * @throws
     */
    public function addSite($data){
        $re = Db::table($this->site_db)->insert($data);
        return $re;
    }

    /**
     * 修改站点数据
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateSite($condition, $data){
        $re = Db::table($this->site_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 删除站点数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteSite($condition){
        $re = Db::table($this->site_db)->where($condition)->delete();
        return $re;
    }
}