<?php
namespace Home\Controller;
use Think\Controller;
class AuthenticationController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];
    }
    // 身份认证
    public function index(){
        //得到信息数据
        $arr=$this->getinfo();
        $this->assign('arr',$arr);
      $this->display();
    }

    private function getinfo(){
        $arr=M('identity_authentication')->where('uid='.$this->uid)->find();//查找是否已经实名认证
        return $arr;
    }

//保存信息
    public function save(){

        //获取传过来的数据
        $realName=I('post.real_name');
        $sex=I('post.sex');
        $idCard=I('post.id_card');
        $age=I('post.user_age');

        $upload=upload();
        $needUrl=array();
        //查询经判断删除旧照片

        $result=M('identity_authentication')->where('uid='.$this->uid)
            ->field('front_path,back_path,figure_path')
            ->find();

        foreach ($upload as $key=>$v){
            $topurl='/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $needUrl[$key]=$topurl;
            if(isset($result[$key])){
                homedelpath($result[$key]);
            }
        }
        //先保存文字信息
        $data=array(
            'real_name'=>$realName,
            'sex'=>$sex,
            'id_card'=>$idCard,
            'age'=>intval($age)
        );
        if($this->getinfo()){//若查询得到  则为修改
            $data['ctime']=time();
            $resInfo=M('identity_authentication')->where('uid='.$this->uid)
                ->data($data)
                ->save();

            if($upload['status']!=4){//没有报错信息，有照片上传
                $needUrl['status']=0;
                $resImg=M('identity_authentication')->where('uid='.$this->uid)
                    ->data($needUrl)
                    ->save();
                send_to_admin(13151263776,'马俊');
                $this->ajaxReturn(true);
            }else{
                $this->ajaxReturn(array('info'=>2,'msg'=>'信息保存成功'));
            }
        }else{//若没查询到，则为添加
            $data['uid']=$this->uid;
            $data['ctime'] = time();
            $resId=M('identity_authentication')
                ->data($data)
                ->add();
            $re=M('identity_authentication')->where('id='.$resId)->data($needUrl)->save();
            send_to_admin(13151263776,'马俊');
            $this->ajaxReturn(true);
        }





    }
}