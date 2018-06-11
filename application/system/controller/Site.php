<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 11:19
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\RoleModel;
use app\api\model\SiteModel;
use think\Db;
use think\facade\Cache;
use think\facade\Request;

class Site extends Base
{
    protected $siteModel;
    protected $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->siteModel = new SiteModel();
        $this->roleModel = new RoleModel();
    }

    /**
     *新增一条站点信息
     */
    public function addSite(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['site_name'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }
        $inputData['site_code'] = createCode();
        $inputData['add_user'] = Cache::get(Request::header('token'))['user_name'];
        $inputData['add_time'] = time();

        //保存站点信息到数据库
        Db::startTrans();
        $re = $this->siteModel->addSite($inputData);
        if($re === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '新增站点数据失败', 2);
            Db::rollback();
            return reJson(500, '站点信息保存失败', []);
        }

        //将新增的站点与超级管理员关联
        $role = $this->roleModel->addRoleSiteAll([['role_code' => 1, 'site_code' => $inputData['site_code']]]);
        if($role === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '新增站点角色失败', 2);
            Db::rollback();
            return reJson(500, '站点关联信息保存失败', []);
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '新增站点');
        return reJson(200, '站点信息保存成功', []);
    }

    /**
     * 系统升级信息
     */
    public function getSystemInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = [];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        //获取缓存中数据
        $info = Cache::get('site_info');
        if(!$info){
            //程序版本
            //产品系列
            $info['server_os'] = php_uname('a');//服务器系统
            $info['php_version'] = 'PHP Version '.PHP_VERSION;//PHP版本
            $info['server_software'] = $_SERVER ['SERVER_SOFTWARE'];//服务器软件
            $info['mysql_version'] = $this->siteModel->getMysqlVersion();//服务器MySQL版本
            $info['upload_max_file_size'] =  ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled";//上传许可
            $info['database_size'] = $this->siteModel->getDatabaseSize();//当前数据库尺寸
            //当前附件根目录
            //当前附件尺寸
            Cache::set('site_info', $info, 300);
        }

        return reJson(200, '获取系统升级信息成功', $info);
    }

    /**
     * 获取站点列表
     */
    public function getSiteList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition = [];
        $field = 'site_id, site_name, site_code, site_intro, is_work, add_time';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        if(!isset($inputData['is_work'])){
            $condition['is_work'] = 1;
        }else{
            if($inputData['is_work'] == 0){
                $condition['is_work'] = 1;
            }else{
                $condition = [];
            }
        }
        //获取站点总数
        $count = $this->siteModel->countSite($condition);
        $totalPage = ceil($count / $pageSize);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow.','.$pageSize;
        $order = 'site_id desc';
        //取出列表分页处理
        $siteList = $this->siteModel->getSiteList($condition, $field, $limit, $order);
        if($siteList === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '获取站点列表失败', 2);
            return reJson(500, '获取站点列表失败', []);
        }
        //转换时间戳
        foreach ($siteList as $key => $value){
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $siteList[$key] = $value;
        }

        //拼接返回结果
        $re = [
            "list" => $siteList,
            "totalPage" => $totalPage,
            "total" => $count
        ];

        return reJson(200, '获取站点列表成功', $re);
    }

    /**
     * 获取一条站点信息
     */
    public function getSiteInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        $condition['site_code'] = $inputData['site_code'];
        $site_info = $this->siteModel->getSiteInfo($condition);
        if($site_info === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '获取站点详情失败', 2);
            reJson(500, '获取站点信息失败', []);
        }

        return reJson(200, '获取站点信息成功', $site_info);
    }

    /**
     * 修改站点信息
     */
    public function updateSite(){
        //判断请求方式以及请求参数
        $inputData = Request::put();
        $method = Request::method();
        $params = ['site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'PUT', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }
        $inputData['modify_user'] = Cache::get(Request::header('token'))['user_name'];
        $inputData['modify_time'] = time();

        $condition['site_code'] = $inputData['site_code'];
        $site_info = $this->siteModel->updateSite($condition, $inputData);
        if($site_info === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '修改站点数据失败', 2);
            reJson(500, '修改站点信息失败', []);
        }
        Logservice::writeArray(['inputData'=>$inputData], '修改站点信息');
        return reJson(200, '修改站点信息成功', []);
    }

    /**
     * 删除站点
     */
    public function deleteSite(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['site_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition = ['site_code' => $inputData['site_code']];
        Db::startTrans();
        $re = $this->siteModel->deleteSite($condition);
        //$re返回影响的行数
        if($re === false){
            Logservice::writeArray(['sql'=>$this->siteModel->getLastSql()], '删除站点数据失败', 2);
            Db::rollback();
            return reJson(500, '删除站点失败', []);
        }

        //删除站点角色关联表数据
        $role = $this->roleModel->deleteRoleSite($condition);
        if($role === false){
            Logservice::writeArray(['sql'=>$this->roleModel->getLastSql()], '删除站点角色数据失败', 2);
            Db::rollback();
            return reJson(500, '删除站点关联信息失败', []);
        }
        Db::commit();
        Logservice::writeArray(['inputData'=>$inputData], '删除站点');
        return reJson(200, '删除站点成功', []);
    }
}