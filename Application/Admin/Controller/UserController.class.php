<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends BaseController{
        //用户信息管理
    //构造方法
    public function _auto(){

    }

    //普通用户注册信息
    public function logininfo(){
        //用户表关联身份认证表
        //满足条件的记录条数
        $where='u.type=0';
        if(I('post.keyword')){
            $keyword=I('post.keyword');
            $where="u.type=0 and u.username like '%{$keyword}%'";//普通用户 type=0
        }
        $count=M('user')->where($where)
                        ->join('identity_authentication as i on i.uid=u.id')
                        ->alias("u")
                        ->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $loginInfo=M('user')->where($where)
                            ->join('identity_authentication as i on i.uid=u.id')
                            ->alias("u")
                            ->field('i.*,u.username,u.type,u.status as ustatus')
                            ->limit($Page->getStart(),6)
                            ->select();
        //var_dump($loginInfo);
        foreach($loginInfo as $key=>$value){
            foreach ($value as $k=>$v){
                if($value[$k]==='' || $value[$k]===null){
                    $loginInfo[$key]['perfect']=4;//表示信息不完善的状态值
                }
            }
        }
        $this->assign('logininfo',$loginInfo);
        $this->assign('page',$show);
        $this->display();
    }
    //签约用户注册信息
    public function bblogininfo(){
        //用户表关联身份认证表
        //满足条件的记录条数
        $where='u.type=1';
        if(I('post.keyword')){
            $keyword=I('post.keyword');
            $where="u.type=1 and u.username like '%{$keyword}%'";//普通用户 type=0
        }
        $count=M('user')->where($where)
            ->join('identity_authentication as i on i.uid=u.id')
            ->alias("u")
            ->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $loginInfo=M('user')->where($where)
            ->join('identity_authentication as i on i.uid=u.id')
            ->alias("u")
            ->field('i.*,u.username,u.type,u.status as ustatus')
            ->limit($Page->getStart(),6)
            ->select();

        foreach($loginInfo as $key=>$value){
            foreach ($value as $k=>$v){
                if($value[$k]==='' || $value[$k]===null){
                    $loginInfo[$key]['perfect']=4;//表示信息不完善的状态值
                }
            }
        }
        $this->assign('logininfo',$loginInfo);
        $this->assign('page',$show);
        $this->display();
    }
    //冻结、解冻用户
    public function dongjie(){
        $id=I('post.id');
        $status=I('post.status');
        $re=M('user')->where('id='.$id)->data(array('status'=>$status))->save();
        if($status==0){
            $msg='冻结';
            $status='解冻成功';
            $name=1;
        }else{
            $msg='解冻';
            $status='冻结成功';
            $name=0;
        }
        if($re>0){
            $arr=array('info'=>true,'msg'=>$msg,'status'=>$status,'name'=>$name);
        }
        $this->ajaxReturn($arr);
    }


    //审核用户照片
    public function reviews(){
        $id=I('get.id');
        $info=M('identity_authentication')->where('id='.$id)
                        ->field('id,id_card,front_path,back_path,uid,real_name,figure_path')
                        ->find();
        //查询电话号码
        $userPhone=M('user')->where('id='.$info['uid'])->field('username')->find();
        $info['phoneNum']=$userPhone['username'];
        $url=$_SERVER['HTTP_REFERER'];//获取上页的url
        $this->assign('url',$url);
        $this->assign('info',$info);
        $this->display();
    }
    //保存审核结果
    public function rsave(){
            $id=I('post.id');
        $status=I('post.status');
        if($status==2){
            $value='通过';
            $msg='审核通过';
        }else{
            $value='未通过';
            $msg='审核失败';
        }
        $phone=I('post.phoneNum');
        $name=I('post.real_name');
        
            $re=M('identity_authentication')->where('id='.$id)
                                            ->data(array('status'=>$status))
                                            ->save();
        $res=send_status_to_admin($phone,$name,$value);
        if($re && ($res->statusCode==200)){
            $info=array('info'=>true,'msg'=>$msg);
        }
        $this->ajaxReturn($info);
    }

//签约用户基本信息
    public function basicinfo(){

        $where='u.type=1';
        if(I('post.keyword')){
            $keyword=I('post.keyword');
            $where="u.type=1 and u.username like '%{$keyword}%'";//普通用户 type=0
        }
        $count=M('userinfo')->where($where)
            ->join('user as u on u.id=us.uid')
            ->alias("us")
            ->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出

         $info=M('userinfo')->where($where)
                        ->join('user as u on u.id=us.uid')
                        ->join('left join identity_authentication as ia on ia.uid=us.uid')
                        ->field('u.username,u.type,us.*,ia.real_name')
                        ->alias('us')
                        ->limit($Page->getStart(),6)
                        ->select();
        $this->assign('arr',$info);
        $this->assign('page',$show);
        $this->display();
    }

    //修改用户jj开
    public function savejjk(){
        $id=I('post.id');
        $jjk=I('post.jjk');
        $re=M('userinfo')->where('id='.$id)->data(array('jjk'=>$jjk))->save();
        if($re>0){
            $this->ajaxReturn(true);
        }
    }
    
}