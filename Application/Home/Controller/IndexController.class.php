<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    // 构造方法
    public $uid;
    public function _auto(){
    	$this->assign('Index','1');
        $this->uid=$_SESSION['userinfo']['uid'];

    }
    // header
    public function index(){
            //var_dump($_SESSION);
      // display
        //顶部banner图
        $bannerImg=M('banner')->where('status=0')->order('sort asc')->select();
        $this->assign('bannerImg',$bannerImg);

        //最新热门,查询订单数最多的依次排序,即倒序排列,订单交易成功 si.type=1
        $order=M('service_indent')->where('si.status=1 or si.status=2')
                                ->join('left join service as s on s.id=si.sid')
                                ->join('left join service_category as sc on sc.id=s.scid')
                                ->group('sc.id')
                                ->order('snum desc')
                                ->field("si.sid,count(sc.id) as snum,sc.cname,sc.id as scid")
                                ->alias('si')
                                ->limit(8)
                                ->select();
        $this->assign('order',$order);


        //查询热门项目
        //热门服务 上架服务 status=0 ,审核成功 type=2  实名认证成功 ia.status=2的宝宝,
        $needInfo=array();

        foreach($order as $Okey=>$Oval){
            $serverInfo=M('service')->where("s.scid={$Oval['scid']}".' and s.type=2 and s.status=0 and ia.status=2')
                ->join('service_category as sc on sc.id=s.scid')
                ->join('left join userinfo as u on u.uid=s.uid')
                ->join('identity_authentication as ia on ia.uid=s.uid')
                ->join('service_indent as si on si.sid=s.id and si.type>=1')
                ->group('si.sid')
                ->order('num desc')
                ->alias('s')
                ->limit(8)
                ->field('s.*,sc.cname,ia.real_name,u.nickname,sc.charge_mode,count(si.sid) as num')
                ->select();
            $needInfo[$Okey]['sid']=$Oval['sid'];
            $needInfo[$Okey]['detail']=$serverInfo;
        }
        $this->assign('needInfo',$needInfo);

        //热门排行榜,首先查询热门类别名，联合订单得出服务表id值
        /*$serviceNameInfo=M('service')->where('s.type=2 and s.status=0 and ia.status=2')
                                     ->join('identity_authentication as ia on ia.uid=s.uid')
                                     ->join('left join service_indent as si on si.sid=s.id and si.type>=1')
                                     ->group('s.id')
                                     ->order('num desc')
                                     ->alias('s')
                                     ->field('s.*,si.id as siid,si.sid,count(s.id) as num')
                                     ->select();
        var_dump($serviceNameInfo);*/


        //收入排行榜
        //排行榜，按照当前收入排行  关联服务表,实名认证表
        $sortMoney=M('userinfo')->where('s.type=2 and s.status=0 and ia.status=2')
            ->join('service as s on s.uid=u.uid')
            ->join('identity_authentication as ia on ia.uid=u.uid')
            ->alias('u')
            ->field('u.*,ia.sex,s.cover_path,ia.age,s.scid,s.uid as suid,ia.id_card,s.id as need_id')
            ->order('u.earning desc')
            ->limit(5)
            ->group('u.id')
            ->select();
        $this->assign('sortMoney',$sortMoney);

        //新人榜
        $newInfo=M('userinfo')->where('s.type=2 and s.status=0 and ia.status=2')
            ->join('service as s on s.uid=u.uid')
            ->join('identity_authentication as ia on ia.uid=u.uid')
            ->alias('u')
            ->field('u.*,ia.sex,s.cover_path,ia.age,s.scid,s.uid as suid,ia.id_card,s.id as need_id')
            ->order('u.uid desc')
            ->limit(5)
            ->group('u.id')
            ->select();
        //将收入倒序排列
        $newInfo = my_sort($newInfo,'earning',SORT_DESC,SORT_NUMERIC);
        $this->assign('newInfo',$newInfo);

        $this->display();
    }


    //热门详细情况
    public function morelist(){
        $sid=I('get.sid');//获取具体热门服务的id
        //获取热门服务的服务类型id
        $scidInfo=M('service')->where('id='.$sid)->field('scid')->find();
        $scid=$scidInfo['scid'];
        //获取该类型的名称
        $scnameInfo=M('service_category')->where('id='.$scid)->field('cname')->find();
        $scname=$scnameInfo['cname'];
        $this->assign('scname',$scname);
        //查询该服务类型的的所有宝宝
        $hotDetail=M('service')->where("s.scid={$scid}".' and s.type=2 and s.status=0 and ia.status=2')
            ->join('service_category as sc on sc.id=s.scid')
            ->join('left join userinfo as u on u.uid=s.uid')
            ->join('identity_authentication as ia on ia.uid=s.uid')
            ->alias('s')
            ->field('s.*,sc.cname,ia.real_name,u.nickname,sc.charge_mode')
            ->select();
        $this->assign('hotDetail',$hotDetail);
        $this->display();
    }
}