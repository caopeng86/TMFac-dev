<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/18
 * Time: 11:38
 */

namespace app\extend\controller;


use think\Controller;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Request;

class Logservice extends Controller
{
    private static $type = [1=>'INFO', 2=>'ERR'];
    private static $size = 1024*1024;

    /**
     * 检查文件大小,返回新文件名
     * @param $dir
     * @param $file
     * @return string
     */
    private static function _mkFile($dir, $file){
        //获取该目录下文件数量 去除 ./ ../
        $num = count(scandir($dir)) - 2;
        //判断文件大小
        if($num == 1){
            if(filesize($file) > Logservice::$size){
                //新建文件
                $newFile = str_replace('.log', '', $file)."({$num})".".log";
                return $newFile;
            }else{
                return $file;
            }
        }elseif($num > 1){
            $num = $num - 1;
            $nowFile = str_replace('.log', '', $file)."({$num})".".log";
            if(filesize($nowFile) > Logservice::$size){
                $num = $num + 1;
                //新建文件
                $newFile = str_replace('.log', '', $file)."({$num})".".log";
                return $newFile;
            }else{
                return $nowFile;
            }
        }else{
            return $file;
        }
    }

    /**
     * 写入数组日志
     * @param $data
     * @param $Msg
     * @param $type
     */
    public static function writeArray($data,$Msg,$type=1){
        $url = Request::module().'\\'.Request::controller().'\\'.Request::action();
        if($type != 1 && $type != 2){
            $type = 1;
        }
        $destination = Env::get('root_path').'runtime'.DIRECTORY_SEPARATOR.'serverlogs'.DIRECTORY_SEPARATOR.
            Request::module().DIRECTORY_SEPARATOR.Request::controller().DIRECTORY_SEPARATOR.
            Logservice::$type[$type].DIRECTORY_SEPARATOR.date('Y_m_d').DIRECTORY_SEPARATOR.date('Y_m_d').'.log';
        $dirPath = dirname($destination);
        if(!is_dir($dirPath)){
            mkdir($dirPath,0777,true);
        }
        $destination = Logservice::_mkFile($dirPath, $destination);

        $now = @date('Y-m-d H:i:s',time());
        $message = "-----------------------------------------------------------------------------------------------------------";
        if($cache = Cache::get(Request::header('token'))){
            $loginInfo = json_encode($cache);
            $message .= "\r\nloginInfo:[{$loginInfo}]";
        }
        $message .= "\r\nTime:[{$now}] Request:[{$url}]\r\n";
        if(is_array($data)){
            $message .= "log:";
            $message .=json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            $message .= "log:";
            $message .= $data;
        }
        $message = rtrim($message,'&');
        file_put_contents($destination,$message." || Msg:{$Msg}\r\n",FILE_APPEND|LOCK_EX);
    }
}