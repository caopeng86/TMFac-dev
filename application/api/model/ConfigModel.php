<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/22
 * Time: 14:01
 */
namespace app\api\model;


use think\Db;
use think\facade\Config;
use think\facade\Env;
use think\Model;

class ConfigModel extends CommonModel
{
    /**
     * 获取配置类型
     */
    public function getConfigType(){
        return ['base','client','app'];
    }

    /**
     * 获取配置
     */
    public function getOneConfig($condition,$cache = false){
        return Db::table($this->config_db)->cache($cache,60)->where($condition)->find();
    }

    /**
     * 获取配置列表
     * @param $condition
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getConfigList($condition,$field = false,$cache = false){
        return Db::table($this->config_db)->where($condition)->cache($cache,60)->field($field)->select();
    }

    /**
     * 添加配置信息
     */
    public function addConfig($data){
        $data['add_time'] = time();
        $data['update_time'] = time();
        return Db::table($this->config_db)->insertGetId($data);
    }

    /**
     * 更新配置信息
     */
    public function saveConfig($condition,$data){
        $data['update_time'] = time();
        return Db::table($this->config_db)->where($condition)->update($data);
    }

    /**
     * 批量保存配置
     */
    public function batchSaveConfig($key,$value,$remarks,$type){
        $config = $this->getOneConfig(['key'=>$key,'type'=>$type]);
        if($config){
            $result = $this->saveConfig(['id'=>$config['id']],['value'=>$value]);
        }else{
            $result = $this->addConfig(['key'=>$key,'value'=>$value,'remarks'=>$remarks,'type'=>$type]);
        }
        return $result;
    }

    /**
     * 调整数组为键对方式
     */
    public function ArrayToKey($data){
        if(is_array($data)){
            return array_column($data,'value','key');
        }elseif(is_object($data)){
            $this->object_to_array($data);
        };
        return false;
    }

    private function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return '';
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }
        return $obj;
    }

    /**
     * 获取阿里短信配置信息
     */
    public static function getAliMsg(){
        $condition['key'] = ['ali_sms_key_id','ali_sign_name','ali_key_secret','ali_check_template_code'];
        $condition['type'] = 'client';
        $ConfigModel = new ConfigModel();
        $ConfigList = $ConfigModel->getConfigList($condition);
        return $ConfigModel->ArrayToKey($ConfigList);
    }

    /**
     * 获取极光推送的配置信息
     */
    public static function getJpush(){
        $condition['key'] = ['Jpush_key','Jpush_secret'];
        $condition['type'] = 'client';
        $ConfigModel = new ConfigModel();
        $ConfigList = $ConfigModel->getConfigList($condition);
        return $ConfigModel->ArrayToKey($ConfigList);
    }

    /**
     * 获取PC版本信息
     * @return array
     */
    public function getPCVersion(){
        $condition['key'] = ['version','must_update'];
        $condition['type'] = 'pc_version';
        $ConfigModel = new ConfigModel();
        $ConfigList = $ConfigModel->getConfigList($condition);
        return $ConfigModel->ArrayToKey($ConfigList);
    }

}