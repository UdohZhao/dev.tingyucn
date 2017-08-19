<?php
namespace Wechat\Controller;
use Think\Controller;
class PlaceanorderController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];//当前登陆用户id
    }
    //下单
    public function index(){
        //查找用户认证信息
        $identific=M('identity_authentication')->where('uid='.$this->uid)
                                                ->field('status')->find();
        $this->assign('identific',$identific);
        $id=I('get.id');//宝宝用户id值
        $scid=I('get.scid');//服务项类型的id值,关联服务类型表
        if($scid){
            //查询该类服务单价.id值
            $scprice=M('service')->where('uid='.$id.' and scid='.$scid)->field('id,bid_price')->find();
            $this->assign('scprice',$scprice);
            $scname=M('service_category')->where('id='.$scid)->field('cname,charge_mode,type')->find();
            $this->assign('scname',$scname);


            //查询优惠券情况  条件当前选择的项目，时间未过期状态值为0的信息，当前uid用户
            $couponInfo=M('discount_coupon')->where('scid='.$scid.' and status=0')->select();
            //找出是否有过期的
            $scidStr='';
            foreach($couponInfo as $k=>$v){
                if(time()>$v['end_time']){//表示已过期
                    $scidStr.=$v['id'].',';//收录过期的优惠券id值字符串
                    unset($couponInfo[$k]);//剩下未过期的数组信息
                }
            }

            $idStr=rtrim($scidStr,',');
            //修改过期的状态值
            if($idStr){
                $result=M('discount_coupon')->where("id in({$idStr})")->data(array('status'=>1))->save();
            }

            //遍历剩下未过期的

            foreach ($couponInfo as $key=>$val){
                $a=unserialize($val['uids']);//反序列化
                foreach($a as  $Ukey=>$Uval){
                    $a['id'][$Ukey]=$a[$Ukey]['id'];//转换下数组的形式
                    unset($a[$Ukey]);
                }
                foreach($a as  $Akey=>$Aval){
                    if(!in_array($this->uid,$Aval)){//判断该数组里面没有当前用户的id值
                        //删除该条数组的信息
                        unset($couponInfo[$key]);//剩下有该用户id值的数组，传到模板显示
                    }
                }
            }
            //该类型优惠券
            $num=count($couponInfo);//满足条件的优惠券数目

            if($num>0){
                //获取id值
                $CouponId='';
                foreach ($couponInfo as $Ck=>$Cv){
                    $CouponId.=$Cv['id'].',';
                }
                $CouponId=rtrim($CouponId,',');//Id字符串
            }else{
                $CouponId='';
            }
            $this->assign('CouponId',$CouponId);
            $this->assign('num',$num);
        }

        //宝宝基本信息
        $arr=M('userinfo')->where('u.uid='.$id)
                            ->join('user as us on us.id=u.uid')
                            ->join('left join identity_authentication as ia on ia.uid=u.uid')
                            ->alias('u')
                            ->field('u.uid,us.username,u.id,u.nickname,u.head_portrait,ia.age,ia.id_card,ia.sex,ia.real_name')
                        ->find();
        $this->assign('arr',$arr);

        //宝宝服务项目,审核通过
        $serviceInfo=M('service')->where('s.uid='.$id.' and s.type=2')
                    ->join('service_category as sc on sc.id=s.scid')
                    ->field('s.cover_path,s.id,sc.cname')
                    ->alias('s')
            ->select();
         $needStr='';
        foreach($serviceInfo as $key=>$value){
             $needStr.="'".$value['cname']."',";
         }
         $needStr=rtrim($needStr,",");//服务项目字符串
      $this->assign('serviceInfo',$serviceInfo);
      $this->assign('needStr',$needStr);

        $this->display();
    }

    public function price(){
        $id=I('post.uid');//宝宝用户id值
        $name=I('post.pri');//服务项目名称
        //查询项目价格
        $price=M('service')->where('s.uid='.$id.' and sc.cname='."'{$name}'")
                        ->join('service_category as sc on sc.id=s.scid')
                        ->alias('s')
                        ->field('s.id as sid,bid_price,sc.id,sc.charge_mode,sc.type')
                        ->find();
        $needPrice=$price['bid_price'];//该服务的价格
        $mode=$price['charge_mode'];//该服务项目的计费方式
        $scid=$price['id'];//服务类项目的id值
        $sid=$price['sid'];//服务表的id值
        $sc_type=$price['type'];//服务类型
        //查询优惠券情况  条件当前选择的项目，时间未过期状态值为0的信息，当前uid用户
        $couponInfo=M('discount_coupon')->where('scid='.$scid.' and status=0')->select();
        //找出是否有过期的
        $scidStr='';
        foreach($couponInfo as $k=>$v){
            if(time()>$v['end_time']){//表示已过期
                $scidStr.=$v['id'].',';//收录过期的优惠券id值字符串
                unset($couponInfo[$k]);//剩下未过期的数组信息
            }
        }

        $idStr=rtrim($scidStr,',');
        //修改过期的状态值
        if($idStr){
            $result=M('discount_coupon')->where("id in({$idStr})")->data(array('status'=>1))->save();
        }

        //遍历剩下未过期的

        foreach ($couponInfo as $key=>$val){
            $a=unserialize($val['uids']);//反序列化
            foreach($a as  $Ukey=>$Uval){
                $a['id'][$Ukey]=$a[$Ukey]['id'];//转换下数组的形式
                unset($a[$Ukey]);
            }
            foreach($a as  $Akey=>$Aval){
                if(!in_array($this->uid,$Aval)){//判断该数组里面没有当前用户的id值
                    //删除该条数组的信息
                    unset($couponInfo[$key]);//剩下有该用户id值的数组，传到模板显示
                }
            }
        }
        //该类型优惠券
        $num=count($couponInfo);//满足条件的优惠券数目

        if($num>0){
            //获取id值
            $CouponId='';
            foreach ($couponInfo as $Ck=>$Cv){
              $CouponId.=$Cv['id'].',';
            }
            $CouponId=rtrim($CouponId,',');//Id字符串
        }else{
            $CouponId='';
        }
        $this->ajaxReturn(array('sc_type'=>$sc_type,'mode'=>$mode,'sid'=>$sid,'info'=>1,'msg'=>$needPrice,'num'=>$num,'id'=>$CouponId));
    }



    public function order(){//生成订单表

        $sid=I('post.sid');//服务表id主键
        $uid=$this->uid;
        $mode=I('post.feeType');//计费方式
        $payment=I('post.allFee');//总计费
        $starTime=I('post.starTime');//转为时间戳
        $starTime=strtotime($starTime);
        $num=I('post.serverTime');//选择的服务总数量，计算出结束时间
        $address=I('post.address');
        $type=0;//未支付
        $status=0;//是否开始  结束

        //生成订单号
        $orderNum=indent_number();
        $cid=I('post.cid');//所选的优惠券id

        //订单生成，删除已使用的优惠券对uid值，并添加一条用户使用记录
        //优惠券处理
        if($cid){
            $couInfo=M('discount_coupon')->where('id='.$cid)->find();
            $uidInfo=unserialize($couInfo['uids']);
            foreach($uidInfo as $key=>$val){
                if($val['id']==$this->uid && count($uidInfo)==1){
                    $newInfo=M('discount_coupon')->where('id='.$cid)
                                                ->data(array('status'=>1))
                                                ->save();//已使用于订单
                }
                if($val['id']==$this->uid && count($uidInfo)>1){
                    unset($uidInfo[$key]);
                    $needUids=serialize($uidInfo);//序列化新数组
                    //删除相应的id值后,修改数组id值信息
                    $rel=M('discount_coupon')->where('id='.$cid)
                                            ->data(array('uids'=>$needUids))
                                            ->save();
                    //添加该用户的使用记录
                    $a=array(0=>array('id'=>$this->uid));
                    $a=serialize($a);
                    unset($couInfo['uids']);
                    unset($couInfo['id']);
                    $couInfo['uids']=$a;
                    $couInfo['status']=1;
                    $res=M('discount_coupon')->data($couInfo)->add();
                }
            }
        }

        $data=array(
            'sid'=>$sid,
            'uid'=>$uid,
            'charge_mode'=>$num.$mode,//数量/计费方式
            'payment_amount'=>$payment,
            'start_time'=>$starTime,
            'type'=>$type,
            'status'=>$status,
            'ctime'=>time(),
            'serial_number'=>$orderNum,
            'address'=>$address
        );

        $re=M('service_indent')->data($data)->add();//生成订单
        if($re){
            //当前签约用户的电话
            $phone=I('post.telPhone');
            //当前签约用户的姓名
             $realName=I('post.realName');
            $result=send_info($phone,$realName,$orderNum);

            //用掉的优惠券处理
            if($cid){
                $useNum=1;
            }else{
                $useNum=0;
            }
            $this->ajaxReturn(array('id'=>$re,'info'=>1,'uid'=>$uid,'cid'=>$cid,'useNum'=>$useNum));//生成成功，$re生成的订单主键
        }else{
            $this->ajaxReturn(array('msg'=>'网络故障','info'=>2));
        }
    }

    public function paynow(){
        $orderId=I('get.orderId');//订单表主键id
        $orderInfo=M('service_indent')->where('id='.$orderId.' and uid='.$this->uid)
                                    ->find();
        $sid=$orderInfo['sid'];//服务表id
        $serviceInfo=M('service')->where('s.id='.$sid)
                        ->join('service_category as sc on sc.id=s.scid')
                        ->join('userinfo as u on u.uid=s.uid')
                        ->join('identity_authentication as ia on ia.uid=s.uid')
                        ->alias('s')
                        ->field('s.*,s.bid_price,sc.cname,ia.sex,ia.age,ia.id_card,u.head_portrait,u.nickname')
                        ->find();
        $this->assign('serviceInfo',$serviceInfo);
        $this->assign('orderInfo',$orderInfo);
        //计算是否有优惠
        $patterns = "/\d+/"; //第一种
        $strs=$orderInfo['charge_mode'];
        preg_match_all($patterns,$strs,$arr);
        $num=$arr[0][0];

        $isCoupon=bcsub(bcmul($serviceInfo['bid_price'],$num,2),$orderInfo['payment_amount'],2);
        $this->assign('isCoupon',$isCoupon);
        $this->display();
    }


    public function paymoney(){
        $id=I('post.id');
        //获取订单支付状态
        $re=M('service_indent')->where('id='.$id)->field('type,payment_amount')->find();
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
                $this->ajaxReturn(array('info'=>7,'msg'=>'账户余额不足'));
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
            $this->redirect('/Myorder/index');
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
        $return_url='http://'.$_SERVER['HTTP_HOST']."/Wechat/index.php/".CONTROLLER_NAME."/returnUrl";
        $notify_url='http://'.$_SERVER['HTTP_HOST']."/Wechat/index.php/".CONTROLLER_NAME."/notifyUrl";
        alipayWap($body,$subject,$total_amount,$out_trade_no,$return_url,$notify_url);
    }
}