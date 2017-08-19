<?php
namespace Admin\Controller;
use Think\Controller;
class ServicereviewController extends BaseController
{
    public $db;
    //构造方法
    public function _auto()
    {
        $this->db = M('service');
    }

    //审核宝宝
    public function index(){
        $where='';
        //搜索手机号码
         if(isset($_GET['type'])){
            $where.="s.type={$_GET['type']}";
             $this->assign('s_type',1);
        }elseif(isset($_GET['status'])){
            $where.="s.status={$_GET['status']} and s.type=2";
             $this->assign('s_status',$_GET['status']);
        }else{
             //默认查询待审核用户
             $where.="s.type=0";
             $this->assign('s_type',1);
        }

        if(I('get.keyword')){
            $keyword=I('get.keyword');
            $where.=" and u.username like '%{$keyword}%'";//普通用户 type=0
        }

        $count=$this->db->join('user as u on u.id=s.uid')
                ->alias('s')
                ->where($where)->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $arr=$this->db->where($where)
                        ->join('user as u on u.id=s.uid')
                      ->join('service_category as sc on sc.id=s.scid')
                      ->field('u.username,sc.cname,s.*')
                      ->limit($Page->getStart(),6)
                      ->alias('s')
                    ->select();//所有用户信息
        $this->assign('arr',$arr);
        $this->assign('page',$show);

        // 查询系统是否添加分成信息
        $divide=M('divide_into')->find();
        $this->assign('divide',$divide);
        $this->display();
    }

    //审核宝宝
    public function reviews(){
            $id=I('get.id');

            $info=$this->db->where('s.id='.$id)
            ->join('user as u on u.id=s.uid')
            ->join('service_category as sc on sc.id=s.scid')
            ->field('s.id,u.username,sc.cname,s.cover_path,s.explain,s.bid_price')
            ->alias('s')
            ->find();//所有用户信息
            //=$this->db->where('id='.$id)->field('cover_path,explain,status')->find();
            $this->assign('info',$info);

        $url=$_SERVER['HTTP_REFERER'];//获取上页的url
        $this->assign('url',$url);

        $this->display();
    }
    //保存审核结果
    public function ssave(){
        $id=I('post.id');
        $status=I('post.status');
        if($status==1){
            $msg='成功下架';
        }else if($status==0){
            $msg='上架成功';
        }
        $re=$this->db->where('id='.$id)
            ->data(array('status'=>$status))
            ->save();
        if($re>=0){
            $info=array('info'=>true,'msg'=>$msg);
            $this->ajaxReturn($info);
        }


    }

    //保存审核结果
    public function rsave(){
        $id=I('post.id');
        $status=I('post.type');
        if($status==1){
            $msg='审核失败';
        }else if($status==2){
            $msg='审核成功';
        }
        $re=$this->db->where('id='.$id)
            ->data(array('type'=>$status))
            ->save();
        if($re){
            $info=array('info'=>true,'msg'=>$msg);
            $this->ajaxReturn($info);
        }

    }

        public function dongjie(){
            $status=I('post.status');
            $id=I('post.id');
            if($status==0){
                $msg='上架成功';
            }else if($status==1){
                $msg='下架成功';
            }

            $re=$this->db->where('id='.$id)->data(array('status'=>$status))->save();

            $this->ajaxReturn(array('info'=>true,'msg'=>$msg));
        }
}