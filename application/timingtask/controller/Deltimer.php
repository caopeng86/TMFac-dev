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
use think\Queue;

// require_once "Base.php";

class Deltimer extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //***********************************************************
    //*
    //*Software: 删除定时任务
    //* companycode 公司或组件标识
    //* code 1=增加 2=修改 3=删除任务
    //* task_id 任务id 删除时必须
    //*********************************************************** 
    public function del_timer(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        // if(!$ret) return reTmJsonObj(500, $msg, []);
        if(empty($inputData['companycode']) 
            || empty($inputData['code'])
            || empty($inputData['task_id']) 
            || !is_numeric($inputData['code'])
            || $inputData['code']!=3) return reTmJsonObj(500,'请按数据类型检查后再请求',[]);
        
        $file=getTaskfile()['task_file'];//获取配置记录文件 
        if(!file_exists($file))  file_put_contents($file,null); //fopen($file,null)
        $json=file_get_contents($file);
        $fp = fopen($file,"w+b") or die("定时文件打开失败!");
        $arr=json_decode($json,true);
        
        if(!is_array($arr)) return reTmJsonObj(500,'原定时任务有误删除失败',[]);
        if (flock($fp,LOCK_EX)) {   
            unset($arr[$inputData['companycode']][$inputData['task_id']]); 
            $arr=json_encode($arr); 
            $arr_len=mb_strlen($arr);
            $file_len=filesize($file);  
            $length=$arr_len>$file_len?$arr_len:$file_len;

            $re=fwrite($fp,$arr,$length);    //sleep(29);
            // $re=file_put_contents($file,$arr,LOCK_EX);
            flock($fp, LOCK_UN); //放

            if(!array_key_exists($inputData['task_id'], $arr[$inputData['companycode']])) return reTmJsonObj(500,'无此任务',[]);
        } else {
            return false;
        }
        if($re)return reTmJsonObj(200,'删除定时任务成功',[]);
        return reTmJsonObj(500,'删除定时任务失败',[]);
    }

}