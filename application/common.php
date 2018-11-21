<?php
// 应用公共文件

/**
 * 接口返回json数据处理
 * @param $status
 * @param $msg
 * @param array $data
 * @return \think\response\Json
 */
function reJson($status,$msg,$data = []){
    if(is_array($data) && 0 == count($data)){
        $data = json($data);
    }
    return json([
        'code'=>$status,
        'data'=>$data,
        'msg'=>$msg,
    ]);
}
/** 
*去掉bom头
*
*/
function removeBOM($data) {
	if (0 === strpos(bin2hex($data), 'efbbbf')) {
	   return substr($data, 3);
	}
	return $data;
}
/**
 * 生成唯一code
 * @param string $namespace
 * @return string
 */
function createCode($namespace = '') {
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash,  0,  8) .
            substr($hash,  8,  4) .
            substr($hash, 12,  4) .
            substr($hash, 16,  4) .
            substr($hash, 20, 12) ;
    return $guid;
  }

/**
 * 函数用途描述:必须参数验证,请求方式验证
 * @param $data array 接收的数据
 * @param $params array 必须的参数
 * @param $request string 请求的方式
 * @param $rule string 规定的请求方式
 * @param $result string 返回信息
 * @return bool
 */
function checkBeforeAction(&$data, $params, $request, $rule, &$result)
{
    $url = \think\facade\Request::module().'\\'.\think\facade\Request::controller().'\\'.\think\facade\Request::action();
    $url = strtolower($url);
    //跳过验证的方法
    $pass = [
        'system\role\saveroleprivilege',
    ];

    //判断请求方式
    if($request !== 'options'){
        if($request !== $rule){
            $result = '请求方式错误';
            return false;
        }
    }

    //判断请求参数
    foreach ($params as $value) {
        if (!array_key_exists($value,$data)){
            $result = $value.'请求参数错误';
            return false;
        }
        if(!in_array($url, $pass)){
            if($data[$value] !== 0 && $data[$value] != '0' ){
                if(empty($data[$value])){
                    $result = '网络异常，请刷新后重试';
                    return false;
                }
            }
        }
    }

    return true;
}

/**
 * 递归获取分类列表数据
 * @param array $arr 需要无限极分类的数组
 * @param int $pid 父id
 * @param string $pidName 父id字段名
 * @param string $idName  id字段名
 * @return array
 */
function getAttr($arr, $pid, $pidName, $idName){
    $tree = [];//每次都声明一个新数组用来放子元素
    foreach($arr as $v){
        if($v[$pidName] == $pid){//匹配子记录
            $v['children'] = getAttr($arr, $v[$idName], $pidName, $idName); //递归获取子记录
            if($v['children'] == null){
                unset($v['children']);//如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
            }
            $tree[] = $v;//将记录存入新数组
        }
    }
    return $tree;
}

/**
 * getCURL
 * @param $url
 * @return mixed
 */
function curlGet($url){
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    if (curl_errno($curl)) {
        \app\extend\controller\Logservice::writeArray(['err'=>curl_error($curl), 'url'=>$url], 'getCURL失败', 2);
        curl_close($curl); // 关闭CURL会话
        return false;
    }
    //关闭URL请求
    curl_close($curl);
    return $tmpInfo;    //返回json对象
}


/**
 * postCURL
 * @param $url
 * @param $postData
 * @return mixed
 */
function curlPost($url, $postData, $header = array()){
    //请求云端验证
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($header)){
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $output = curl_exec($ch);
    if (curl_errno($ch)) {
        \app\extend\controller\Logservice::writeArray(['err'=>curl_error($ch), 'postData'=>$postData, 'url'=>$url], 'postCURL失败', 2);
        curl_close($ch); // 关闭CURL会话
        return false;
    }

    curl_close($ch); // 关闭CURL会话
    return $output; // 返回数据
}



