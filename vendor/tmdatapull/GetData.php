<?php

class GetData {
  public $aa = 1;
  private $key = ""; //您的key值

  public function getKey(){
      return $this->key;
  }
  /*
   * 设置key值*/
  public function setKey($key){
      $this->key = $key;
  }

    /*拉取数据
            (1) 说明：
            每次返回数据限制为 10 篇文章，并且选取符合条件的、id 最小的 10 篇文章返回；暂时
            没有提供翻页参数，可以利用 min_id 等参数来实现类似翻页效果；


            arr数组中参数名称 参数含义 参数样例
            column_ids 分类 id；选择某分类文章 7 or 1,7
            from_ids 来源网站 id；选择某来源文章 7 or 1,7
            min_id 最小文章 id； 100
            max_id 最大文章 id； 1000
            min_publish_time 最小发布时间； 2018-10-14
            max_publish_time 最大发布时间； 2018-10-12 12:30:30
            min_collect_time 最小采集时间； 2018-10-14
            max_collect_time 最大采集时间； 2018-10-12 12:30:30

            这些参数全部放入一个arr数组中传入，建议：拉取数据时拉取全部类型数据，用min_id作为分页条件，保存到本地后再做筛选，
            备注：拉取频率不要操作该用户所获得的频率，或者返回失败，频率通过平台限制。

            eg：
            $arr = [
            'column_ids'=>'1,2' ,//or 'column_ids'=>'1' 分类id，不传代表全部，以英文逗号分隔多个，只能拉取拥有的栏目，
            'from_ids'=>'1,2' ,//or 'from_ids'=>'1' 来源id，不传代表全部，以英文逗号分隔多个，只能拉取拥有的栏目，
            'max_id'=>'1'
            'min_publish_time'=>'2018-10-14'
            ]

            (3) 类型名称和 id 表
            所需要的分类类别：
            1. 热点头条
            2. 科技
            3. 娱乐
            4. 游戏
            5. 体育
            6. 汽车
            7. 财经
            8. 时尚
            9. 搞笑
            10. 旅游
            11. 育儿/母婴
            12. 美食
            13. 美文
            14. 历史
            15. 养生/健康
            16. 国际
            17. 军事
            18. 宠物
            19. 星座
            20. 动漫

            所需要的来源网站类别
           1：澎湃新闻



    返回数据格式，json形式

    返回数据格式示例：

    {
        "code": 200,
        "msg": "成功",
        "data": [
            {
                "id": 1,
                "website_name": "澎湃网",
                "title": "170亿！虚开单位260家！上海破获特大虚开增值税发票案",
                "content": "近日，上海宝山警方成功破获一起特大虚开增值税专用发票案",
                "keyword": "虚开增值税发票,上海警方,税务",
                "abstract": "近日，上海宝山警方",
                "from_source": " 来源：澎湃新闻",
                "from_source_url": "",
                "url": "https://www.thepaper.cn/newsDetail_forward_2529144",
                "author": "澎湃新闻记者 杨帆",
                "organization": "",
                "column": "头条",
                "publish_time": "2018-10-15 13:14",
                "comment_num": "18",
                "read_num": "",
                "collect_time": "2018-10-15 21:43:28",
                "img_url1": "//image.thepaper.cn/image/11/317/5.jpg",
                "img_url2": "",
                "img_url3": "",
                "column_id": "1",
                "from_id": ""
            },
            {
                "id": 3,
                "website_name": "澎湃网",
                "title": "上海百岁老人增至2281人：最高111岁，女性占75%",
                "content": "截至2018年9月30日，上海百岁及以上老年人口已有2281人",
                "keyword": "百岁老人,寿星,百岁夫妻",
                "abstract": "截至2018年9月30日",
                "from_source": " 来源：澎湃新闻",
                "from_source_url": "",
                "url": "https://www.thepaper.cn/newsDetail_forward_2528782",
                "author": "澎湃新闻记者 栾晓娜",
                "organization": "",
                "column": "头条",
                "publish_time": "2018-10-15 10:31",
                "comment_num": "69",
                "read_num": "",
                "collect_time": "2018-10-15 21:44:53",
                "img_url1": "//image2.thepaper.cn/image/11/313/566.jpg",
                "img_url2": "",
                "img_url3": "",
                "column_id": "1",
                "from_id": ""
            }
        ]
    }

      只有返回有数据并且code值为200时为正常状态。其它形式都是错误状态，如整个返回为false或有数据并且code值不为200

      备注：以上说明以后端文档为准

    */
    public function pullData($arr = [])
    {
        if(empty($this->getKey())){
          //  file_put_contents(__DIR__."/log/error.txt", "key值为空", FILE_APPEND);   //测试时可以打开
            return false;
        }
        $arr['key'] = $this->getKey();
        $objMy = $this->http("https://shop.360tianma.com/reptile/Reptile/getData",$arr,'POST');
        $dataMy = json_decode($objMy);
        if(!empty($dataMy) && !empty($dataMy->code) && (200 == $dataMy->code || '200' == $dataMy->code)){
        }else{
        //    file_put_contents(__DIR__."/log/error.txt", $objMy, FILE_APPEND);
        }
        return $objMy;
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
