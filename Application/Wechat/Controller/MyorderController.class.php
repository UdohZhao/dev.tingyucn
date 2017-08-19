<?php
namespace Wechat\Controller;
use Think\Controller;
class MyorderController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];//当前登陆用户id
    }
    //我的订单
    public function index(){

        //查询订单页面  联表服务类型  服务表  评价表
        $userType=M('user')->where('id='.$this->uid)->find();
        if($userType['type']==0){//普通用户 只查看自己的订单信息
            $orderInfo=M('service_indent')->where('si.uid='.$this->uid)
                ->join('left join service as s on si.sid=s.id')
                ->join('left join service_category as sc on s.scid=sc.id')
                ->join('left join service_estimate as se on se.order_id=si.id')
                ->alias('si')
                ->field('si.*,s.cover_path,sc.cname,se.grade')
                ->select();
            //suid 关联宝宝的id值
            foreach($orderInfo as $key=>$val){
                if($val['uid']==$this->uid){//是当前用户的订单
                    $orderInfo[$key]['isSelf']=1;
                }
            }

            $finalInfo=array();
            foreach($orderInfo as $Okey=>$Oval){
                //收录具体分类
                //待确认类订单
                if($Oval['reply_status']==0){
                    $finalInfo[0]['statusName']='待确认';
                    $finalInfo[0]['orderDetail'][]=$Oval;
                }
                //已确认待付款订单
                if($Oval['reply_status']==1 && $Oval['type']==0){
                    $finalInfo[1]['statusName']='待付款';
                    $finalInfo[1]['orderDetail'][]=$Oval;
                }
                //已付款，进行中的订单
                if($Oval['status']==1 && $Oval['type']==1){
                    $finalInfo[2]['statusName']='进行中';
                    $finalInfo[2]['orderDetail'][]=$Oval;
                }
                //已完成的订单
                if($Oval['status']==2 && $Oval['type']==2){
                    $finalInfo[3]['statusName']='已完成';
                    $finalInfo[3]['orderDetail'][]=$Oval;
                }
                //已取消的订单
                if($Oval['reply_status']==2){
                    $finalInfo[4]['statusName']='已取消';
                    $finalInfo[4]['orderDetail'][]=$Oval;
                }
            }
            $this->assign('finalInfo',$finalInfo);
            $this->assign('orderInfo',$orderInfo);
        }else if($userType['type']==1){//签约用户即可看自己的订单，也可看被下单的订单
                $serviceInfo=M('service')->field('id')->where('uid='.$this->uid)->select();//找出服务表的id
            $sid='';
            foreach ($serviceInfo as $key=>$val){
                $sid.=$val['id'].',';
            }
            $sid=rtrim($sid,',');
            $where='';
            if($sid){
                $where=" or si.sid in ({$sid})";
            }
            $orderInfo=M('service_indent')->where('si.uid='.$this->uid.$where)
                ->join('left join service as s on si.sid=s.id')
                ->join('left join service_category as sc on s.scid=sc.id')
                ->join('left join service_estimate as se on se.order_id=si.id')
                ->alias('si')
                ->order('si.id desc')
                ->field('si.*,s.cover_path,sc.cname,se.grade')
                ->select();
            foreach($orderInfo as $key=>$val){
                if($val['uid']==$this->uid){//是当前用户值的订单
                    $orderInfo[$key]['isSelf']=1;
                }
            }

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
                    $finalInfo[0]['statusName']='待确认';
                    $finalInfo[0]['orderDetail'][]=$Oval;
                }
                //已确认待付款订单
                if($Oval['reply_status']==1 && $Oval['type']==0){
                    $finalInfo[1]['statusName']='待付款';
                    $finalInfo[1]['orderDetail'][]=$Oval;
                }
                //已付款，进行中的订单
                if($Oval['status']==1 && $Oval['type']==1){
                    $finalInfo[2]['statusName']='进行中';
                    $finalInfo[2]['orderDetail'][]=$Oval;
                }
                //已完成的订单
                if($Oval['status']==2 && $Oval['type']==2){
                    $finalInfo[3]['statusName']='已完成';
                    $finalInfo[3]['orderDetail'][]=$Oval;
                }
                //已取消的订单
                if($Oval['reply_status']==2){
                    $finalInfo[4]['statusName']='已取消';
                    $finalInfo[4]['orderDetail'][]=$Oval;
                }
            }
            $this->assign('finalInfo',$finalInfo);
            $this->assign('orderInfo',$orderInfo);

        }

      $this->display();
    }
    public function myorderDetails(){
            $id=I('get.id');//服务订单表主键id
        $info=M('service_indent')->where('si.id='.$id)
            ->join('left join service as s on si.sid=s.id')
            ->join('left join service_category as sc on s.scid=sc.id')
            ->alias('si')
            ->field('si.*,sc.cname,s.uid as suid,sc.type as sc_type')
            ->find();
        $this->assign('info',$info);
        //查找当前用户的类型

        $userType=M('user')->where('id='.$this->uid)->find();
        $this->assign('userType',$userType);

        //查找订单用户的信息  若为该宝宝自己的订单则显示出来
        $userInfo=M('user')->where('u.id='.$info['uid'])
                ->join('left join identity_authentication as ia on ia.uid=u.id')
                ->field('ia.real_name,u.username')
                ->alias('u')
                ->find();
        $this->assign('userInfo',$userInfo);
        //当前用户id等于订单的服务所关联的宝宝id，判断为是是自己的单子
        if($this->uid==$info['suid']){
            $this->assign('needType',1);//判断是否是自己的单子
        }
        //该订单的服务所关联的宝宝id等于当前订单关联的uid，判断为无法支付
        if($info['suid']==$info['uid']){
            $this->assign('doubleId',1);//判断是否是自己的单子
        }

        //查询订单关联的宝宝的信息，若订单支付成功,显示双方信息
        $bbInfo=M('user')->where('u.id='.$info['suid'])
                        ->join('identity_authentication as ia on ia.uid=u.id')
                        ->alias('u')
                        ->field('u.*,ia.real_name')
                        ->find();
        $this->assign('bbInfo',$bbInfo);
        $this->display();
    }
    //确认接单
    public function sure(){
        $id=I('post.orderId');//订单表主键
        $phone=I('post.phone');//用户电话，用以通知用户
        $realName=I('post.realName');//用户电话，用以通知用户
        $isRe=M('service_indent')->where('id='.$id)->field('reply_status')->find();
        if($isRe['reply_status']!=0){
            $this->ajaxReturn(array('info'=>2,'msg'=>'该订单已处理，请勿重复'));
            exit;
        }
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
    //评价信息
    public function evaluation(){
        $bbId=I('get.sid');//订单关联的服务表id
        $orderId=I('get.orderId');//订单表id
        $this->assign('suid',$bbId);
        $this->assign('orderId',$orderId);
            $this->display();
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
}