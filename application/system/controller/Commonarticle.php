<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14
 * Time: 15:17
 */

namespace app\system\controller;


use think\Controller;
use think\Db;


class Commonarticle extends Controller
{

    protected $farUrl; //远程url地址

    public function __construct()
    {
        parent::__construct();
        $this->farUrl = "http://www.360tianma.com";
      //  $this->farUrl = "http://shop.com";
    }

    public function putArticle(){
        $common_article_aids = Db::table(TM_PREFIX.'common_article_aids')->find();
        if(1 == $common_article_aids['is_stop']){
            die;
        }
        $data = [
            'aid'=>1,
            'website_name'=>'adad',
            'put_time'=>date('Y-m-d H:i:s', time())
        ];
        Db::table(TM_PREFIX.'common_article')->insert($data);
        sleep(3);
        file_get_contents("http://tmadmin.com/system/Commonarticle/putArticle");
        die;
    }

    /*拿id*/
    public function getArticlesIds()
    {
      //  dump(input('server.REQUEST_SCHEME')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);die;
        $config_data = Db::table(TM_PREFIX.'config')->field("value")->where(['key'=>"PullDataKey"])->find();
        if(empty($config_data)){
            die;
        }
        $common_article_aids_data = Db::table(TM_PREFIX.'common_article_aids')->find();
        $objMy = $this->http($this->farUrl ."/reptile/Reptile/getArticlesIds",['key'=>$config_data['value']],'POST');
        if(empty($objMy)){
            die;
        }
        $dataMy = json_decode($objMy);
        if(!isset($dataMy->status) || 1 == $dataMy->status){
            die;
        }
     //   dump($dataMy);die;
        if(!empty($dataMy) && !empty($dataMy->code) && (200 == $dataMy->code || '200' == $dataMy->code)){
            if(isset($dataMy->data) && is_array($dataMy->data)){
                $all_arr = [];
               if(!empty($common_article_aids_data)){
                   $all_arr = array_merge($dataMy->data,explode(",,,", $common_article_aids_data['aids']));
               }else{
                   $all_arr = $dataMy->data;
               }
               if(count($all_arr)>10000){    //待处理的数据最多留一万条
                   array_splice($all_arr,10000);
               }
               if(empty($common_article_aids_data)){
                   $insert_data = [
                       'aids'=> implode(",,,", $all_arr),
                       'create_time'=>date('Y-m-d H:i:s', time())
                   ];
                   Db::table(TM_PREFIX.'common_article_aids')->insert($insert_data);
               }else{
                   $update_data = [
                       'aids'=> implode(",,,", $all_arr),
                       'update_time'=>date('Y-m-d H:i:s', time())
                   ];
                   Db::table(TM_PREFIX.'common_article_aids')->where(['id'=>$common_article_aids_data['id']])->update($update_data);
               }

            }
        }else{
            //    file_put_contents(__DIR__."/log/error.txt", $objMy, FILE_APPEND);
        }
   //     sleep((int)$dataMy->aid_frequency);
   //     file_get_contents(input('server.REQUEST_SCHEME')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
     //   exit(1);
    }


    /*拉取详情*/
    public function getArticlesDetail()
    {
        $config_data = Db::table(TM_PREFIX.'config')->field("value")->where(['key'=>"PullDataKey"])->find();
        if(empty($config_data)){
            die;
        }
        $aids = "";
        $extra_aids = "";
        $common_article_aids_data = Db::table(TM_PREFIX.'common_article_aids')->find();
        if(!empty($common_article_aids_data) && !empty($common_article_aids_data['aids'])){
            $aids_arr = explode(",,,", $common_article_aids_data['aids']);
            if(count($aids_arr)>10){
                $aids = implode(",", array_slice($aids_arr, 0,10));
                array_splice($aids_arr,0,10);
                $extra_aids = implode(",,,", $aids_arr);
            }else{
                $aids = implode(",",$aids_arr);
            }
        }
        $aids_update_data = [
            'aids'=> $extra_aids,
            'update_time'=>date('Y-m-d H:i:s', time())
        ];
        Db::table(TM_PREFIX.'common_article_aids')->where(['id'=>$common_article_aids_data['id']])->update($aids_update_data);
        $objMy = $this->http($this->farUrl ."/reptile/Reptile/getArticlesDetail",['key'=>$config_data['value'],'aids'=>$aids],'POST');
        if(empty($objMy)){
            die;
        }
        $dataMy = json_decode($objMy);
      //  dump($dataMy);die;
        if(!isset($dataMy->status) || 1 == $dataMy->status){
            die;
        }
        if(!empty($dataMy) && !empty($dataMy->code) && (200 == $dataMy->code || '200' == $dataMy->code)){
            if(isset($dataMy->data) && is_array($dataMy->data)){

                $resultKey = ["aid","website_name","title","content","keyword","abstract","from_source",
                    "from_source_url","url","author","organization","column","publish_time","comment_num",
                    "read_num", "collect_time", "img_url1", "img_url2","img_url3","column_id","from_id"];
                $add_common_articleArr = [];
                foreach ($dataMy->data as $key=>$value){
                    $value = (array)$value;
                    $objk = [];
                    foreach ($resultKey as $va){
                        if(isset($value[$va])){
                            $objk[$va] = $value[$va];
                        }else{
                            $objk[$va] = "";
                        }
                    }
                    $objk['put_time'] = date('Y-m-d H:i:s', time());
                    $add_common_articleArr[] = $objk;
                }
                Db::table(TM_PREFIX.'common_article')->insertAll($add_common_articleArr);
            }
        }else{
            //    file_put_contents(__DIR__."/log/error.txt", $objMy, FILE_APPEND);
        }
   //     sleep((int)$dataMy->data_frequency);
     //   file_get_contents(input('server.REQUEST_SCHEME')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    //    exit(1);
    }

    /*删除爬虫库里面数据*/
    public function deleteArticles(){
        Db::table(TM_PREFIX.'common_article')->where('put_time','<',date('Y-m-d H:i:s', time()-24*60*60))->delete();
    }



    /*发送远程请求方法*/
    public function http($url, $params, $method = 'GET', $multi = false, $header = array()){
        $opts = array(
            CURLOPT_TIMEOUT        => 6000,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_USERAGENT      => 'curl'
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }






}