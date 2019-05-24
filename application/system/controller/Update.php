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
use think\facade\Request;
use app\api\model\ConfigModel;
use app\extend\server\MakeZip;

class Update extends \think\Controller {
    protected $sql_file = "";
    protected $sql_arr = [];
    protected $sql = "";
    protected  $ConfigModel = '';
    protected $remote_url = "http://www.360tianma.com";

    public function __construct()
    {
        parent::__construct();
        $this->ConfigModel = new ConfigModel();
       $this->remote_url = config("tm_shop_url");
    }

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

    /*审核自动化执行组件sql*/
    public function indexCompent(){
        set_time_limit(9000) ;
        $id=input("app_id");
        if(!$id){
            return reTmJsonObj(500,'未传入组件id');
        }
        $temp = "tftemp".$this->getRandChar()."_";
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
            $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$temp.$value["table_name"].'\';');
            if(!empty($tmp)){
                Db::query('DROP TABLE '.$temp.$value["table_name"].' ;');
            }
        }
        foreach ($tables as $value){
            Db::query('create table '.$temp.$value["table_name"].' select * from '.$value["table_name"].';');
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
                    throw new \Exception('sql文件不正确,');
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
                $this->sql_file = $val;
                if (is_file($root_path."db/".$id."/".$val)){
                    $val = strstr($val,'.sql',true); //去除.sql
                    if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                        $sql = $this->readSqlFile($root_path."db/".$id."/".$val.".sql");
                        if(!$sql){
                            throw new \Exception("$val.sql文件不正确，");
                        }
                        $this->sql_arr = $sql;
                        foreach ($sql as $value){
                            if(!empty($value) && strlen($value)>4 && !ctype_space($value)){  //这儿有个>4是因为最小的sql语句支付也大于4
                                $value = str_replace("MyISAM", "InnoDB", $value);
                                //   dump($value);
                                $this->sql = $value;
                                Db::query($value);
                            }
                        }
                        Db::table("tm_component")->where(['component_code'=>$id])->update(["sql_version"=>$val]);
                    }
                }
            }

            $tables_err = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$temp.'%\';');
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
                Db::query('RENAME TABLE '.$temp.$value["table_name"].' TO '.$value["table_name"].';');
            }
            $msg = $e->getMessage()."请删除掉sql文件中的注释，锁表,事务，视图，事件等操作。只留下业务需要的sql语句。";
            if(!empty($this->sql_file)){
                $msg.=" \n 报错sql文件".$this->sql_file;
            }
            if(!empty($this->sql_arr)){
                //   $msg.=" \n,报错sql文件中包含的全部sql语句有:".implode(';\n',$this->sql_arr);
            }
            if(!empty($this->sql)){
                $msg.="  \n ,报错sql语句是".$this->sql;
            }
            return reTmJsonObj(500,$msg);
        }
    }

    /**
     * 随机生成字符串
     * @param int $length
     * @return null|string
     */
    protected static function getRandChar($length = 3){
        $str = null;
        $strPol = "abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }


    public function readSqlFile($file = ""){
        //读取SQL文件
        if(!is_file($file)){
            return false;
        }
        $sql = file_get_contents($file);
        $sql=removeComment($sql);
        $sql = str_replace("\r", "\n", $sql);
        $sql = str_replace("\\ufeff", "", $sql);
        $sql = explode(";\n", $sql);
        return $sql;
    }


    /*更新框架 */
    public function handleFrame(){
        set_time_limit(0);
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ["version"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        if(Env::get(SERVER_ENV.'VERSION') == $inputData['version']){
            return reTmJsonObj(500, '当前就是该版本，不需更新', []);
        }

        $condition = [];
        $condition['key'] = ['license'];
        $condition['type'] = 'license';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(601, 'license没有配置', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        if(empty($ConfigList['license'])){
            return reTmJsonObj(601, 'license没有配置', []);
        }

        try {
            $component = "";
            $down_url = "";
            $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>$ConfigList['license'],'domain'=>input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')]);
          //  $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>'74a65b65e411a92afd0cb0044bdba0e3']);
            $data = (array)json_decode($data);
            if($data['status']!=1){
                return reTmJsonObj(500, $data['msg'], []);
            }
            $data = (array)$data['data'];
            $frame_version = $data['app']->frame_version;
            foreach ($frame_version as $value){
                if($inputData['version'] == $value->version){
                    $down_url = $value->down_url;
                }
            }
            if(empty($down_url)){
                return reTmJsonObj(500, '没有改版本', []);
            }

              $this->mkFolder(Env::get('root_path')."installfile"); //检测目录是否存在，不存在就创建
              if(file_exists(Env::get('root_path')."installfile/TM_LAB.zip")){
                  unlink(Env::get('root_path')."installfile/TM_LAB.zip");
              }
              $this->downFile($down_url,Env::get('root_path')."installfile/","TM_LAB.zip");
              if(is_dir(Env::get('root_path')."installfile/TM_LAB")){
                  $this->delDirAndFile(Env::get('root_path')."installfile/TM_LAB");
              }
              $this->unDirZip(Env::get('root_path')."installfile/TM_LAB.zip",Env::get('root_path')."installfile/");
            $rtFormalExecuteFrame = $this->formalExecuteFrame(); //更新框架数据库
            if($rtFormalExecuteFrame){
                /*修改框架版本*/
                $arr =  file(Env::get('root_path').".env");
                foreach ($arr as $k=>$v){
                    if((strpos($v,'SQL_VERSION')!==false || strpos($v,'sql_version')!==false)){
                        $arr[$k] = "SQL_VERSION=".$rtFormalExecuteFrame."\n"; //这儿不能有空格
                    }
                }
                file_put_contents(Env::get('root_path').".env", implode("", $arr));

            }else{
                return reTmJsonObj(500, '框架安装失败，已经自动回滚', []);
            }
            //   unlink(Env::get('root_path')."installfile/TM_LAB.zip");
            if(file_exists(Env::get('root_path')."installfile/TM_LAB/.env")){
                unlink(Env::get('root_path')."installfile/TM_LAB/.env");
            }
            $this->copydir(Env::get('root_path')."installfile/TM_LAB/",Env::get('root_path'));

            /*修改框架版本*/
            $arr =  file(Env::get('root_path').".env");
            foreach ($arr as $k=>$v){
                if((strpos($v,'VERSION')!==false || strpos($v,'version')!==false) && strpos($v,'SQL_VERSION')===false){
                    $arr[$k] = "VERSION=".$inputData['version']."\n"; //这儿不能有空格
                }
            }
            file_put_contents(Env::get('root_path').".env", implode("", $arr));

        } catch (\Exception $e) {
            return reTmJsonObj(500, '部署失败', []);
        }
        return reTmJsonObj(200, '成功', []);

    }


    /*正式环境自动化执行框架sql*/
    public function formalExecuteFrame(){
        $pix = [];  //定义操作表的前缀
        $pix[] = "tm_";
        $tables = [];//定义被操作的表
        /*选择需要操作的表*/
        foreach ($pix as $val){
            $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$val.'%\';');
            $tables = array_merge($tables,$table);
        }
        /*创建临时表，如果已经存在临时表，删除*/
        foreach ($tables as $value){
            $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'ttemp_'.$value["table_name"].'\';');
            if(!empty($tmp)){
                Db::query('DROP TABLE ttemp_'.$value["table_name"].' ;');
            }
        }
        foreach ($tables as $value){
            Db::query('create table ttemp_'.$value["table_name"].' select * from '.$value["table_name"].';');
        }

        $re_val = $version = Env::get(SERVER_ENV.'SQL_VERSION');
        try{
            $file=scandir(Env::get('root_path')."installfile/TM_LAB/db");
            foreach ($file as $val){
                $this->sql_file = $val;
                if (is_file(Env::get('root_path')."installfile/TM_LAB/db/".$val)){
                    $val = strstr($val,'.sql',true); //去除.sql
                    if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                        $re_val = $val;
                        $sql = $this->readSqlFile(Env::get('root_path')."installfile/TM_LAB/db/".$val.".sql");
                        if(!$sql){
                            throw new \Exception("$val.sql文件不正确，");
                        }
                        $this->sql_arr = $sql;
                        foreach ($sql as $value){
                            if(!empty($value) && strlen($value)>4 && !ctype_space($value)){  //这儿有个>4是因为最小的sql语句支付也大于4
                                $value = str_replace("MyISAM", "InnoDB", $value);
                                $this->sql = $value;
                                Db::query($value);
                            }
                        }
                        //    Db::table("tm_component")->where(['component_code'=>$id])->update(["sql_version"=>$val]);
                    }
                }
            }
            return $re_val;
        } catch (\Exception $e) {

            $tables_err = [];//定义被操作的表
            /*选择需要操作的表*/
            foreach ($pix as $val){
                $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$val.'%\';');
                $tables_err = array_merge($tables_err,$table);
            }
            /*删除*/
            foreach ($tables_err as $value){
                $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$value["table_name"].'\';');
                if(!empty($tmp)){
                    Db::query('DROP TABLE '.$value["table_name"].' ;');
                }
            }

            foreach ($tables as $value){

                $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'ttemp_'.$value["table_name"].'\';');
                if(!empty($tmp)){
                    Db::query('RENAME TABLE ttemp_'.$value["table_name"].' TO '.$value["table_name"].';');
                }
            }
            return false;
        }
    }


    /*没有目录就创建目录*/
    protected  function mkFolder($path,$mode = 0775)
    {
        if(!is_readable($path))
        {
            is_file($path) or mkdir($path,$mode);
        }
    }

    /*下载远程文件*/
    protected function downFile($url,$path,$fileName){
        $file=file_get_contents($url);
        file_put_contents($path.$fileName,$file);
    }

    /*解压zip文件*/
    protected function unDirZip($zipFile,$path){
        try{
            $zip = new \ZipArchive();
            if ($zip->open($zipFile) === TRUE) {//中文文件名要使用ANSI编码的文件格式
                $zip->extractTo($path);//提取全部文件
                //$zip->extractTo('/my/destination/dir/', array('pear_item.gif', 'testfromfile.php'));//提取部分文件
                $zip->close();
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e){
            return false;
        }
    }

    /*压缩文件夹成zip*/
    protected function zipDir($zipName,$basePath){
        try{
            if(!is_dir($basePath))
            {
                return false;
            }
            if(!file_exists($zipName))
            {
                $myfile = fopen($zipName, "w");
                fclose($myfile);
            }

            $zip = new \ZipArchive();
            //参数1:zip保存路径，参数2：ZIPARCHIVE::CREATE没有即是创建
            if(!$zip->open($zipName,\ZIPARCHIVE::CREATE))
            {
                return false;
            }
            $this->createZip(opendir($basePath),$zip,$basePath);
            $zip->close();
        }catch (\Exception $e){
            return false;
        }
    }


    /*压缩多级目录
    $openFile:目录句柄
    $zipObj:Zip对象
    $sourceAbso:源文件夹路径
*/
    protected function createZip($openFile,$zipObj,$sourceAbso,$newRelat = '')
    {
        try{
            while(($file = readdir($openFile)) != false)
            {
                if($file=="." || $file=="..")
                    continue;

                /*源目录路径(绝对路径)*/
                $sourceTemp = $sourceAbso.'/'.$file;
                /*目标目录路径(相对路径)*/
                $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;
                if(is_dir($sourceTemp))
                {
                    //echo '创建'.$newTemp.'文件夹<br/>';
                    $zipObj->addEmptyDir($newTemp);/*这里注意：php只需传递一个文件夹名称路径即可*/
                    $this->createZip(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);
                }
                if(is_file($sourceTemp))
                {
                    //echo '创建'.$newTemp.'文件<br/>';
                    $zipObj->addFile($sourceTemp,$newTemp);
                }
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    /*删除文件夹*/
    protected function delDirAndFile( $dirName )
    {
        try{
            if ( $handle = opendir( "$dirName" ) ) {
                while ( false !== ( $item = readdir( $handle ) ) ) {
                    if ( $item != "." && $item != ".." ) {
                        if ( is_dir( "$dirName/$item" ) ) {
                            $this->delDirAndFile( "$dirName/$item" );
                        } else {
                            unlink( "$dirName/$item" );
                        }
                    }
                }
                closedir( $handle );
                rmdir( $dirName );
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 复制文件夹
     * @param $source
     * @param $dest
     */
    protected function copydir($source, $dest)
    {
        if (!file_exists($dest)) mkdir($dest);
        $handle = opendir($source);
        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') continue;
            $_source = $source . '/' . $item;
            $_dest = $dest . '/' . $item;
            if (is_file($_source)) copy($_source, $_dest);
            if (is_dir($_source)) $this->copydir($_source, $_dest);
        }
        closedir($handle);
    }

    /*正式项目包部署组件，包括代码和sql的部署*/
    public function handleCompent(){
        set_time_limit(0);
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ["spec_sn"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition = [];
        $condition['key'] = ['license'];
        $condition['type'] = 'license';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(601, 'license没有配置', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        if(empty($ConfigList['license'])){
            return reTmJsonObj(601, 'license没有配置', []);
        }

        try {
            $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>$ConfigList['license'],'domain'=>input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')]);
           // $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>'74a65b65e411a92afd0cb0044bdba0e3']);
            $data = (array)json_decode($data);
            if($data['status']!=1){
                return reTmJsonObj(500, $data['msg'], []);
            }
            $data = (array)$data['data'];
            $nav = $data['nav'];
            $component =  "";
            foreach ($nav as $value){
                if($inputData['spec_sn'] == $value->goods->spec_sn){
                    $component =  $value;
                }
            }
            if(!$component){
                return reTmJsonObj(601, '没有购买该组件或已经过期', []);
            }
            $this->mkFolder(Env::get('root_path')."installfile"); //检测目录是否存在，不存在就创建
            if(file_exists(Env::get('root_path')."installfile/".$component->goods->spec_sn.".zip")){
                unlink(Env::get('root_path')."installfile/".$component->goods->spec_sn.".zip");
            }
            $this->downFile($component->goods->php_url,Env::get('root_path')."installfile/",$component->goods->spec_sn.".zip");
            if(is_dir(Env::get('root_path')."installfile/".$component->goods->spec_sn)){
                $this->delDirAndFile(Env::get('root_path')."installfile/".$component->goods->spec_sn);
            }
            $this->unDirZip(Env::get('root_path')."installfile/".$component->goods->spec_sn.".zip",Env::get('root_path')."installfile/");
            $this->zipDir(Env::get('root_path').'installfile/'.$component->goods->spec_sn."/".$component->goods->spec_sn.'.zip',Env::get('root_path').'installfile/'.$component->goods->spec_sn."/".$component->goods->spec_sn."/");
            /*备份组件代码*/
            if(is_dir(Env::get('app_path')."/".$component->goods->spec_sn)){
                if(file_exists(Env::get('root_path').'installfile/'."beifen".$component->goods->spec_sn.'.zip')){
                    unlink(Env::get('root_path').'installfile/'."beifen".$component->goods->spec_sn.'.zip');
                }
                $this->zipDir(Env::get('root_path').'installfile/'."beifen".$component->goods->spec_sn.'.zip',Env::get('app_path')."/".$component->goods->spec_sn."/");
            }
            $this->unDirZip(Env::get('root_path').'installfile/'.$component->goods->spec_sn."/".$component->goods->spec_sn.'.zip',Env::get('app_path')."/".$component->goods->spec_sn);

            /*开始操作数据库*/
            if(is_dir(Env::get('root_path')."installfile/".$component->goods->spec_sn."/db")){
                $this->zipDir(Env::get('root_path')."installfile/".$component->goods->spec_sn.'/db.zip',Env::get('root_path')."installfile/".$component->goods->spec_sn."/db/");
                // $this->mkFolder(Env::get('root_path')."/db/".$component->goods->spec_sn);
                $this->unDirZip(Env::get('root_path')."installfile/".$component->goods->spec_sn.'/db.zip',Env::get('root_path')."/db/".$component->goods->spec_sn);
                if(!$this->formalExecuteCompent($component->goods->spec_sn,$component)){
                    if(file_exists(Env::get('root_path').'installfile/beifen'.$component->goods->spec_sn.'.zip')){
                        $this->unDirZip(Env::get('root_path').'installfile/beifen'.$component->goods->spec_sn.'.zip',Env::get('app_path')."/".$component->goods->spec_sn);
                    }
                    return reTmJsonObj(500, '部署失败', []);
                }
            }
        } catch (\Exception $e) {
            return reTmJsonObj(500, '部署失败', []);
        }
        return reTmJsonObj(200, '成功', []);
    }


    /*正式环境自动化执行组件sql*/
    public function formalExecuteCompent($id,$componentGood){
        if(empty($id)){
            return false;
        }
        $pix = [];  //定义操作表的前缀
        $pix[] = $id;
        $tables = [];//定义被操作的表
        /*选择需要操作的表*/
        foreach ($pix as $val){
            $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$val.'%\';');
            $tables = array_merge($tables,$table);
        }
        /*创建临时表，如果已经存在临时表，删除*/
        foreach ($tables as $value){
            $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'tftemp_'.$value["table_name"].'\';');
            if(!empty($tmp)){
                Db::query('DROP TABLE tftemp_'.$value["table_name"].' ;');
            }
        }
        foreach ($tables as $value){
            Db::query('create table tftemp_'.$value["table_name"].' select * from '.$value["table_name"].';');
        }
        $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$id.'_tm_component\';');
        if(!empty($tmp)){
            Db::query('DROP TABLE '.$id.'_tm_component ;');
        }
        Db::query('create table '.$id.'_tm_component  select * from tm_component;');
        $root_path = Env::get('root_path');
        $version = 0;
        try{
            //检查是否存在组件
            $component =  Db::table("tm_component")->where(['component_code'=>$id])->find();
            if(!$component){ //如果不存在 则执行新增component
                //生成访问路径
                $portal1 = $portal =  Db::table("tm_portal")->find();
                $portal = json_decode($portal['portal_value'],true);
                $num = count($portal[0]['children']) - 1;
                $data = [
                    "key"=>$portal[0]['key']."-".$num,
                    "title"=>$componentGood->nav_name,
                    "type"=>"url",
                    "app_code"=>"",
                    "admin_url"=>$componentGood->goods->url,
                    "index_url"=>"",
                    "category"=>"0",
                    "url"=>"",
                    "thumb"=>$componentGood->goods->original_img,
                    "site_code"=>"00000000000000000000000000000000",
                    "webUrl"=>$componentGood->goods->url
                ];
                array_push($portal[0]['children'],$data);
                Db::query('update tm_portal set portal_value = \''.addslashes(json_encode($portal)).'\';');

                $data = [
                    "component_code"=>$componentGood->goods->spec_sn,
                    "component_name"=>$componentGood->goods->goods_name,
                    "component_key"=>$componentGood->goods->spec_sn,
                    "developer_code"=>$componentGood->goods->goods_id,
                    "access_key"=>$componentGood->goods->spec_sn,
                    "secret_key"=>$componentGood->goods->spec_sn,
                    "index_url"=>$componentGood->goods->front_end_entry,
                    "index_version"=>$componentGood->goods->version,
                    "admin_url"=>$componentGood->goods->url,
                    "admin_version"=>$componentGood->goods->version,
                    "app_code"=>"",
                    "create_time"=>time(),
                    "company_name"=>$componentGood->store->store_name,
                    "address"=>"",
                    "tel"=>"",
                    "description"=>"",
                    "linkman"=>$componentGood->goods->front_end_entry,
                    "note"=>"",
                    "component_pic"=>"",
                    "ios_info"=>$componentGood->goods->ios_entry,
                    "android_info"=>$componentGood->goods->android_entry,
                    "sql_version"=>0,
                ];
                Db::table("tm_component")->insertGetId($data);
            }else{
                $version = empty($component['sql_version'])?0:$component['sql_version'];
            }
            $file=scandir(Env::get('root_path')."db/".$id);
            foreach ($file as $val){
                $this->sql_file = $val;
                if (is_file($root_path."db/".$id."/".$val)){
                    $val = strstr($val,'.sql',true); //去除.sql
                    if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                        $sql = $this->readSqlFile($root_path."db/".$id."/".$val.".sql");
                        if(!$sql){
                            throw new \Exception("$val.sql文件不正确，");
                        }
                        $this->sql_arr = $sql;
                        foreach ($sql as $value){
                            if(!empty($value) && strlen($value)>4 && !ctype_space($value)){  //这儿有个>4是因为最小的sql语句支付也大于4
                                $value = str_replace("MyISAM", "InnoDB", $value);
                                //   dump($value);
                                $this->sql = $value;
                                Db::query($value);
                            }
                        }
                        Db::table("tm_component")->where(['component_code'=>$id])->update(["sql_version"=>$val]);
                    }
                }
            }
            Db::table("tm_component")->where(['component_code'=>$id])->update(["index_version"=>$componentGood->goods->version,'admin_version'=>$componentGood->goods->version]);
            return true;
        } catch (\Exception $e) {
            $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$id.'_tm_component\';');
            if(!empty($tmp)){
                $component =  Db::table($id."_tm_component")->where(['component_code'=>$id])->find();
                if(empty($component)){
                    Db::table("tm_component")->where(['component_code'=>$id])->delete();
                }else{
                    Db::table("tm_component")->where(['component_code'=>$id])->update(["sql_version"=>$component['sql_version'],"component_name"=>$component['component_name'],
                        "index_url"=>$component['index_url'],"index_version"=>$component['index_version'],"admin_url"=>$component['admin_url'],
                        "admin_version"=>$component['admin_version'],"create_time"=>$component['create_time'],"linkman"=>$component['linkman'],
                        "ios_info"=>$component['ios_info'],"android_info"=>$component['android_info']]);
                }
            }

            $tables_err = [];//定义被操作的表
            /*选择需要操作的表*/
            foreach ($pix as $val){
                $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$val.'%\';');
                $tables_err = array_merge($tables_err,$table);
            }
            /*删除*/
            foreach ($tables_err as $value){
                $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$value["table_name"].'\';');
                if(!empty($tmp)){
                    Db::query('DROP TABLE '.$value["table_name"].' ;');
                }
            }

            foreach ($tables as $value){
                $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'tftemp_'.$value["table_name"].'\';');
                if(!empty($tmp)){
                    Db::query('RENAME TABLE tftemp_'.$value["table_name"].' TO '.$value["table_name"].';');
                }
            }
            $msg = "";
            if(!empty($this->sql_file)){
                $msg.=" \n 报错sql文件".$this->sql_file;
            }
            if(!empty($this->sql)){
                $msg.="  \n ,报错sql语句是".$this->sql;
            }
            file_put_contents("zhidong.txt",$msg);
            return false;
        }
    }

    /*回退组件版本，所有数据都将回退会上一个版本*/
    public function backCompent(){
        set_time_limit(0);
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ["spec_sn"];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        try {
            $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'tftemp_'.$inputData['spec_sn'].'%\';');
            $tmp_tm_component = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$inputData['spec_sn'].'_tm_component\';');
            if(empty($tmp) || empty($tmp_tm_component) || !file_exists(Env::get('root_path')."installfile/beifen".$inputData['spec_sn'].".zip")){
                return reTmJsonObj(500, '当前是第一个版本，不能回退', []);
            }


            $component =  Db::table($inputData['spec_sn']."_tm_component")->where(['component_code'=>$inputData['spec_sn']])->find();
            if(empty($component)){
                return reTmJsonObj(500, '当前是第一个版本，不能回退', []);
            }
            Db::table("tm_component")->where(['component_code'=>$inputData['spec_sn']])->update(["sql_version"=>$component['sql_version'],"component_name"=>$component['component_name'],
                "index_url"=>$component['index_url'],"index_version"=>$component['index_version'],"admin_url"=>$component['admin_url'],
                "admin_version"=>$component['admin_version'],"create_time"=>$component['create_time'],"linkman"=>$component['linkman'],
                "ios_info"=>$component['ios_info'],"android_info"=>$component['android_info']]);

            $tables_err = [];//定义被操作的表
            /*选择需要操作的表*/

            $table = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$inputData['spec_sn'].'%\';');
            $tables_err = array_merge($tables_err,$table);

            /*删除*/
            foreach ($tables_err as $value){
                $tmp = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \''.$value["table_name"].'\';');
                if(!empty($tmp)){
                    Db::query('DROP TABLE '.$value["table_name"].' ;');
                }
            }

            $tables = Db::query('select table_name from information_schema.tables where table_schema=\''.Env::get(SERVER_ENV.'DATABASE_NAME').'\' and table_name LIKE \'tftemp_'.$inputData['spec_sn'].'%\';');
            foreach ($tables as $value){
                Db::query('RENAME TABLE '.$value["table_name"].' TO '.substr($value["table_name"],7).';');
            }
            $this->unDirZip(Env::get('root_path').'installfile/beifen'.$inputData['spec_sn'].'.zip',Env::get('app_path')."/".$inputData['spec_sn']);
            return reTmJsonObj(200, '成功', []);
        } catch (\Exception $e) {
            return reTmJsonObj(500, '部署失败', []);
        }
        return reTmJsonObj(200, '成功', []);
    }

    /*获取组件列表*/
    public function getAppInfo()
    {
        $condition = [];
        $condition['key'] = ['license'];
        $condition['type'] = 'license';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(601, 'license没有配置', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        if(empty($ConfigList['license'])){
            return reTmJsonObj(601, 'license没有配置', []);
        }
           $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>$ConfigList['license'],'domain'=>input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')]);
        //$data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>'74a65b65e411a92afd0cb0044bdba0e3']);
        $data = (array)json_decode($data);
        if($data['status']!=1){
            return reTmJsonObj(500, $data['msg'], []);
        }
        $data = (array)$data['data'];
        $nav_list = [];
        $tm_component =  Db::table("tm_component")->select();
        if(!empty($data['nav']) && is_array($data['nav'])){
            foreach ($data['nav'] as $value){
                $value = (array)$value;
                $value['goods'] = (array)$value['goods'];
                $value['goods']['now_version'] = 0;
                $value['store'] = (array)$value['store'];
                foreach ($tm_component as $valu){
                    if($value['goods']['spec_sn'] == $valu['component_key']){
                        $value['goods']['now_version'] = empty($valu['index_version'])?0:$valu['index_version'];
                        $value['goods']['ios_entry'] = $valu['ios_info'];
                        $value['goods']['android_entry'] = $valu['android_info'];
                        $value['goods']['front_end_entry'] = $valu['linkman'];
                    }
                }
                array_push($nav_list,$value);
            }
        }
        $frame = [];
        $frame['app_name'] = $data['app']->app_name;
        $frame['app_keys'] = $data['app']->app_keys;
        $frame['frame_version'] = $data['app']->frame_version;
        $frame['now_version'] = Env::get(SERVER_ENV.'VERSION');
        $frame['application_license'] = $data['application_license'];
        return reTmJsonObj(200, '成功', ["nav_list"=>$nav_list,"frame"=>$frame]);
    }

    /*更新配置文件*/
    public function updateConfig(){
        $condition = [];
        $condition['key'] = ['license'];
        $condition['type'] = 'license';
        $ConfigList = $this->ConfigModel->getConfigList($condition);
        if($ConfigList === false){
            return reTmJsonObj(601, 'license没有配置', []);
        }
        $ConfigList = $this->ConfigModel->ArrayToKey($ConfigList);
        if(empty($ConfigList['license'])){
            return reTmJsonObj(601, 'license没有配置', []);
        }
           $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>$ConfigList['license'],'domain'=>input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')]);
      //  $data = tmBaseHttp(config("tm_shop_url")."/api/License/getAppInfo",['license'=>'74a65b65e411a92afd0cb0044bdba0e3']);
        $data = (array)json_decode($data);
        if($data['status']!=1){
            return reTmJsonObj(500, $data['msg'], []);
        }
        $data = (array)$data['data'];

        $path = Env::get('root_path').'appconf/'.$data['navConfig'];
        //如果传入base64编码数据则先执行覆盖操作
        if(is_file($path)){
            file_put_contents($path, $data['file']);
        }else{
            file_put_contents($path, $data['file']);
            chmod($path, 0777);
        }
        Db::table("tm_config")->where(['key'=>'ali_check_template_code'])->update(['value'=>$data['app']->alimsg->theme_code]);
        Db::table("tm_config")->where(['key'=>'ali_sms_key_id'])->update(['value'=>$data['app']->alimsg->Access_key_id]);
        Db::table("tm_config")->where(['key'=>'ali_key_secret'])->update(['value'=>$data['app']->alimsg->Access_key_secret]);
        Db::table("tm_config")->where(['key'=>'ali_sign_name'])->update(['value'=>$data['app']->alimsg->Sign_name]);
        Db::table("tm_config")->where(['key'=>'Jpush_key'])->update(['value'=>$data['app']->jpush->AppKey]);
        Db::table("tm_config")->where(['key'=>'Jpush_secret'])->update(['value'=>$data['app']->jpush->Master_secret]);
        return reTmJsonObj(200, '成功', []);

    }


}