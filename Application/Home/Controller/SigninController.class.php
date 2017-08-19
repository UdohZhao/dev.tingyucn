<?php
namespace Home\Controller;
use Think\Controller;
Class SigninController extends Controller{
    //短信验证码验证
    public function checkverify(){
        $verify=$_SESSION['code'];//获取短信发送的验证码
        $data=$this->getdata();
        //与前台传入的验证码对比
        if($verify==$data['verify']){
            unset($_SESSION['code']);
            return true;
        }else{
            return false;
        }
    }
    //初始化数据
    private function getdata(){
        $data=array();
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
        $data['password']=iPassword($data['password']);//加密
        $data['verify']=I('post.verify','','strip_tags,htmlspecialchars');//短息验证码
        $data['type']=intval(0);//用户初始类型   0 普通用户
        $data['status']=intval(0);//用户初始状态  0 正常
        return $data;
    }

    public function save(){
        if(!$this->checkverify()){//验证短信验证码
            $this->ajaxReturn(array('info'=>2,'msg'=>'短信验证码不正确'));
        }else{
            $data=$this->getdata();
            $phone=$data['username'];
            $phoneArr=M('user')->where("username='{$phone}'")->find();
            if($phoneArr){
                $this->ajaxReturn(array('info'=>3,'msg'=>'该手机号已被注册,请勿重复注册'));
                exit;
            }
            unset($data['verify']);
            $re=M('user')->data($data)->add();//$re用户表主键id
            if($re){
                //向用户基本信息表中加入用户信息待用户完善
                $arr=M('userinfo')->data(array('uid'=>$re))->add();
                $this->ajaxReturn(true);
            }

        }
    }

    //获取验证码
    private function getCode(){
        $code = generate_code();
        return $code;
    }
    //发送验证码
    public function getVerify(){
        $phone=I('post.phone');
        $type=I('post.myOper');
        //$type==1 为忘记密码，否则为注册程序
        //查找是否已经注册
        if($type==1){
            $model=C('TEMPLATE_CODE.ALTER_IN');
            $phoneArr=M('user')->where("username='{$phone}'")->find();
            if(!$phoneArr){
                $this->ajaxReturn(array('info'=>3,'msg'=>'该手机号未注册,请注册'));
                exit;
            }
        }else{
            $model=C('TEMPLATE_CODE.SIGN_IN');
            $phoneArr=M('user')->where("username='{$phone}'")->find();
            if($phoneArr){
                $this->ajaxReturn(array('info'=>3,'msg'=>'该手机号已被注册,请勿重复注册'));
                exit;
            }
        }
        $code=$this->getCode();
        //记录验证码SESSION
        $_SESSION['code']=$code;
        //codeMsg($phone);  //返回 true 或者 false


        if(codeMsg($phone,$code,$model)){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }

    //忘记密码修改
    public function fsave(){
        if(!$this->checkverify()){//验证短信验证码
            $this->ajaxReturn(array('info'=>2,'msg'=>'短信验证码不正确'));
        }else{
            $phone=I('post.username');
            $password = I('post.password','','strip_tags,htmlspecialchars');
            $password=iPassword($password);//加密
            $re=M('user')->where("username='{$phone}'")->data(array('password'=>$password))->save();//$re用户表主键id
            if($re || $re===0){
                $this->ajaxReturn(true);
            }else{
                $this->ajaxReturn(false);
            }

        }
    }
}