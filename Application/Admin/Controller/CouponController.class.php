<?php
namespace Admin\Controller;
use Think\Controller;
class CouponController extends BaseController{
    public $db;
    public function _auto(){
        $this->db=M('discount_coupon');
    }
    //新用户管理
    public function indexNew(){
        //所用用户id
       $allId=$this->getid();
        $oldId=$allId['oldId'];//优惠券里面的id
        $userId=$allId['all'];
        //获取新用户id
        $oldIdArr=explode(',',$oldId);
        $userIdArr=explode(',',$userId);//所有用户id值
        foreach($userIdArr as $key=>$val){
            foreach($oldIdArr as $k=>$v){
                if($v==$val){
                    unset($userIdArr[$key]);//得到新用户id值数组
                }
            }
        }
        $needStr=implode(',',$userIdArr);
        if($needStr==''){
            $this->assign('noId','暂无新用户');
            $this->display();
            exit;
        }
        $where='u.id in ('.$needStr.') and u.status=0';
        //搜索手机号码
        if(I('post.keyword')){
            $keyword=I('post.keyword');
            $where="u.id in({$needStr}) and  and u.status=0 u.username like '%{$keyword}%'";//普通用户 type=0
        }
        $count=M('user')->where($where)->alias('u')->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $needInfo=M('user')->where($where)
                            ->join('left join identity_authentication as ia on ia.uid=u.id')
                            ->field('ia.sex,ia.ctime,u.*')
                            ->limit($Page->getStart(),6)
                            ->alias('u')
                            ->select();
        $this->assign('needInfo',$needInfo);
        $this->assign('page',$show);
        $this->display();
}

//老用户管理
    public function indexOld(){
        //所用用户id
        $allId=$this->getid();
        $needStr=$allId['oldId'];//优惠券里面的id

        if($needStr==''){
            $this->assign('noId','暂无用户');
            $this->display();
            exit;
        }
        $where='u.id in ('.$needStr.') and u.status=0';
        //搜索手机号码
        if(I('post.keyword')){
            $keyword=I('post.keyword');
            $where="u.id in({$needStr}) and  and u.status=0 u.username like '%{$keyword}%'";//普通用户 type=0
        }
        $count=M('user')->where($where)->alias('u')->count();

        $Page       = new selfPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出
        $needInfo=M('user')->where($where)
            ->join('left join identity_authentication as ia on ia.uid=u.id')
            ->field('ia.sex,ia.ctime,u.*')
            ->limit($Page->getStart(),6)
            ->alias('u')
            ->select();
        $this->assign('needInfo',$needInfo);
        $this->assign('page',$show);
        $this->display();
    }



    //获取优惠券的用户Id，所有用户id
    private function getid(){
        //查找优惠券里面的用户id
        $arr=$this->db->field('uids')->select();
        $idStr='';
        foreach($arr as $key=>$value){
            $value['uids']=unserialize($value['uids']);//反序列化数组
            foreach($value['uids'] as $k=>$v){
                $idStr.=$v['id'].',';//优惠券里的用户id值
            }
        }
        $idStr=rtrim($idStr,',');
        $userInfo=M('user')->select();
        $userId='';
        foreach($userInfo as $Ukey=>$Uval){
            $userId.=$Uval['id'].',';//所有用户的id值
        }
        $userId=rtrim($userId,',');

        $arrInfo=array('all'=>$userId,'oldId'=>$idStr);
        return $arrInfo;
    }


    public function gift(){
        $id=I('get.id');//获取赠送的用户i
        $status=I('get.status');
        $this->assign('status',$status);
        if($id){
            $userInfo=M('user')->where('id='.$id)->find();
        }elseif($status){
            $userInfo=array('username'=>'所有老用户');
        }else{
            $userInfo=array('username'=>'所有新用户');
        }
        //查询所有服务名称
        $sevice=M('service_category')->group('type')->field('type')->select();
        foreach($sevice as $key=>$val){
            $needService=M('service_category')->where('type='.$val['type'])->select();
            $sevice[$key]['detail']=$needService;
            if($val['type']==0){
                $sevice[$key]['type']='线上游戏';
            }else if($val['type']==1){
                $sevice[$key]['type']='线上娱乐';
            }else if($val['type']==2){
                $sevice[$key]['type']='线下娱乐';
            }
        }

        $this->assign('sevice',$sevice);
        $this->assign('userInfo',$userInfo);
        $this->display();
    }
    public function save(){
        $uid=I('post.uid');
        unset($_POST['username']);
        $_POST['end_time']=strtotime($_POST['end_time'].' 23:59:59');
        if($uid){
            $info=array(0=>array('id'=>$uid));
            $info=serialize($info);//用户id数组序列化
            $_POST['uids']=$info;
            $re=$this->db->data($_POST)->add();
            if($re){
                $this->ajaxReturn(array('info'=>1,'msg'=>'赠送成功'));
            }
        }else{//赠送所有新用户
            $allId=$this->getid();
            $oldId=$allId['oldId'];//优惠券里面的id
            $userId=$allId['all'];
            //获取新用户id
            $oldIdArr=explode(',',$oldId);
            $userIdArr=explode(',',$userId);//所有用户id值
            foreach($userIdArr as $key=>$val){
                foreach($oldIdArr as $k=>$v){
                    if($v==$val){
                        unset($userIdArr[$key]);//得到新用户id值数组
                    }
                }
            }
            $idArr=array();
            foreach ($userIdArr as $Ukey=>$Uval){
                $idArr[$Ukey]['id']=$Uval;
            }
                $info=serialize($idArr);//所有用户id数组序列化
                $_POST['uids']=$info;
            $res=$this->db->data($_POST)->data($_POST)->add();
            if($res){
                $this->ajaxReturn(array('info'=>2,'msg'=>'所有用户增送成功'));
            }

        }
    }

    public function Oldsave(){
        $uid=I('post.uid');
        unset($_POST['username']);
        $_POST['end_time']=strtotime($_POST['end_time'].' 23:59:59');
        if($uid){
            $info=array(0=>array('id'=>$uid));
            $info=serialize($info);//用户id数组序列化
            $_POST['uids']=$info;
            $re=$this->db->data($_POST)->add();
            if($re){
                $this->ajaxReturn(array('info'=>1,'msg'=>'赠送成功'));
            }
        }else{//赠送所有老用户
            $allId=$this->getid();
            $oldId=$allId['oldId'];//优惠券里面的id

            $userIdArr=explode(',',$oldId);//所有用户id值
            $needIdArr=array();
            foreach ($userIdArr as $key=>$value){
                if(!in_array($value,$needIdArr)){//防止Id重复;
                    $needIdArr[]=$value;//收录所有老用户uid
                }
            }
            $idArr=array();
            foreach ($needIdArr as $Ukey=>$Uval){
                $idArr[$Ukey]['id']=$Uval;
            }
            $info=serialize($idArr);//所有用户id数组序列化
            $_POST['uids']=$info;
            $res=$this->db->data($_POST)->data($_POST)->add();
            if($res){
                $this->ajaxReturn(array('info'=>2,'msg'=>'所有用户增送成功'));
            }
        }
    }
}