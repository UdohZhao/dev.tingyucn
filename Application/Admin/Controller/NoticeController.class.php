<?php
namespace Admin\Controller;
use Think\Controller;
class NoticeController extends BaseController{
    public $db;
    public function _auto(){
        $this->db=M('message');
    }
    public function index(){
        $this->display();
    }
    public function save(){
        $_POST['ctime']=time();
        $re=$this->db->data($_POST)->add();
        if($re){
            $this->ajaxReturn(true);
        }
    }
    public function update(){
        $id=I('get.id');
        $info=$this->db->where('id='.$id)->find();
        $this->assign('info',$info);
        $url=$_SERVER['HTTP_REFERER'];//上一页的跳转地址
        $this->assign('url',$url);
        $this->display();
    }
    public function usave(){

        $_POST['ctime']=time();
        $re=$this->db->data($_POST)->save();
        if($re>=0){
            $this->ajaxReturn(array('info'=>true));
        }
    }
    public function oper(){

        $count=$this->db->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出

        $arr = $this->db->order("id asc")
            ->limit($Page->getStart(),6)
            ->select();
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->display();
    }
    public function del(){
        $id=I('post.id');
        $re=$this->db->where('id='.$id)->delete();
        if($re){
            $this->ajaxReturn(true);
        }
    }
}