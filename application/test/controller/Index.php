<?php
/**
 * Created by PhpStorm.
 * User: caopeng
 * Date: 2018/10/31
 * Time: 16:33
 */


namespace app\test\controller;

use app\extend\controller\TmUpload;
use think\Controller;
use think\facade\Env;
use Itxiao6\Upload\Upload;
use app\extend\controller\FtpInternal;
use FtpClient\FtpClient;
use think\facade\Request;
use think\facade\Cache;

class Index extends controller
{
    public function index()
    {
        # 本地存储器

        # 设置文件存储驱动
        Upload::set_driver('Local');

        # 定义上传的文件夹
        $directory = __DIR__ . '/';

        # 定义上传完的webUrl
        $webUrl = '/';

        $file = $_FILES['image'];
        //var_dump($_FILES);

        # 启动上传组件
        Upload::start($directory, $webUrl);
//$data=Upload::upload(Env::get('root_path').'uploads/default.png');
        $data = Upload::upload($file);

        var_dump($data);
        var_dump(Upload::get_error_message());
    }

    public function ossMethod()
    {
        # 阿里云OSS存储器
        Upload::set_driver('Alioss');
        // 桶的名字
        $bucket_name = 'tianma-shop-goods';
        # 您选定的OSS数据中心访问域名 参考(https://help.aliyun.com/document_detail/31837.html?spm=5176.doc32100.2.4.QQpTvt)
        $data_host = 'oss-cn-qingdao.aliyuncs.com';
        # 阿里云的secretKey
        $accessKey = 'LTAItVl4NjfG303c';
        # 阿里云的secretKey
        $secretKey = '2x0vFnfq0y7FHaniCN2Wo3sA8k6Izu';

        Upload::start($accessKey, $secretKey, $bucket_name, $data_host);
        # 上传文件
        //$data = Upload::uploads('image');
        # 上传base64 文件
        $data = Upload::upload($_FILES['image']);
        // $data = Upload::upload_base64($_FILES['image']);
        # 判断是否上传成功
        if ($data != false) {
            # 输出图片
            echo "<img src='" . $data . "'>";
        } else {
            # 输出错误信息
            echo Upload::get_error_message();
        }
    }

    public function qiniuMethod()
    {
        # 七牛云存储器
        # 设置文件存储驱动
        Upload::set_driver('Qiniu');

        # 定义accessKey
        $accessKey = 'mOmsEmq2vOKp5AwAged65eMLWiZXbjO31XC6lXem';
        # 定义secretKey
        $secretKey = 'ZY0IpAlLwpjBSW4R04Asc4mIY-fAUALwYThQzRGz';
        # 定义桶的名字
        $Bucket_Name = 'qingyuan';

        # 定义外网访问路径
        $host = 'http://qiniuoss.360tianma.com/';

        # 启动上传组件
        Upload::start($accessKey, $secretKey, $Bucket_Name, $host);
        # 获取七牛云的上传token
        $token = Upload::get_token();
        $data = Upload::upload($_FILES['image']);
        if ($data != false) {
            # 输出图片
            echo "<img src='" . $data . "'>";
        } else {
            # 输出错误信息
            echo Upload::get_error_message();
        }
    }

    public function upload()
    {
        return $this->fetch();
    }
    public function testInternal(){
//        $file = $_FILES['image'];
        $file = Request::file('image');

        $upload=new TmUpload($file->getInfo());
        $re=$upload->uploadFile();
        if ($re==false)
            var_dump($upload->getErrorMessage());
        else{
            var_dump($re);
            var_dump(TmUpload::getUrls(),TmUpload::getUrl($re['type']).$re['path']);

            if($upload->delFile($re['path'],$re['type']))
                var_dump("删除成功");
            else
                var_dump($upload->getException());
        }

    }
    public function testToken(){
//        Cache::get('B87C2FA03339EB69A92523B5A2EA208C');
//        var_dump(Env::get('app_path'));
//            var_dump());
        $file='/uploads/181113/269185573841331746611631949613052016145520287.png';
        $upload=new TmUpload();
        if($upload->delFile($file,'local'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());

    }
    public function testDelFtp(){
        $file='/uploads/181113/413484343522469102417555380290415292903929472.png';
        $upload=new TmUpload();
        if($upload->delFile($file,'ftp'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());
    }
    public function testDelOss(){
        $file='/181113/1683022024817111753919736290194093381275206.png';
        $upload=new TmUpload();
        if($upload->delFile($file,'oss'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());
    }

    public function testDelQn(){
        $file='/181113/5636116916382342232435399922030950041115130.png';
        $upload=new TmUpload();
        if($upload->delFile($file,'qn'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());
    }
}