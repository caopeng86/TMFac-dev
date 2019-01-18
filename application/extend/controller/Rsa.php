<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/12/3
 * Time: 16:40
 */
namespace app\extend\controller;

class Rsa
{
    public $config;
    //config 是指 OPENSSL路径
    public function setConfig($config){
        $this->config = $config?$config:false;
    }
    /**
     * 创建私钥与公钥
     */
    public function createKey(){
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 512,                     //字节数    512 1024  2048   4096 等
            "private_key_type" => OPENSSL_KEYTYPE_RSA,     //加密类型
        );
        if($this->config){
            $config["config"] = $this->config;
        }
        //创建公钥和私钥
        $res = openssl_pkey_new($config); #此处512必须不能包含引号。
        if($res == false) return false;
        //提取私钥
        openssl_pkey_export($res, $private_key,null,$config);
        if(!$private_key)return false;
        //生成公钥
        $public_key = openssl_pkey_get_details($res);
        return array('public_key'=>$public_key["key"],'private_key'=>$private_key);
    }

    /**
     * 私钥加密 最大加密长度
     * @param $json
     */
    public function enCodePrivateKey($private_key,$data){
        if(is_array($data)){
            $data = json_encode($data);
        }
        $encrypted = array();
        foreach (str_split($data,50) as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, $private_key);
            $encrypted[]= base64_encode($encryptData);
        }
        return $encrypted;
    }

    /**
     * 公钥解密
     */
    public function deCodePublicKey($public_key,$encrypted){
        $crypto = '';
        foreach ($encrypted as $chunk) {
            $chunk = base64_decode($chunk);
            openssl_public_decrypt($chunk, $decryptData,$public_key);
            $crypto .= $decryptData;
        }
        $crypto = json_decode($crypto,true);
        return $crypto;
    }

    /**
     * 公钥加密
     */
    public function enCodePublicKey($public_key,$data){
        if(is_array($data)){
            $data = json_encode($data);
        }
        $encrypted = array();
        foreach (str_split($data,50) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData,$public_key);
            $encrypted[] = base64_encode($encryptData);
        }
        return $encrypted;
    }

    /**
     * 私钥解密
     */
    public function deCodePrivateKey($private_key,$encrypted){
        $crypto = '';
        foreach ($encrypted as $chunk) {
            openssl_private_decrypt(base64_decode($chunk), $decryptData,$private_key);
            $crypto .= $decryptData;
        }
        $crypto = json_decode($crypto,true);
        return $crypto;
    }

}