<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 9:45
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\ComponentModel;
use app\api\model\PortalModel;
use app\api\model\RoleModel;
use app\api\model\SiteModel;
use think\facade\Request;

class Portal extends Base
{
    protected $portalModel;
    public function __construct()
    {
        parent::__construct();
        $this->portalModel = new PortalModel();
    }

    /**
     * 获取应用列表
     */
    public function getPortal(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['portal_key'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }
        $portal = $this->portalModel->getPortal(['portal_key'=>$inputData['portal_key']]);
        if(!$portal){
            //应用列表不存在则获取该用户的全部应用
            //根据用户code获取对应角色
            $roleObj = new RoleModel();
            $roleCodes = $roleObj->getRoleUserList(['user_code' => $inputData['portal_key']], 'role_code');
            if($roleCodes === false){
                Logservice::writeArray(['sql'=>$roleObj->getLastSql()], '获取角色失败', 2);
                return reJson(500, '获取角色信息失败', []);
            }
            $roleCodes = array_column($roleCodes, 'role_code');

            //根据角色获取所有应用code
            $componentCodes = $roleObj->getRoleComponentList([['role_code', 'in', $roleCodes]], 'component_code');
            if($componentCodes === false){
                Logservice::writeArray(['sql'=>$roleObj->getLastSql()], '获取应用失败', 2);
                return reJson(500, '获取角色应用信息失败', []);
            }
            $componentCodes = array_column($componentCodes, 'component_code');

            //根据应用code获取应用信息
            $componentObj = new ComponentModel();
            $field = 'component_code, component_name, component_key, developer_code, access_key, secret_key,
            index_version, admin_version, app_code, create_time, company_name, address, tel, description, linkman, note,admin_url,index_url,
            component_pic';
            $list = $componentObj->getComponentList([['component_code', 'in', $componentCodes]], $field);
            if($list === false){
                Logservice::writeArray(['sql'=>$componentObj->getLastSql()], '获取应用详情失败', 2);
                return reJson(500, '获取应用列表失败', []);
            }

            //查询站点第一条数据
            $siteModel = new SiteModel();
            $siteCode = $siteModel->getSiteInfo([], 'site_code');
            if($siteCode === false){
                Logservice::writeArray(['sql'=>$siteModel->getLastSql()], '查询站点第一条数据失败', 2);
                return reJson(500, '获取站点code失败', []);
            }
            //加入每个应用
            foreach ($list as $k => $v){
                $v['site_code'] = $siteCode['site_code'];
                $list[$k] = $v;
            }
            return reJson(200,'获取应用列表成功',['list' => $list,'status' => 0]);
        }else{
            return reJson(200,'获取应用列表成功',['list' => $portal['portal_value'],'status' => 1]);
        }
    }

    /**
     * 保存应用列表
     */
    public function savePortal(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['portal_key', 'portal_value'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500,$msg,[]);
        }

        //查找名称是否已经存在
        $condition = ['portal_key' => $inputData['portal_key']];
        $key = $this->portalModel->getPortal($condition);
        if($key){
            //已存在则修改
            $re = $this->portalModel->updatePortal($condition, ['portal_value' => $inputData['portal_value']]);
        }else{
            //不存在则新增
            $re = $this->portalModel->addPortal($inputData);
        }
        if($re === false){
            Logservice::writeArray(['sql'=>$this->portalModel->getLastSql()], '保存应用列表失败', 2);
            return reJson(500,'保存应用列表失败',[]);
        }
        Logservice::writeArray(['inputData'=>$inputData], '保存应用列表');
        return reJson(200,'保存应用列表成功',[]);
    }
}