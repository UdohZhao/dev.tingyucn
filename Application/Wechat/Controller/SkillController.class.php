<?php
namespace Wechat\Controller;
use Think\Controller;
class SkillController extends BaseController {
    //构造方法
    public $uid;
    public function _auto(){
        $this->uid=$_SESSION['userinfo']['uid'];//用户id
    }
    //技能说明
    public function index(){
        $id=I('get.id');//服务项目id
        $this->assign('id',$id);
        $babyInfo=M('service')->where('uid='.$this->uid.' and scid='.$id)->find();
        $this->assign('babyInfo',$babyInfo);
      $this->display();
    }

    //保存成为宝宝
    public function save(){
        //查询宝宝身份认证状态
        $checkIa=M('identity_authentication')->where('uid='.$this->uid)
                                        ->field('status')->find();
        if($checkIa['status']==2){
            $url='Index';//跳转页面
        }else{
            $url='Authentication';//跳转到身份认证页面
            $this->ajaxReturn(array('status'=>4,'msg'=>'检测到没有身份认证未通过,进入身份认证页面','url'=>$url));
            exit;
        }
        //描述内容
        $content=I('post.explain','','strip_tags,htmlspecialchars');
        //选择的服务id
        $id=I('post.scid');
        //封面照路径

        $data=array(
                'uid'=>$this->uid,
                'scid'=>$id,
                'explain'=>$content,
                'bid_price'=>I('bid_price'),
                'ctime'=>time(),
        );

        $babyInfo=M('service')->where('uid='.$this->uid.' and scid='.$id)->find();
        $needId=$babyInfo['id'];//该条记录的主键id值

        $upload=upload();
        $needUrl=array();
        //查询经判断删除旧照片
        if($needId){
            $result=M('service')->where('id='.$needId)
                ->field('cover_path')
                ->find();
        }
        foreach ($upload as $key=>$v){
            $topurl='/Wechat/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $needUrl[$key]=$topurl;
            $data[$key]=$needUrl[$key];//数组加入图片信息
            if(isset($result[$key])){
                $a=webdelpath($result[$key]);  //若删除，则为重新上传了照片
                if($a){
                    $data['status']=0;
                    $data['type']=2;//审核状态也为待审核
                }
            }
        }
        //数据数组里添加上传信息;

        if($babyInfo){//若查询得到，为修改

            unset($data['ctime']);
            $data['status']=$babyInfo['status'];
            $res=M('service')->where('uid='.$this->uid.' and id='.$needId)->data($data)->save();
            if($res===0){
                $this->ajaxReturn(array('info'=>3,'msg'=>'已保存','url'=>$url));
            }elseif($res>0){
                if($data['type']===0){
                    $this->ajaxReturn(array('info'=>2,'msg'=>'已修改','url'=>$url));
                }else{
                    $this->ajaxReturn(array('info'=>2,'msg'=>'保存成功','url'=>$url));
                }
            }
        }else{
            //先添加文字信息
            $data['status']=0;
            $data['type']=2;
            $result=M('user')->where('id='.$this->uid)->data(array('type'=>1))->save();
            $re=M('service')->data($data)->add();
            $this->ajaxReturn(array('info'=>1,'url'=>$url,'checkStatus'=>$checkIa['status']));
        }
    }

}