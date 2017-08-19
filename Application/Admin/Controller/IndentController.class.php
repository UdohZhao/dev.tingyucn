<?php
namespace Admin\Controller;
use Think\Controller;
class IndentController extends BaseController
{


    //构造方法
    public function _auto()
    {

    }

    //查询用户订单
    public function oper(){
        $where='';
        $arr=array();
        if(I('get.reply_status')==='0'){
            $where.='si.reply_status=0';
            $arr['reply_status']=0;
        }elseif(I('get.reply_status')==='2'){
            $where.='si.reply_status=2';
            $arr['reply_status']=2;
        }elseif(I('get.type')==='0'){
            $where.='si.type=0 and si.reply_status=1';
            $arr['type']=0;
        }elseif(I('get.status')==='1'){
            $where.='si.status=1';
            $arr['status']=1;
        }elseif(I('get.status')==='2'){
            $where.='si.status=2';
            $arr['status']=2;
        }
        $this->assign('arr',$arr);

        if(I('get.keyword')){
            $keyword=I('get.keyword');
            $where.=" and us.username like '%{$keyword}%'";
        }
        $count=M('service_indent')->where($where)
            ->join('left join service as s on si.sid=s.id')
            ->join('left join service_category as sc on s.scid=sc.id')
            ->join('left join userinfo as u on u.uid=s.uid and si.sid=s.id')
            ->join('left join identity_authentication as ia on ia.uid=si.uid')
            ->join('left join user as us on us.id=si.uid')
            ->alias('si')->count();//满足条件的数量

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->pages();// 分页显示输出
        $orderInfo=M('service_indent')->where($where)
            ->join('left join service as s on si.sid=s.id')
            ->join('left join service_category as sc on s.scid=sc.id')
            ->join('left join userinfo as u on u.uid=s.uid and si.sid=s.id')
            ->join('left join identity_authentication as ia on ia.uid=si.uid')
            ->join('left join user as us on us.id=si.uid')
            ->alias('si')
            ->order('si.id desc')
            ->field('si.*,s.cover_path,sc.cname,s.uid as suid,us.username,ia.real_name,u.nickname,sc.type as sc_type')
            ->limit($Page->getStart(),6)
            ->select();

        foreach($orderInfo as $key=>$val){
            $baybyInfo=M('user')->where('u.id='.$val['suid'])
                ->join('left join identity_authentication as ia on ia.uid=u.id')
                ->field('ia.real_name,u.username')
                ->alias('u')
                ->find();
            $orderInfo[$key]['babyName']=$baybyInfo['real_name'];
            $orderInfo[$key]['babyPhone']=$baybyInfo['username'];
        }
        $this->assign('orderInfo',$orderInfo);
        $this->assign('page',$show);
       $this->display();
    }
}