<?php
//***********************************************************
//*by yhb
//*
//*Software: 简易定时
//*
//***********************************************************

namespace app\timingtask\controller;

use think\Controller;
use think\facade\Request;
// use think\facade\Config;
use think\Queue;

// require_once "Base.php";

class Selecttimer extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //***********************************************************
    //*
    //*Software: 查看定时任务
    //* companycode 公司或组件标识
    //*********************************************************** 
    public function get_timer(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];           
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret) return reTmJsonObj(500, $msg, []);
        // if(empty($inputData['companycode'])) return reTmJsonObj(500,'请按数据类型检查后再请求',[]);

        $file=getTaskfile()['task_file'];//获取配置记录文件 
        if(!file_exists($file))  file_put_contents($file,null); //fopen($file,null)
        $json=file_get_contents($file);
        $arr=json_decode($json,true);
        if(!empty($arr)&&!is_array($arr)) return reTmJsonObj(500,'原定时任务有错误',[]);
        if(empty($arr)) return reTmJsonObj(500,'战无定时任务',[]);

        if(isset($inputData['companycode']) || !empty($inputData['companycode'])){
            foreach ($arr[$inputData['companycode']] as $key => $value) {
                $value['task_id']=$key;
                unset($arr[$inputData['companycode']][$key]);
                $arr[$inputData['companycode']][]= $value;
                // $key=$value;
            }
            if($arr[$inputData['companycode']]) return reTmJsonObj(200,'获取定时任务成功',[$inputData['companycode']=>$arr[$inputData['companycode']]]);
        }else{
            foreach ($arr as $key => $value) {
                foreach ($value as $k => $v) {
                    $v['task_id']=$k;
                    unset($arr[$key][$k]);
                    $arr[$key][]= $v;
                }
            }
            return reTmJsonObj(200,'获取定时任务成功',$arr);
        }
       
    }

    // require_once $_SERVER['DOCUMENT_ROOT'] . "./../../Modular/use.php";
}