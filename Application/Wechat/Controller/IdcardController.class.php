<?php
namespace Wechat\Controller;
use Think\Controller;
class IdcardController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //身份证
    public function index(){
        $info=M('identity_authentication')->field('id_card')->where('uid='.$this->uid)->find();
        $this->assign('info',$info);
      $this->display();
    }

    public function save(){

        $name=I('post.idcard');
        $data=array('id_card'=>$name);
        $data1=array('id_card'=>$name,'uid'=>$this->uid);
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