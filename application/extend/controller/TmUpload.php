<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/11/1
 * Time: 10:32
 */
namespace app\extend\controller;

use think\exception\ErrorException;
use think\facade\Env;
use think\facade\Config;
use Itxiao6\Upload\Validation\Code;
use Itxiao6\Upload\Validation\Size;
use Itxiao6\Upload\Validation\Mimetype;
use Itxiao6\Upload\Exception\UploadException;
use Itxiao6\Upload\Upload;

class TmUpload
{

    /**
     * @ConfigUploadInfo array|mixed 上传配置信息
     * @file object 待上传文件对象
     * @size_limit int 上传文件大小限制
     * @allowed_file_type array 允许的上传文件类型
     * @validation array 验证规则
     * @exception 保存异常信息
     */
    private $ConfigUploadInfo = array();
    private $file;
    private $size_limit;
    private $allowed_types;
    private $validation=null;
    const LOCAL_TYPE='local';
    const FTP_TYPE='ftp';
    const QN_TYPE='qn';
    const OSS_TYPE='oss';
    /**
     * 异常信息
     * @var null|array
     */
    protected $exception = null;


    public function __construct($file=null)
    {
        $this->file=$file;
        if(is_null($this->file))
            return;
        $this->ConfigUploadInfo = Config::get('upload_info');
        if(!in_array($this->ConfigUploadInfo['type'],['local','qn','oss','ftp'])){ // 默认file
            $this->ConfigUploadInfo['type'] = 'local';
        }
        $this->size_limit=$this->ConfigUploadInfo['size_limit'];
        $this->allowed_types=$this->ConfigUploadInfo['allowed_types'];

        $this->validation=[new Size($this->size_limit),new Code(),new Mimetype($this->allowed_types)];
    }


    /**
     * 统一上传方法
     * @param  string $path 资源路径
     * @param  string $name 资源名字
     * @return array|boolean 存储介质域名
     */
    public function uploadFile($path = '', $name = ''){
        $type = $this->ConfigUploadInfo['type'];

        # 验证验证规则
        try{
            if($this->validation == null){
                # 默认的验证规则
                $this->validation = [new Code()];
            }
            # 判断是否存在验证
            if($this->validation!=null){
                # 循环处理验证规则
                foreach ($this->validation as $item){
                    # 验证

                    $item -> validation($this->file);

                }
            }
        }catch (UploadException $exception){
            # 保存异常信息
            $this -> exception = $exception;
            return false;
        }

        return $this->$type($path,$name);
    }


    /**
     * 统一删除方法
     * @param  string $path 资源路径
     * @param  string $type 资源名字
     * @return array|boolean 存储介质域名
     */
    public function delFile($path = '', $type = ''){
        $type=$type.'Del';
        return $this->$type($path);
    }


    /**
     * 统一根据类型获取域名
     * @param  string $type 存储类型
     * @return string 存储介质域名
     */
    public static function getUrl($type){
        $type = $type.'Url';
        return self::$type();
    }


    /**
     * 获取异常对象
     * @return array|mixed
     */
    public function getException(){
        return $this->exception;
    }

    /**
     * 获取错误信息
     * @return array | string
     */
    public function getErrorMessage()
    {
        return $this->exception->getMessage();
    }


    /**
     * ftp上传方法
     * @param  string $path 资源路径
     * @param  string $name 资源名字
     * @return array|boolean 存储介质域名
     */
    public function ftp($path='',$name=''){

        $ftp=new FtpInternal($this->file);
        $re= $ftp->up_file($path,$name);
        if(!$ftp||!$re){
            $this->exception=$ftp->get_exception();
            return false;
        }
        return ['path'=>$re,'type'=>$this->ConfigUploadInfo['type']];
    }

    /**
     * local删除方法
     * @param  string $path 资源路径
     * @return boolean
     */
    public function ftpDel($path=''){

        $ftp=new FtpInternal();
        $re=$ftp->remove_file($path);
        if(!$ftp||!$re){
            $this->exception=$ftp->get_exception();
            return false;
        }
        return true;
    }

