<?php
namespace Wechat\Controller;
use Think\Controller;
class NameController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //姓名填写
    public function index(){
        $name=I('get.name');
        if($name!=''){
            $this->assign('name',$name);
        }
      $this->display();
    }


    public function save(){
        $name=I('post.real_name');
        $data=array('real_name'=>$name);
        $data1=array('real_name'=>$name,'uid'=>$this->uid);
        $info=M('identity_authentication')->where('uid='.$this->uid)->find();//查找是否已经实名认证
            if($info){//若进行过实名认证，则进行修改
                $arr=M('identity_authentication')->where('uid='.$this->uid)
                    ->data($data)
                    ->save();
                if($arr>=0){
                    $this->ajaxReturn(true);
                }
            }else{//没有,则进行添加
                $data1['ctime']=time();
                $arr=M('identity_authentication') ->data($data1)->add();
                if($arr){
                    $this->ajaxReturn(true);
                }
            }

    }

    public function user_age(){
            $age=$_GET['age'];
        if($age!=''){
            $this->assign('age',$age);
        }
        $this->display();
    }

        public function age_save(){

            $name=I('post.real_name');
            $data=array('age'=>$name);
            $data1=array('age'=>$name,'uid'=>$this->uid);
            $info=M('identity_authentication')->where('uid='.$this->uid)->find();//查找是否已经实名认证
            if($info){//若进行过实名认证，则进行修改
                $arr=M('identity_authentication')->where('uid='.$this->uid)
                    ->data($data)
                    ->save();
                if($arr>=0){
                    $this->ajaxReturn(true);
                }
            }else{//没有,则进行添加
                $data1['ctime']=time();
                $arr=M('identity_authentication') ->data($data1)->add();
                if($arr){
                    $this->ajaxReturn(true);
                }
            }
    }
}