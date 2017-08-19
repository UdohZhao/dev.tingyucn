<?php
namespace Wechat\Controller;
use Think\Controller;
class BalanceController extends BaseController {
    //构造方法
    public $uid;
    public $db;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
        $this->db=M('userinfo');
    }
    //余额
    public function index(){
        $this->assign('uid',$this->uid);
        $userType=M('user')->where('id='.$this->uid)->field('type')->find();
        $this->assign('userType',$userType);
        $info=$this->db->where('uid='.$this->uid)->find();
       $this->assign('info',$info);
        if($_GET['gift'] && $_SESSION['gift']==1){
                $this->assign('gift',1);
        }
        if($_GET['wx_gift'] == 1){
            $this->assign('gift',1);
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
                $TypeInfo[$Tkye]['type']='线上游戏';
            }else if($Tval['type']==1){
                $TypeInfo[$Tkye]['type']='线上娱乐';
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
                $this->ajaxReturn(array('info'=>true,'finalMoney'=>$finalMoney,'msg'=>$get));
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
    //充值明细
    public function rechargeDetails(){
        //当前用户的充值明细   type=0充值类型
        $info=M('account')->where('uid='.$this->uid.' and type=0')->order('id desc')->select();
        $this->assign('info',$info);
        $this->display();
    }

    //金额提现，绑定支付宝
    public function withdraw(){
        //查找用户是否绑定支付宝
        $isWith=$this->db->where('uid='.$this->uid)->field('alipay,earning')->find();
        $this->assign('isWith',$isWith);
        $this->display();
    }

    //金额提现
    public function getMoney(){
        $needMoney=I('post.money');//提现金额
        $alipay=I('post.applyNum');//支付宝账号
        $allMoney=M('userinfo')->where('uid='.$this->uid)
                            ->field('earning,alipay')
                            ->find();
        if($needMoney>$allMoney['earning']){
            $this->ajaxReturn(array('info'=>4,'msg'=>'提现金额不能大于当前收入'));
        }else{
            //修改用户收入金额
            $earning=$allMoney['earning'];//原来的收入
            //减去提现金额
            $newMoney=bcsub($earning,$needMoney,2);//剩下的金额
            //判断是否第一次绑定
            $data=array();
            if(!$allMoney['alipay']){
                $data['alipay']=$alipay;
                $data['earning']=$newMoney;
            }else{
                $data['earning']=$newMoney;
            }
            //存入记录
            $result=$this->db->where('uid='.$this->uid)
                                ->data($data)
                                ->save();

            if($result){
                //记录提现记录
                $record=array(
                    'uid'=>$this->uid,
                    'money'=>$needMoney,
                    'ctime'=>time(),
                    'type'=>1,
                    'status'=>0
                );
                $re=M('account')->data($record)->add();
            }

            if($result){
                $this->ajaxReturn(array('info'=>1,'myMoney'=>$newMoney,'msg'=>'提现申请成功,待管理员处理'));
            }else{
                $this->ajaxReturn(false);
            }

        }
    }

    public function incomeDetails(){
        //该用户的收入明细,//该签约用户关联的服务项目
        $serId=M('service')->where('uid='.$this->uid)->field('id')->select();
        $userId='';
        foreach($serId as $key=>$val){
                $userId.=$val['id'].',';
        }
        $userIdStr=rtrim($userId,',');
        if($userIdStr!=''){
            $arr=M('service_indent')->where("si.status=2 and si.sid in({$userIdStr})")
                                    ->join('service as s on s.id=si.sid')
                                    ->join('service_category as sc on sc.id=s.scid')
                                    ->field('si.*,sc.cname,s.bid_price')
                                    ->alias('si')
                                    ->select();
            //查询该用户的分成
            $divide=M('userinfo')->where('uid='.$this->uid)->field('jjk,earning')->find();
            if($divide['jjk']==0){
                $divideInfo=M('divide_into')->find();
                //得出签约用户分成
                $bbDivide=$divideInfo['sign_user'];
                $getDivide=bcdiv($bbDivide,10,2);//分成比例，精度计算
            }else{
                $getDivide=bcdiv($divide['jjk'],10,2);//不为默认分成，即为当前分成
            }
            //计算每一单的收入
            foreach($arr as $k=>$v){
                //每一单的支付金额
                $hasMoney=$v['payment_amount'];
                //签约用户该得的金额
                $getMoney=bcmul($hasMoney,$getDivide,2);
                $arr[$k]['getMoney']=$getMoney;//实得金额赋值给数组
            }

            $this->assign('arr',$arr);

        }
        $this->display();
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
        $return_url='http://'.$_SERVER['HTTP_HOST']."/Wechat/index.php/".CONTROLLER_NAME."/returnUrl";
        $notify_url='http://'.$_SERVER['HTTP_HOST']."/Wechat/index.php/".CONTROLLER_NAME."/notifyUrl";
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

           //记录充值记录   total_amount
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