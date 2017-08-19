<?php
namespace Home\Controller;
use Think\Controller;
class MybalanceController extends BaseController {
    public $uid;
    public function _auto(){
        if(!$_SESSION['userinfo']['uid']){
            echo alert('未登陆',__APP__.'/Index/index',5);
            die;
        }
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    // 我的余额
    public function index(){
      // display

        //查找用户信息
        $userInfo1=M('userinfo')->where('u.uid='.$this->uid)
            ->join('left join identity_authentication as i on u.uid=i.uid')
            ->alias('u')
            ->field('u.*,i.sex,i.id_card,i.status as istatus')
            ->find();//查找相关信息
        $this->assign('userInfo1',$userInfo1);

        $userType=M('user')->where('id='.$this->uid)->field('type')->find();
        $this->assign('userType',$userType);

        //查询优惠券情况  条件时间未过期状态值为0的信息，当前uid用户
        $couponInfo=M('discount_coupon')->where('status=0')->select();
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
        //得出有效优惠券数目
        $count=count($couponInfo);
        $this->assign('num',$count);
        if($count>0){
            //获取id值
            $CouponId='';
            foreach ($couponInfo as $Ck=>$Cv){
                $CouponId.=$Cv['id'].',';
            }
            $CouponId=rtrim($CouponId,',');//Id字符串
        }else{
            $CouponId='';
        }

        //查找用户优惠券,从首页我的页面点击优惠券
        if($CouponId){
            $allCoupon=M('discount_coupon')->where("id in({$CouponId})")->select();
            $this->assign('allCoupon',$allCoupon);
        }else{
            $this->assign('allCoupon','');
        }

        //$this->assign('CouponId',$CouponId);



        //当前用户的充值明细   type=0充值类型
        $chargeDetail=M('account')->where('uid='.$this->uid.' and type=0')->order('id desc')->select();
        $this->assign('chargeDetail',$chargeDetail);




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


        //查找用户是否绑定支付宝
        $isWith=M('userinfo')->where('uid='.$this->uid)->field('alipay,earning')->find();
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
            $result=M('userinfo')->where('uid='.$this->uid)
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

}