<?php
//对比验证码
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

// 密码加密
function iPassword($password){
    return md5(crypt($password,substr($password,0,2)));
}

// 图片上传
function upload($size){
    $upload = new \Think\Upload();// 实例化上传类
    if(!$size){
        $size=5242880;
    }
    $upload->maxSize   =     $size ;// 设置附件上传大小
    $upload->exts      =     array('jpg', 'gif',
                                'png', 'jpeg',
                    'mp3','wma','aac','ogg',
                                'mp4','m4a');// 设置附件上传类型
    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
    $upload->savePath  =     ''; // 设置附件上传（子）目录
    // 上传文件
    $info   =   $upload->upload();
    if(!$info) {// 上传错误提示错误信息
        return array('status'=>4,'errorInfo'=>$upload->getError());
    }else{// 上传成功
        return $info;
    }
}
//前端删除图片函数
function homedelpath($path){
    $path=".".$path;
    $result=@unlink($path);
    return $result;
}
//后台删除图片函数
function delpath($path){
    $str=substr($path,strlen("/Admin"));
    $path=".".$str;
    $result=@unlink($path);
    return $result;
}

//移动端删除图片函数
function webdelpath($path){
    $str=substr($path,strlen("/Wechat"));
    $path=".".$str;
    $result=@unlink($path);
    return $result;
}

//弹出窗口
function alert($msg='',$url='',$icon='',$time=3){
    $str='<script type="text/javascript" src="/Public/Common/jquery/jquery.js"></script><script type="text/javascript" src="/Public/Common/layer/layer.js"></script>';//加载jquery和layer
    $str.='<script>$(function(){layer.alert("'.$msg.'",{icon:'.$icon.',time:'.($time*1000).'});setTimeout(function(){self.location.href="'.$url.'"},2000)});</script>';//主要方法
    return $str;
}


//短信接口
// xml对象转数组
function xmlToArray($simpleXmlElement){
    $simpleXmlElement=(array)$simpleXmlElement;
    foreach($simpleXmlElement as $k=>$v){
        if($v instanceof SimpleXMLElement ||is_array($v)){
            $simpleXmlElement[$k]=xmlToArray($v);
        }
    }
    return $simpleXmlElement;
}

// 生成随机数
function generate_code($length = 6) {
    $min = pow(10 , ($length - 1));
    $max = pow(10, $length) - 1;
    return rand($min, $max);
}

// 发送阿里大于短信验证码
function SendSignInCode($phone,$code,$signName,$product,$templateCode){
    // 引入阿里大于入口文件
    Vendor('AlidayuMsg.TopSdk');
    $c = new \TopClient();
    $c->appkey = C('APPKEY');
    $c->secretKey = C('APPSECRET');
    // 引入阿里大于短信类
    $req = new \AlibabaAliqinFcSmsNumSendRequest();
    $req->setExtend($code);
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($signName);
    $req->setSmsParam('{"code":"'.$code.'","product":"'.$product.'"}');
    $req->setRecNum($phone);
    $req->setSmsTemplateCode($templateCode);
    // 获取结果
    return $c->execute($req);
}



//阿里大于短信
/*@param model 模板id值
 * */
 function alidy($phone,$code,$model){
     // 发送短信验证码DEMO
     // $code = generate_code();
     // 获取结果
     $res = SendSignInCode($phone, $code, C('SIGN_NAME'), C('PRODUCT'), $model);
     $res = xmlToArray($res);
     // 成功 or 失败 ？
     if ($res['result']['success'] === 'true') {
         return true;
     } else {
         return false;
     }
 }
//二维数组排序
function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return $arrays;
}

