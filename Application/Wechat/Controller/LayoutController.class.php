<?php
namespace Webchat\Controller;
use Think\Controller;
class LayoutController extends BaseController {
    public $uid;
    // 构造方法
    public function _auto(){
        
    }
    public function header(){
        $this->display();
    }
    public function footer(){
        $this->display();
    }
    public function login(){
        $this->display();
    }

}