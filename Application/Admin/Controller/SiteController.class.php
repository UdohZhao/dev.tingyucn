<?php
namespace Admin\Controller;
use Think\Controller;
class SiteController extends BaseController{
    public $db;
    //构造方法
    public function _auto()
    {
        $this->db=M('website_config');
    }

    public function index(){
        if(IS_GET===True){
            $this->display();
            die;
        }
    }

    public function save(){
        $_POST['status']=0;
        $info=$this->db->data($_POST)->add();

        if($info>0){
            $msg=array("info"=>true);
        }else{
            $msg=array('info'=>false);
        }
        $this->ajaxReturn($msg);
    }

    public function imgread(){

        $upload=upload();

        $path=I('path');
        if($path!=''){
            delpath($path);
        }

        $id=I('id');
        $imgpath=I('icon_path');
        if($id){
            $result=$this->db->where('id='.$id)
                ->field('logo_path,weblogo')
                ->find();
            if(!in_array($imgpath,$result)){
                delpath($imgpath);
            }
        }
        $needUrl=array();
        foreach ($upload as $key=>$v){
            $topurl='/Admin/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $needUrl[$key]=$topurl;
        }
        $re=array('msg'=>$topurl);
        $this->ajaxReturn($re);
    }
    public function csave(){
        //添加页面请求判断
        $path=I("imgpath");
        $path1=I("imgpath1");
        if($path){
            $re=delpath($path);
        }
        if($path1){
            $re=delpath($path1);
        }

        //修改页面请求时判断
        $id=I('id');
        $path1=I("icoimg1");
        if($path1){
            $result=delpath($path1);
        }
        $path=I("icoimg");
        if($path){
            $result=delpath($path);
        }
        if($id){
            $pathInfo=$this->db->where('id='.$id)->find();
            $imgpath=$pathInfo['logo_path'];
            $imgpath1=$pathInfo['weblogo'];
        }
        if($re || $result){
            $msg=array('info'=>true,'img'=>$imgpath,'img1'=>$imgpath1);
        }else{
            $msg=array('info'=>false);
        }
        $this->ajaxReturn($msg);
    }

    public function oper(){


        $count=$this->db->count();

        $Page       = new selfPage($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出

        $arr = $this->db->order("id asc")
            ->limit($Page->getStart(),5)
            ->select();
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->display();
    }

    public function update(){
        $id=I('id');
        $info=$this->db->where('id='.$id)->find();
        $this->assign('info',$info);
        $this->display();
    }
    public function usave(){
        $id=I('id');
        $pathinfo=$this->db->where('id='.$id)->find();
        $pathname=$_POST['pathname'];
        $pathname1=$_POST['pathname1'];

        if($pathname){
            $path=$pathinfo['logo_path'];
            $str=substr($path,strlen("/Admin"));
            $path=".".$str;
            unlink($path);
        }
        if($pathname1){
            $path=$pathinfo['weblogo'];
            $str=substr($path,strlen("/Admin"));
            $path=".".$str;
            unlink($path);
        }
        //删除数组多余的
        unset($_POST['pathname']);
        unset($_POST['pathname1']);
        $info=$this->db->data($_POST)->save();
        if($info>=0){
            $msg=array("info"=>true);
        }else{
            $msg=array('info'=>false);
        }
        $this->ajaxReturn($msg);
    }

    public function del(){
        $id=I('id');
        $info=$this->db->where('id='.$id)->find();
        $path=$info['logo_path'];
        $str=substr($path,strlen("/Admin"));
        $path=".".$str;
        $result=@unlink($path);
        if($result){
            $result=$this->db->where('id='.$id)->delete();
        }
        $this->ajaxReturn($result);
    }

}