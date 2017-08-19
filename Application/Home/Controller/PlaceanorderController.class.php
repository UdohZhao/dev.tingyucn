<?php
namespace Home\Controller;
use Think\Controller;
class PlaceanorderController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];//当前登陆用户id
    }
    // 下单
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
            $scname=M('service_category')->where('id='.$scid)->field('cname,charge_mode,id,type')->find();
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
            ->field('s.cover_path,s.id,sc.cname,s.scid')
            ->alias('s')
            ->select();
        $this->assign('serviceInfo',$serviceInfo);
        $this->display();
    }

    //请求优惠券
    public function coupon(){
        $couponId=I('post.couponId');//该品类优惠券id字符串
        $allId=I('get.all');//所有优惠券id字符串
        //查询优惠券表里该记录
        if($couponId){
            $arrCoupon=M('discount_coupon')->where("id in({$couponId})")->select();
            $this->assign('couponId',$couponId);
            $url=$_SERVER['HTTP_REFERER'];//上一页url地址
            $this->assign('url',$url);
        }else if($allId){//自己的页面传过来的链接
            //查找用户优惠券,从首页我的页面点击优惠券
            $arrCoupon=M('discount_coupon')->where("id in({$allId})")->select();
            $this->assign('allId',$allId);
        }else{
            $arrCoupon='';
        }
        //转化日日期格式
        foreach($arrCoupon as $key=>$val){
            $arrCoupon[$key]['end_time']=date('Y-m-d H:i:s',$val['end_time']);
        }
        $this->ajaxReturn($arrCoupon);
    }


    public function price(){
        $serId=I('post.id');//关联服务表id主键
        //查询项目价格
        $price=M('service')->where('s.id='.$serId)
            ->join('service_category as sc on sc.id=s.scid')
            ->alias('s')
            ->field('s.id as sid,bid_price,sc.id,sc.charge_mode,sc.cname,sc.type')
            ->find();
        $needPrice=$price['bid_price'];//该服务的价格
        $mode=$price['charge_mode'];//该服务项目的计费方式
        $scid=$price['id'];//服务类项目的id值
        $sid=$price['sid'];//服务表的id值
        $name=$price['cname'];//服务的名称
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
        $this->ajaxReturn(array('sc_type'=>$sc_type,'cname'=>$name,'mode'=>$mode,'sid'=>$sid,'info'=>1,'msg'=>$needPrice,'num'=>$num,'id'=>$CouponId));
    }


    public function order(){//生成订单表

        $sid=I('post.sid');//服务表id主键
        $uid=$this->uid;
        $mode=I('post.charge_mode');//计费方式
        $payment=I('post.payment_amount');//总计费
        $starTime=I('post.start_time');//转为时间戳
        $starTime=strtotime($starTime);
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
            'charge_mode'=>$mode,//数量/计费方式
            'payment_amount'=>$payment,
            'start_time'=>$starTime,
            'type'=>$type,
            'serial_number'=>$orderNum,
            'status'=>$status,
            'ctime'=>time(),
            'address'=>$address,
        );

        $re=M('service_indent')->data($data)->add();//生成订单
        if($re){
            //当前签约用户的电话
            $phone=I('post.telPhone');
            $realName=I('post.realName');
            $result=send_info($phone,$realName,$orderNum);
            $this->ajaxReturn(array('id'=>$re,'info'=>1,'uid'=>$uid));//生成成功，$re生成的订单主键
        }else{
            $this->ajaxReturn(array('msg'=>'网络故障','info'=>2));
        }
    }

}