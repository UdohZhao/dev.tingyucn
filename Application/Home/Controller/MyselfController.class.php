<?php
namespace Home\Controller;
use Think\Controller;
class MyselfController extends BaseController {
    //构造方法
    public $uid;
    public $db;
    public function _auto(){
        if(!$_SESSION['userinfo']['uid']){
            echo alert('未登陆',__APP__.'/Index/index',5);
            die;
        }
        $this->uid=$_SESSION['userinfo']['uid'];
        $this->db=M('userinfo');
    }
    // 我
    public function index(){
      // display
        if($this->getinfo()){
            $info=$this->getinfo();
            $arr=$info;
            $arr['iid']=unserialize($arr['iid']);//反序列化数组 关联兴趣表
            $needStr='';
            $needIdStr='';
            foreach ($arr['iid'] as $k=>$v){
                $needStr.=$v['cname'].'/';
                $needIdStr.=$v['id'].',';
            }
            $needStr=rtrim($needStr,'/');
            $needIdStr=rtrim($needIdStr,',');
            $arr['iid']=$needStr;
            $arr['id']=$needIdStr;
            $arr['pid']=$info['pid'];
            $arr['prof']=$info['pname'];//职业名
            $this->assign('info',$arr);

        }
        //职业名称
        $jobname=M('profession')->select();

        $this->assign('jobname',$jobname);

        //兴趣名称
        $interestInfo=M('interest')->select();
        $this->assign('interestInfo',$interestInfo);

        //获取已选择的兴趣id数组
        $arr['iid']=unserialize($info['iid']);//反序列化数组
        $needStr='';
        foreach ($arr['iid'] as $k=>$v){
            $needStr.=$v['id'].',';
        }

        $needStr=rtrim($needStr,',');
        $this->assign('needStr',$needStr);
        $needStr=explode(',',$needStr);//转为数组

        foreach ($interestInfo as $key=>$value){
            if(in_array($interestInfo[$key]['id'],$needStr)){
                $interestInfo[$key]['status']=1;//标记为已选择
            }
        }

        $this->assign('interestInfo',$interestInfo);


        //成为宝宝页面

        //查询宝宝已有的项目
        $hasService=M('service')->where('uid='.$this->uid.' and status=0 and type=2')->select();
        //得出已有项目的scid值数组
        $scidCollect=array();
        foreach($hasService as $Hkey=>$Hval){
            $scidCollect[]=$Hval['scid'];
        }
        //成为签约用户，提供的服务
        $type=M('service_category')->field('type')->group('type')->select();
        $needInfo=array();
        foreach($type as $key=>$value){
            $serverInfo=M('service_category')->where('type='.$value['type'])->select();
            if($value['type']==0){
                $value['type']='线上游戏';
            }elseif($value['type']==1){
                $value['type']='线上娱乐';
            }elseif($value['type']==2){
                $value['type']='线上娱乐';
            }
            //对比所有服务项目
            foreach($serverInfo as $Skey=>$Sval){
                if(in_array($Sval['id'],$scidCollect)){
                    $serverInfo[$Skey]['selStatus']=1;
                }
            }
            $needInfo[$key]['typename']=$value['type'];  //属于哪一大类
            $needInfo[$key]['detail']=$serverInfo;   //大类的服务项目数组信息
        }

        $this->assign('needInfo',$needInfo);


        //黑名单
        $blackInfo=M('userinfo')->where('uid='.$this->uid)->field('blacklist')->find();

        $blackArr=unserialize($blackInfo['blacklist']);
        $idStrArr='';
        foreach ($blackArr['uid'] as $key=>$val){
            $idStrArr.=$val.',';
        }
        $idStrArr=rtrim($idStrArr,',');
        if($idStrArr){
            $arrInfoArr=M('service')->where("s.uid in ({$idStrArr})")
                ->join('userinfo  as u on u.uid=s.uid')
                ->join('identity_authentication as ia on ia.uid=u.uid')
                ->alias('s')
                ->field('s.*,u.nickname,ia.id_card,ia.sex')
                ->group('s.uid')
                ->select();
            $this->assign('arrInfoArr',$arrInfoArr);
        }

        $this->display();
    }

    private function getinfo(){
        $arr=$this->db->where('u.uid='.$this->uid)
            ->join('left join identity_authentication as i on u.uid=i.uid')
            ->join('left join profession as p on p.id=u.pid')
            ->join('left join user_media as um on um.uid=u.uid')
            ->alias('u')
            ->field('u.*,i.sex,p.cname as pname,i.id_card,um.video_path,um.audio_path')
            ->find();//查找相关信息
        return $arr;
    }

