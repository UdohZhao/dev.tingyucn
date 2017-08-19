<?php
namespace Admin\Controller;
use Think\Controller;
class InterestController extends BaseController{

    public function _auto(){

    }
    public function index(){
        $this->display();
    }
    public function save(){
        if($_POST['status']==1){  //兴趣表
            unset($_POST['status']);
            $re=M('interest')->data($_POST)->add();
            if($re){
                $this->ajaxReturn(true);
            }
        }else if($_POST['status']==2){
            unset($_POST['status']);
            $re=M('profession')->data($_POST)->add();
            if($re){
                $this->ajaxReturn(true);
            }
        }
    }


    //职业兴趣列表
     public function poper(){

         //职业

         $count=M('profession')->count();

         $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)
         $show       = $Page->pages();// 分页显示输出
         $arr1 = M('profession')->order("sort asc")
             ->limit($Page->getStart(),6)
             ->select();

         $this->assign('page',$show);

         $this->assign('arr1',$arr1);

         $this->display();
     }

    public function ioper(){
        //兴趣

        $count=M('interest')->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->pages();// 分页显示输出
        $arr = M('interest')->order("sort asc")
            ->limit($Page->getStart(),6)
            ->select();

        $this->assign('page',$show);
        $this->assign('arr',$arr);
        $this->display();

    }



     public function del(){
         if(I('post.type')==1){//兴趣表
             $id=I('post.id');
             $result=M('interest')->where('id='.$id)->delete();
             if($result){
                 $this->ajaxReturn(true);
             }
         }else if(I('post.type')==2){//职业表
             $id=I('post.id');
             $re=M('profession')->where('id='.$id)->delete();
             if($re){
                 $this->ajaxReturn(true);
             }
         }
     }


     public function update(){
         if(I('get.id')){//兴趣表
             $id=I('get.id');
             $info=M('interest')->where('id='.$id)->find();
             $this->assign('info',$info);
             $this->assign('status',1);
             $url=$_SERVER['HTTP_REFERER'];//上一页的跳转地址
             $this->assign('iurl',$url);
         }else if(I('get.pid')){ //职业表
             $id=I('get.pid');
             $info=M('profession')->where('id='.$id)->find();
             $this->assign('info',$info);
             $this->assign('status',2);
             $url=$_SERVER['HTTP_REFERER'];//上一页的跳转地址
             $this->assign('purl',$url);
         }
         $this->display();
     }


    public function usave(){
        if($_POST['status']==1){  //兴趣表
            unset($_POST['status']);
            $re=M('interest')->data($_POST)->save();
            if($re){
                $this->ajaxReturn(true);
            }
        }else if($_POST['status']==2){  //职业表
            unset($_POST['status']);
            $re=M('profession')->data($_POST)->save();
            if($re){
                $this->ajaxReturn(true);
            }
        }
    }
}