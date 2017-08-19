<?php
namespace Wechat\Controller;
use Think\Controller;
class FeedbackController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];//用户id
    }
    //意见反馈
    public function index(){
      $this->display();
    }

    public function save(){
        $content=I('post.content');
        $data=array(
            'uid'=>$this->uid,
            'content'=>$content,
            'ctime'=>time(),
            'status'=>0,
        );
        //当日0点的时间戳,
        $startime=strtotime(date('Ymd'));
        //当日24点时间戳
        $endtime=strtotime(date('Ymd'))+86400;
        $arr=M('opinion')->where('uid='.$this->uid.' and ctime>'.$startime.' and ctime<'.$endtime)->select();


        if($arr){
            if(count($arr)>=3){//一日三次
                $this->ajaxReturn(array('info'=>2,'msg'=>'今日您已提交三次，请于24小时后再提交'));
            }else{
                $re=M('opinion')->data($data)->add();
                $this->ajaxReturn(true);
            }
        }else{
            $re=M('opinion')->data($data)->add();
            $this->ajaxReturn(true);
        }
    }
}