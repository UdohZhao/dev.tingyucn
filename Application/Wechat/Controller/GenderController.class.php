<?php
namespace Wechat\Controller;
use Think\Controller;
class GenderController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }

    public function index(){
        $info=M('identity_authentication')->field('sex')->where('uid='.$this->uid)->find();
        $this->assign('info',$info);
        if(I('get.url')){
            $url=I('get.url');
            $this->assign('url',$url);
        }
      $this->display();
    }

    public function save(){
        $name=I('post.sex');
        $url=I('post.url');
        $data=array('sex'=>$name);
        $data1=array('sex'=>$name,'uid'=>$this->uid);
        $info=M('identity_authentication')->where('uid='.$this->uid)->find();//查找是否已经实名认证
        if($info){//若进行过实名认证，则进行修改
            $arr=M('identity_authentication')->where('uid='.$this->uid)
                ->data($data)
                ->save();
            if($arr>=0){
                if($url){
                    $this->ajaxReturn(array('info'=>true,'msg'=>'Mydata'));
                }else{
                    $this->ajaxReturn(array('info'=>true,'msg'=>'Authentication'));
                }

            }
        }else{//没有,则进行添加
            $data1['ctime']=time();
            $arr=M('identity_authentication') ->data($data1)->add();
            if($arr){
                if($url){
                    $this->ajaxReturn(array('info'=>true,'msg'=>'Mydata'));
                }else{
                    $this->ajaxReturn(array('info'=>true,'msg'=>'Authentication'));
                }
            }
        }

    }
}