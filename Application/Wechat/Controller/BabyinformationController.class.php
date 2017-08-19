<?php
namespace Wechat\Controller;
use Think\Controller;
class BabyinformationController extends Controller {
    //构造方法
    public $uid;
    public function _initialize(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //宝宝资料
    public function index(){

        $id=I('get.id');//服务宝宝的id值  即宝宝uid
        //查询个人信息认证表
        $scid=I('get.scid');//服务类型的scid
        $this->assign('scid',$scid);
        $firstInfo=M('identity_authentication')
                    ->where('ia.uid='.$id)
                    ->join('left join userinfo as u on u.uid=ia.uid')
                    ->join('left join profession as p on u.pid=p.id')//职业
                    ->join('left join user_media as um on um.uid=ia.uid')
                    ->alias('ia')
                   // ->field('ia.id,ia.uid,ia.real_name,ia.sex,ia.id_card,ia.figure_path,ia.status')
                    ->field('ia.id,ia.age,ia.uid,ia.real_name,ia.sex,ia.id_card,ia.figure_path,ia.status,u.nickname,u.pid,u.iid,u.signature,p.cname as pname,um.video_path,um.audio_path')
                    ->find();
                
        $firstInfo['iid']=unserialize($firstInfo['iid']);//反序列化数组
        $needStr='';
        foreach ($firstInfo['iid'] as $k=>$v){
            $needStr.=$v['cname'].'/';
        }
        $needStr=rtrim($needStr,'/');
        $firstInfo['iid']=$needStr;//将兴趣转为字符串



        //查看是否被该用户拉黑或关注
        if($_SESSION['userinfo']['uid']){

        $blackInfo=M('userinfo')->where('uid='.$this->uid)->field('blacklist,attention')->find();
        $blackArr=unserialize($blackInfo['blacklist']);//反序列化黑名单为数组
        $reviewsArr=unserialize($blackInfo['attention']);//反序列化关注名单为数组

            if(in_array($id,$blackArr['uid'])){//该宝宝是否在当前用户黑名单里
                $firstInfo['blackStatus']=1;
            }

            if(in_array($id,$reviewsArr['uid'])){//该宝宝是否在当前用户关注名单里
                $firstInfo['reviewsStatus']=1;
            }

        }
        $this->assign('firstInfo',$firstInfo);
        //var_dump($firstInfo);
        //查找服务项目
        $twoInfo=M('service')->where('s.uid='.$id.' and s.type=2')
                        ->join('service_category as sc on sc.id=s.scid')
                        ->field('sc.cname,s.id,s.uid,s.scid,s.cover_path,s.explain,s.bid_price,s.ctime,s.type,s.status')
                        ->alias('s')
                    ->select();//该宝宝所有的服务项目

        $this->assign('twoInfo',$twoInfo);


        //关于该宝宝 ，该服务的评价
        $sid=I('get.sid');//该条服务的id
        $this->assign('sid',$sid);
        $evaluate=M('service_estimate')->where('s.sid='.$sid)
                        ->join('userinfo as u on u.uid=s.uid')
                        ->join('identity_authentication as ia on ia.uid=s.uid')
                        ->alias('s')
                        ->field('s.id,s.sid,s.order_id,s.uid,s.estimate,s.grade,s.ctime,u.nickname,u.head_portrait,ia.real_name')
                        ->limit(4)
                        ->select();
         foreach($evaluate as $Ekey=>$Eval){
        
            $evaluate[$Ekey]['real_name']=mb_substr($Eval['real_name'], 0,1,'utf-8')."<span style='color:red'>****</span>";
         }               
        $this->assign('evaluate',$evaluate);
      $this->display();
    }
//拉黑宝宝
    public function blackInto(){
        $bbUid=I('post.bbUid');//宝宝的uid
        $userInfo=M('userinfo')->where('uid='.$this->uid)->field('blacklist')->find();
            $needArr=unserialize($userInfo['blacklist']);
            $needArr['uid'][]=$bbUid;
            $needArr=serialize($needArr);//序列化字符串
            $re=M('userinfo')->where('uid='.$this->uid)->data(array('blacklist'=>$needArr))->save();
            $this->ajaxReturn(true);//拉黑成功

    }
//关注宝宝
    public function reviewInto(){
        $bbUid=I('post.bbUid');//宝宝的uid
        $userInfo=M('userinfo')->where('uid='.$this->uid)->field('attention')->find();
        $needArr=unserialize($userInfo['attention']);
        $needArr['uid'][]=$bbUid;
        $needArr=serialize($needArr);//序列化字符串
        $re=M('userinfo')->where('uid='.$this->uid)->data(array('attention'=>$needArr))->save();
        $this->ajaxReturn(true);//关注成功

    }
    //取消拉黑
    public function cblack(){
        $bbId=I('post.bbId');
        $userInfo=M('userinfo')->where('uid='.$this->uid)->field('blacklist')->find();
        $userInfo['blacklist']=unserialize($userInfo['blacklist']);
        $needArr=$userInfo['blacklist'];

        foreach($needArr['uid'] as $key=>$val){
            if(in_array($bbId,$needArr['uid'])){
                unset($needArr['uid'][$key]);
            }
        }
        $needArr=serialize($needArr);
        $userInfo['blacklist']=$needArr;
        //存数据
        $re=M('userinfo')->where('uid='.$this->uid)->data($userInfo)->save();


      $this->ajaxReturn(true);
    }
//取消关注
    public function creview(){
        $bbId=I('post.bbId');
        $userInfo=M('userinfo')->where('uid='.$this->uid)->field('attention')->find();
        $userInfo['attention']=unserialize($userInfo['attention']);
        $needArr=$userInfo['attention'];

        foreach($needArr['uid'] as $key=>$val){
            if(in_array($bbId,$needArr['uid'])){
                unset($needArr['uid'][$key]);
            }
        }
        $needArr=serialize($needArr);
        $userInfo['attention']=$needArr;
        //存数据
        $re=M('userinfo')->where('uid='.$this->uid)->data($userInfo)->save();


        $this->ajaxReturn(true);
    }


    //所有评论
    public function allEvaluation(){

        $sid=I('get.sid');//该条服务的id
        $this->assign('sid',$sid);
            $this->display();
    }

    //所有评论ajax请求
    public function detail(){
        $sid=I('get.sid');
        $evaluate=M('service_estimate')->where('s.sid='.$sid)
            ->join('userinfo as u on u.uid=s.uid')
            ->alias('s')
            ->field('s.id,s.sid,s.order_id,s.uid,s.estimate,s.grade,s.ctime,u.nickname,u.head_portrait')
            ->select();
        foreach($evaluate as $key=>$val){
            if($val['grade']==0){
                $evaluate[$key]['grade']='好评';
            }elseif($val['grade']==1){
                $evaluate[$key]['grade']='中评';
            }elseif($val['grade']==2){
                $evaluate[$key]['grade']='差评';
            }
            $evaluate[$key]['ctime']=date('Y-m-d H:i:s',$val['ctime']);
        }
       $this->ajaxReturn($evaluate);
    }
}