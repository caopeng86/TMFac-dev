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
ignore_user_abort(true);

class Timerdo extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //***********************************************************
    //*
    //*Software: 执行定时任务
    //* companycode 公司或组件标识
    //*********************************************************** 
    public function timerdoing(){

        $file=getTaskfile()['task_file'];//获取配置记录文件 
        if(!file_exists($file))  file_put_contents($file,null); //fopen($file,null)
        $json=file_get_contents($file);
        $fp = fopen($file,"w+b") or die("定时文件打开失败!");
        // $getdata= fread($fp,filesize($file)); 
        //$getdata2= fgets($fp);  dump($getdata2);die();
        
        $arr=json_decode($json,true);   //数组
  
        if (flock($fp,LOCK_EX)) { 
            $doing_now=[];  
            foreach ($arr as $k => $v) {
                foreach ($v as $ke => $val) {
                    $now_time=(int)time();
                    if(($val['dotime']<=$now_time+5)){
                        $doing_now[]=$val;
                        if($val['looptime']<=0){
                            unset($arr[$k][$ke]);
                        }else{$arr[$k][$ke]['dotime']=$now_time+(int)$val['looptime'];}  
                    }
                }
            }

            $arr=json_encode($arr,true);
            $arr_len=mb_strlen($arr);
            $file_len=filesize($file);  //dump($file_len);
            $length=$arr_len>$file_len?$arr_len:$file_len;
            fwrite($fp,$arr,$arr_len+$file_len); //写入长度不会因为允许得长度($arr_len+$file_len)而占用超过写入得实际长度
            
            flock($fp, LOCK_UN); // 释放锁定
        } else {
            return false;
        }
        fclose($fp);

        // dump($doing_now);
        foreach ($doing_now as $k => $v) {
            $url=(stristr($v['url'],'http://'))?$v['url']:'http://' . $_SERVER['SERVER_NAME'].url($v['url']);
            $data['platform']='fw';
            $s=$this->curl_get_contents($url, http_build_query($data));  
        }   //dump($s);
        return reTmJsonObj(500,'鸥了',[]);
    }

    //———————————————————————————————————————
    //|Software: 
    //|    
    //|=======================================
    public function curl_get_contents($url,$data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_URL, $url);
        $head_info = array(
            // 'Content-type: text/plain'
            'Content-type: multipart/form-data'
        );
        // curl_setopt($ch, CURLOPT_HTTPHEADER,$head_info);
        curl_setopt($ch, CURLOPT_POST, 1);//发送数据方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //发送数据
        
        $r ='';
        $r =curl_exec($ch);
        curl_close($ch);
        // if(curl_error($ch)) return curl_error($ch);
        return $r;
    }
}