//发送短信
function codeMsg($phone,$code)
{
    $smsOperator=objectSend();
    $infoContent='您的验证码是'.$code;
    $data['mobile'] = $phone;
    $data['text'] = '【听娱神游约玩】'.$infoContent;
    $result = $smsOperator->single_send($data);
    if($result->statusCode==200){
        return true;
    }else{
        return false;
    }
}
// 生成订单号
function indent_number(){
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

// 订单发送单条短信
/*@param phone 电话号码
 * @param name 签约用户姓名
 * @param order_number 订单号
 * */
function send_info($phone,$name,$order_number){
    //require_once '../../../ThinkPHP/Library/Vendor/YunpianAPI/YunpianAutoload.php';
    //$smsOperator = new SmsOperator();
    $smsOperator=objectSend();
    $infoContent="尊敬的".$name."，您有一个工单号为：".$order_number."的待处理工单，请您及时处理！";
    $data['mobile'] = $phone;
    $data['text'] = '【听娱神游约玩】'.$infoContent;
    $result = $smsOperator->single_send($data);
    return $result;
    /*if($result->statusCode==200){
        return true;
    }else{
        return false;
    }*/
}

//通知普通用户短信
function send_to_user($phone,$name,$order_number){
    //require_once '../../../ThinkPHP/Library/Vendor/YunpianAPI/YunpianAutoload.php';
    //$smsOperator = new SmsOperator();
    $smsOperator=objectSend();
    $infoContent="尊敬的".$name."，您申请的工单号为：".$order_number."已经接单，请您及时处理！";
    $data['mobile'] = $phone;
    $data['text'] = '【听娱神游约玩】'.$infoContent;
    $result = $smsOperator->single_send($data);
    return $result;
    /*if($result->statusCode==200){
        return true;
    }else{
        return false;
    }*/
}

//通知管理员短信
function send_to_admin($phone,$name){
    //require_once '../../../ThinkPHP/Library/Vendor/YunpianAPI/YunpianAutoload.php';
    //$smsOperator = new SmsOperator();
    $smsOperator=objectSend();
    $infoContent="尊敬的".$name."，有用户申请了实名认证待审核，请您及时处理！";
    $data['mobile'] = $phone;
    $data['text'] = '【听娱神游约玩】'.$infoContent;
    $result = $smsOperator->single_send($data);
    return $result;
    /*if($result->statusCode==200){
        return true;
    }else{
        return false;
    }*/
}

//通知用户审核状态短信
function send_status_to_admin($phone,$name,$status){
    //require_once '../../../ThinkPHP/Library/Vendor/YunpianAPI/YunpianAutoload.php';
    //$smsOperator = new SmsOperator();
    $smsOperator=objectSend();
    $infoContent="尊敬的".$name."，您本次申请的实名认证审核".$status."！";
    $data['mobile'] = $phone;
    $data['text'] = '【听娱神游约玩】'.$infoContent;
    $result = $smsOperator->single_send($data);
    return $result;
    /*if($result->statusCode==200){
        return true;
    }else{
        return false;
    }*/
}
function objectSend(){
    Vendor('YunpianApi.YunpianAutoload');
    $smsOperator = new \SmsOperator();
    return $smsOperator;
}

/**
 * alipayPC 支付宝PC端支付
 * @param $body  商品描述
 * @param $subject  订单名称
 * @param $total_amount  付款金额
 * @param $out_trade_no  商户订单号
 */
function alipayPC($body,$subject,$total_amount,$out_trade_no,$return_url,$notify_url){
    Vendor('Alipay.PC.pagepay.service.AlipayTradeService');
    Vendor('Alipay.PC.pagepay.buildermodel.AlipayTradePagePayContentBuilder');
    //构造参数
    $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
    $payRequestBuilder->setBody($body);
    $payRequestBuilder->setSubject($subject);
    $payRequestBuilder->setTotalAmount($total_amount);
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    $aop = new \AlipayTradeService(C('ALIPAY'));
    $response = $aop->pagePay($payRequestBuilder,$return_url,$notify_url);
    return $response;
}

/**
 * alipayWap 支付宝Wap端支付
 * @param $body  商品描述
 * @param $subject  订单名称
 * @param $total_amount  付款金额
 * @param $out_trade_no  商户订单号
 * @param $timeout_express  超时时间
 */
function alipayWap($body,$subject,$total_amount,$out_trade_no,$return_url,$notify_url,$timeout_express='1m'){
    Vendor('Alipay.Wap.wappay.service.AlipayTradeService');
    Vendor('Alipay.Wap.wappay.buildermodel.AlipayTradeWapPayContentBuilder');
    //构造参数
    $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
    $payRequestBuilder->setBody($body);
    $payRequestBuilder->setSubject($subject);
    $payRequestBuilder->setTotalAmount($total_amount);
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    $payRequestBuilder->setTimeExpress($timeout_express);
    $payResponse = new \AlipayTradeService(C('ALIPAY'));
    $result=$payResponse->wapPay($payRequestBuilder,$return_url,$notify_url);
    return ;
}


//微信支付
function wxJsapiPay($openId,$goods,$order_sn,$total_fee,$attach){
    Vendor('wxpay.WxPay#Api');
    Vendor('wxpay.WxPay#JsApiPay');
    Vendor('wxpay.log');

    //初始化日志
    $logHandler= new \CLogFileHandler("./logs/".date('Ymd').'.log');
    $log = \Log::Init($logHandler, 15);

    $tools = new \JsApiPay();
    if(empty($openId)) $openId = $tools->GetOpenid();

    $input = new \WxPayUnifiedOrder();
    $input->SetBody($goods);                 //商品名称
    $input->SetAttach($attach);                  //附加参数,可填可不填,填写的话,里边字符串不能出现空格
    $input->SetOut_trade_no($order_sn);          //订单号
    $input->SetTotal_fee($total_fee);            //支付金额,单位:分
    $input->SetTime_start(date("YmdHis"));       //支付发起时间
    $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
    $input->SetGoods_tag("test3");
    //$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/payment.php");  //支付回调验证地址
    $input->SetNotify_url("http://".$_SERVER['SERVER_NAME']."/Wechatpay/notify");
    $input->SetTrade_type("JSAPI");              //支付类型
    $input->SetOpenid($openId);                  //用户openID
    $order = \WxPayApi::unifiedOrder($input);    //统一下单

    $jsApiParameters = $tools->GetJsApiParameters($order);

    return $jsApiParameters;
}
//扫码支付
function wxNativePay($goods,$order_sn,$total_fee,$productId,$attach){
    Vendor('wxpay.WxPay#Api');
    Vendor('wxpay.WxPay#NativePay');
    Vendor('wxpay.log');

    //初始化日志
    $logHandler= new \CLogFileHandler("./logs/".date('Ymd').'.log');
    $log = \Log::Init($logHandler, 15);

    $notify = new \NativePay();

    // 统一下单
    $input = new \WxPayUnifiedOrder();
    $input->SetBody($goods);                 //商品名称
    $input->SetAttach($attach);                  //附加参数,可填可不填,填写的话,里边字符串不能出现空格
    $input->SetOut_trade_no($order_sn);          //订单号
    $input->SetTotal_fee($total_fee);            //支付金额,单位:分
    $input->SetTime_start(date("YmdHis"));       //支付发起时间
    $input->SetTime_expire(date("YmdHis", time() + 600));//支付超时
    $input->SetGoods_tag("test3");
    //$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/payment.php");  //支付回调验证地址
    $input->SetNotify_url("http://".$_SERVER['SERVER_NAME']."/Wechatpay/notify");
    $input->SetTrade_type("NATIVE");              //支付类型

    $input->SetProduct_id($productId);
    $result = $notify->GetPayUrl($input);
    return $result["code_url"];
}
/**
 * 生成签名
 * @return 签名，本函数不覆盖sign成员变量
 */
function makeSign($data){
    //获取微信支付秘钥
    Vendor('wxpay.WxPay#Api');
    $key = \WxPayConfig::KEY;
    // 去空
    $data=array_filter($data);
    //签名步骤一：按字典序排序参数
    ksort($data);
    $string_a=http_build_query($data);
    $string_a=urldecode($string_a);
    //签名步骤二：在string后加入KEY
    //$config=$this->config;
    $string_sign_temp=$string_a."&key=".$key;
    //签名步骤三：MD5加密
    $sign = md5($string_sign_temp);
    // 签名步骤四：所有字符转为大写
    $result=strtoupper($sign);
    return $result;
}

/**
 * @param string $url 二维码链接
 */
function QRcode($url){
    Vendor('wxpay.phpqrcode.phpqrcode');
    $url = urldecode($url);
    // 纠错级别：L、M、Q、H
    $level = 'L';
    // 点的大小：1到10,用于手机端4就可以了
    $size = 10;
    return \QRcode::png($url, false, $level, $size);
}

// xml转换成数组
function wx_xmlToArray($xml){

    //禁止引用外部xml实体

    libxml_disable_entity_loader(true);

    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

    $val = json_decode(json_encode($xmlstring),true);

    return $val;

}