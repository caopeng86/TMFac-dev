<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/2
 * Time: 14:38
 */

namespace app\member\behavior;


use app\extend\controller\Logservice;
use think\facade\Request;

class logBehavior
{
    /**
     * 行为控制器:记录日志
     * @param $params
     * @return bool
     */
    public function run($params)
    {
//        Logservice::writeArray(['inputData'=>Request::param()], '');
        return true;
    }
}