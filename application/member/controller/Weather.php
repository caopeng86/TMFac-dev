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
     * ��ȡ������Ϣ
     */
    public function getWeatherInfo(){
        //�ж�����ʽ�Լ��������
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
     * ��ȡ������Ϣ
     */
    public function getCalendarInfo(){
        //�ж�����ʽ�Լ��������
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