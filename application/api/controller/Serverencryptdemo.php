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
        $encrypt = new Encrypt();
        $todata = [
            "user_name"=>"admin",
            "password"=>"Tianma321321",
            "verify"=>11
        ];
        $data = $encrypt->tmHttpToOwn(
            input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')."/system/login/userLogin",
            ['tm_encrypt_data'=>$encrypt->tmEncryptForServer(json_encode($todata))],
            "POST",
            true,
            []
        );
        $data = json_decode($data);
        if(isset($data->code) && 200 == $data->code){
            dump((array)json_decode($encrypt->tmDecryptForServer($data->data)));
        }
    }
}