    //保存头像
    public function saveImg(){
        $upload=upload();
        $imgpath=M('userinfo')->where('uid='.$this->uid)
            ->field('head_portrait')
            ->find();
        $oldPath=$imgpath['head_portrait'];
        foreach ($upload as $key=>$v){
            $topurl='/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
        }
        $data=array();
        if($upload['status']!=4){
            $data['head_portrait']=$topurl;
            homedelpath($oldPath);
        }else{
            $this->ajaxReturn(array('info'=>3,'msg'=>$upload['errorInfo']));
            exit;
        }
        $re=M('userinfo')->where('uid='.$this->uid)->data($data)->save();
        if($re>0){
            $this->ajaxReturn(array('info'=>true,'msg'=>$topurl));
        }

    }

//保存基本信息
    public function saveInfo(){
        $sex=I('post.sex');
        $signature=I('post.signature');
        $iid=I('post.iid');
        $interestInfo=M('interest')->where("id in({$iid})")->field('id,cname')->select();
        $interestInfo=serialize($interestInfo);//序列化数组为字符串

        $pid=I('post.pid');
        $data=array(
            'signature'=>$signature,
            'iid'=>$interestInfo,
            'pid'=>$pid,
            'nickname'=>I('post.nickname')
        );

        $idata=array('sex'=>$sex);
        //修改身份认证性别
        $result=M('identity_authentication')->where('uid='.$this->uid)->find();
        if(!$result){
            $rel=M('identity_authentication')->data(array('sex'=>$sex,'uid'=>$this->uid))->add();
        }else{
            $rel=M('identity_authentication')->where('uid='.$this->uid)->data($idata)->save();
        }

        $re=M('userinfo')->where('uid='.$this->uid)->data($data)->save();
        $this->ajaxReturn(true);
        exit;
    }


    //服务信息请求
    public function serviceDetail(){
        //服务类型的id
        $scid=I('post.id');
        //查询该项目，该用户是否成为或者申请过
        $arr=M('service')->where('uid='.$this->uid.' and scid='.$scid)->find();
       //选择的服务类型id
        $arr['selId']=$scid;
        $num=count($arr);
        $arr['lengthArr']=$num;
        $this->ajaxReturn($arr);
    }

    public function save(){
        //查询宝宝身份认证状态
        $checkIa=M('identity_authentication')->where('uid='.$this->uid)
            ->field('status')->find();
        if($checkIa['status']==2){
            $url='Myself';//跳转页面
        }else{
            $url='Authentication';//跳转到身份认证页面
            $this->ajaxReturn(array('status'=>4,'msg'=>'检测到身份认证未通过,请先进入身份认证','url'=>$url));
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
            $topurl='/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $needUrl[$key]=$topurl;
            $data[$key]=$needUrl[$key];//数组加入图片信息
            if(isset($result[$key])){
                $a=homedelpath($result[$key]);  //若删除，则为重新上传了照片
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


    //意见保存
    public function ideaSave(){
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


    public function metaSave(){
        if(I('get.type')==1){
            //音频,100Kb以内
            $size=102400;
        }else if(I('get.type')==2){
            //视频,3M以内
            $size=3145728;
        }
        $upload=upload($size);
        if($upload['status']==4){
            $this->ajaxReturn(array('info'=>4,'msg'=>$upload['errorInfo']));
            exit;
        }
        $re=M('user_media')->where('uid='.$this->uid)->find();
        $metaArr=array();
        foreach ($upload as $key=>$v){
            $audiourl='/Uploads/'.$upload[$key]['savepath'].$upload[$key]['savename'];
            $metaArr[$key]=$audiourl;
            if($re[$key]){
                homedelpath($re[$key]);
            }
        }


        if($re){
            //查找到则为修改
            $result=M('user_media')->where('uid='.$this->uid)
                ->data($metaArr)->save();
        }else{
            $metaArr['uid']=$this->uid;
            $result=M('user_media')->data($metaArr)->add();
        }
        if($result>0){
            $this->ajaxReturn(array('info'=>true,'url'=>$audiourl));
        }

    }

    public function metaDel(){
        $type=I('post.type');
        if($type==1){
            //删除音频
            $delName='audio_path';
        }else{
            //删除视频
            $delName='video_path';
        }
        $pathInfo=M('user_media')->where('uid='.$this->uid)->field($delName)->find();
        homedelpath($pathInfo[$delName]);
        $re=M('user_media')->where('uid='.$this->uid)->data(array($delName=>''))->save();
        if($re>0  || $re===0){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
}