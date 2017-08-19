<?php
namespace Home\Controller;
use Think\Controller;
class EachclassController extends BaseController {
    // 构造方法
    public $classGet;//顶部样式改变,服务项目类型的type值
    public function _auto(){
        $type=I('get.type');
        $this->classGet=$type;
    	$this->assign('Eachclass',$this->classGet);
    }
    // header
    public function index(){
      // display
       //查询对应的type值下的服务项目名称对应的签约用户
        $type=$this->classGet;
       if($type==0){
           $this->assign('typeName',"<span style='color:#ed9d54;'>线上游戏</span>");
       }elseif ($type==1){
           $this->assign('typeName',"<span style='color:#3a8eee;'>线上娱乐</span>");
       }elseif ($type==2){
           $this->assign('typeName',"<span style='color:#b668bc;'>线下娱乐</span>");
       }

       //查询对应类型服务的id值,名字
        $serviceName=M('service')->where('sc.type='.$type.' and s.type=2 and s.status=0 and ia.status=2')
                                ->join('service_category as sc on sc.id=s.scid')
                                ->join('identity_authentication as ia on ia.uid=s.uid')
                                ->field('sc.cname,s.scid')
                                ->alias('s')
                                ->group('s.scid')
                                ->select();

        $this->assign('serviceName',$serviceName);
        $needInfo=array();
        //查询对应的服务项目，对应的签约用户,上架，审核成功,实名认证成功,所有服务签约用户
         foreach($serviceName as $key=>$val){
             $serviceInfo=M('service')->where('s.scid='.$val['scid'].' and sc.type='.$type.' and s.type=2 and s.status=0 and ia.status=2')
                 ->join('service_category as sc on sc.id=s.scid')
                 ->join('left join userinfo as u on u.uid=s.uid')
                 ->join('identity_authentication as ia on ia.uid=s.uid')
                 ->alias('s')
                 ->field('s.*,sc.cname,u.nickname,ia.real_name,sc.charge_mode')
                 ->select();
             $needInfo[$key]['scid']=$val['scid'];
             $needInfo[$key]['cname']=$val['cname'];
             $needInfo[$key]['detail']=$serviceInfo;
         }

        $this->assign('needInfo',$needInfo);
      $this->display();
    }
}