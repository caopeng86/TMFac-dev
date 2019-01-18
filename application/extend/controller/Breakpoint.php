<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 10:32
 */
namespace app\extend\controller;

use think\Controller;
use think\facade\Request;
use app\extend\controller\TmUpload;

class Breakpoint extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function breakUpload(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['isFirstUpload','isLastChunk','totalSize'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reTmJsonObj(500, $msg, []);
        }
        if((0 == $inputData['isFirstUpload'] && empty($inputData['tmp_file'])) || (0 == $inputData['isFirstUpload'] && !file_exists($inputData['tmp_file']))){
            return reTmJsonObj(500, '文件上传失败', []);
        }
        if ($_FILES['theFile']['error'] > 0) {
            return reTmJsonObj(500, '文件上传失败', []);
        }
        $_FILES['theFile']['name'] = empty($inputData['fileName'])?$_FILES['theFile']['name']:$inputData['fileName'];
        $tmp_file = 1 == $inputData['isFirstUpload']?"uploads/".time().rand(1000, 9999).".".substr(strrchr($_FILES['theFile']['name'], '.'), 1):$inputData['tmp_file'];
        if (!file_put_contents($tmp_file, file_get_contents($_FILES['theFile']['tmp_name']), FILE_APPEND)) {
            return reTmJsonObj(500, '文件上传失败', []);
        } else {
            // 在上传的最后片段时，检测文件是否完整（大小是否一致）
            if (1 == $inputData['isLastChunk']) {
             //   if (1) {
                if (filesize($tmp_file) == $inputData['totalSize']) {
                    $file = [
                        "size"=>filesize($tmp_file),
                        "error"=>0,
                        "tmp_name"=>$tmp_file,
                        "name"=>substr(strrchr($tmp_file, '/'), 1),
                        "type"=>$_FILES['theFile']['type']
                    ];
                    $upload = new TmUpload($file);
                    $re = $upload->uploadFile();
                    if ($re == false) {
                        return reTmJsonObj(500,'上传失败',[]);
                    } else {
                        $url=TmUpload::getUrl($re['type']);
                        $re['all_path'] = $url."/".$re['path'];
                    }
                    $totalSize = filesize($tmp_file);
                    unlink($tmp_file);
                    return reTmJsonObj(200, '文件成功', ["totalSize"=>$totalSize,"isLastChunk"=>$inputData['isLastChunk'],"tmp_file"=>$tmp_file,'real_file'=>$re]);
                } else {
                    unlink($tmp_file);
                    return reTmJsonObj(500, '文件上传失败', []);
                }
            }
        }
        return reTmJsonObj(200, '文件成功', ["totalSize"=>filesize($tmp_file),"isLastChunk"=>$inputData['isLastChunk'],"tmp_file"=>$tmp_file]);
    }


}