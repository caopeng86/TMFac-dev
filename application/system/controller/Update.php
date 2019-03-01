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
use think\Db;
use app\api\model\SiteModel;

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

    public function index1(){
        $file_path = 'db';
        $now_sql_version = $version =  Env::get(SERVER_ENV.'SQL_VERSION');
        $dbconfig = Config::get('')['database'];
        $db       = \think\Db::connect($dbconfig);
        $now_sql_version = create_tables_multi($db,$file_path,$now_sql_version,true);
        if($now_sql_version){
            Cache::set('site_info',null);
            return reTmJsonObj(200,'更新成功');
        }else{
            return reTmJsonObj(500,'写入SQL版本失败');
        }
    }

    public function indexCompent(){
        set_time_limit(1000) ;
        $id=input("app_id");
        if(!$id){
            return reTmJsonObj(500,'未传入组件id');
        }
        $pix = [];  //定义操作表的前缀
        $pix[] = $id;
        $pix[] = 'tm_component';
        $pix[] = 'tm_portal';
        $tables = [];//定义被操作的表
        /*选择需要操作的表*/
        foreach ($pix as $val){
            $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$val.'%\';');
            $tables = array_merge($tables,$table);
        }
        /*创建临时表，如果已经存在临时表，删除*/
        foreach ($tables as $value){
            $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'tmtemp_'.$value["table_name"].'\';');
            if(!empty($tmp)){
                Db::query('DROP TABLE tmtemp_'.$value["table_name"].' ;');
            }
        }
        foreach ($tables as $value){
            Db::query('create table tmtemp_'.$value["table_name"].' select * from '.$value["table_name"].';');
        }
        $root_path = Env::get('root_path');
        $version = 0;
        // 启动事务
        try{
            //检查是否存在组件
            $component =  Db::table("tm_component")->where(['component_code'=>$id])->find();
            if(!$component){ //如果不存在 则执行新增component
                $componentSql = $this->readSqlFile($root_path."db/".$id."/"."component.sql");
                if(!$componentSql){
                    throw new \Exception('sql文件不正确');
                }
                foreach ($componentSql as $value){
                    Db::query($value);
                }
                //生成访问路径
                $portal1 = $portal =  Db::table("tm_portal")->find();
                $portal = json_decode($portal['portal_value'],true);
                $num = count($portal[0]['children']) - 1;
                $component =  Db::table("tm_component")->where(['component_code'=>$id])->find();
                if($component){
                    $data = [
                        "key"=>$portal[0]['key']."-".$num,
                        "title"=>$component['component_name'],
                        "type"=>"url",
                        "app_code"=>"",
                        "admin_url"=>$component['admin_url'],
                        "index_url"=>"",
                        "category"=>"0",
                        "url"=>"",
                        "thumb"=>$component['component_pic'],
                        "site_code"=>"00000000000000000000000000000000",
                        "webUrl"=>$component['admin_url']
                    ];
                    array_push($portal[0]['children'],$data);
                    //   Db::table("tm_portal")->where(['portal_key'=>$portal1['portal_key']])->update(["portal_value"=>"\'".addslashes(json_encode($portal)).'\']);
                    Db::query('update tm_portal set portal_value = \''.addslashes(json_encode($portal)).'\';');
                }
            }else{
                $version = empty($component['sql_version'])?0:$component['sql_version'];
            }
            $file=scandir(Env::get('root_path')."db/".$id);
            foreach ($file as $val){
                if (is_file($root_path."db/".$id."/".$val)){
                    $val = strstr($val,'.sql',true); //去除.sql
                    if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                        $sql = $this->readSqlFile($root_path."db/".$id."/".$val.".sql");
                        if(!$sql){
                            throw new \Exception('sql文件不正确');
                        }
                        foreach ($sql as $value){
                            if(!empty($value) && strlen($value)>4){  //这儿有个>4是因为最小的sql语句支付也大于4
                                $value = str_replace("MyISAM", "InnoDB", $value);
                             //   dump($value);
                                Db::query($value);
                            }
                        }
                        Db::table("tm_component")->where(['component_code'=>$id])->update(["sql_version"=>$val]);
                    }
                }
            }

            $tables_err = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'tmtemp_%\';');
            /*创建临时表，如果已经存在临时表，删除*/
            if(!empty($tables_err)){
                foreach ($tables_err as $value){
                    $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$value["table_name"].'\';');
                    if(!empty($tmp)){
                        Db::query('DROP TABLE '.$value["table_name"].' ;');
                    }
                }
            }
            return reTmJsonObj(200,'更新成功');
        } catch (\Exception $e) {
            $tables_err = [];//定义被操作的表
            $tables_err[] = ['table_name'=>'tm_component'];
            $tables_err[] = ['table_name'=>'tm_portal'];
            /*选择需要操作的表*/
            foreach ($pix as $val){
                $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$val.'%\';');
                $tables_err = array_merge($tables_err,$table);
            }
            /*创建临时表，如果已经存在临时表，删除*/
            foreach ($tables_err as $value){
                $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$value["table_name"].'\';');
                if(!empty($tmp)){
                    Db::query('DROP TABLE '.$value["table_name"].' ;');
                }
            }
            foreach ($tables as $value){
                Db::query('RENAME TABLE tmtemp_'.$value["table_name"].' TO '.$value["table_name"].';');
            }
            return reTmJsonObj(500,$e->getMessage()."请删除掉sql文件中的注释，锁表,事务，视图，事件等操作。只留下业务需要的sql语句");
        }
    }

    public function readSqlFile($file = ""){
        //读取SQL文件
        if(!is_file($file)){
            return false;
        }
        $sql = file_get_contents($file);
        $sql=removeComment($sql);
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql);
        return $sql;
    }

}