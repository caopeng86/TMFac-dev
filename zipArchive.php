<?php
/**
 * Created by PhpStorm.
 * User: wcc
 * Date: 2019/4/2
 * Time: 15:30
 */

try{
    $zip = new \ZipArchive();
    var_dump($zip);
    echo "    ";
    echo "支持自动化打包";
}catch (\Exception $e){
    echo "不支持自动化打包";
}