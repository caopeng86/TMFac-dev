<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/16
 * Time: 10:13
 */
namespace app\api\controller;

use app\extend\controller\TmUpload;
use think\App;
use think\Controller;
use think\facade\Env;
use think\facade\Request;

class Ueditor extends Controller
{
    private $CONFIG;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $config = Env::get('root_path').'public/ueditor/php/config.json';
        $this->CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config)), true);
    }

    /**
     * 文件上传
     */
    public function index(){

        $action = $_GET['action'];
        switch ($action) {
            case 'config':
                $result =  json_encode($this->CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = $this->file_upload();
                break;

            /* 列出图片 */
            case 'listimage':
                $result =  $this->file_list();
                break;
            /* 列出文件 */
            case 'listfile':
                $result = $this->file_list();
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->file_crawler();
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }
    /*
     * 文件上传
     */
    private function file_upload(){
        switch (htmlspecialchars($_GET['action'])) {
            case 'uploadimage':
                $fieldName = $this->CONFIG['imageFieldName'];
                break;
            case 'uploadscrawl':
                $fieldName = $this->CONFIG['scrawlFieldName'];
                break;
            case 'uploadvideo':
                $fieldName = $this->CONFIG['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $fieldName = $this->CONFIG['fileFieldName'];
                break;
        }
        $file = Request::file($fieldName);
        $upload = new TmUpload($file->getInfo());
        $re = $upload->uploadFile();
        if ($re==false){
            $this->returnErrorInfo($upload->getErrorMessage());
        }
        $url = TmUpload::getUrl($re['type']) . $re['path'];
        if($url){
            $data['original'] = $file->getInfo()['name'];
            $data['size'] = $file->getInfo()['size'];
            $data['title'] = '';
            $data['type'] = '';
            $data['url'] = $url;
            $this->returnSuccessInfo($data);
        }
    }

    /**
     * 文件列表
     */
    private function file_list(){
        $this->returnErrorInfo('接口关闭');
    }

    private function file_crawler(){
        $this->returnErrorInfo('接口关闭');
    }


    private function returnErrorInfo($info){
        echo json_encode(array(
            'state'=>$info
        ));
        die;
    }

    private function returnSuccessInfo($data){
        $data['state'] = 'SUCCESS';
        echo json_encode($data);
        die;
    }
}