<?php
namespace Home\Controller;
use Think\Controller;
class MyorderController extends BaseController {
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    // 我的订单
    public function index(){
      // display
        //查询订单页面  联表服务类型  服务表  评价表
        $userType=M('user')->where('id='.$this->uid)->find();
        $this->assign('userType',$userType);
        if($userType['type']==0){//普通用户 只查看自己的订单信息
            $orderInfo=M('service_indent')->where('si.uid='.$this->uid)
                ->join('left join service as s on si.sid=s.id')
                ->join('left join service_category as sc on s.scid=sc.id')
                ->join('left join service_estimate as se on se.order_id=si.id')
                ->join('left join userinfo as u on u.uid=s.uid and si.sid=s.id')
                ->join('left join identity_authentication as ia on ia.uid=si.uid')
                ->join('left join user as us on us.id=si.uid')
                ->alias('si')
                ->order('si.id desc')
                ->field('si.*,s.cover_path,sc.cname,se.grade,s.uid as suid,us.username,ia.real_name,u.nickname,sc.type as sc_type')
                ->select();
            //suid 关联宝宝的id值
            foreach($orderInfo as $key=>$val){
                if($val['uid']==$this->uid){//是当前用户的订单
                    $orderInfo[$key]['isSelf']=1;
                }
                $baybyInfo=M('user')->where('u.id='.$val['suid'])
                    ->join('left join identity_authentication as ia on ia.uid=u.id')
                    ->field('ia.real_name,u.username')
                    ->alias('u')
                    ->find();
                $orderInfo[$key]['babyName']=$baybyInfo['real_name'];
                $orderInfo[$key]['babyPhone']=$baybyInfo['username'];
            }

            $finalInfo=array();
            foreach($orderInfo as $Okey=>$Oval){
                //收录具体分类
                //待确认类订单
                if($Oval['reply_status']==0){
                    $finalInfo[0]['statusName']='待确认订单';
                    $finalInfo[0]['orderDetail'][]=$Oval;
                }
                //已确认待付款订单
                if($Oval['reply_status']==1 && $Oval['type']==0){
                    $finalInfo[1]['statusName']='待付款订单';
                    $finalInfo[1]['orderDetail'][]=$Oval;
                }
                //已付款，进行中的订单
                if($Oval['status']==1 && $Oval['type']==1){
                    $finalInfo[2]['statusName']='进行中的订单';
                    $finalInfo[2]['orderDetail'][]=$Oval;
                }
                //已完成的订单
                if($Oval['status']==2 && $Oval['type']==2){
                    $finalInfo[3]['statusName']='已完成订单';
                    $finalInfo[3]['orderDetail'][]=$Oval;
                }
                //已取消的订单
                if($Oval['reply_status']==2){
                    $finalInfo[4]['statusName']='已取消订单';
                    $finalInfo[4]['orderDetail'][]=$Oval;
                }
            }
            $this->assign('orderInfo',$orderInfo);
            $this->assign('finalInfo',$finalInfo);
        }else if($userType['type']==1){//签约用户即可看自己的订单，也可看被下单的订单
            $serviceInfo=M('service')->field('id')->where('uid='.$this->uid)->select();//找出服务表的id
            $sid='';
            foreach ($serviceInfo as $key=>$val){
                $sid.=$val['id'].',';
            }
            $sid=rtrim($sid,',');
            //sid存在的情况
            $where='';
            if($sid){
             $where=" or si.sid in ({$sid})";
            }
                $orderInfo=M('service_indent')->where('si.uid='.$this->uid.$where)
                    ->join('left join service as s on si.sid=s.id')
                    ->join('left join service_category as sc on s.scid=sc.id')
                    ->join('left join service_estimate as se on se.order_id=si.id')
                    ->join('left join userinfo as u on u.uid=s.uid and si.sid=s.id')
                    ->join('left join identity_authentication as ia on ia.uid=si.uid')
                    ->join('left join user as us on us.id=si.uid')
                    ->alias('si')
                    ->order('si.id desc')
                    ->field('si.*,s.cover_path,sc.cname,se.grade,s.uid as suid,us.username,ia.real_name,u.nickname,sc.type as sc_type')
                    ->select();

            foreach($orderInfo as $key=>$val){
                if($val['uid']==$this->uid){//是当前用户值的订单
                    $orderInfo[$key]['isSelf']=1;
                }
                if($val['suid']==$this->uid){//是自己被下的订单
                    $orderInfo[$key]['myOrder']=1;
                }

                $baybyInfo=M('user')->where('u.id='.$val['suid'])
                    ->join('left join identity_authentication as ia on ia.uid=u.id')
                    ->field('ia.real_name,u.username')
                    ->alias('u')
                    ->find();
                $orderInfo[$key]['babyName']=$baybyInfo['real_name'];
                $orderInfo[$key]['babyPhone']=$baybyInfo['username'];
            }

            //var_dump($orderInfo);
            /*$MyorderInfo=M('service_indent')->where("si.sid in ({$sid})")//自己被下单的信息
                ->join('service as s on si.sid=s.id')
                ->join('service_category as sc on s.scid=sc.id')
                ->join('left join service_estimate as se on se.order_id=si.id')
                ->alias('si')
                ->field('si.*,sc.icon_path,sc.cname,se.grade')
                ->select();
            $this->assign('MyorderInfo',$MyorderInfo);*/
            $finalInfo=array();
            foreach($orderInfo as $Okey=>$Oval){
                //收录具体分类
                //待确认类订单
                if($Oval['reply_status']==0){
                    $finalInfo[0]['statusName']='待确认订单';
                    $finalInfo[0]['orderDetail'][]=$Oval;
                }
                //已确认待付款订单
                if($Oval['reply_status']==1 && $Oval['type']==0){
                    $finalInfo[1]['statusName']='待付款订单';
                    $finalInfo[1]['orderDetail'][]=$Oval;
                }
                //已付款，进行中的订单
                if($Oval['status']==1 && $Oval['type']==1){
                    $finalInfo[2]['statusName']='进行中订单';
                    $finalInfo[2]['orderDetail'][]=$Oval;
                }
                //已完成的订单
                if($Oval['status']==2 && $Oval['type']==2){
                    $finalInfo[3]['statusName']='已完成订单';
                    $finalInfo[3]['orderDetail'][]=$Oval;
                }
                //已取消的订单
                if($Oval['reply_status']==2){
                    $finalInfo[4]['statusName']='已取消订单';
                    $finalInfo[4]['orderDetail'][]=$Oval;
                }
            }
            $this->assign('orderInfo',$orderInfo);

            $this->assign('finalInfo',$finalInfo);
        }
      $this->display();
    }

