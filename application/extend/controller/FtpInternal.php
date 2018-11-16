<?php
/**
 * Created by PhpStorm.
 * User: caopeng
 * Date: 2018/11/6
 * Time: 10:39
 */

namespace app\extend\controller;

use think\Exception;
use think\exception\ErrorException;
use think\facade\Config;
use FtpClient\FtpClient;
use FtpClient\FtpException;
use Itxiao6\Upload\Tools\Tool;

/**
 * 作用：FTP操作类( 拷贝、移动、删除文件/创建目录 )
 */
class FtpInternal
{
    public $status;
    public $ftp; // FtpClient 实例
    public $ConfigUploadInfo =array();

    protected $file;
    protected $name='default';
    /**
     * 异常信息
     * @var null|array
     */
    protected $exception = null;

    private $FTP_HOST = '';
    private $FTP_PORT = '';
    private $FTP_USER = '';
    private $FTP_PASS = '';
    private $FTP_ROOT = '';

    /**
     * 方法：FTP连接
     * @FTP_HOST -- FTP主机
     * @FTP_PORT -- 端口
     * @FTP_USER -- 用户名
     * @FTP_PASS -- 密码
     * @param array $file 待上传文件
     */
    function __construct($file=null)
    {
        if (!is_null($file)){
            $this->file=$file;
            # 判断是否为数组
            if(is_array($this->file)){
                $this->name = $this->file['name'];
            }else{
                # 保存异常信息
                $this -> exception[$this->name] = new FtpException('要上传的文件不存在');
                return false;
            }
        }

        $this->ConfigUploadInfo = Config::get('upload_info');
        $this->FTP_HOST=$this->ConfigUploadInfo['ftp_param']['host'];
        $this->FTP_PORT=$this->ConfigUploadInfo['ftp_param']['port'];
        $this->FTP_USER=$this->ConfigUploadInfo['ftp_param']['username'];
        $this->FTP_PASS=$this->ConfigUploadInfo['ftp_param']['password'];
        $this->FTP_ROOT=$this->ConfigUploadInfo['ftp_param']['path'];
        try{
            $this->ftp=new FtpClient();
            $this->ftp->connect($this->FTP_HOST,false,$this->FTP_PORT);

            $this->ftp->login($this->FTP_USER, $this->FTP_PASS);
        }catch (FtpException $e){
            $this -> exception[$this->name] = new FtpException($e);
            return false;
        }

    }

    /**
     * 方法：上传单个文件
     * @param string path -- 组件自定义的相对路径
     * @param string name -- 组件自定义的资源存储名称，下一个版本会废弃
     * @return string -- 资源完整相对路径
     */
    function up_file($path = '',$name= '')
    {
        //按照日期生成相应存储目录
        $remote_path=$this->FTP_ROOT.date("ymd").'/'.$path;
        if(!$this->ftp){
            $this -> exception=new FtpException('FTP链接失败');
            return false;
        }
        try{
            # 判断远程路径是否存在
            if(!$this->ftp->isDir($remote_path)){
                $this->ftp->mkdir($remote_path);
            }

        }catch (FtpException $e){
            $this -> exception=new FtpException('创建目录失败');
            return false;
        }

        # 获取新文件名
        $newName = Tool::getARandLetter(15).'.'.explode('/',$this->file['type'])[1];
        # 上传文件
        $this->status=$this->ftp->put($remote_path.$newName,$this->file['tmp_name'],FTP_BINARY);
        if(!$this->status){
            # 保存异常信息
//            $this -> exception[$file] = new UploadException('文件上传失败');
            $this -> exception=new FtpException('文件上传失败，请检查权限及路径是否正确！');
            return false;
        }
        return $remote_path.$newName;
    }

    /**
     * 方法：上传单个文件
     * @param string path -- 组件自定义的相对路径
     * @param string name -- 组件自定义的资源存储名称，下一个版本会废弃
     * @return boolean
     */
    function remove_file($path = '')
    {
        $this->status=$this->ftp->remove($path);
        if(!$this->status){
            # 保存异常信息
            $this -> exception=new FtpException('文件删除失败，请检查权限及路径是否正确！');
            return false;
        }
        return true;
    }


    public function get_exception(){
        return $this->exception;
    }

    /**
     * 获取错误信息
     * @param null | string $name
     * @return array | string
     */
    public function get_error_message($name = null)
    {
        if($name!=null){
            return $this -> exception[$name] -> getMessage();
        }else{
            if($this -> exception === null){
                return false;
            }
            $message = [];
            foreach ($this -> exception as $item) {
                $message[] = $item -> getMessage();
            }
            return $message;
        }
    }





//    /**
//     * 方法：移动文件
//     * @path -- 原路径
//     * @newpath -- 新路径
//     * @type -- 若目标目录不存在则新建
//     */
//    function move_file($path, $newpath, $type = true)
//    {
//        if ($type) $this->dir_mkdirs($newpath);
//        $this->off = @ftp_rename($this->conn_id, $path, $newpath);
//        if (!$this->off) echo "文件移动失败，请检查权限及原路径是否正确！";
//    }
//
//    /**
//     * 方法：复制文件
//     * 说明：由于FTP无复制命令,本方法变通操作为：下载后再上传到新的路径
//     * @path -- 原路径
//     * @newpath -- 新路径
//     * @type -- 若目标目录不存在则新建
//     */
//    function copy_file($path, $newpath, $type = true)
//    {
//        $downpath = "c:/tmp.dat";
//        $this->off = @ftp_get($this->conn_id, $downpath, $path, FTP_BINARY);// 下载
//        if (!$this->off) echo "文件复制失败，请检查权限及原路径是否正确！";
//        $this->up_file($downpath, $newpath, $type);
//    }
//
//    /**
//     * 方法：删除文件
//     * @path -- 路径
//     */
//    function del_file($path)
//    {
//        $this->off = @ftp_delete($this->conn_id, $path);
//        if (!$this->off) echo "文件删除失败，请检查权限及路径是否正确！";
//    }
//
//    /**
//     * 方法：生成目录
//     * @path -- 路径
//     */
//    function dir_mkdirs($path)
//    {
//        $path_arr = explode('/', $path); // 取目录数组
//        $file_name = array_pop($path_arr); // 弹出文件名
//        $path_div = count($path_arr); // 取层数
//        foreach ($path_arr as $val) // 创建目录
//        {
//            if (@ftp_chdir($this->conn_id, $val) == FALSE) {
//                $tmp = @ftp_mkdir($this->conn_id, $val);
//                if ($tmp == FALSE) {
//                    echo "目录创建失败，请检查权限及路径是否正确！";
//                    exit;
//                }
//                @ftp_chdir($this->conn_id, $val);
//            }
//        }
//        for ($i = 1; $i = $path_div; $i++) // 回退到根
//        {
//            @ftp_cdup($this->conn_id);
//        }
//    }
//
//    /**
//     * 方法：关闭FTP连接
//     */
//    function close()
//    {
//        @ftp_close($this->conn_id);
//    }
}// class class_ftp end

