<?php
header("Content-Type:text/html;charset=utf-8");
//echo date_default_timezone_get();die;
function genSign(array $param) {
    //0. 删除原数据中自带的sign值,防止干扰计算结果
    unset($param['sign']);

    //1. 按key由a到z排序
    ksort($param);



    foreach ($param as $key => $value) {
        if (is_array($value)) {
            $param[$key] = genSign($value);
        }
    }

    //2. 生成以&符链接的key=value形式的字符串
    $paramString = urldecode(http_build_query($param));

    var_dump($paramString);


    //3. 拼接我们的服务秘钥，并md5加密
    $sign = md5($paramString . 'secret@9maibei.com');

    echo "string:" . $paramString, "\n\n", "param:", var_dump($param), "sign:", $sign, "\n\n";

    return $sign;
}


//本地环境
$api_url = 'http://laraapi.com/api/';


//公共参数
$xml_data = array(
    "token"=>"", //除部分接口调用时还没用token之外，都要上传
    "version"=>"2.1.0",//1.0.0 当前app的版本号
    "type"=>"0",//0:h5 1:android 2:ios 3:winphone
    "timestamp"=>time(),
);


//检查版本更新
//$data = array(
//    "current_version"=> "1.0.0",//客户端当前版本
//    "app_type"=> "1", //1:android客户端，2:android商户端，3:ios客户端，4:ios商户端，5:winphone客户端，6:phone商户端
//    "channel_id"=> "1313",//渠道号"
//    "device_id"=> "h5"//设备id
//);
//$sign = genSign($data);
//$url = $api_url."Home/System/checkUpgrade";
//$data['sign'] = $sign;


//获取预下单数据
$data = array(
    "phone" => 18612052726,
);
$url = $api_url.'verificationCodes';




echo '<br /><br />';
echo $url;
echo '<br /><br />';

//传输的最后数据
//$xml_data['data'] = $data;
//var_dump($xml_data);
//$json_data =   json_encode($xml_data);

$json_data =   json_encode($data);


echo "<br /><br />------------------------------------------------------------------ <br /><br />";


echo $json_data;
$response = curl_init_get($url, $json_data);

echo "<br /><br /><br />";

//打印返回的数据
print_r(json_decode($response,true));
//var_dump(json_decode($response,true));


echo "<br />------------------------------------------------------------------<br /><br />";

echo "<font color='red'>".decodeUnicode($response)."</font>";
//echo "<font color='red'>".$response."</font>";
// if(json_decode($response)) $response = json_decode($response);
//return var_dump($result);
//echo "<xmp>";
//	echo "<pre>";
//	print_r($response);





function curl_init_get($url, $xml_data)
{
    $ch=curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);

//    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//        'Content-Type: application/json',
//    ));

    $response = curl_exec($ch);
    if(curl_errno($ch))
    {
        print curl_error($ch);
    }
    return $response;
    curl_close($ch);
}


function decodeUnicode($str) { return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function( '$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");' ), $str); }


//模拟上传图片
function buildCurlimg($param,$url){
    //  初始化
    $ch = curl_init();
    // 要上传的本地文件地址"@F:/xampp/php/php.ini"上传时候，上传路径前面要有@符号
//		$post_data = array (
//				"upload" => $furl
//		);
    //print_r($post_data);
    //CURLOPT_URL 是指提交到哪里？相当于表单里的“action”指定的路径
    //$url = "http://localhost/DemoIndex/curl_pos/";
    //  设置变量
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//执行结果是否被返回，0是返回，1是不返回
    curl_setopt($ch, CURLOPT_HEADER, 0);//参数设置，是否显示头部信息，1为显示，0为不显示
    //伪造网页来源地址,伪造来自百度的表单提交
    curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
    //表单数据，是正规的表单设置值为非0
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);//设置curl执行超时时间最大是多少
    //使用数组提供post数据时，CURL组件大概是为了兼容@filename这种上传文件的写法，
    //默认把content_type设为了multipart/form-data。虽然对于大多数web服务器并
    //没有影响，但是还是有少部分服务器不兼容。本文得出的结论是，在没有需要上传文件的
    //情况下，尽量对post提交的数据进行http_build_query，然后发送出去，能实现更好的兼容性，更小的请求数据包。
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    //   执行并获取结果
    curl_exec($ch);
    if(curl_exec($ch) === FALSE)
    {
        echo "<br/>"," cUrl Error:".curl_error($ch);
    }
    //  释放cURL句柄
    curl_close($ch);
}
