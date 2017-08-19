<?php
namespace Wechat\Controller;
use Think\Controller;
class BlacklistController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    //黑名单
    public function index(){
        $blackInfo=M('userinfo')->where('uid='.$this->uid)->field('blacklist')->find();

       $blackArr=unserialize($blackInfo['blacklist']);
        $idStr='';
        foreach ($blackArr['uid'] as $key=>$val){
            $idStr.=$val.',';
        }
        $idStr=rtrim($idStr,',');
        if($idStr){
            $arrInfo=M('service')->where("s.uid in ({$idStr})")
                ->join('userinfo  as u on u.uid=s.uid')
                ->alias('s')
                ->field('s.*,u.nickname')
                ->group('s.uid')
                ->select();
            $this->assign('arrInfo',$arrInfo);
        }

        $this->display();
    }
}