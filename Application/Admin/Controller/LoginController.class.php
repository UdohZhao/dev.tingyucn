<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{
    public $db;
    //构造方法
    public function _initialize(){
        $this->db=M('admin_user');
        $logo=M('website_config')->find();
        $this->assign('logo',$logo);
    }
    public function index(){
        if(IS_GET===true){
            $this->display();
            die;
        }
        // data
        $data = $this->getData();
        // cookie
        if( $data['remember'] == 1 ){
            setcookie('username',$data['username'],time()+3600);
        }else{
            setcookie('username',$data['username'],time()-3600);
        }
        // 验证用户名和密码
        $username = $data['username'];
        $password = iPassword($data['password']);
        $userinfo = M('admin_user')->where("username='$username' AND password='$password'")->find();
        if( $userinfo ){
            $_SESSION['admininfo'] = $userinfo;
            $this->ajaxReturn(U('Index/index'));
        }else{
            $this->ajaxReturn(false);
        }

    }

    public function verifyCode(){
        // GET
        if( IS_GET === true ){

            ob_clean();
            $Verify =     new \Think\Verify();
            $Verify->fontSize = 30;
            $Verify->length   = 4;
            $Verify->useNoise = false;
            $Verify->entry();
            die;
        }
        // 核对验证码
        if(check_verify($_POST['code'])){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
   //初始化数据
    private function getData(){
        $data = array();
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
        return $data;
    }

}