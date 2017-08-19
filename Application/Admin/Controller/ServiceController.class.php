<?php
namespace Admin\Controller;
use Think\Controller;
class ServiceController extends BaseController{
    public $db;
    //构造方法
    public function _auto()
    {
            $this->db=M('service_category');
    }

    public function index(){
        if(IS_GET===True){
            $this->display();
            die;
        }
    }

    public function save(){
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
                ->field('icon_path')
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
        if($path){
            $re=delpath($path);
        }

        //修改页面请求时判断
        $id=I('id');
        $path1=I("icoimg");
        if($path1){
            $result=delpath($path1);
        }
        if($id){
            $pathInfo=$this->db->where('id='.$id)->find();
            $imgpath=$pathInfo['icon_path'];
        }
        if($result || $re){
            $msg=array('info'=>true,'img'=>$imgpath);
        }else{
            $msg=array('info'=>$result);
        }
        $this->ajaxReturn($msg);
    }

    public function oper(){


        $count=$this->db->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出

        $arr = $this->db->order("sort asc")
                            ->limit($Page->getStart(),6)
                            ->select();
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->display();
    }

    public function update(){
        $id=I('id');
        $info=$this->db->where('id='.$id)->find();
        $this->assign('info',$info);
        $url=$_SERVER['HTTP_REFERER'];//上一页的跳转地址
        $this->assign('url',$url);
        $this->display();
    }
    public function usave(){
        $id=I('id');
        $pathinfo=$this->db->where('id='.$id)->find();
        $pathname=$_POST['pathname'];

        if($pathname){
            $path=$pathinfo['icon_path'];
            $str=substr($path,strlen("/Admin"));
            $path=".".$str;
            unlink($path);
        }
        //删除数组多余的
        unset($_POST['pathname']);
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
        $path=$info['icon_path'];
        $str=substr($path,strlen("/Admin"));
        $path=".".$str;
        $result=@unlink($path);
        if($result){
            $result=$this->db->where('id='.$id)->delete();
        }
        $this->ajaxReturn($result);
    }

}