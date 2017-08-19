<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller{
//获取数据 初始化
    private function getdata(){
        $data=array();
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
        $data['password']=iPassword($data['password']);//加密
        return $data;
    }

    //对比数据库
    public function check(){
        $data=$this->getdata();
        $userinfo=M('user')->where("username='{$data['username']}' and password='{$data['password']}'")->find();

        if($userinfo){
            if($userinfo['status']==1){
                $this->ajaxReturn(array('info'=>4,'msg'=>'该用户已被冻结'));
                exit;
            }
            $_SESSION['userinfo']['uid'] = $userinfo['id'];      //用户id
            $_SESSION['userinfo']['status'] = $userinfo['status'];  //用户状态
            $_SESSION['userinfo']['type'] = $userinfo['type']; //用户类型  普通  签约
            $_SESSION['userinfo']['username'] = $userinfo['username'];
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);//用户名或密码错误
        }
    }

    public function loginout(){
        $_SESSION=array();
        session_destroy();//销毁SESSION
        $this->redirect('Index/index');
    }
}