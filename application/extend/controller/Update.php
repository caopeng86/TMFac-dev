<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/12
 * Time: 9:58
 */
namespace app\system\controller;

use app\api\model\ConfigModel;
use app\extend\controller\Rsa;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;

class Update extends \think\Controller {

    /*
     * 更新SQL版本
     */
    public function index(){
//        $result = $this->checkLicense();
//        if($result['status'] !== 1){
//            return reJson(500,$result['msg']);
//        }
        $file_path = 'db';
        $now_sql_version = $version =  Env::get(SERVER_ENV.'SQL_VERSION');
        $dbconfig = Config::get('')['database'];
        $db       = \think\Db::connect($dbconfig);
        $now_sql_version = create_tables_multi($db,$file_path,$now_sql_version);
        if($now_sql_version == $version){
            return reTmJsonObj(200,'更新成功');
        }
        $env = file_get_contents(Env::get('root_path') . '.env');
        $env = str_replace("SQL_VERSION=".$version, "SQL_VERSION=".$now_sql_version, $env);
        //写入应用配置文件
        if (file_put_contents(Env::get('root_path') . '.env', $env)) {
            Cache::set('site_info',null);//清除备注缓存
            return reTmJsonObj(200,'更新成功');
        }else{
            return reTmJsonObj(500,'写入SQL版本失败');
        }
    }

    /**
     *
     */
    public function getLicenseInfo(){
       $result = $this->checkLicense();
       return reJson($result['status'],$result['msg'],empty($result['data'])?[]:$result['data']);
    }

    /**
     * 检查license
     */
    public function checkLicense(){
        $ConfigModel = new ConfigModel();
        $condition = [
            ['type','=','framework'],
            ['key','IN',['license','public_key','domain']]
        ];
        $license = $ConfigModel->getConfigList($condition,false,true);
        if(!$license){
            return ['status'=>0,'msg'=>'license相关参数不存在'];
        }
        $license = $ConfigModel->ArrayToKey($license);
        if(empty($license['license']))return ['status'=>0,'msg'=>'请检查license'];
        if(empty($license['public_key']))return ['status'=>0,'msg'=>'请检查公钥'];
        if(empty($license['domain'])) return ['status'=>0,'msg'=>'请检查域名'];
        $url = $license['domain'].'/api/license/checkLicenseStatus.html';
        $result = curlGet($url.'?license='.$license['license']);
        $result = json_decode($result,true);
        if(empty($result['status']))return ['status'=>0,'msg'=>'检查接口调用失败'];
        if($result['status'] != 1)return $result;
        $Rsa = new Rsa();
        $result['data'] = $Rsa->deCodePublicKey($license['public_key'],$result['data']);
        if(!$result['data']){
            return ['status'=>0,'msg'=>'解析失败'];
        }
        if($result['data']['status'] !== 1){
            return ['status'=>-1,'msg'=>'未激活','data'=>$result['data']];
        }
        if(time() < $result['data']['start_time'] || time() > $result['data']['end_time']){
            return ['status'=>-2,'msg'=>'已过期','data'=>$result['data']];
        }
        return $result;
    }

}