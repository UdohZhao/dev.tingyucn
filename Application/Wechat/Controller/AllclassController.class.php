<?php
namespace Wechat\Controller;
use Think\Controller;
class AllclassController extends Controller {
    //构造方法
    public function _auto(){

    }
    //全部分类
    public function index(){

        //服务类型
        $typeInfo=M('service_category')->group('type')->field('id,type')->select();
        $this->assign('typeInfo',$typeInfo);
        $needInfo=array();
        foreach ($typeInfo as $key=>$value){
            $serverInfo=M('service_category')->where('type='.$value['type'])
                                            ->order('type asc')
                                            ->select();
            $needInfo[$key]['type']=$typeInfo[$key]['type'];
            $needInfo[$key]['typeid']=$typeInfo[$key]['id'];
            $needInfo[$key]['detail']=$serverInfo;
        }
        $this->assign('needInfo',$needInfo);
      $this->display();
    }
}