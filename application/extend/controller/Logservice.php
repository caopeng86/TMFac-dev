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

	protected static $_timeStart = array('custom'=>array(),'random'=>array());

    /**
     * 检查文件大小,返回新文件名
     * @param $dir
     * @param $file
     * @return string
     */
    private static function _mkFile($dir, $file){
		if(file_exists($file) && filesize($file) > Logservice::$size){
			$num = count(scandir($dir)) - 2;
			$newFile = str_replace('.log', '', $file)."_{$num}".".log";
			rename($file,$newFile);
		}
    }

    /**
     * 写入数组日志
     * @param $data
     * @param $Msg
     * @param $type
     */
    public static function writeArray($data,$Msg,$type=1,$costkey=null){
        $url = Request::module().'\\'.Request::controller().'\\'.Request::action();
        if($type != 1 && $type != 2){
            $type = 1;
        }

        $destination = Env::get('root_path').'runtime'.DIRECTORY_SEPARATOR.'serverlogs'.DIRECTORY_SEPARATOR.
            //Request::module().DIRECTORY_SEPARATOR.Request::controller().DIRECTORY_SEPARATOR.
            Logservice::$type[$type].DIRECTORY_SEPARATOR.date('Y_m_d').DIRECTORY_SEPARATOR.date('Y_m_d').'.log';
        $dirPath = dirname($destination);
        if(!is_dir($dirPath)){
            mkdir($dirPath,0777,true);
        }

        Logservice::_mkFile($dirPath, $destination);

        $now = @date('Y-m-d H:i:s',time());

		$timeUsed = self::getCostMsec($costkey);

		$level = self::$type[$type];
		$ip = self::getClientIP();
		$message = "------Time:[{$now}] Level[{$level}] Request:[{$url}] Ip[{$ip}] Cost[{$timeUsed}] ";
		$message .= "Log[";
        if(is_array($data)){
			$message .=json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            $message .= $data;
        }
		$message .= "] ";
		$message .= "Msg[{$Msg}]";
		
        $message = rtrim($message,'&');
		$message .= "\r\n"; 
        file_put_contents($destination,$message,FILE_APPEND|LOCK_EX);
    }

	public static function seedMsec($key=null)
    {
    	if ($key != null) 
    	{
    		self::$_timeStart['custom'][$key] = microtime ( true );
    	}else{
    		self::$_timeStart['random'][] = microtime ( true );	
    	}
    }
    public static function getCostMsec($key=null)
    {
    	if ($key != null) 
    	{
    		if (is_array(self::$_timeStart['custom']) && !empty(self::$_timeStart['custom'][$key]) && self::$_timeStart['custom'][$key] > 0) 
	    	{
	    		$timeStart = self::$_timeStart['custom'][$key];
		    	$timeEnd = microtime ( true );
				$timeUsed = intval ( ($timeEnd - $timeStart) * 1000 );
				return $timeUsed;
	    	}else{
	    		return 0;
	    	}
    	}
    	else
    	{
    		if (is_array(self::$_timeStart['random']) && count(self::$_timeStart['random']) > 0) 
	    	{
	    		$timeStart = array_pop (self::$_timeStart['random']);
		    	$timeEnd = microtime ( true );
				$timeUsed = intval ( ($timeEnd - $timeStart) * 1000 );
				return $timeUsed;
	    	}else{
	    		return 0;
	    	}
    	}
    	return 0;
    }

	public static function getClientIP($hasTransmit = false)
	{
		$strIp = '';
		if(isset($_SERVER['HTTP_CLIENTIP']))
		{
			$strIp = strip_tags($_SERVER['HTTP_CLIENTIP']);
        }
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
            $strIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            //获取最后一个
            $strIp = strip_tags(trim($strIp));
            $intPos = strrpos($strIp, ',');
            if($intPos > 0)
			{
                $strIp = substr($strIp, $intPos + 1);
            }
        }
		elseif(!$hasTransmit && isset($_SERVER['REMOTE_ADDR']))
		{
           $strIp = strip_tags($_SERVER['REMOTE_ADDR']);
        }
		elseif(isset($_SERVER['HTTP_CLIENT_IP']))
		{
            $strIp = strip_tags($_SERVER['HTTP_CLIENT_IP']);
        }
		elseif(isset($_SERVER['REMOTE_ADDR']))
		{
			$strIp = strip_tags($_SERVER['REMOTE_ADDR']);
		}
		$strIp = trim($strIp);
		if(!ip2long($strIp))
		{
			$strIp = '127.0.0.1';
		}
		
		return $strIp;
    }
}