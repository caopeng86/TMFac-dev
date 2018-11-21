<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/16
 * Time: 16:26
 */

namespace app\api\model;


use think\Db;
use think\facade\Config;
use think\facade\Env;
use think\Model;

class SystemArticleModel extends CommonModel
{

    /**
     * 获取系统文章
     * @return mixed
     */
    public function getArticleInfo($condition){
        return Db::table($this->system_article_db)->where($condition)->find();
    }

    public function updateArticleInfo($condition,$article){
        $data = array();
        if(!empty($article['article'])){
            $data['article'] = $article['article'];
        }
        if(!empty($article['content'])){
            $data['content'] = $article['content'];
        }
        $data['update_time'] = time();
        return Db::table($this->system_article_db)->where($condition)->update($data);
    }

    public function addArticleInfo($article){
        $data = array();
        if(!empty($article['article'])){
            $data['article'] = $article['article'];
        }
        if(!empty($article['content'])){
            $data['content'] = $article['content'];
        }
        $data['add_time'] = $data['update_time'] = time();
        return Db::table($this->system_article_db)->insertGetId($data);
    }
}