/**
 * 获取用户列表，不是会员列表
 * 入参 页面、每页数量。任意值不传默认返回全部
 */
 function getUserList($index = 0,$page_size = 0){
     /*定义出参*/
     $return = [
         'list' => [],
         'totalPage' => 1,
         'total' => 0
     ];
     $total = Db::table(TM_PREFIX.'user')->count('user_id');
     $return['total'] = $total;
     if(1 > $index || 0 == $page_size){   //不传参数进来
         $list = Db::table(TM_PREFIX.'user')->field("user_id,real_name")->select();
         if(!empty($list)){
             $return['list'] = $list;
         }
     }else{    //传参数进来
         $totalPage = ceil($total / $page_size);
         $firstRow = ($index - 1) * $page_size;
         $limit = $firstRow . ',' . $page_size;
         $list = Db::table(TM_PREFIX.'user')->field("user_id,real_name")->limit($limit)->select();
         $return['list'] = $list;
         $return['totalPage'] = $totalPage;
     }
     return $return;
}





/*
 * 获取各大网站新闻方法
 * 新闻数据已经由框架保存在tm_common_article 表中
 * 如果用户在天马平台购买了获取各大网站新闻数据的服务，tm_common_article表中的数据会自动实时添加最新新闻
 * $index 页码
 * $pageSize ，每页数量
 *
 * 返回字段
 *          "article_id": 3,
            "aid":"DADASSDADAD131,//文章唯一标示
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
            "from_id": "1"

        column_id对应的栏目名称：
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

        from_id 对应的来源名称
       1：澎湃新闻
*/
function getCommonArticle($index = 1,$pageSize = 20){
    $firstRow = ($index - 1) * $pageSize;
    $limit = $firstRow . ',' . $pageSize;
    $re = Db::table(TM_PREFIX."common_article")->field("aid as 'otheraid',".TM_PREFIX."common_article.*")->limit($limit)->order("article_id desc")->group('aid')->select();
    if(empty($re)){
        return [];
    }
    foreach ($re as &$value){
        unset($value['otheraid']);
    }
    return $re;
}

/*获取微信配置信息*/
function  getWecatpayConfig(){
    $condition = [];
    $condition['key'] = ['wechat_app_id','wechat_mch_id','wechat_key'];
    $condition['type'] = 'payment';
    $config =  Db::table(TM_PREFIX.'config')->where($condition)->select();
    if($config === false){
        return false;
    }
    $ConfigList =  array_column($config,'value','key');
    $ConfigList['wechat_app_id'] = empty($ConfigList['wechat_app_id'])?"":$ConfigList['wechat_app_id'];
    $ConfigList['wechat_mch_id'] = empty($ConfigList['wechat_mch_id'])?"":$ConfigList['wechat_mch_id'];
    $ConfigList['wechat_key'] = empty($ConfigList['wechat_key'])?"":$ConfigList['wechat_key'];
    $ConfigList['apiclient_cert'] = file_exists(Env::get('root_path')."Wechatpayfile/apiclient_cert.pem")?Env::get('root_path')."Wechatpayfile/apiclient_cert.pem":"";
    $ConfigList['apiclient_key'] = file_exists(Env::get('root_path')."Wechatpayfile/apiclient_key.pem")?Env::get('root_path')."Wechatpayfile/apiclient_key.pem":"";
    return $ConfigList;
}

/*获取微信配置信息*/
function  getAlipayConfig(){
    $condition = [];
    $condition['key'] = ['alipay_app_id','alipay_public_key','alipay_private_key'];
    $condition['type'] = 'payment';
    $config =  Db::table(TM_PREFIX.'config')->where($condition)->select();
    if($config === false){
        return false;
    }
    $ConfigList =  array_column($config,'value','key');
    $ConfigList['alipay_app_id'] = empty($ConfigList['alipay_app_id'])?"":$ConfigList['alipay_app_id'];
    $ConfigList['alipay_public_key'] = empty($ConfigList['alipay_public_key'])?"":$ConfigList['alipay_public_key'];
    $ConfigList['alipay_private_key'] = empty($ConfigList['alipay_private_key'])?"":$ConfigList['alipay_private_key'];
    return $ConfigList;
}

