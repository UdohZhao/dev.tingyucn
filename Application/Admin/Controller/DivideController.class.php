<?php
namespace Admin\Controller;
use Think\Controller;
class DivideController extends BaseController{
    public $db;
    public function _auto(){
        $this->db=M('divide_into');
    }
    public function index(){
        $this->display();
    }

    public function save(){
        $re=$this->db->data($_POST)->add();
        if($re){
            $this->ajaxReturn(true);
        }
    }

    public function oper(){
        $info=$this->db->select();
        $this->assign('info',$info);
        $this->display();
    }

    public function del(){
        $id=I('post.id');
        $re=$this->db->where('id='.$id)->delete();
        if($re){
            $this->ajaxReturn(true);
        }
    }

    public function update(){
        $id=I('get.id');
        $info=$this->db->where('id='.$id)->find();
        $this->assign('info',$info);
        $this->display();
    }

    public function usave(){
        $re=$this->db->data($_POST)->save();
        if($re){
            $this->ajaxReturn(true);
        }
    }
}