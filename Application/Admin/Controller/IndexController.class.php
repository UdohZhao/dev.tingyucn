<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {

    //构造方法
    public function _auto(){

    }
    public function index(){
        if(IS_GET===true){
            $this->display();
            die;
        }
    }
    public function logout(){
        //session('[destroy]');
        session_destroy();
        header('Location:'.U('admin/login'));
    }
}