/**
 * 消息推送
 * @param $title 标题
 * @param $content 内容
 * @param $url 链接
 * @param $android_info 例子 : ['native'=>true,'src'=>'com.tenma.ventures.usercenter.view.PcWeiduNewActivity','paramStr'=>'','wwwFolder'=>'']
 * @param $ios_info 例子 : ['native'=>true,'src'=>'SetI001MessageController','paramStr'=>'','wwwFolder'=>'',]
 * @param string $type 类型
 * @param int $push_time 时间戳
 * @return bool
 */

function pushMessage($title,$content,$url,$android_info,$ios_info,$type = '系统消息',$push_time = ''){
    $inputData['title'] = $title;
    $inputData['content'] = $content;
    $inputData['url'] = $url;
    $inputData['ios_info'] = json_encode($ios_info);
    $inputData['android_info'] = json_encode($android_info);
    $inputData['type'] = $type;
    $inputData['add_time'] = time();
    $inputData['push_time'] = $push_time > $inputData['add_time']?$push_time:$inputData['add_time']; //如果存在推送时间并大于当前时间 则定时推送
    $inputData['status'] = 1;
    $PushMessageModel = new \app\member\model\PushMessageModel();
    $result = $PushMessageModel->addInfo($inputData);
    if($result){
        $PushMessageModel->addPushMessage($result);
    }
    if($result){
        $jobController = new \app\queue\controller\Job();
        $jobController->actionPushMessage();
        $jobController->actionGetRes();
        return true;
    }
    return false;
}
/*生成支付宝签名
入参：
$out_trade_no 订单号  必须
$total_amount 商品价格 单位 ：分 币种：人民币  必须
$notifyUrl 异步回调地址
$subject  商品标题 必须

返回值：如果签名失败返回false，签名成功返回一段签名后的字符串，可以不做任何修改客户端就能使用
*/
function signAlipay($out_trade_no = 0,$total_amount = 0,$notifyUrl = "",$subject = "商品标题"){
    $alipayConfig = getAlipayConfig();
    if(empty($alipayConfig['alipay_app_id']) || empty($alipayConfig['alipay_public_key']) || empty($alipayConfig['alipay_private_key'])){
        return false;
    }
    require_once '../vendor/alipay/AopClient.php';
    require_once '../vendor/alipay/AlipayTradeAppPayRequest.php';

    try{
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        $aop->appId = $alipayConfig['alipay_app_id'];

        $aop->rsaPrivateKey = $alipayConfig['alipay_private_key'];
        $aop->alipayrsaPublicKey = $alipayConfig['alipay_public_key'];
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $request = new \AlipayTradeAppPayRequest();
        $array=array(
            'out_trade_no'=>$out_trade_no,
            'subject'=>$subject,
            'product_code'=>'QUICK_MSECURITY_PAY',
            'total_amount'=>$total_amount/100,
            "timeout_express"=>"30m"
        );
        if(!empty($body)){
            $array['body']  =  $body;
        }
        $json=json_encode($array);
        $aop->notifyUrl=$notifyUrl;
        $request->setBizContent($json);
        $request->setNotifyUrl($notifyUrl);

        $result = $aop->sdkExecute ( $request);
        return $result;

    } catch(Exception $e) {
        return false;
    }
}

