<?php
/**
 * Created by PhpStorm.
 * Member: Administrator
 * Date: 2017/12/13
 * Time: 12:17
 */

namespace app\member\controller;



use app\member\model\MemberModel;
use think\Controller;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
}