<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 15:22
 */
namespace app\member\controller;

use app\member\model\WeatherModel;
use think\facade\Request;
use think\facade\Config;

class Weather extends Base
{
	protected $weatherModel;
    public function __construct()
    {
        parent::__construct();
		$this->weatherModel = new WeatherModel();
    }

    /**
     * 获取天气信息
     */
    public function getWeatherInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
		$city_id = $inputData["cityid"]??"";
		$ip = $inputData["ip"]??getClientIP();
		if($ip == "127.0.0.1")
		{
			$ip = "";
		}

        $weaInfo = $this->weatherModel->getWeatherInfo($city_id, $ip);
        if($weaInfo){
            return reTmJsonObj(200,'success',$weaInfo);
        }
        return reTmJsonObj(500,'failed', []);
    }

	/**
     * 获取日历信息
     */
    public function getCalendarInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
		$date = $inputData['date'] ?? date("Y-m-d");

        $dateInfo = $this->weatherModel->getCalendarInfo($date);
        if($dateInfo){
            return reTmJsonObj(200,'success',$dateInfo['data']);
        }
        return reTmJsonObj(500,'failed', []);
    }
}