    //确认接单
    public function sure(){
        $id=I('post.orderId');//订单表主键
        $phone=I('post.phone');//用户电话，用以通知用户
        $realName=I('post.realName');//用户电话，用以通知用户
        $isRe=M('service_indent')->where('id='.$id)->field('reply_status,serial_number')->find();
        if($isRe['reply_status']!=0){
            $this->ajaxReturn(array('info'=>2,'msg'=>'该订单已处理，请勿重复'));
            exit;
        }
        //通知普通用户，短信接口
        send_to_user($phone,$realName,$isRe['serial_number']);
        $re=M('service_indent')->where('id='.$id)->data(array('reply_status'=>1))->save();
        if($re){
            $this->ajaxReturn(true);
        }
    }

    //取消接单
    public function csure(){
        $id=I('post.orderId');//订单表主键
        $isRe=M('service_indent')->where('id='.$id)->field('reply_status')->find();
        if($isRe['reply_status']!=0){
            $this->ajaxReturn(array('info'=>2,'msg'=>'该订单已处理，请勿重复'));
            exit;
        }
        $re=M('service_indent')->where('id='.$id)->data(array('reply_status'=>2))->save();
        if($re){
            $this->ajaxReturn(true);
        }
    }

    //支付
    public function paymoney(){
        $id=I('post.id');
        //获取订单支付状态
        $re=M('service_indent')->where('id='.$id)->field('type,payment_amount,serial_number,sid,ctime')->find();
        $service=M('service')->where('s.id='.$re['sid'])
            ->join('service_category as sc on sc.id=s.scid')
            ->alias('s')
            ->field('sc.cname')
            ->find();
        if($re['type']!=0){
            $this->ajaxReturn(array('info'=>2,'msg'=>'订单已处理，请勿重复'));
            exit;
        }
        //获取支付方式
        $type=I('post.myType');
        if($type==1){
            //扣除余额,获取订单支付金额
            $payMoney=$re['payment_amount'];
            //查询该账户的余额
            $userMoney=M('userinfo')->where('uid='.$this->uid)->field('balance')->find();
            //判断余额是否大于要支付的金额
            $result=bccomp($userMoney['balance'],$payMoney,2);
            if($result>=0){
                //可以支付//支付并扣除余额
                $finalMoney=bcsub($userMoney['balance'],$payMoney,2);
                $finalRe=M('userinfo')->where('uid='.$this->uid)
                    ->data(array('balance'=>$finalMoney))
                    ->save();
                if($finalRe || $finalRe===0){
                    //支付成功，修改订单状态值   type=1 订单已经支付, status=1订单正在进行中
                    $re=M('service_indent')->where('id='.$id)->data(array('type'=>1,'status'=>1))->save();
                    if($re){
                        $this->ajaxReturn(true);
                    }
                    $this->ajaxReturn(true);
                }
            }else{
                //支付失败
                $this->ajaxReturn(array('info'=>7,'msg'=>'账户余额不足,请充值或选择其他方式支付'));
                exit;
            }
        }
        //微信支付
        if($type==2){
            //获取当前用户手机号码
            $tel=$_SESSION['userinfo']['username'];
            //调用支付接口函数
            //获取支付状态
            $status=true;//测试支付状态为成功
            //获取订单id
            if($status){
                //支付成功，修改订单状态值   type=1 订单已经支付, status=1订单正在进行中
                $re=M('service_indent')->where('id='.$id)->data(array('type'=>1,'status'=>1))->save();
                if($re){
                    $this->ajaxReturn(true);
                }
            }else{
                //支付失败页面返回订单
                $this->ajaxReturn(false);
            }
        }
    }