    /**
     * local上传方法
     * @param  string $path 资源路径
     * @param  string $name 资源名字
     * @return array|boolean 存储介质域名
     */
    public function local($path='',$name=''){

        # 本地存储器

        # 设置文件存储驱动
        Upload::set_driver('Local');
        if(!empty(Config::get('upload_info')['local_param']['absolute_path'])){
            # 定义上传的文件夹
            $directory = Config::get('upload_info')['local_param']['path'].date("ymd").'/';
            $upload_path = Config::get('upload_info')['local_param']['absolute_path'].$directory;
        }else{
            # 定义上传的文件夹
            $directory = Config::get('upload_info')['local_param']['path'].date("ymd").'/';
            $upload_path = Env::get('root_path').$directory;
        }

        if(!$this->createDir($upload_path))
            return false;

        # 启动上传组件
        Upload::start($upload_path, '');
        $data = Upload::upload($this->file,'');
        if ($data==false){
            $this->exception=Upload::get_exception();
            return false;
        }
        return ['path'=>$directory.$data,'type'=>$this->ConfigUploadInfo['type']];
    }

    /**
     * local删除方法
     * @param  string $path 资源路径
     * @return boolean
     */
    public function localDel($path=''){

        # 设置文件存储驱动
        Upload::set_driver('Local');
        try{
            //获取绝对路径
            if(!empty(Config::get('upload_info')['local_param']['absolute_path'])){
                $upload_path = Config::get('upload_info')['local_param']['absolute_path'].$path;
            }else{
                $upload_path = Env::get('root_path').$path;
            }
            unlink(realpath($upload_path));
        }catch (ErrorException $e){
            $this->exception=new UploadException($e);
            return false;
        }
        return true;
    }
    /**
     * oss上传方法
     * @param  string $path 资源路径
     * @param  string $name 资源名字
     * @return array|boolean 存储介质域名
     */
    public function oss($path='',$name=''){
        # 阿里云OSS存储器
        Upload::set_driver('Alioss');
        // 桶的名字
        $bucket_name =Config::get('upload_info')['oss_param']['bucket'];
        # 您选定的OSS数据中心访问域名 参考(https://help.aliyun.com/document_detail/31837.html?spm=5176.doc32100.2.4.QQpTvt)
        $data_host = Config::get('upload_info')['oss_param']['endpoint'];
        # 阿里云的secretKey
        $accessKey = Config::get('upload_info')['oss_param']['accessKeyId'];
        # 阿里云的secretKey
        $secretKey = Config::get('upload_info')['oss_param']['accessKeySecret'];

        $directory = date("ymd").'/';

        Upload::start($accessKey, $secretKey, $bucket_name, $data_host);
        # 上传文件
        $data = Upload::upload($this->file,$directory);
        # 判断是否上传成功
        if ($data == false) {
            # 输出错误信息
            $this->exception= Upload::get_exception();
            return false;
        }
        return ['path'=>$data,'type'=>$this->ConfigUploadInfo['type']];
    }

