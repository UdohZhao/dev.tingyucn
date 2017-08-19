<?php
namespace Home\Controller;
use Think\Controller;
class RankingController extends BaseController {
    // 构造方法
    public function _auto(){
        $this->assign('Ranking',1);
    }
    //排行
    public function index(){
      // display
//收入排行榜
        //排行榜，按照当前收入排行  关联服务表,实名认证表
        $sortMoney=M('userinfo')->join('service as s on s.uid=u.uid')
            ->join('identity_authentication as ia on ia.uid=u.uid')
            ->alias('u')
            ->field('u.*,ia.sex,ia.age,s.cover_path,s.scid,s.uid as suid,ia.id_card,s.id as need_id')
            ->order('u.earning desc')
            ->limit(10)
            ->group('u.id')
            ->select();
        $this->assign('sortMoney',$sortMoney);

        //新人榜,按照申请宝宝时间最近且已成功上架的签约用户的余额查询,近一个月申请为宝宝的用户
        $newInfo=M('userinfo')
            ->join('service as s on s.uid=u.uid')
            ->join('identity_authentication as ia on ia.uid=u.uid')
            ->alias('u')
            ->field('u.*,ia.sex,ia.age,s.cover_path,s.scid,s.uid as suid,ia.id_card,s.id as need_id')
            ->order('u.uid desc')
            ->limit(10)
            ->group('u.id')
            ->select();
        $newInfo = my_sort($newInfo,'earning',SORT_DESC,SORT_NUMERIC);
        $this->assign('newInfo',$newInfo);
      $this->display();
    }
}