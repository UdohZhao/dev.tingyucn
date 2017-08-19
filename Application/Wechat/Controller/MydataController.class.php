<?php
namespace Wechat\Controller;
use Think\Controller;
class MydataController extends BaseController {
    //构造方法
    public $uid;
    public $db;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
        $this->db=M('userinfo');
    }
    //我的资料
    public function index(){
        if($this->getinfo()){
            $info=$this->getinfo();
            $arr=$info;
            $arr['iid']=unserialize($arr['iid']);//反序列化数组 关联兴趣表
            $needStr='';
            $needIdStr='';
            foreach ($arr['iid'] as $k=>$v){
                $needStr.=$v['cname'].'/';
                $needIdStr.=$v['id'].',';
            }
            $needStr=rtrim($needStr,'/');
            $needIdStr=rtrim($needIdStr,',');
            $arr['iid']=$needStr;
            $arr['id']=$needIdStr;
            $arr['prof']=$info['pname'];//职业名
            $this->assign('info',$arr);

        }
        //职业名称
        $jobname=M('profession')->select();
        $nameStr='';
        foreach($jobname as $key=>$value){
            $nameStr.="'".$value['cname']."',";//组成字符串
        }
        $nameStr=rtrim($nameStr,',');//去掉最后一个,
        $this->assign('jobStr',$nameStr);
      $this->display();
    }
    //昵称修改
    public function nickname(){
        $info=$this->getinfo();
        if($info['nickname']){
            $this->assign('nickname',$info['nickname']);
        }
      $this->display();
    }

//昵称保存
    public  function savename(){
        $name=I('post.nickname');
        $re=$this->db->data(array('nickname'=>$name))->where('uid='.$this->uid)->save();
        if($re>=0){
            $this->ajaxReturn(true);
        }
    }
//签名保存
    public  function savesign(){
        $name=I('post.job');
        $re=$this->db->data(array('signature'=>$name))->where('uid='.$this->uid)->save();
        if($re>=0){
            $this->ajaxReturn(true);
        }
    }

    //职业名称
    public  function jobname(){


    }
    private function getinfo(){
        $arr=$this->db->where('u.uid='.$this->uid)
                        ->join('left join identity_authentication as i on u.uid=i.uid')
                        ->join('left join profession as p on p.id=u.pid')
                        ->join('left join user_media as um on um.uid=u.uid')
                        ->alias('u')
                        ->field('u.*,i.sex,p.cname as pname,um.video_path,um.audio_path')
                        ->find();//查找相关信息
        return $arr;
    }

    public function interest(){
        $info=M('interest')->select();

        $arr=$this->getinfo();

        //获取已选择的兴趣id数组
        $arr['iid']=unserialize($arr['iid']);//反序列化数组
        $needStr='';
        foreach ($arr['iid'] as $k=>$v){
            $needStr.=$v['id'].',';
        }
        $needStr=rtrim($needStr,',');
        $needStr=explode(',',$needStr);//转为数组

        foreach ($info as $key=>$value){
            if(in_array($info[$key]['id'],$needStr)){
                $info[$key]['status']=1;//标记为已选择
            }
        }

        $idStr=$needStr;
        $this->assign('info',$info);
        $this->assign('idstr',$idStr);
        $this->display();
    }
    //签名请求
    public function signature(){
        $info=M('userinfo')->where('uid='.$this->uid)->field('signature')->find();
        $this->assign('info',$info);
        $this->display();
    }

//保存兴趣
    public function saveinter(){
            $iid=I('post.interestid');//获取id字符串
            $interestInfo=M('interest')->where("id in({$iid})")->field('id,cname')->select();
            $interestInfo=serialize($interestInfo);//序列化数组为字符串
            $re=M('userinfo')->where('uid='.$this->uid)->data(array('iid'=>$interestInfo))->save();
            if($re){
                $this->ajaxReturn(true);
            }

    }
//保存头像
    public function saveImg(){
        $upload=upload();
        $imgpath=M('userinfo')->where('uid='.$this->uid)
            ->field('head_portrait')
            ->find();
        $oldPath=$imgpath['head_portrait'];
        foreach ($upload as $key=>$v){
            $topurl='/Wechat/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
        }
        $data=array();
        if($upload['status']!=4){
            $data['head_portrait']=$topurl;
            webdelpath($oldPath);
        }else{
            $this->ajaxReturn(array('info'=>3,'msg'=>$upload['errorInfo']));
            exit;
        }
        $re=M('userinfo')->where('uid='.$this->uid)->data($data)->save();
        if($re>0){
            $this->ajaxReturn(array('info'=>true,'msg'=>$topurl));
        }

    }
    public function saveInfo(){
        //检测是否有图片上传
        $sex=I('post.sex');
        if($sex=='男'){
            $sex=1;
        }else{
            $sex=0;
        }
        $signature=I('post.signature');
        $iid=I('post.iid');
        $interestInfo=M('interest')->where("id in({$iid})")->field('id,cname')->select();
        $interestInfo=serialize($interestInfo);//序列化数组为字符串

        $pid=I('post.pid');
        $pInfo=M('profession')->where("cname='{$pid}'")->field('id')->find();
        $pid=$pInfo['id'];
        $data=array(
            'signature'=>$signature,
            'iid'=>$interestInfo,
            'pid'=>$pid,
            'nickname'=>I('post.nickname')
        );

        $idata=array('sex'=>$sex);
        //修改身份认证性别
        $result=M('identity_authentication')->where('uid='.$this->uid)->find();
        if(!$result){
            $rel=M('identity_authentication')->data(array('sex'=>$sex,'uid'=>$this->uid))->add();
        }else{
            $rel=M('identity_authentication')->where('uid='.$this->uid)->data($idata)->save();
        }
        $re=M('userinfo')->where('uid='.$this->uid)->data($data)->save();
        $this->ajaxReturn(true);
        exit;
    }


      public function metaSave(){
          if(I('get.type')==1){
              //音频,100Kb以内
              $size=102400;
          }else if(I('get.type')==2){
              //视频,3M以内
              $size=3145728;
          }
          $upload=upload($size);
          if($upload['status']==4){
              $this->ajaxReturn(array('info'=>4,'msg'=>$upload['errorInfo']));
              exit;
          }
          $re=M('user_media')->where('uid='.$this->uid)->find();
          $metaArr=array();
          foreach ($upload as $key=>$v){
              $audiourl='/Wechat/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
                $metaArr[$key]=$audiourl;
              if($re[$key]){
                  webdelpath($re[$key]);
              }
          }


          if($re){
              //查找到则为修改
              $result=M('user_media')->where('uid='.$this->uid)
                                    ->data($metaArr)->save();
          }else{
              $metaArr['uid']=$this->uid;
              $result=M('user_media')->data($metaArr)->add();
          }
          if($result>0){
              $this->ajaxReturn(array('info'=>true,'url'=>$audiourl));
          }

      }

      public function audio(){
          $audioInfo=M('user_media')->where('uid='.$this->uid)->field('video_path,audio_path')->find();
          $this->assign('audioInfo',$audioInfo);
          $this->display();
      }

      //删除媒体
    public function metaDel(){
        $type=I('post.type');
        if($type==1){
            //删除音频
            $delName='audio_path';
        }else{
            $delName='video_path';
        }
        $pathInfo=M('user_media')->where('uid='.$this->uid)->field($delName)->find();
        webdelpath($pathInfo[$delName]);
        $re=M('user_media')->where('uid='.$this->uid)->data(array($delName=>''))->save();
        if($re>0  || $re===0){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
}