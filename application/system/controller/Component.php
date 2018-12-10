<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 18:47
 */

namespace app\system\controller;


use app\extend\controller\Logservice;
use app\api\model\ComponentModel;
use think\facade\Config;
use think\facade\Request;

class Component extends Base
{
    protected $componentModel;
    public function __construct()
    {
        parent::__construct();
        $this->componentModel = new ComponentModel();
    }

    /**
     * curl获取应用版本更新数据
     * @param $componentCodes
     * @param $developerCode
     * @return mixed
     */
    private function _getNewVersion($componentCodes, $developerCode=''){
        $url = Config::get('component_url');
        $postData = ['component_codes' => $componentCodes];
        if($developerCode){
            $postData['developer_code'] = $developerCode;
        }

        $output = curlPost($url, $postData);
        if($output === false){
            return false;
        }

        //返回获得的数据
        return json_decode($output, true);
    }

    /**
     * 获取应用列表
     */
    public function getComponentList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        $condition = [];
        $field = 'component_name, component_code, component_pic, index_version, admin_version';
        empty($inputData['page_size']) ? $pageSize = 20 : $pageSize = $inputData['page_size'];
        //获取应用总数
        $count = $this->componentModel->countComponent($condition);
        $totalPage = ceil($count / $pageSize);
        $firstRow = ($inputData['index'] - 1) * $pageSize;
        $limit = $firstRow.','.$pageSize;
        $order = 'component_id desc';
        //取出列表分页处理
        $componentList = $this->componentModel->getComponentList($condition, $field, $limit, $order);
        if(!$componentList){
            Logservice::writeArray(['sql'=>$this->componentModel->getLastSql()], '获取应用列表失败', 2);
            return reTmJsonObj(500, '获取应用列表失败', []);
        }
        //获取该开发者所有应用版本数据
        $componentCodes = array_column($componentList,'component_code');
        $componentCodes = implode(',', $componentCodes);
        $componentVersion = $this->_getNewVersion($componentCodes);
        if(!$componentVersion){
            Logservice::writeArray(['componentCode'=>$componentCodes], '获取最新版本数据失败', 2);
        }

        //处理数据
        if(is_array($componentVersion)){
            foreach ($componentList as $k => $v){
                //获取最新版本信息
                foreach ($componentVersion as $key => $value){
                    $value['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                    if($v['component_code'] == $value['component_code']){
                        if($value['type'] == 1){
                            //前端最新版本数据
                            $v['now_index_version'] = $value;
                        }else{
                            //后端最新版本数据
                            $v['now_admin_version'] = $value;
                        }
                    }
                }
                $componentList[$k] = $v;
            }
        }

        //拼接返回结果
        $re = [
            "list" => $componentList,
            "totalPage" => $totalPage,
            "total" => $count
        ];

        return reTmJsonObj(200, '获取应用列表成功', $re);
    }

    /**
     * 获取应用详情
     */
    public function getComponentInfo(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['component_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }

        //获取详情数据
        $condition['component_code'] = $inputData['component_code'];
        $field = 'component_code, component_name, component_key, developer_code, access_key, secret_key,
         index_version, admin_version, app_code, create_time, company_name, address, tel, description, linkman, note,
         component_pic';
        $info = $this->componentModel->getComponentInfo($condition, $field);
        if($info === false){
            Logservice::writeArray(['sql'=>$this->componentModel->getLastSql()], '获取应用详情失败', 2);
            return reTmJsonObj(500, '获取应用详情失败', []);
        }

        //处理数据
        $info['create_time'] = date('Y-m-d H:i:s', $info['create_time']);
        //获取最新版本数据
        $componentVersion = $this->_getNewVersion($inputData['component_code'], $info['developer_code']);
        if(!$componentVersion){
            Logservice::writeArray(['componentCode'=>$inputData['component_code'], 'developer_code'=>$info['developer_code']], '获取最新版本数据失败', 2);
        }

        //处理数据
        if(is_array($componentVersion)){
            foreach ($componentVersion as $k => $v){
                if(isset($v['type'])){
                    if($v['type'] == 1){
                        //获取前端最新版本
                        $info['now_index_version'] = $v;
                    }else{
                        //获取后端最新版本
                        $info['now_admin_version'] = $v;
                    }
                }
                if(isset($v['developer_name'])){
                    $info['developer_name'] = $v['developer_name'];
                }
            }
        }


        return reTmJsonObj(200, '获取应用详情成功', $info);
    }
}