    //完成订单
    public function hasDone(){
        $id=I('post.orderId');//订单表主键
        //查出该订单的金额,完成状态
        $money=M('service_indent')->where('id='.$id)->field('payment_amount,status,start_time')->find();
        //判断是否已完成过
        if($money['status']==2){
            $this->ajaxReturn(array('info'=>2,'msg'=>'订单已处理，请勿重复'));
            exit;
        }
        //判断该订单距离开始时间是否超过24小时，否则不能点击完成
        if($money['start_time']+24*3600<time()){
            $this->ajaxReturn(array('info'=>2,'msg'=>'请在距离开始时间24小时后点击完成'));
            exit;
        }
        //type=2 待评价  status=2订单完成  time()当前时间戳，结束时间
        $re=M('service_indent')->where('id='.$id)
            ->data(array('status'=>2,'type'=>2,'end_time'=>time()))
            ->save();

        $payMoney=$money['payment_amount'];
        $bbId=I('post.bbId');//签约用户id
        //签约用户分成，当前收入
        $divide=M('userinfo')->where('uid='.$bbId)->field('jjk,earning')->find();
        if($divide['jjk']==0){
            $divideInfo=M('divide_into')->find();
            //得出签约用户分成
            $bbDivide=$divideInfo['sign_user'];
            $getDivide=bcdiv($bbDivide,10,2);//分成比例，精度计算
        }else{
            $getDivide=bcdiv($divide['jjk'],10,2);//不为默认分成，即为当前分成
        }
        //订单金额计算分成
        $getMoney=bcmul($payMoney,$getDivide,2);//得出金额

        //当前签约用户收入，
        $ear=$divide['earning'];
        //累加改单分的的金额
        $finalMoney=bcadd($getMoney,$ear,2);//最后存入数据库 存入用户收入
        $result=M('userinfo')->where('uid='.$bbId)->data(array('earning'=>$finalMoney))->save();

        if($re && $result){
            $this->ajaxReturn(true);
        }
    }


    //保存评价内容
    public function saveEvaluate(){

        $bbId=I('post.bbId');//订单关联的服务表id
        $order_id=I('post.orderId');//订单的id
        $status=M('service_estimate')->where('order_id='.$order_id)->find();
        if($status){//已评
            $this->ajaxReturn(array('info'=>2,'msg'=>'订单已评请勿重复'));
            exit;
        }
        $grade=I('post.grade');//分数
        $content=I('post.content');
        $uid=$this->uid;//当前评价用户id
        $data=array(
            'sid'=>$bbId,
            'uid'=>$uid,
            'grade'=>$grade,
            'estimate'=>$content,
            'order_id'=>$order_id,
            'ctime'=>time()
        );
        $re=M('service_estimate')->data($data)->add();
        if($re){
            $this->ajaxReturn(true);
        }

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
            $re=M('service_indent')->where('serial_number='."'{$out_trade_no}'")->data(array('type'=>1,'status'=>1))->save();
            header("Location:index");
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
                $re=M('service_indent')->where('serial_number='."'{$out_trade_no}'")->data(array('type'=>1,'status'=>1))->save();
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success"; //请不要修改或删除
        }else {
            //验证失败
            echo "fail";

        }
    }

    // 支付宝PC端支付demo
    public function pc(){
        //构造参数
        $id=I('get.id');
        //获取订单支付状态
        $re=M('service_indent')->where('id='.$id)->field('type,payment_amount,serial_number,sid,ctime')->find();
        $service=M('service')->where('s.id='.$re['sid'])
            ->join('service_category as sc on sc.id=s.scid')
            ->alias('s')
            ->field('sc.cname')
            ->find();
        $body = $service['cname'];
        $subject = $service['cname'];
        $total_amount = $re['payment_amount'];
        $out_trade_no = $re['serial_number'];
        $return_url='http://'.$_SERVER['HTTP_HOST']."/index.php/".CONTROLLER_NAME."/returnUrl";
        $notify_url='http://'.$_SERVER['HTTP_HOST']."/index.php/".CONTROLLER_NAME."/notifyUrl";
        alipayPC($body,$subject,$total_amount,$out_trade_no,$return_url,$notify_url);
    }

}