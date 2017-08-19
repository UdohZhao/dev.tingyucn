<?php
namespace Wechat\Controller;
use Think\Controller;
class IndexController extends Controller {
    //构造方法
    public $uid;
    public function _initialize(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //微信模块
    public function index(){
        //banner图片
        $bannerImg=M('banner')->where('status=0')->order('sort asc')->select();
        $this->assign('bannerImg',$bannerImg);

        //服务类型
        $typeInfo=M('service_category')->order('sort asc')->where('sort<8')->select();
        $this->assign('typeInfo',$typeInfo);

        //服务 首页上架服务 status=0 ,审核成功 type=2 实名认证成功 ia.status=2的宝宝,
        $needInfo=array();
        foreach($typeInfo as $key=>$value){
            if($key<4){
                $serverIdInfo=M('service')->where('scid='.$value['id'])->field('id')->select();
                $newServer=array();
                foreach($serverIdInfo as $Skey=>$Sval){
                    $newServer[]=$Sval['id'];
                }

                $newArr=array_rand($newServer,3);//随机取三个键名
                $needId='';
                foreach($newArr as $Nkey=>$Nval){
                    $needId.=$newServer[$newArr[$Nkey]].',';//键名的键名即为三个随机id
                }
                $needId=rtrim($needId,',');
                $where='';
               if($needId){//若不为空
                   $where='s.id in ('.$needId.') and ';
               }
                $serverInfo=M('service')->where($where.'s.scid='.$value['id'].' and s.type=2 and s.status=0 and ia.status=2')
                                    ->join('service_category as sc on sc.id=s.scid')
                                    ->join('left join userinfo as u on u.uid=s.uid')
                                    ->join('identity_authentication as ia on ia.uid=s.uid')
                                    ->alias('s')
                                    ->limit(3)
                                    ->field('s.*,sc.cname,u.nickname,ia.real_name,sc.charge_mode')
                                    ->select();
                $needInfo[$key]['typename']=$value['cname'];//该类型的名字
                $needInfo[$key]['typeid']=$value['id'];//该类型的id值
                $needInfo[$key]['detail']=$serverInfo;//该类型的具体服务呢项目名字
            }
        }
        $this->assign('needInfo',$needInfo);


        //查找用户信息
        if($_SESSION['userinfo']['uid']){

        $userInfo=M('userinfo')->where('u.uid='.$this->uid)
                                ->join('left join identity_authentication as i on u.uid=i.uid')
                                ->alias('u')
                                ->field('u.*,i.sex,i.id_card,i.status as istatus')
                                ->find();//查找相关信息
        $this->assign('userInfo',$userInfo);

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
        $this->assign('CouponId',$CouponId);
        //$this->assign('couponInfo',$couponInfo);
        }
        //排行榜，按照当前收入排行  关联服务表,实名认证表
        $sortMoney=M('userinfo')->join('service as s on s.uid=u.uid')
                                ->join('identity_authentication as ia on ia.uid=u.uid')
                                ->alias('u')
                                ->field('u.*,ia.sex,ia.age,s.cover_path,s.scid,s.uid,ia.id_card,s.id as need_id')
                                ->order('u.earning desc')
                                ->limit(10)
                                ->group('u.id')
                                ->select();
        $this->assign('sortMoney',$sortMoney);


        //系统消息
        $newSys=M('message')->order('ctime desc')->select();
        //处理首页显示文字
        foreach($newSys as $nkey=>$nval){
            //首页显示20字
            $lenth=mb_strlen($nval['content'],'utf-8');
            if($lenth>15){
                $newSys[$nkey]['content']=mb_substr($nval['content'],0,15,'utf-8').'...';
            }
        }
        $this->assign('newSys',$newSys);
      $this->display();
    }

    public function newsDetails(){
        $id=I('get.id');
        $info=M('message')->where('id='.$id)->find();
        $this->assign('info',$info);
        $this->display();
    }


    public function city(){
        if($_SESSION['userinfo']['uid']){
            $city=I('post.city');
            $re=M('userinfo')->where('uid='.$this->uid)->data(array('city'=>$city))->save();
        }
         $this->ajaxReturn(true);
    }
}