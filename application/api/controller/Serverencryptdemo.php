<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 15:17
 */

namespace app\api\controller;


use think\Controller;
use think\facade\Request;
use app\extend\server\Encrypt;

class Serverencryptdemo extends Controller
{

    /*先用户登录接口发送请求*/
    public function userLogin(){
        /*
         * step1：
         * 首先new一个加解密实例对象
         * */
        $encrypt = new Encrypt();
        /*
         * step2：
         * 拼装请求数据
         * */
        $todata = [
            "user_name"=>"admin",
            "password"=>"Tianma321321",
            "verify"=>11
        ];
        /*
         * step3：
         * 调用 向天马服务端自己发送请求的方法
         * */
        $data = $encrypt->tmHttpToOwn(
            input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')."/system/login/userLogin",
            ['tm_encrypt_data'=>$encrypt->tmEncryptForServer(json_encode($todata))], //step4：调动加密函数
            "POST",
            true,
            []
        );
        $data = json_decode($data);
        if(isset($data->code) && 200 == $data->code){
            /*
             * step5：调用解密函数
             * */
            dump((array)json_decode($encrypt->tmDecryptForServer($data->data)));
        }
    }
}