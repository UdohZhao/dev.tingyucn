<?php
namespace Admin\Controller;
use Think\Controller;
class RechargeController extends BaseController{
    public $db;
    //构造方法
    public function _auto(){
        $this->db=M('account');
    }

    //用户充值记录列表
    public function oper(){

        $where='a.type=0';
        if(I('get.keyword')){
            $keyword=I('get.keyword');
            $where.=" and us.username like '%{$keyword}%'";
        }
        $count=$this->db->where($where)
            ->join('userinfo as u on u.uid=a.uid')
            ->join('user as us on us.id=a.uid')
            ->alias('a')
            ->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $arr=$this->db->where($where)
            ->join('userinfo as u on u.uid=a.uid')
            ->join('user as us on us.id=a.uid')
            ->alias('a')
            ->field('a.*,u.alipay,us.username')
            ->limit($Page->getStart(),6)
            ->select();//未处理的提现,或者提现失败的
        $this->assign('page',$show);
        $this->assign('info',$arr);
        $this->display();
    }
}