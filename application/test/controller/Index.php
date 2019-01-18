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
        $str  = '{"code":200,"data":{"metrics":"new_user_count","date":[["2018\/12\/08"],["2018\/12\/09"],["2018\/12\/10"],["2018\/12\/11"],["2018\/12\/12"],["2018\/12\/13"],["2018\/12\/14"],["2018\/12\/15"],["2018\/12\/16"],["2018\/12\/17"],["2018\/12\/18"],["2018\/12\/19"],["2018\/12\/20"],["2018\/12\/21"],["2018\/12\/22"],["2018\/12\/23"],["2018\/12\/24"],["2018\/12\/25"],["2018\/12\/26"],["2018\/12\/27"],["2018\/12\/28"],["2018\/12\/29"],["2018\/12\/30"],["2018\/12\/31"],["2019\/01\/01"],["2019\/01\/02"],["2019\/01\/03"],["2019\/01\/04"],["2019\/01\/05"],["2019\/01\/06"],["2019\/01\/07"]],"android":[[8],[9],[17],[5],[14],[59],[864],[196],[217],[1832],[3382],[1364],[605],[338],[141],[104],[394],[236],[310],[178],[168],[95],[59],[43],[32],[55],[143],[232],[86],[99],[135]],"iOS":[[1],[1],[2],[1],[1],[7],[60],[18],[15],[158],[309],[129],[38],[24],[8],[9],[32],[18],[31],[20],[12],[5],[1],[2],[1],[3],[5],[10],[5],[1],[2]]},"msg":"获取成功","tmcode":1}';
        $arr  = json_decode($str, true);
        $sumA = 0;
        $sumI = 0;
        foreach ($arr['data']['android'] as $v) {
            $sumA = $sumA + $v[0];
        }
        foreach ($arr['data']['iOS'] as $v) {
            $sumI = $sumI + $v[0];
        }
        dump($sumA);
        dump($sumI);
        dump($sumI + $sumA);
        die();
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
        $data  = Upload::upload($_FILES['image']);
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

    public function testInternal()
    {
//        $file = $_FILES['image'];
        $file = Request::file('image');

        $upload = new TmUpload($file->getInfo());
        $re     = $upload->uploadFile();
        if ($re == false)
            var_dump($upload->getErrorMessage());
        else {
            var_dump($re);
            var_dump(TmUpload::getUrls(), TmUpload::getUrl($re['type']) . $re['path']);

            if ($upload->delFile($re['path'], $re['type']))
                var_dump("删除成功");
            else
                var_dump($upload->getException());
        }

    }

    public function testToken()
    {
//        Cache::get('B87C2FA03339EB69A92523B5A2EA208C');
//        var_dump(Env::get('app_path'));
//            var_dump());
        $file   = '/uploads/181113/269185573841331746611631949613052016145520287.png';
        $upload = new TmUpload();
        if ($upload->delFile($file, 'local'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());

    }

    public function testDelFtp()
    {
        $file   = '/uploads/181113/413484343522469102417555380290415292903929472.png';
        $upload = new TmUpload();
        if ($upload->delFile($file, 'ftp'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());
    }

    public function testDelOss()
    {
        $file   = '/181113/1683022024817111753919736290194093381275206.png';
        $upload = new TmUpload();
        if ($upload->delFile($file, 'oss'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());
    }

    public function testDelQn()
    {
        $file   = '/181113/5636116916382342232435399922030950041115130.png';
        $upload = new TmUpload();
        if ($upload->delFile($file, 'qn'))
            var_dump("删除成功");
        else
            var_dump($upload->getException());
    }
}