<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends BaseController{
    public $db;
    public function _auto(){
        $this->db=M('admin_user');
    }
    public function index(){
        $this->display();
    }
    //初始化注册数据
    private function getData(){
        $data = array();
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
        return $data;
    }

    public function save(){
        $data=$this->getData();
        $data['password']=iPassword($data['password']);
        $data['ctime']=time();
        $data['status']=0;
        $re=$this->db->data($data)->add();
        if($re){
            $this->ajaxReturn(true);
        }
    }

    public function del(){
        $id=I('post.id');
        $re=$this->db->where('id='.$id)->delete();
        if($re){
            $this->ajaxReturn(true);
        }
    }

    public function oper(){

        $arr=$this->db->select();
        $this->assign('arr',$arr);
        $this->display();
    }

    public function dongjie(){
        $id=I('post.id');
        $status=I('post.status');
        $re=$this->db->where('id='.$id)->data(array('status'=>$status))->save();
        if($re>=0){
            if($status==0){
                $msg=array('info'=>'解冻成功');
            }else{
                $msg=array('info'=>'冻结成功');
            }

            $this->ajaxReturn($msg);
        }

    }
}