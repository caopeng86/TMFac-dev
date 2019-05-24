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
// use think\Request;

// require_once "Base.php";

class Settimer extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //***********************************************************
    //*
    //*Software: 设置定时任务
    //* companycode 公司或组件标识,  
    //* url 访问地址本框架相对路径
    //* time 从现在设定时间起定时时长  looptime 循环时间  二选一 都传则只按looptime执行
    //* code 1=增加 2=修改 3=删除任务
    //* title 任务名称 
    //* task_id 任务id 删除时提供
    //*********************************************************** 
    public function set(){
        $inputData = input('param.'); //判断请求方式以及请求参数
        // $method = Request::method();
        $method = $_SERVER['REQUEST_METHOD'];
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        // if(!$ret) return reTmJsonObj(500, $msg, []);
        if(empty($inputData['companycode']) || empty($inputData['code']) ||!is_numeric($inputData['code'])) return reTmJsonObj(500,'请按数据类型检查后再请求1',[]);
        
        if((!empty($inputData['time']) && !is_numeric($inputData['time']))
            || (!empty($inputData['looptime']) && !is_numeric($inputData['looptime']))
            || ($inputData['code']==1 && empty($inputData['time']) && empty($inputData['looptime']))
            || ($inputData['code']==2 && empty($inputData['task_id']))
            ) return reTmJsonObj(500,'请按数据类型检查后再请求2',[]);        

        $file=getTaskfile()['task_file'];//获取配置记录文件 
        if(!file_exists($file))  file_put_contents($file,null); //fopen($file,null)
        $json=file_get_contents($file);
        $fp = fopen($file,"w+b") or die("定时文件打开失败!");//r+不清空文件内容 w+b会
        $file_len=mb_strlen($json);  
        $arr=json_decode($json,true);
        
        // $getdata= fgets($fp);//字符串 
        // $arr=json_decode($getdata,true);//数组
        if((!empty($arr)||($inputData['code']==2))&&!is_array($arr)) return reTmJsonObj(500,'原定时任务有错误',[]);
        if (flock($fp,LOCK_EX)) {  
            if(is_numeric($inputData['code']) && $inputData['code']==1){
                if(!is_numeric($inputData['time'])) return reTmJsonObj(500,'定时时间为整型秒',[]);
                $dotime=isset($inputData['time']) ?(isset($inputData['looptime'])?time():(int)$inputData['time']+(int)time()) :time();
                $looptime=isset($inputData['looptime']) ?(int)$inputData['looptime']:0;

                $task_id=(string)time().'_'.(string)rand(1,1000);
                $arr[$inputData['companycode']][$task_id]=[ 'title'=>$inputData['title'],
                                                            'url'=>$inputData['url'],
                                                            'dotime'=>$dotime,
                                                            'looptime'=>$looptime];
            }elseif ($inputData['code']==2) {
                //修
                if(!array_key_exists($inputData['task_id'], $arr[$inputData['companycode']])) return reTmJsonObj(500,'无此任务',[]);
                $that_task=$arr[$inputData['companycode']][$inputData['task_id']];

                $dotime=isset($inputData['time'])?(isset($inputData['looptime'])?$that_task['dotime']:(int)$inputData['time']+(int)time()) :$that_task['dotime'];   //两个时间同时传 保持原执行时间不变 在下次执行任务时按新循环时间跟新下次执行时间
                $looptime=isset($inputData['looptime']) ?(int)$inputData['looptime']:0;
                $url=isset($inputData['url']) ?$inputData['url']:$that_task['url'];
                $title=isset($inputData['title']) ?$inputData['title']:$that_task['title'];
                $arr[$inputData['companycode']][$inputData['task_id']]=['title'=>$title,
                                                                        'url'=>$url,
                                                                        'dotime'=>$dotime,
                                                                        'looptime'=>$looptime];                                                       
            } 

            $arr=json_encode($arr,true); 
            $arr_len=mb_strlen($arr);
            $length=$arr_len>$file_len?$arr_len:$file_len; 
            $re=fwrite($fp,$arr,$length); //写入长度不会因为允许得长度($arr_len+$file_len)而占用超过写入得实际长度 
            flock($fp, LOCK_UN); // 释放锁定
        } else { return false;}

        // $arr=json_encode($arr);
        // $re=file_put_contents($file,$arr,LOCK_EX);
        if($re)return reTmJsonObj(200,'设置成功将在'.date('Y-m-d H:i:s',$dotime).'执行',[]);
        return reTmJsonObj(5001,'设置定时任务失败!',[]);
    }

    // require_once $_SERVER['DOCUMENT_ROOT'] . "./../../Modular/use.php";
}