<?php

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = '',$sqlFile='db/backup/cl01kkdy_6RrqH.sql',$is_show_msg = true)
{

    //读取SQL文件
    if(!is_file(Env::get('root_path') . $sqlFile)){
        return false;
    }
    $sql = file_get_contents(Env::get('root_path') . $sqlFile);
    $sql=removeComment($sql);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    //开始安装
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) continue;
        if (substr($value, 0, 12) == 'CREATE TABLE') {
            // $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            preg_match('|EXISTS `(.*?)`|',$value,$outValue1);
            preg_match('|TABLE `(.*?)`|',$value,$outValue2);
            try {
                $db->execute($value);
            } catch (Exception $e) {
                echo json_encode(['code'=>500,'msg'=>$e->getMessage(),'sql'=>$value]);
                exit();
            }
        } else {
            try {
                $db->execute($value);
            } catch (Exception $e) {
                echo json_encode(['code'=>500,'msg'=>$e->getMessage(),'sql'=>$value]);
                exit();
            }
        }
    }
}

/**
 * 去除PHP代码注释
 * @param string $content 代码内容
 * @return string 去除注释之后的内容
 */
function removeComment($content){
    $filterArr = [
        '/(\/\*.*?\*\/.*?\n)',
        '(-- .*?\n)',
        '(--)',
//        '(SET .*?\n)',
        '(START TRANSACTION;)',
        '(COMMIT;)',
        '(LOCK TABLES `.+` WRITE;\n)',
        '(UNLOCK TABLES;\n)/s'
    ];
    $reg= implode('|',$filterArr); //--
//    $content=preg_replace('/.*?TRANSACTION.*?\n/i','',$content);
//    $content=preg_replace('/^COMMIT.*?\n/i','',$content);
    return preg_replace($reg, '', str_replace(array("\r\n", "\r"), "\n", $content));
}

/**
 * 循环处理
 */
function create_tables_multi($db,$file_path,$version,$is_tm = false){
    $dir_array = [];
    $file=scandir(Env::get('root_path').$file_path);
    $now_sql_version = $version;
    foreach ($file as $val){
        if (is_file(Env::get('root_path').$file_path.'/'.$val)){
            $val = strstr($val,'.sql',true); //去除.sql
            if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                create_tables($db,'',$file_path.'/'.$val.'.sql',false);
                if($is_tm){
                    $env = file_get_contents(Env::get('root_path') . '.env');
                    $env = str_replace("SQL_VERSION=".$now_sql_version, "SQL_VERSION=".$val, $env);
                    file_put_contents(Env::get('root_path') . '.env', $env);
                }
                //记录当前版本
                $now_sql_version = $val;
            }
        }elseif($val != '.' && $val != '..' && is_dir(Env::get('root_path').$file_path.'/'.$val)){
            $dir_array[] = $val;
        }
    }
    if(count($dir_array) > 0){  //递归文件夹处理
        foreach ($dir_array as $sql){
            $dir_version = 0;
            //检查是否存在组件
            $component = $db->query('select `sql_version` from tm_component where `component_code` = \''.$sql.'\' limit 1;');
            if(!$component){ //如果不存在 则执行新增component
                create_tables($db,'',$file_path.'/'.$sql.'/component.sql',false);
                //生成访问路径
                $portal = $db->query('select * from tm_portal limit 1;');
                $portal = json_decode($portal[0]['portal_value'],true);
                $num = count($portal[0]['children']) - 1;
                $component = $db->query('select * from tm_component where `component_code` = \''.$sql.'\' limit 1;');
                if($component){
                    $data = [
                        "key"=>$portal[0]['key']."-".$num,
                        "title"=>$component[0]['component_name'],
                        "type"=>"url",
                        "app_code"=>"",
                        "admin_url"=>$component[0]['admin_url'],
                        "index_url"=>"",
                        "category"=>"0",
                        "url"=>"",
                        "thumb"=>$component[0]['component_pic'],
                        "site_code"=>"00000000000000000000000000000000",
                        "webUrl"=>$component[0]['admin_url']
                    ];
                    array_push($portal[0]['children'],$data);
                    $db->query('update tm_portal set portal_value = \''.addslashes(json_encode($portal)).'\';');
                }
            }else{
                $dir_version = $component[0]['sql_version'];
            }
            $now_dir_version = create_tables_multi($db,$file_path.'/'.$sql,$dir_version);
            write_component($db,$sql,$now_dir_version);
        }
    }
    return $now_sql_version;
}


/**
 * 循环处理
 */
function create_tables_multi_compent($db,$file_path,$version){
    $dir_array = [];
    $file=scandir(Env::get('root_path').$file_path);
    $now_sql_version = $version;
    foreach ($file as $val){
        if (is_file(Env::get('root_path').$file_path.'/'.$val)){
            $val = strstr($val,'.sql',true); //去除.sql
            if(is_numeric($val) && ($val > $version)){ //判断是否有新的版本
                create_tables($db,'',$file_path.'/'.$val.'.sql',false);
                //记录当前版本
                $now_sql_version = $val;
            }
        }elseif($val != '.' && $val != '..' && is_dir(Env::get('root_path').$file_path.'/'.$val)){
            $dir_array[] = $val;
        }
    }
    if(count($dir_array) > 0){  //递归文件夹处理
        foreach ($dir_array as $sql){
            $dir_version = 0;
            //检查是否存在组件
            $component = $db->query('select `sql_version` from tm_component where `component_code` = \''.$sql.'\' limit 1;');
            if(!$component){ //如果不存在 则执行新增component
                create_tables($db,'',$file_path.'/'.$sql.'/component.sql',false);
                //生成访问路径
                $portal = $db->query('select * from tm_portal limit 1;');
                $portal = json_decode($portal[0]['portal_value'],true);
                $num = count($portal[0]['children']) - 1;
                $component = $db->query('select * from tm_component where `component_code` = \''.$sql.'\' limit 1;');
                if($component){
                    $data = [
                        "key"=>$portal[0]['key']."-".$num,
                        "title"=>$component[0]['component_name'],
                        "type"=>"url",
                        "app_code"=>"",
                        "admin_url"=>$component[0]['admin_url'],
                        "index_url"=>"",
                        "category"=>"0",
                        "url"=>"",
                        "thumb"=>$component[0]['component_pic'],
                        "site_code"=>"00000000000000000000000000000000",
                        "webUrl"=>$component[0]['admin_url']
                    ];
                    array_push($portal[0]['children'],$data);
                    $db->query('update tm_portal set portal_value = \''.addslashes(json_encode($portal)).'\';');
                }
            }else{
                $dir_version = $component[0]['sql_version'];
            }
            $now_dir_version = create_tables_multi($db,$file_path.'/'.$sql,$dir_version);
            write_component($db,$sql,$now_dir_version);
        }
    }
    return $now_sql_version;
}
/*
 * 写入组件版本
 */
function write_component($db,$component_code,$version){
    $db->execute('update tm_component set `sql_version` = \''.$version.'\' where component_code = \''.$component_code.'\';');
}