<?php
namespace App\Domain;

use App\Model\Comment as Model_Comment;
use App\Domain\Skill as Domain_Skill;
use App\Domain\Orders as Domain_Orders;
use App\Domain\User as Domain_User;

class Comment {

    /* 评论主播 */
	public function setComment($data) {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('评价成功'), 'info' => array());
        
        $where=[
            'orderid'=>$data['orderid'],
        ];
        
        $model = new Model_Comment();
		$isexist = $model->getCommentInfo($where);
        if($isexist){
            $rs['code']=1003;
            $rs['msg']=\PhalApi\T('您已经评价过了');
            return $rs;
        }
        
        $where=[
            'id'=>$data['orderid'],
            'uid'=>$data['uid'],
        ];
        $Domain_Orders = new Domain_Orders();
		$orderinfo = $Domain_Orders->getOrderInfo($where);
        if(!$orderinfo || $orderinfo['status']!=-2){
            $rs['code']=1004;
            $rs['msg']=\PhalApi\T('订单无效，无法评价');
            return $rs;
        }
        
        $data['liveuid']=$orderinfo['liveuid'];
        $data['skillid']=$orderinfo['skillid'];
        
        $Domain_Skill = new Domain_Skill();
        if($data['label']!=''){
            $label='';
            
            $label_a=preg_split('/，|,/',$data['label']);
            $label_a=array_filter($label_a);
            
            $nums=count($label_a);
            if($nums>3){
                $rs['code']=1003;
                $rs['msg']=\PhalApi\T('最多选择三个标签');
                return $rs;
            }
            
            
        
            $list = $Domain_Skill->getLabel($data['skillid'],0);
            foreach($label_a as $k=>$v){
                foreach($list as $k1=>$v1){
                    if($v==$v1['id']){
                        $label.=$v1['id'].';';
                        
                        $Domain_Skill->upLabelNums($data['liveuid'],$data['skillid'],$v1['id']);
                    }
                }
            }
            $data['label']=$label;
        }

        $data['addtime']=time();
        
		$res = $model->setComment($data);
        if(!$res){
            $rs['code']=1003;
            $rs['msg']=\PhalApi\T('评价失败，请重试');
            return $rs;
        }
        
        /* 更新星级、评论数 */
        $where=[
            'uid'=>$data['liveuid'],
            'skillid'=>$data['skillid'],
        ];
        $Domain_Skill->upStar($where,$data['star'],1);
        
        /* 更新订单 */
        $where2=[
            'id'=>$data['orderid']
        ];
        $data2=[
            'iscommnet'=>'1'
        ];
        $Domain_Orders->upOrder($where2,$data2);
		return $rs;
	}

    /* 评价用户 */
	public function setEvaluate($data) {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('评价成功'), 'info' => array());
        
        $where=[
            'orderid'=>$data['orderid'],
        ];
        
        $model = new Model_Comment();
		$isexist = $model->getEvaluateInfo($where);
        if($isexist){
            $rs['code']=1003;
            $rs['msg']=\PhalApi\T('您已经评价过了');
            return $rs;
        }
        
        $where=[
            'id'=>$data['orderid'],
            'liveuid'=>$data['uid'],
        ];
        $Domain_Orders = new Domain_Orders();
		$orderinfo = $Domain_Orders->getOrderInfo($where);
        if(!$orderinfo || $orderinfo['status']!=-2){
            $rs['code']=1004;
            $rs['msg']=\PhalApi\T('订单无效，无法评价');
            return $rs;
        }
        
        $data['touid']=$orderinfo['uid'];
        $data['skillid']=$orderinfo['skillid'];

        $data['addtime']=time();
        
		$res = $model->setEvaluate($data);
        if(!$res){
            $rs['code']=1003;
            $rs['msg']=\PhalApi\T('评价失败，请重试');
            return $rs;
        }
        
        /* 更新用户星级、评论数 */
        $Domain_User = new Domain_User();
        $Domain_User->upStar($data['touid'],$data['star'],1);

        /* 更新订单 */
        $where2=[
            'id'=>$data['orderid']
        ];
        $data2=[
            'isevaluate'=>'1'
        ];
        $Domain_Orders->upOrder($where2,$data2);
        
		return $rs;
	}
    
    /* 技能评论总数 */
    public function getCommentNums($where=''){
        
        $model = new Model_Comment();
        $nums=$model->getCommentNums($where);
        
        return $nums;
        
    }

    /* 技能评论 */
    public function getComment($p='1',$where='',$order='id desc'){
        
        $model = new Model_Comment();
        
        $Domain_Skill = new Domain_Skill();
        
        $list=$model->getComment($p,$where,$order);
        
        foreach($list as $k=>$v){
            
            $userinfo=\App\getUserInfo($v['uid']);
            unset($userinfo['birthday']);
            $v['userinfo']=$userinfo;
            
            $v['add_time']=\App\offtime($v['addtime']);
            
            $label=[];
            
            $label_a=preg_split('/;|；/',$v['label']);
            $label_a=array_filter($label_a);
            
            $labellist=$Domain_Skill->getLabel($v['skillid']);
            foreach($label_a as $k1=>$v1){
                foreach($labellist as $k2=>$v2){
                    if($v1==$v2['id']){
                        $label[]=$v2['name'];
                    }
                }
            }
            
            $v['label_a']=$label;
            unset($v['label']);
            
            $list[$k]=$v;
        }
        
        return $list;
        
    }

    /* 用户评论 */
    public function getEvaluate($where=''){
        
        $model = new Model_Comment();
        
        $info=$model->getEvaluateInfo($where);
        if($info){
            $userinfo=\App\getUserInfo($info['touid']);
            unset($userinfo['birthday']);
            $info['userinfo']=$userinfo;
        }
        

        return $info;
        
    }
	
}
