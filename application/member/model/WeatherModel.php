<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11
 * Time: 15:54
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;
use think\facade\Cache;

class WeatherModel extends CommonModel
{
    /**
     * 获取天气信息
     * @param $data
     * @return int|string
     */
    public function getWeatherInfo($city_id="", $ip=""){
		$cacheKey = TM_MEMBER_WEATHER_INFO . "_" . $city_id ."_" . $ip;
		$data = Cache::get($cacheKey);
		if(!empty($data)){
			return $data;
		}
        try {
            $data = tmBaseHttp(config("tianqiapi_url")."/api/",['version'=>"v1",'cityid'=>$city_id,"ip"=>$ip]);
            $data = json_decode($data,true);
			if(!empty($data)){
				Cache::set($cacheKey,$data,3600);
			}
        } catch (Exception $e) {
            return false;
        }
        return $data;
    }

	/**
     * 获取日历信息
     * @param $data
     * @return int|string
     */
    public function getCalendarInfo($date){
		$cacheKey = TM_MEMBER_CALENDAR_INFO . "_" . $date;
		$data = Cache::get($cacheKey);
		if(!empty($data)){
			return $data;
		}
        try {
            $data = tmBaseHttp(config("sojson_url")."/open/api/lunar/json.shtml",['date'=>$date]);
            $data = json_decode($data,true);
			if(!empty($data)){
				Cache::set($cacheKey,$data,3600);
			}
        } catch (Exception $e) {
            return false;
        }
        return $data;
    }
}