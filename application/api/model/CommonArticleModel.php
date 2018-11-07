<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\api\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;

class CommonArticleModel extends CommonModel
{

    public function getCount($condition){
        $re = Db::table($this->common_article)->where($condition)->count('article_id');
        return $re;
    }


    public function getList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->common_article)
            ->field($field)->where($condition)->limit($limit)->order($order)->select();
        return $re;
    }


    public function getInfo($condition, $field=''){
        $re = Db::table($this->common_article)->field($field)->where($condition)->find();
        return $re;
    }


    public function addInfo($data){
        $re = Db::table($this->common_article)->insertGetId($data);
        return $re;
    }

    public function updateInfo($condition, $data){
        $re = Db::table($this->common_article)->where($condition)->update($data);
        return $re;
    }
    public function deleteInfo($condition){
        $re = Db::table($this->common_article)->where($condition)->delete();
        return $re;
    }
}