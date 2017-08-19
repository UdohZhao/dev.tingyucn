<?php
namespace Home\Controller;
use Think\Controller;
class RechargeController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    // 充值
    public function index(){
      // display
        //累计充值
        $this->assign('uid',$this->uid);
        $allMoney=M('account')->where('uid='.$this->uid.' and type=0 and status=1')->select();
        $starMoney=0;
        foreach($allMoney as $key=>$val){
            $starMoney+=bcadd($val['money'],0,2);
        }
        $this->assign('allMoney',$starMoney);
        if($_GET['gift'] && $_SESSION['gift']==1){
            $giftMoney=end($allMoney);
            $realMoney=$giftMoney['money'];
            if($realMoney>=100){
                $this->assign('gift',1);
            }
        }
        //获取所有服务类型
        $serviceType=M('service_category')->select();
        $TypeInfo=M('service_category')->group('type')->field('type')->select();
        foreach($TypeInfo as $Tkye=>$Tval){
            foreach ($serviceType as $key=>$val){
                if($val['type']==$Tval['type']){
                    $TypeInfo[$Tkye]['typeDetail'][$key]['cname']=$val['cname'];
                    $TypeInfo[$Tkye]['typeDetail'][$key]['id']=$val['id'];
                }
            }
            if($Tval['type']==0){
                $TypeInfo[$Tkye]['type']='线上手游';
            }else if($Tval['type']==1){
                $TypeInfo[$Tkye]['type']='线上网游';
            }else if($Tval['type']==2){
                $TypeInfo[$Tkye]['type']='线下娱乐';
            }
        }
        $this->assign('TypeInfo',$TypeInfo);
      $this->display();
    }



    //充值
    public function shop(){
        $money=I('post.money');//充值金额
        if($money>=100){
            $get=1;
        }else{
            $get='';
        }
        //累计充值
        $LjMoney=M('account')->where('uid='.$this->uid.' and type=0 and status=1')->select();
        $starMoney=0;
        foreach($LjMoney as $key=>$val){
            $starMoney+=bcadd($val['money'],0,2);
        }
        //调用充值函数,测试充值成功
        $status=false;
        if($status){
            //记录充值记录
            $data=array('uid'=>$this->uid,
                'money'=>$money,
                'ctime'=>time(),
                'type'=>0,//充值类型代码 0
                'status'=>1,//操作成功
            );
            $record=M('account')->data($data)->add();
            //查询账户余额
            $yue=M('userinfo')->where('uid='.$this->uid)->field('balance')->find();
            $finalMoney=bcadd($yue['balance'],$money,2);//当前余额累加起来
            //保存入库
            $yue=M('userinfo')->where('uid='.$this->uid)
                ->data(array('balance'=>$finalMoney))
                ->save();
            if($record){
                $this->ajaxReturn(array('info'=>true,'msg'=>$get,'finalMoney'=>$finalMoney,'money'=>$money,'LjMoney'=>$starMoney));
            }
        }else{
            //记录充值记录
            $data=array('uid'=>$this->uid,
                'money'=>$money,
                'ctime'=>time(),
                'type'=>0,//充值类型代码 0
                'status'=>0,//操作失败
            );
            $record=M('account')->data($data)->add();
            if($record){
                $this->ajaxReturn(false);
            }
        }
    }

    public function gift(){
        $scid=I('post.scid');
        //赠送金额  20;
        //时间期限,当前时间加7天时间的以后一刻
        $needTime=strtotime(date('Y-m-d',time()))+3600*24*8-1;

        $info=array(0=>array('id'=>$this->uid));
        $info=serialize($info);//用户id数组序列化

        $data=array(
            'price'=>20,
            'end_time'=>$needTime,
            'explain'=>'充满100送20优惠券',
            'uids'=>$info,
            'scid'=>$scid,
            'cname'=>'充值优惠券',
            'status'=>0
        );

        $arr=M('discount_coupon')->data($data)->add();
        if($arr){
            unset($_SESSION['gift']);
            $this->ajaxReturn(true);
        }
    }

    // 支付宝PC端支付demo
    public function pc(){
        //构造参数
        $money=I('get.money');
        //获取订单支付状态

        $body = '账户充值';
        $subject = '账户充值';
        $total_amount = $money;
        $out_trade_no = time().$this->uid;
        $return_url='http://'.$_SERVER['HTTP_HOST']."/index.php/".CONTROLLER_NAME."/returnUrl";
        $notify_url='http://'.$_SERVER['HTTP_HOST']."/index.php/".CONTROLLER_NAME."/notifyUrl";
        alipayPC($body,$subject,$total_amount,$out_trade_no,$return_url,$notify_url);
    }

    // returnUrl
    public function returnUrl(){
        Vendor('Alipay.PC.pagepay.service.AlipayTradeService');
        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService(C('ALIPAY'));
        $result = $alipaySevice->check($arr);
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);
            //echo "验证成功<br />支付宝交易号：".$trade_no;
            //记录充值记录
            $num=substr($out_trade_no,0,strlen($out_trade_no)-strlen($this->uid));
            $data=array('uid'=>$this->uid,
                'money'=>$_GET['total_amount'],
                'ctime'=>$num,
                'type'=>0,//充值类型代码 0
                'status'=>1,//操作成功
            );
            $record=M('account')->data($data)->add();
            //查询账户余额
            $yue=M('userinfo')->where('uid='.$this->uid)->field('balance')->find();
            $finalMoney=bcadd($yue['balance'],$_GET['total_amount'],2);//当前余额累加起来
            //保存入库
            $yue=M('userinfo')->where('uid='.$this->uid)
                ->data(array('balance'=>$finalMoney))
                ->save();

            $LjMoney=M('account')->where('uid='.$this->uid.' and type=0 and status=1')->select();
            //取出最后一次充值记录
            $finalMoney=end($LjMoney);
            $money=$finalMoney['money'];

            if($money>=100){
                $_SESSION['gift']=1;
                header("Location:index?gift=1");
            }else{
                header("Location:index");
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "验证失败";
        }
    }

    // notifyUrl
    public function notifyUrl(){

        Vendor('Alipay.PC.pagepay.service.AlipayTradeService');
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService(C('ALIPAY'));
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知

            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success"; //请不要修改或删除
        }else {
            //验证失败
            echo "fail";

        }
    }
}

