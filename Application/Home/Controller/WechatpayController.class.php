<?php
namespace Home\Controller;
use Think\Controller;
class WechatpayController extends BaseController {

    // 微信JsApi支付
    public function jsapi(){
        if($_GET['recharge']==1){
            $uid=$_GET['uid'];
            $goods = '用户充值';
            $order_sn = time().$uid;
            $total_fee = bcmul($_GET['money'],100,0);
            $attach = $uid;
            $this->assign('money',$_GET['money']);
            $this->assign('gift',1);
        }

        if($_GET['indent']==1){
            $orderId=$_GET['orderId'];
            $re=M('service_indent')->where('id='.$orderId)->field('payment_amount,serial_number,sid')->find();
            $goods = '订单支付';
            $order_sn = $re['serial_number'];
            $total_fee = bcmul($re['payment_amount'],100,0);
            $attach = 'indent_pay';
            $this->assign('money',$re['payment_amount']);
        }
         $openId = '';
        $jsApiParameters = wxJsapiPay($openId,$goods,$order_sn,$total_fee,$attach);
        // assign
        $this->assign('jsApiParameters',$jsApiParameters);
        // display
        $this->display();
        die;
    }
    // 微信Native支付
    public function native(){
        if($_GET['recharge']==1){
            $uid=$_GET['uid'];
            $goods = '用户充值';
            $order_sn = time().$uid;
            $total_fee = bcmul($_GET['money'],100,0);
            $productId = 7;
            $attach = $uid;
        }
        if($_GET['indent']==1){
            $orderId=$_GET['orderId'];
            $re=M('service_indent')->where('id='.$orderId)->field('payment_amount,serial_number,sid,ctime')->find();
            $goods = '订单支付';
            $order_sn = $re['serial_number'];
            $total_fee = bcmul($re['payment_amount'],100,0);
            $productId = $re['sid'];
            $attach = 'indent_pay';
        }
        $code_url = wxNativePay($goods,$order_sn,$total_fee,$productId,$attach);
        QRcode($code_url);
    }

    // 微信支付回调
    public function notify(){

        //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = file_get_contents("php://input");

        // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
        file_put_contents(__ROOT__."/Runtime/Logs/checkNotify.txt",$xml.PHP_EOL,FILE_APPEND);

        //将服务器返回的XML数据转化为数组
        //$data = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        $data = wx_xmlToArray($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        unset($data['sign']);
        $sign = makeSign($data);

        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') && ($data['result_code']=='SUCCESS') ) {
            $result = $data;
            // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
            file_put_contents(__ROOT__."/Runtime/Logs/okNotify.txt",$xml.PHP_EOL,FILE_APPEND);

            //获取服务器返回的数据
            $order_sn = $data['out_trade_no'];  //订单单号
            $order_id = $data['attach'];        //附加参数,选择传递订单ID
            $openid = $data['openid'];          //付款人openID
            $total_fee = $data['total_fee'];    //付款金额


            $time=substr($order_sn,0,strlen(time()));
            $uid=substr($order_sn,strlen(time()));
            if($order_id===$uid){
                $data=array('uid'=>$uid,
                    'money'=>bcdiv($total_fee,100,2),
                    'ctime'=>$time,
                    'type'=>0,//充值类型代码 0
                    'status'=>1,//操作成功
                );
                $record=M('account')->data($data)->add();
                //查询账户余额
                $yue=M('userinfo')->where('uid='.$uid)->field('balance')->find();
                $finalMoney=bcadd($yue['balance'],bcdiv($total_fee,100,2),2);//当前余额累加起来
                //保存入库
                $yue=M('userinfo')->where('uid='.$uid)
                    ->data(array('balance'=>$finalMoney))
                    ->save();
            }

            if($order_id==='indent_pay'){
                M('service_indent')->data(array('type'=>1,'status'=>1))->where('serial_number='.$order_sn)->save();
            }
            //更新数据库
            //$this->updateDB($order_id,$order_sn,$openid,$total_fee);
        }else{
            $result = false;
        }
        // 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;
    }

}