<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
/*签名验证*/
require __DIR__ . '/../thinkphp/Tminit.php';
$tminit = new Tminit();
$inithead = $tminit->initExecute();
if(!$inithead){
    echo json_encode([
        'code'=>551,
        'data'=>[],
        'msg'=>"签名验证失败",
        'tmcode'=>1
    ]);
    die;
}

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';



// 支持事先使用静态方法设置Request对象和Config对象

//允许跨域访问
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, token");

//定义环境，用于区别线上和开发环境，方便切换
define('SERVER_ENV',strtoupper(str_replace('.','_',$_SERVER['SERVER_NAME'])).'_');

//天马工场 model前缀
define('TM_PREFIX','tm_');

use app\extend\controller\Logservice;
Logservice::seedMsec('index');

// 执行应用并响应
Container::get('app')->run()->send();

Logservice::writeArray("================index end==============", '接口执行结束',1,"index");