/*
 * 验证阿里支付异步回调是否正确
 * 入参：无
 * 返回值：验证失败返回false。验证成功返回一个数组，格式如下：
 * [
        "out_trade_no"=>"21231312", //订单号
        "total_amount"=>1 //订单金额，单位分
    ];
 * 备注：
 * 1该方法会直接接受支付宝异步回调返回的值，因此调用该方法之前不能将$_POST里面的值做修改
 * 2：验证成功表示交易成功，否则失败。开发者应该在业务代码中再验证一遍订单金额是否正确
*/
function checkAlipayNotify(){
    $alipayConfig = getAlipayConfig();
    if(empty($alipayConfig['alipay_app_id']) || empty($alipayConfig['alipay_public_key']) || empty($alipayConfig['alipay_private_key'])){
        return false;
    }
    include_once '../vendor/alipay/AopSdk.php';
    include_once '../vendor/alipay/wappay/service/AlipayTradeService.php';
    try{
        $payset=[];
        $payset['charset']='UTF-8';
        $payset['sign_type']='RSA2';
        $payset['gatewayUrl']='https://openapi.alipay.com/gateway.do';
        $payset['app_id'] = $alipayConfig['alipay_app_id'];
        $payset['merchant_private_key'] = $alipayConfig['alipay_private_key'];
        $payset['alipay_public_key'] = $alipayConfig['alipay_public_key'];
        $alipaySevice = new \AlipayTradeService($payset);
        $verify_result = $alipaySevice->check($_POST);
        if(!$verify_result){
            return false;
        }
        if("TRADE_SUCCESS" != $_POST['trade_status']){
            return false;
        }
        return [
            "out_trade_no"=>$_POST['out_trade_no'],
            "total_amount"=>$_POST['total_amount']*100
        ];
    } catch(Exception $e) {
        return false;
    }
}

/*生成微信签名
入参：
$out_trade_no 订单号  必须
$total_amount 商品价格 单位 ：分 币种：人民币  必须
$notifyUrl 异步回调地址
$subject  商品标题 必须

返回值：如果签名失败返回false。签名成功返回一个数组转的json，数组格式如下：
array(7) {
  ["appid"] => string(18) "wxf5434529e3d5f55c"
  ["noncestr"] => string(16) "0KJQGD8Sm9AAawFn"
  ["package"] => string(10) "Sign=WXPay"
  ["partnerid"] => string(10) "1518233991"
  ["prepayid"] => string(36) "wx14150211047841e41f7532623622978451"
  ["timestamp"] => int(1542178931)
  ["sign"] => string(32) "C0C4CB308E7EB88B01F519ECF5071764"
}
*/
function signWechat($out_trade_no = 0,$total_amount = 0,$notifyUrl = "",$subject = "商品标题"){
    $wecatpayConfig = getWecatpayConfig();
    if(empty($wecatpayConfig['wechat_app_id']) || empty($wecatpayConfig['wechat_mch_id']) || empty($wecatpayConfig['wechat_key'])){
        return false;
    }
    $payset = [
        "appid"=>$wecatpayConfig['wechat_app_id'],
        "mchid"=>$wecatpayConfig['wechat_mch_id'],
        "key"=>$wecatpayConfig['wechat_key']
    ];
    $payset['notify_url']=$notifyUrl;
    include_once '../vendor/Wxpay/WxPay.Api.php';
    include_once '../vendor/Wxpay/WxPay.Data.php';
    try{
        $unifiedOrder = new \WxPayUnifiedOrder();
        $unifiedOrder->SetBody($subject); //商品或支付单简要描述
        $unifiedOrder->SetOut_trade_no($out_trade_no);
        $unifiedOrder->SetTotal_fee($total_amount);
        $unifiedOrder->SetTrade_type("APP");
        $unifiedOrder->SetNotify_url($notifyUrl);
        $result = \WxPayApi::unifiedOrder($unifiedOrder,6,$payset);
        return json_encode($result);
    } catch(Exception $e) {
        return false;
    }

}

