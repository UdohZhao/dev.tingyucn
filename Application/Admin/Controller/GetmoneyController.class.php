<?php
namespace Admin\Controller;
use Think\Controller;
class GetmoneyController extends BaseController{
    public $db;
    public function _auto(){
        $this->db=M('account');
    }
    public function index(){
        //查询提现为成功的，status=0;

        $count=$this->db->where('status=0')->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $arr=$this->db->where('a.status=0')
                    ->join('userinfo as u on u.uid=a.uid')
                    ->join('user as us on us.id=a.uid')
                    ->alias('a')
                    ->field('a.*,u.alipay,us.username')
                    ->limit($Page->getStart(),6)
                    ->select();//未处理的提现,或者提现失败的
        $this->assign('page',$show);
        $this->assign('arr',$arr);
        $this->display();
    }

    //支付后，点击完成
    public function del(){
            $id=I('post.id');
        $re=$this->db->where('id='.$id)->data(array('status'=>1))->save();
        if($re){
            $this->ajaxReturn(true);
        }
    }
}