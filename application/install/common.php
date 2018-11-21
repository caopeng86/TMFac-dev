<?php
// 检测环境是否支持可写
define('IS_WRITE', true);

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = array(
        'os' => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php' => array('PHP版本', '5.6.0', '5.6+', PHP_VERSION, 'success'),
        'upload' => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd' => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'disk' => array('磁盘空间', '30M', '不限制', '未知', 'success'),
    );

    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session('error', true);
    }

    //附件上传检测
    if (@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(Env::get('root_path')) / (1024 * 1024)) . 'M';
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = array(
        array('dir', '可写', 'success', 'appconf'),
        array('dir', '可写', 'success', 'application'),
        array('dir', '可写', 'success', 'db'),
        array('dir', '可写', 'success', 'config'),
        array('dir', '可写', 'success', 'runtime'),
        array('dir', '可写', 'success', 'uploads'),

    );

    foreach ($items as &$val) {
        if('dir' == $val[0]){
            if(!is_writable(Env::get('root_path') . $val[3])) {
                if(is_dir(Env::get('root_path') . $val[3])) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
            if(file_exists(Env::get('root_path') . $val[3])) {
                if(!is_writable(Env::get('root_path') . $val[3])) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {

                if(!is_writable(dirname(Env::get('root_path') . $val[3]))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }
    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func()
{
    $items = array(
        array('pdo', '支持', 'success', '类'),
        array('pdo_mysql', '支持', 'success', '模块'),
        array('file_get_contents', '支持', 'success', '函数'),
        array('mb_strlen', '支持', 'success', '函数'),
    );

    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0]))
            || ('模块' == $val[3] && !extension_loaded($val[0]))
            || ('函数' == $val[3] && !function_exists($val[0]))
        ) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config)
{
    if (is_array($config)) {
        $config['servername']=strtoupper(str_replace('.','_',$_SERVER['SERVER_NAME']));
        $config['domain']='http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
        $config['internal_api_host']=$config['domain'];
        $config['platform_domain']=$config['domain'];
        //读取配置内容
        $conf = file_get_contents(Env::get('root_path') . 'public/tpl/env');
        //替换配置项
        foreach ($config as $name => $value) {
            $name=strtoupper($name);
            $conf = str_replace("[{$name}]", $value, $conf);
        }


        file_put_contents(Env::get('app_path') . '/install.lock', 'ok');
        //写入应用配置文件
        if (file_put_contents(Env::get('root_path') . '.env', $conf)) {
            show_msg('配置文件写入成功');
        } else {
            show_msg('配置文件写入失败！', 'error');
            session('error', true);
        }
        return '';
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

function create_tables_multi($db=null, $prefix = '',$dir = 'db'){
    $file=scandir(Env::get('root_path').$dir);
    $sql_version = '';
    $dir_array = [];
    foreach ($file as $sql){  //当前sql文件处理
        if (is_file(Env::get('root_path').$dir.'/'.$sql)){
            create_tables($db,$prefix,$dir.'/'.$sql);
            if(is_numeric(strstr($sql,'.sql',true))){
                $sql_version = strstr($sql,'.sql',true);
            }
        }elseif($sql != '.' && $sql != '..' && is_dir(Env::get('root_path').$dir.'/'.$sql)){
            $dir_array[] = $sql;
        }
    }
    if(count($dir_array) > 0){  //递归文件夹处理
        foreach ($dir_array as $sql){
            $version = create_tables_multi($db,$prefix,$dir.'/'.$sql);
            write_component($db,$sql,$version);
        }
    }
    return $sql_version;
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = '',$sqlFile='db/backup/cl01kkdy_6RrqH.sql',$is_show_msg = true)
{

    //读取SQL文件
    if(!is_file(Env::get('root_path') . $sqlFile)){
        show_msg(Env::get('root_path') . $sqlFile.' 安装数据库文件不存在');
        exit;
    }
    $sql = file_get_contents(Env::get('root_path') . $sqlFile);
    $sql=removeComment($sql);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

//    //替换表前缀
//    $orginal = 'tm_';
//    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
    //开始安装
    if($is_show_msg){
        show_msg('开始安装数据库...');
    }
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) continue;
        if (substr($value, 0, 12) == 'CREATE TABLE') {
           // $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $name='';
            preg_match('|EXISTS `(.*?)`|',$value,$outValue1);
            preg_match('|TABLE `(.*?)`|',$value,$outValue2);
            if(isset($outValue1[1]) && !empty($outValue1[1])){
                $name=$outValue1[1];
            }
            if(isset($outValue2[1]) && !empty($outValue2[1])){
                $name=$outValue2[1];
            }
            $msg = "创建数据表{$name}";
            if (false !== $db->execute($value) && $is_show_msg) {
                show_msg($msg . '...成功');
            } else {
                if($is_show_msg){
                    show_msg($msg . '...失败！', 'error');
                    session('error', true);
                }
            }
        } else {
            $db->execute($value);
        }
    }
}

/**
 * 写入组件版本
 */
function write_component($db,$component_code,$version){
    $db->execute('update tm_component set `sql_version` = \''.$version.'\' where component_code = \''.$component_code.'\';');
}


function register_administrator($db, $prefix, $admin)
{
    show_msg('开始注册创始人帐号...');
    $password = md5(md5($admin['password']));
    $userCode=createCode();
    $sql = "INSERT INTO `[PREFIX]user` (`user_id`,`user_code`,`user_name`,`real_name`,`password`,`branch_id`,`is_branch_admin`,`status`,`email`,`create_time`,`head_pic`) VALUES " .
        "('1', '[USER_CODE]', '[USER_NAME]','[REAL_NAME]','[PASS]','0','[IS_BRANCH_ADMIN]','0','[EMAIL]','[CREATE_TIME]','[HEAD_PIC]');";
    $sql = str_replace(
        array('[PREFIX]', '[USER_CODE]', '[USER_NAME]','[REAL_NAME]','[PASS]','[IS_BRANCH_ADMIN]','[EMAIL]','[CREATE_TIME]','[HEAD_PIC]'),
        array($prefix,$userCode, $admin['username'],'超管',$password, 1,$admin['email'],time(), '/uploads/default/20180515/c4d86ef4c589701e41760a07ecbfea10.jpg'),
        $sql);
    $db->execute($sql);

    $sql_role="INSERT INTO `[PREFIX]role_user` (`role_code`, `user_code`) VALUES
('1', '[USER_CODE]');";
    $sql_role=str_replace(
        array('[PREFIX]', '[USER_CODE]'),
        array($prefix,$userCode),
        $sql_role);
    $db->execute($sql_role);
    show_msg('创始人帐号注册完成！');
}


/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = 'primary')
{
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";

    //ob_flush();
    //flush();
}

/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars = str_shuffle($chars);
    return substr($chars, 0, 40);
}

