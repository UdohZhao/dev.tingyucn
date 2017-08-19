<?php
namespace Wechat\Controller;
use Think\Controller;
class EachclassController extends Controller {
    //构造方法
    public $db;
    public $uid;
    public function _initialize(){
        $this->db=M('service');
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //服务每一类
    public function index(){

        $id=I('get.id');//服务类型的id值
       //所有宝宝名单
        $name=M('service_category')->where('id='.$id)->find();
        $this->assign('name',$name);
        //审核成功，上架的,实名认证成功的宝宝,
       $arr = $this->db->where('s.scid='.$id.' and s.type=2 and s.status=0 and ia.status=2')
                        ->join('service_category as sc on sc.id=s.scid')
                        ->join('left join userinfo as u on u.uid=s.uid')
                        ->join('identity_authentication as ia on ia.uid=s.uid')
                        ->alias('s')
                        ->field('s.*,sc.cname,sc.charge_mode,u.nickname')
                        ->select();
        $this->assign('arr',$arr);

        //该用户关注的宝宝
        //关注的idzhi
        if($_SESSION['userinfo']['uid']){

        $reviews=M('userinfo')->where('uid='.$this->uid)->field('attention')->find();
        $reviewArr=unserialize($reviews['attention']);
        $Rid='';
        //遍历uid后链接所有id
        foreach ($reviewArr['uid'] as $rKey=>$rVal){
           $Rid.=$rVal.',';
       }
       $Rid=rtrim($Rid,',');//得到关注的uid
        if($Rid){
            $arrInfo=M('service')->where("s.uid in ({$Rid}) and s.scid=".$id)
                ->join('service_category as sc on s.scid=sc.id')
                ->join('userinfo  as u on u.uid=s.uid')
                ->alias('s')
                ->field('s.*,u.nickname,sc.charge_mode')
                ->select();
            $this->assign('arrInfo',$arrInfo);
        }

        }
      $this->display();
    }
}