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
            if($data[$value] != 0 && $data[$value] != '0' ){
                if(empty($data[$value])){
                    $result = '必传参数不能为空';
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
function curlPost($url, $postData){
    //请求云端验证
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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