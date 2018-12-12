<?php
/**
 * Created by PhpStorm.
 * User: wcc
 * Date: 2018/11/27
 * Time: 15:08
 */
namespace think;

class Tminit{
    public function get_all_header()
    {
        // 忽略获取的header数据。这个函数后面会用到。主要是起过滤作用
        $ignore = array('host','accept','content-length','content-type');
        $headers = array();
        foreach($_SERVER as $key=>$value){
            if(substr($key, 0, 5)==='HTTP_'){
                //这里取到的都是'http_'开头的数据。
                //前去开头的前5位
                $key = substr($key, 5);
                //把$key中的'_'下划线都替换为空字符串
                $key = str_replace('_', ' ', $key);
                //再把$key中的空字符串替换成‘-’
                $key = str_replace(' ', '-', $key);
                //把$key中的所有字符转换为小写
                $key = strtolower($key);
                if(!in_array($key, $ignore)){
                    $headers[$key] = $value;
                }
            }
        }
        return $headers;
    }
    /*
     * 判断是否加密，并且解密是否通过
    如果解密有问题则返回false，否则返回true
    */
    public function initExecute(){
        $get_all_head = $this->get_all_header();
        if(!isset($get_all_head['tmencrypt'])){
            return true;
        }
        if(1!=$get_all_head['tmencrypt']){
            return true;
        }
        if(empty($get_all_head['tmtimestamp']) || empty($get_all_head['tmrandomnum']) || empty($get_all_head['tmencryptkey'])){
            return false;
        }
        if($get_all_head['tmencryptkey'] !== md5(base64_encode(md5($get_all_head['tmtimestamp']).$get_all_head['tmrandomnum']).$get_all_head['tmrandomnum'])){
            return false;
        }
        return true;
    }
}