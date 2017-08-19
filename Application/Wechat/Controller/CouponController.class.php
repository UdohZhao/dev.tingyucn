<?php
namespace Wechat\Controller;
use Think\Controller;
class CouponController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];//用户id
    }
    //优惠券
    public function index(){
        $couponId=I('get.couponId');//该品类优惠券id字符串
        $allId=I('get.all');//所有优惠券id字符串
        //查询优惠券表里该记录

        if($couponId){
            $arr=M('discount_coupon')->where("id in({$couponId})")->select();
            $this->assign('couponId',$couponId);
            $url=$_SERVER['HTTP_REFERER'];//上一页url地址
            $this->assign('url',$url);
        }else if($allId){//自己的页面传过来的链接
            //查找用户优惠券,从首页我的页面点击优惠券
            $arr=M('discount_coupon')->where("id in({$allId})")->select();
            $this->assign('allId',$allId);
        }else{
            $arr='';
        }

        $this->assign('arr',$arr);

      $this->display();
    }
}