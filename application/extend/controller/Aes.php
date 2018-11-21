<?php

namespace app\extend\controller;
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/9/29
 * Time: 17:40
 */
class Aes
{
    private $hex_iv = '';
    # converted JAVA byte code in to HEX and placed it here
    private $key = '';

    function __construct($key)
    {
        $this->key = $key;
        $this->hex_iv = $this->key;
    }

    public function encrypt($input)
    {
        $data = openssl_encrypt($input, 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA, $this->hex_iv);
        $data = bin2hex($data);
        return $data;
    }

    public function decrypt($input)
    {
        $decrypted = openssl_decrypt(hex2bin($input), 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA,$this->hex_iv);
        return $decrypted;
    }
}