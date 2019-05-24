<?php


//***********************************************************
//*
//*Software: 获取定时配置文件
//*
//***********************************************************
function getTaskfile()
{
    // require_once $_SERVER['DOCUMENT_ROOT'] . "./../../Modular/use.php"; //require_once此处不能使用
    // $s=include $_SERVER['DOCUMENT_ROOT'].'/application/timingtask/config.php';//used
    $data=include("config.php");
    return $data;
}