    /**
     * oss删除方法
     * @param  string $path 资源路径
     * @return boolean
     */
    public function ossDel($path=''){

        Upload::set_driver('Alioss');
        // 桶的名字
        $bucket_name =Config::get('upload_info')['oss_param']['bucket'];
        # 您选定的OSS数据中心访问域名 参考(https://help.aliyun.com/document_detail/31837.html?spm=5176.doc32100.2.4.QQpTvt)
        $data_host = Config::get('upload_info')['oss_param']['endpoint'];
        # 阿里云的secretKey
        $accessKey = Config::get('upload_info')['oss_param']['accessKeyId'];
        # 阿里云的secretKey
        $secretKey = Config::get('upload_info')['oss_param']['accessKeySecret'];

        Upload::start($accessKey, $secretKey, $bucket_name, $data_host);
        # 删除文件
        $data = Upload::remove(substr($path,1));
        # 判断是否删除成功
        if ($data == false) {
            # 输出错误信息
            $this->exception= Upload::get_exception();
            return false;
        }
        return true;

//        try{
//            unlink(realpath(Env::get('root_path').$path));
//        }catch (ErrorException $e){
//            $this->exception=new UploadException($e);
//            return false;
//        }
//        return true;
    }
    /**
     * 七牛上传方法
     * @param  string $path 资源路径
     * @param  string $name 资源名字
     * @return array|boolean 存储介质域名
     */
    public function qn($path='',$name=''){
        # 七牛云存储器
        # 设置文件存储驱动
        Upload::set_driver('Qiniu');

        # 定义accessKey
        $accessKey = Config::get('upload_info')['qn_param']['accessKey'];
        # 定义secretKey
        $secretKey = Config::get('upload_info')['qn_param']['secretKey'];
        # 定义桶的名字
        $Bucket_Name = Config::get('upload_info')['qn_param']['bucket'];

        # 定义外网访问路径
        $host = Config::get('upload_info')['qn_param']['cdn'];

        # 上级目录
        $directory = date("ymd").'/';
        # 启动上传组件
        Upload::start($accessKey, $secretKey, $Bucket_Name, $host);

        # 上传文件
        $data = Upload::upload($this->file,$directory);
        # 判断是否上传成功
        if ($data == false) {
            # 输出错误信息
            $this->exception= Upload::get_exception();
            return false;
        }
        return ['path'=>$data,'type'=>$this->ConfigUploadInfo['type']];
    }

    /**
     * 七牛上传方法
     * @param  string $path 资源路径
     * @return array|boolean 存储介质域名
     */
    public function qnDel($path=''){
        # 七牛云存储器
        # 设置文件存储驱动
        Upload::set_driver('Qiniu');

        # 定义accessKey
        $accessKey = Config::get('upload_info')['qn_param']['accessKey'];
        # 定义secretKey
        $secretKey = Config::get('upload_info')['qn_param']['secretKey'];
        # 定义桶的名字
        $Bucket_Name = Config::get('upload_info')['qn_param']['bucket'];

        # 定义外网访问路径
        $host = Config::get('upload_info')['qn_param']['cdn'];

        # 启动上传组件
        Upload::start($accessKey, $secretKey, $Bucket_Name, $host);

        # 删除文件
        $data = Upload::remove(substr($path,1));
        # 判断是否删除成功
        if ($data == false) {
            # 输出错误信息
            $this->exception= Upload::get_exception();
            return false;
        }
        return true;
    }


    /**
     * 获取所有存储介质域名
     * @return array 存储介质域名集合
     */
    public static function getUrls(){
        return array_filter(array(
            self::LOCAL_TYPE=>self::localUrl(),
            self::FTP_TYPE=>self::ftpUrl(),
            self::QN_TYPE=>self::qnUrl(),
            self::OSS_TYPE=>self::ossUrl()
        ));
    }

    /**
     * 获取oss域名
     * @return string
     */
    private static function ossUrl(){
        return Config::get('upload_info')['oss_param']['cdn'];
    }
    /**
     * 获取七牛域名
     * @return string
     */
    private static function qnUrl(){
        return Config::get('upload_info')['qn_param']['cdn'];
    }
    /**
     * 获取ftp域名
     * @return string
     */
    private static function ftpUrl(){
        return 'http://'.Config::get('upload_info')['ftp_param']['host'];
    }
    /**
     * 获取本地域名
     * @return string
     */
    private static function localUrl(){
        if(!empty(Config::get('upload_info')['local_param']['absolute_path'])){
            return Config::get('upload_info')['local_param']['cdn'];
        }else{
            return Config::get('upload_info')['local_param']['host'];
        }
    }

    /**
     * 目录不存在则创建
     * @param string $path 需要创建的目录
     * @return boolean
     */
    public function createDir($path){
        //判断目录存在否，存在返回true，不存在则创建目录
        if (is_dir($path)){
            return true;
        }else{
            try{
                //第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
                $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true);
            }catch (ErrorException $e){
                $this->exception=new UploadException("创建目录:$path 失败");
                return false;
            }
            return true;
        }

    }

}