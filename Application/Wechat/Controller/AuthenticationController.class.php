<?php
namespace Wechat\Controller;
use Think\Controller;
class AuthenticationController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //身份认证
    public function index(){

        //得到信息数据
        $arr=$this->getinfo();
        $this->assign('arr',$arr);
      $this->display();
    }

    private function getinfo(){
        $arr=M('identity_authentication')->where('uid='.$this->uid)->find();//查找是否已经实名认证
        return $arr;
    }

        //读取图片
   /* public function imgread(){

        $upload=upload();

        if($upload['status']==4){
            $this->ajaxReturn(array('info'=>4,'msg'=>$upload['errorInfo'].'大小5M以内'));
            exit;
        }
        $path=I('path');
        $path=substr($path,strlen("url(&quot;"),-strlen("&quot;)"));

        $result=M('identity_authentication')->where('uid='.$this->uid)
            ->field('front_path,back_path,figure_path')
            ->find();
        if(!in_array($path,$result) && $path!=''){
            webdelpath($path);
        }

        $needUrl=array();
        foreach ($upload as $key=>$v){
            $topurl='/Wechat/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $needUrl[$key]=$topurl;
        }
        $re=array('info'=>true,'msg'=>$topurl);
        $this->ajaxReturn($re);
    }*/

    public function saveimg(){  //此处只保存图片，其他为分别保存的

        $upload=upload();
        $needUrl=array();
        //查询经判断删除旧照片
        $result=M('identity_authentication')->where('uid='.$this->uid)
            ->field('front_path,back_path,figure_path')
            ->find();
        foreach ($upload as $key=>$v){
            $topurl='/Wechat/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $needUrl[$key]=$topurl;
            if(isset($result[$key])){
                webdelpath($result[$key]);
            }
        }

        if($this->getinfo()){//若查询得到  则为修改
            if($upload['status']!=4){//没有报错信息，有照片上传
                $needUrl['ctime']=time();
              $needUrl['status']=0;
                $res=M('identity_authentication')->where('uid='.$this->uid)
                    ->data($needUrl)
                    ->save();
                send_to_admin(13151263776,'马俊');
                $this->ajaxReturn(true);
            }else{
                $this->ajaxReturn(array('info'=>2,'msg'=>'保存成功'));

            }
        }else{//若没查询到，则为添加
            $needUrl['uid']=$this->uid;
            $needUrl['ctime']=time();
            $re=M('identity_authentication')->data($needUrl)->add();
            send_to_admin(13151263776,'马俊');
            $this->ajaxReturn(true);
        }


    }
}