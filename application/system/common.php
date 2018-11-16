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
            $db->execute($value);
        } else {
            $db->execute($value);
        }
    }
}

/**
 * 去除PHP代码注释
 * @param string $content 代码内容
 * @return string 去除注释之后的内容
 */
function removeComment($content){
    $reg="/(\/\*.*?\*\/.*?\n)|(-- .*?\n)|(--)/s"; //--
//    $content=preg_replace('/.*?TRANSACTION.*?\n/i','',$content);
//    $content=preg_replace('/^COMMIT.*?\n/i','',$content);
    return preg_replace($reg, '', str_replace(array("\r\n", "\r"), "\n", $content));
}