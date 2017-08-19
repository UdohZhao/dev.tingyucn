<?php
namespace Home\Controller;
use Think\Controller;
class MynewsController extends BaseController {
    // 构造方法
    public function _auto(){
    	
    }
    // 我的消息
    public function index(){
      // display
        //系统消息
        $newSys=M('message')->order('ctime desc')->select();
        //处理首页显示文字
        foreach($newSys as $nkey=>$nval){
            //首页显示20字
            $lenth=mb_strlen($nval['content'],'utf-8');
            if($lenth>15){
                $newSys[$nkey]['content']=mb_substr($nval['content'],0,15,'utf-8').'...';
            }
        }
        $this->assign('newSys',$newSys);
      $this->display();
    }

    public function newsDetails(){
        $id=I('get.id');
        $info=M('message')->where('id='.$id)->find();
        $this->assign('info',$info);
        $this->display();
    }
}