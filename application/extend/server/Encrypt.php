<?php

namespace app\extend\server;
class Encrypt
{
    private $tmtimestamp;
    private $tmrandomnum;
    private $tmencryptkey;
    function __construct()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $tmrandomnum = "";
        for ($i = 0; $i < 16; $i++) {
            $tmrandomnum .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $tmtimestamp = time();
        $tmencryptkey = md5(base64_encode(md5($tmtimestamp).$tmrandomnum).$tmrandomnum);
        $this->tmtimestamp = $tmtimestamp;
        $this->tmrandomnum = $tmrandomnum;
        $this->tmencryptkey = $tmencryptkey;
    }

    /*解密公共函数
   入参：待解密字符串
   出参：解密后的字符串
   注意：该解密函数只能解密天马服务端提供的加密函数{common公共函数》》tmEncrypt}加密的数据,并且服务端发送请求的head中必须加入服务端封装得head参数
  */
    public  function tmDecryptForServer($data=""){
        $head = getAllHeader();
        if(empty($this->tmtimestamp) || empty($this->tmrandomnum)){
            return false;
        }
        return openssl_decrypt(base64_decode($data), 'AES-128-CBC', substr(md5(base64_encode($this->tmtimestamp).md5($this->tmtimestamp)),0,16), OPENSSL_RAW_DATA, substr(md5($this->tmrandomnum),0,16));
    }

    /*
     * 加密公共函数
        入参：待加密字符串，如果想对数组加密可以先转成json字符串再传进来
        出参：加密后的字符串
        备注：加密后的数据可以通过天马服务端提供的解密方法{common公共函数》》tmDecrypt}解密,并且服务端发送请求的head中必须加入服务端封装得head参数
    */
    public function tmEncryptForServer($data = ""){
        $head = getAllHeader();
        if(empty($this->tmtimestamp) || empty($this->tmrandomnum)){
            return false;
        }else{
            $data= openssl_encrypt($data, 'AES-128-CBC',substr(md5(base64_encode($this->tmtimestamp).md5($this->tmrandomnum)),0,16), OPENSSL_RAW_DATA, substr(md5(base64_encode($this->tmrandomnum).md5($this->tmtimestamp)),0,16));
            return base64_encode($data);
        }
    }


    /*服务端向天马服务端自己发送请求方法
    入参：
    $url 请求地址
    $params 请求参数
    $method 请求方式 值 POST 或 GET
    $isAddHead  true false是否加上头部秘钥，对于要验证头部秘钥的接口必须加上，建议默认加上
    $header head传函数，数组
     $multi 判断是否传输文件 false true
     * */
    public function tmHttpToOwn($url, $params, $method = 'GET',$isAddHead = true, $header = array(), $multi = false){
        if($isAddHead){
            array_push($header,"tmencrypt:1");
            array_push($header,"tmtimestamp:".$this->tmtimestamp);
            array_push($header,"tmrandomnum:".$this->tmrandomnum);
            array_push($header,"tmencryptkey:".$this->tmencryptkey);
        }
        return tmBaseHttp($url, $params, $method, $multi, $header);
    }



}