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

class Tmpaydemo extends Controller
{
  /*生成签名，支付宝、微信兼容*/
  public function paySign(){
      //判断请求方式以及请求参数
      $inputData = Request::get();
      $method = Request::method();
      $params = ["type"];
      $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
      if(!$ret){
          return reTmJsonObj(500, $msg, []);
      }
      if(!in_array((int)$inputData['type'],[1,2])){   //type值1表示支付宝，2表示微信，暂时只支持支付宝、微信
          return reTmJsonObj(500, "type值不对", []);
      }
      /*这一步是生成签名，只需要调用paySign()方法，不需要添加其他配置
        * 入参：
           $type 支付类型 1支付宝，2微信  必须
           $out_trade_no 订单号  必须
           $total_amount 商品价格 单位 ：分 币种：人民币  必须
           $notifyUrl 异步回调地址 必须
           $subject  商品标题 必须
       * */
     $ret = paySign((int)$inputData['type'],time(),1, input('server.REQUEST_SCHEME') . '://' . input('server.SERVER_NAME')."/api/tmpaydemo/notify","商品标题");
     /*签名失败返回false,签名成功就返回签名信息*/
     if(!$ret){
         return reTmJsonObj(500, "签名失败", []);
     }
      return reTmJsonObj(500, "签名成功", $ret);
  }

    /*支付异步回调，支付宝、微信兼容*/
    public function notify(){
        /*
         * 首先直接调用checkNotify()方法验证异步回调来源的正确性
         * 如果返回false表示验证有误，不可信，直接中断操作
         * */
        $checkNotify = checkNotify();
        if(!$checkNotify){
            return;  //中断操作
        }else{  //验证通过再进行业务操作
            file_put_contents("demonotify.txt",json_encode($checkNotify));
            /*
             * do：业务操作
             *
             * 验证通过checkNotify()方法返回格式为一个数组，如下
             * [
                    "out_trade_no"=>"21231312", //订单号
                    "total_amount"=>1, //订单金额，单位分
                    "type"=>1, //类型，1代表支付宝回调，2代表微信回调
                ];
             *
             *
             *
             * */

            echo returnNotify($checkNotify['type']);  //业务功能操作完成后，通知支付宝、微信。直接调用returnNotify()方法，传入的参数就是checkNotify()函数返回的type值
        }
    }
}