/*
 * 验证微信支付异步回调是否正确
 * 入参：无
 * 返回值：验证失败返回false。验证成功返回一个数组，格式如下：
 * [
        "out_trade_no"=>"21231312", //订单号
        "total_amount"=>1 //订单金额，单位分
    ];
 * 备注：
 * 1：验证成功表示交易成功，否则失败。开发者应该在业务代码中再验证一遍订单金额是否正确
*/
function checkWechatNotify(){

    $wecatpayConfig = getWecatpayConfig();
    if(empty($wecatpayConfig['wechat_app_id']) || empty($wecatpayConfig['wechat_mch_id']) || empty($wecatpayConfig['wechat_key'])){
        return false;
    }
    $postXml =  file_get_contents('php://input');
    $arr = json_decode(json_encode(simplexml_load_string($postXml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    if(empty($arr) || !is_array($arr)){
        return false;
    }
    ksort($arr);
    $buff = '';
    foreach ($arr as $k => $v){
        if($k != 'sign'){
            $buff .= $k . '=' . $v . '&';
        }
    }
    $stringSignTemp = $buff . 'key='.$wecatpayConfig['wechat_key'];
    $sign = strtoupper(md5($stringSignTemp));
  //判断算出的签名和通知信息的签名是否一致
    if($sign != $arr['sign']){
        return false;
    }
    if("SUCCESS" != $arr['result_code']){
        return false;
    }
    return [
        "out_trade_no"=>$arr['out_trade_no'],
        "total_amount"=>$arr['total_fee']
    ];
}

/*
 * 生成支付签名。
 * 该方法是支付宝，微信支付签名的集合，将来如果新增其它支付方式，也会增加到里面
     * 入参：
    $type 支付类型 1支付宝，2微信   必须
    $out_trade_no 订单号  必须
    $total_amount 商品价格 单位 ：分 币种：人民币  必须
    $notifyUrl 异步回调地址  必须
    $subject  商品标题 必须

    返回值：如果签名失败返回false。如果签名成功返回对应支付类型的签名结果：
    $type=1(支付宝)返回一段签名后的字符串，可以不做任何修改客户端就能使用：
    $type=2(微信)返回一个数组转的json，和微信官网demo返回的一样，数组格式如下：
    array(7) {
      ["appid"] => string(18) "wxf5434529e3d5f55c"
      ["noncestr"] => string(16) "0KJQGD8Sm9AAawFn"
      ["package"] => string(10) "Sign=WXPay"
      ["partnerid"] => string(10) "1518233991"
      ["prepayid"] => string(36) "wx14150211047841e41f7532623622978451"
      ["timestamp"] => int(1542178931)
      ["sign"] => string(32) "C0C4CB308E7EB88B01F519ECF5071764"
    }
 * */
function paySign($type =0,$out_trade_no = 0,$total_amount = 0,$notifyUrl = "",$subject = "商品标题"){
    if(empty($type)){
        return false;
    }
    if(1==$type){
        return signAlipay($out_trade_no,$total_amount,$notifyUrl,$subject);
    }elseif (2==$type){
        return signWechat($out_trade_no,$total_amount,$notifyUrl,$subject);
    }
}

/*
 * 验证异步回调是否正确，支付宝和微信的异步回调验证都可以用这个方法
 * 入参：无
 * 返回值：验证失败返回false。验证成功返回一个数组，格式如下：
 * [
        "out_trade_no"=>"21231312", //订单号
        "total_amount"=>1, //订单金额，单位分
        "type"=>1, //类型，1代表支付宝回调，2代表微信回调
    ];
 * 备注：
 * 1：验证成功表示交易成功，否则失败。开发者应该在业务代码中再验证一遍订单金额是否正确
*/
function checkNotify(){
    if(!empty($_POST['app_id'])){  //支付宝,只有支付宝才有这个字段
        $checkAlipayNotify = checkAlipayNotify();
        if(is_array($checkAlipayNotify)){
            $checkAlipayNotify['type'] = 1;
        }
        return $checkAlipayNotify;
    }else{
        $checkWechatNotify = checkWechatNotify();  //微信
        if(is_array($checkWechatNotify)){
            $checkWechatNotify['type'] = 2;
        }
        return $checkWechatNotify;
    }
}

/*
 * 支付异步回调处理完成后返回第三方（支付宝，微信）的值
 *    * 入参：
    $type 支付类型 1支付宝，2微信

    返回值：该方法会直接返回第三方需要的异步回调返回值
 * */
function returnNotify($type = 1){
 if(1==$type){
    return "success";
 }elseif (2==$type){
     $ret = ['return_code'=>'SUCCESS','return_msg'=>'OK'];
     $xml = '<xml>';
     foreach($ret as $k=>$v){
         $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
     }
     $xml.='</xml>';
     return $xml;
 }
}