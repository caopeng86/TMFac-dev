<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/12
 * Time: 9:58
 */
namespace app\system\controller;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;

class Update extends \think\Controller {

    /*
     * 更新SQL版本
     */
    public function index(){
        $file_path = 'db';
        $now_sql_version = $version =  Env::get(SERVER_ENV.'SQL_VERSION');
        $dbconfig = Config::get('')['database'];
        $db       = \think\Db::connect($dbconfig);
        $now_sql_version = create_tables_multi($db,$file_path,$now_sql_version);
        if($now_sql_version == $version){
            return reTmJsonObj(200,'更新成功');
        }
        $env = file_get_contents(Env::get('root_path') . '.env');
        $env = str_replace("SQL_VERSION=".$version, "SQL_VERSION=".$now_sql_version, $env);
        //写入应用配置文件
        if (file_put_contents(Env::get('root_path') . '.env', $env)) {
            Cache::set('site_info',null);//清除备注缓存
            return reTmJsonObj(200,'更新成功');
        }else{
            return reTmJsonObj(500,'写入SQL版本失败');
        }
    }

}