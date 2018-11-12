<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/12
 * Time: 9:58
 */
namespace app\system\controller;

use think\facade\Config;
use think\facade\Env;

class Update extends \think\Controller {

    /*
     * 更新SQL版本
     */
    public function index(){
        $file_path = 'db';
        $now_sql_version = $version =  Env::get(SERVER_ENV.'SQL_VERSION');
        $file=scandir(Env::get('root_path').$file_path);
        $dbconfig = Config::get('')['database'];
        $db       = \think\Db::connect($dbconfig);
        foreach ($file as $val){
            $val = strstr($val,'.sql',true); //去除.sql
            if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                create_tables($db,'',$file_path.'/'.$val.'.sql',false);
                //记录当前版本
                $now_sql_version = $val;
            }
        }
        if($now_sql_version == $version){
            return reJson(500,'当前版本是最新版本');
        }
        $env = file_get_contents(Env::get('root_path') . '.env');
        $env = str_replace("SQL_VERSION=".$version, "SQL_VERSION=".$now_sql_version, $env);
        //写入应用配置文件
        if (file_put_contents(Env::get('root_path') . '.env', $env)) {
            return reJson(200,'更新成功');
        }else{
            return reJson(500,'写入SQL版本失败');
        }
    }

}