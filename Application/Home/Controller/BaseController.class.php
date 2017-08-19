<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    //构造方法
    public function _initialize(){
      // auto
        $siteInfo=M('website_config')->order('id desc')->find();
        $this->assign('siteInfo',$siteInfo);
        if( $_SESSION['userinfo']['uid'] ){
            $uid=$_SESSION['userinfo']['uid'];
            // status

            if( $_SESSION['userinfo']['status'] == 1 ){
                echo alert('无法登录，原因是该用户已被冻结，请自行联系管理员！',U('Index/index'),5);
                die;
            }
            // 查找用户信息
            //查找用户信息
            $userInfo=M('userinfo')->where('u.uid='.$uid)
                ->join('left join identity_authentication as i on u.uid=i.uid')
                ->alias('u')
                ->field('u.*,i.sex,i.id_card,i.status as istatus')
                ->find();//查找相关信息
            if($userInfo['sex']==1){
                $userInfo['sex']='男';
            }else{
                $userInfo['sex']='女';
            }
            $this->assign('userInfo',$userInfo);
            //查找站点信息，最后一条信息
        }

      if(method_exists($this,'_auto'))
            $this->_auto();
    }
}