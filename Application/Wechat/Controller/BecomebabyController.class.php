<?php
namespace Wechat\Controller;
use Think\Controller;
class BecomebabyController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
            $this->uid=$_SESSION['userinfo']['uid'];//用户id
    }
    //成为宝宝
    public function index(){
        //查询服务，服务类型代码
        $type=M('service_category')->field('type')->group('type')->select();
        $needInfo=array();
        foreach($type as $key=>$value){
            $serverInfo=M('service_category')->where('type='.$value['type'])->select();
            if($value['type']==0){
                    $value['type']='线上游戏';
            }elseif($value['type']==1){
                    $value['type']='线上娱乐';
            }elseif($value['type']==2){
                $value['type']='线下娱乐';
            }
            $needInfo[$key]['typename']=$value['type'];  //属于哪一大类
            $needInfo[$key]['detail']=$serverInfo;   //大类的服务项目数组信息
        }
        $this->assign('needInfo',$needInfo);
      $this->display();
    }

    //成为宝宝保存

}