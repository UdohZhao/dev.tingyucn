<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller{
    public function _initialize(){
        // userinfo

        if( !isset($_SESSION['admininfo']) ){
            header('Location:'.U('Login/index'));
            die;
        }
        $logo=M('website_config')->find();
        $this->assign('logo',$logo);
        //控制器初始化
        if(method_exists($this,'_auto'))
            $this->_auto();
    }


}