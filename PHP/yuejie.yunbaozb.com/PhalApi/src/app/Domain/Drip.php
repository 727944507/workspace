<?php
namespace App\Domain;

use App\Model\Drip as Model_Drip;
use App\Domain\Skill as Domain_Skill;
use App\Domain\Orders as Domain_Orders;

class Drip {
    
    public function getDrip($where){
        
        $model = new Model_Drip();
		$rs = $model->getDrip($where);
        
        return $rs;
    }

    /* 检测 */
    public function checkDrip($uid){
        
        $status='0';
        
        $where=[
            'uid'=>$uid,
            'status'=>0,
        ];
        $model = new Model_Drip();
		$res = $model->getDrip($where);
        if($res){
            $status='1';
        }
        
        return $status;
    }

    /* 生成订单 */
	public function setDrip($data) {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('发布成功'), 'info' => array());
        
        $uid=$data['uid'];
        $skillid=$data['skillid'];
        $levelid=$data['levelid'];
        $sex=$data['sex'];
        $type=$data['type'];
        $svctm=$data['svctm'];
        $nums=$data['nums'];
        $des=$data['des'];
        unset($data['svctm']);
        unset($data['type']);
        
        $model = new Model_Drip();
        
        $where=[
            'uid'=>$uid,
            'status'=>0,
        ];
		$drip = $model->getDrip($where);
        
        if($drip){
            $rs['code']=1007;
			$rs['msg']=\PhalApi\T('同时只能有一个滴滴下单');
			return $rs;
        }
        
        $Domain_Skill=new Domain_Skill();
        
        $skillinfo=$Domain_Skill->getSkill($skillid);
        
        if(!$skillinfo){
            $rs['code']=1006;
			$rs['msg']=\PhalApi\T('技能不存在');
			return $rs;
        }
        
        if($levelid>0){

            $levelinfo=$Domain_Skill->getLevelInfo($skillid,$levelid);
            
            if(!$levelinfo){
                $rs['code']=1008;
                $rs['msg']=\PhalApi\T('技能和等级不匹配');
                return $rs;
            }
        
        }
        
        $svctminfo=\App\treatsvctm($type,$svctm);
        if($svctminfo['code']!=0){
            return $svctminfo;
        }
        $data['svctm']=$svctminfo['info']['svctm'];
        
        $nowtime=time();
        
        if(mb_strlen($des) > 50){
            $rs['code']=1005;
            $rs['msg']=\PhalApi\T('备注不能超过50字');
            return $rs;
        }
        

        $data['addtime']=$nowtime;
        
		$res = $model->setDrip($data);
        
        if(!$res){
            $rs['code']=1009;
            $rs['msg']=\PhalApi\T('发布失败，请重试');
            return $rs;
        }

		return $rs;
	}
    
    /* 滴滴订单 */
    public function getMyDrip($uid,$p){
        
        $model = new Model_Drip();
		$list = $model->getMyDrip($uid,$p);
        
        $Domain_Skill=new Domain_Skill();
        
        foreach($list as $k=>$v){
            $skill=$Domain_Skill->getSkill($v['skillid']);
            if(!$skill){
                $skill=(object)[];
            }
            $v['skill']=$skill;
            
            $level='不限';
            $levelinfo=$Domain_Skill->getLevelInfo($v['skillid'],$v['levelid']);
            if($levelinfo){
                $level=$levelinfo['name'];
            }
            $v['level']=$level;
            
            $v['datesvctm']=\App\handelsvctm($v['svctm']);
            $v['addtime']=date('Y-m-d H:i',$v['addtime']);
            
            $list[$k]=$v;
        }
        
        return $list;
    }

    /* 大神列表 */
    public function getLiveid($uid,$dripid,$lastid){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $model = new Model_Drip();
        
        $where1=[
            'id'=>$dripid,
        ];
        $info=$this->getDrip($where1);
        if(!$info){
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T('订单信息错误');
			return $rs;
        }
        
        if($info['status']!=0){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('订单信息错误');
			return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('无权操作');
			return $rs;
        }
        
        $where="dripid={$dripid}";
        
        if($lastid>0){
            $where.=" and id<{$lastid}";
        }
        
        $Domain_Skill = new Domain_Skill();
        
		$list = $model->getLiveid($where);
        
        foreach($list as $k=>$v){
            $v['userinfo']=\App\getUserInfo($v['liveuid']);
            
            $where=[
                'uid'=>$v['liveuid'],
                'skillid'=>$v['skillid'],
                'status'=>'1',
            ];
            $auth=$Domain_Skill->getSkillAuth($where);
            
            $v['authinfo']=$auth[0];
            
            $list[$k]=$v;
        }
        
        $rs['info']=$list;
        return $rs;
    }
	
    /* 选择大神 */
    public function selectLive($uid,$dripid,$liveuid,$paytype){
        $rs = array('code' => 0, 'msg' => \PhalApi\T('已抢单'), 'info' => array());
		$t2u = \App\isBlack($liveuid,$uid);
		if($t2u){
			$rs['code']=1010;
            $rs['msg']=\PhalApi\T('对方已将您拉黑,不能选择该大神');
            return $rs;
		}
        $where2=[
            'id'=>$dripid,
        ];
        
        $info=$this->getDrip($where2);
        
        if(!$info){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('订单信息不存在');
			return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T('无权操作');
			return $rs;
        }
        
        if($info['status']!=0){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('订单已结束');
			return $rs;
        }
        
        if($info['liveuid']!=0){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('已选择大神了');
			return $rs;
        }
        
        $model = new Model_Drip();
        $where=[
            'liveuid'=>$liveuid,
            'dripid'=>$info['id'],
        ];
        $grapinfo=$model->getGrap($where);
        if(!$grapinfo){
            $rs['code']=1005;
			$rs['msg']=\PhalApi\T('大神未抢单');
			return $rs;
        }
        
        $Domain_Skill = new Domain_Skill();
        $where2=[
            'uid'=>$liveuid,
            'skillid'=>$info['skillid'],
            'status'=>1,
            'switch'=>1,
        ];
        
        $auth=$Domain_Skill->getAuthInfo($where2);
        if(!$auth){
            $rs['code']=1006;
			$rs['msg']=\PhalApi\T('大神相关技能未认证或未开启');
			return $rs;
        }
        
        if($info['sex']!=0 && $auth['sex']!=$info['sex']){
            $rs['code']=1007;
			$rs['msg']=\PhalApi\T('大神不符合订单要求');
			return $rs;
        }
        
        if($info['levelid']> $auth['levelid']){
            $rs['code']=1008;
			$rs['msg']=\PhalApi\T('大神不符合订单要求');
			return $rs;
        }
        
        $data=[
            'uid'=>$uid,
            'liveuid'=>$liveuid,
            'skillid'=>$info['skillid'],
            'type'=>$paytype,
            'svctm'=>$info['svctm'],
            'nums'=>$info['nums'],
            'order_type'=>1,
        ];
        
        $Domain_Orders = new Domain_Orders();
        
        $res=$Domain_Orders->setOrder($data);
        if($res['code']==0 && $paytype==0){
            $where3=[
                'id'=>$dripid,
            ];
            $data2=[
                'status'=>'1',
                'liveuid'=>$liveuid,
            ];
            
            $model->upDrip($where3,$data2);
        }
        
        
        return $res;
    }
    
    /* 抢单大厅 */
    public function getDripList($uid,$lastid){
        
        
        $Domain_Skill = new Domain_Skill();
        $where2=[
            'uid'=>$uid,
            'status'=>1,
            'switch'=>1,
        ];
        
        $auth=$Domain_Skill->getSkillAuth($where2);
        if(!$auth){
            return [];
        }
        
        $userinfo=\App\getUserInfo($uid);
        
        $where3='';
        foreach($auth as $k=>$v){
            if($where3){
                $where3.=" or (skillid={$v['skillid']} and levelid<={$v['levelid']})";
            }else{
                $where3.="(skillid={$v['skillid']} and levelid<={$v['levelid']})";
            }
            
        }
        $nowtime=time();
        
        $where="status=0 and svctm>{$nowtime} and uid!={$uid}";
        
        $sex=$userinfo['sex'];
        if($sex==1){
             $where.=" and sex != 2";
        }else{
            $where.=" and sex != 1";
        }
        if($where3){
            $where.=" and ({$where3})";
        }
        
        if($lastid>0){
            $where.=" and id < {$lastid}";
        }
        
        $model = new Model_Drip();
		$list = $model->getDripList($where);
        
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($v['uid']);
			if($userinfo['user_status']=='3'){
				unset($list[$k]);
				continue;
			}
            $v['userinfo']=$userinfo;
            
            $skill=$Domain_Skill->getSkill($v['skillid']);
            if(!$skill){
                $skill=(object)[];
            }
            $v['skill']=$skill;
            
            $coin='0';
            $coinid='0';
            foreach($auth as $k1=>$v1){
                if($v['skillid']==$v1['skillid']){
                    $coin=$v1['coin'];
                    $coinid=$v1['coinid'];
                    break;
                }
            }
            $v['coin']=$coin;
            $total=$coin*$v['nums'];
            
            $coininfo=$Domain_Skill->getCoin($coinid);
        
            $fee_base=isset($coininfo['fee']) ? $coininfo['fee'] : '0';
            
            $fee=$fee_base * $v['nums'];
            
            $profit=$total-$fee;
            
            $v['total']=$total;
            $v['fee']=$fee==0? '0':'-'.$fee;
            $v['profit']=$profit;
            
            $v['datesvctm']=\App\handelsvctm($v['svctm']);
            $v['addtime']=date('Y-m-d H:i',$v['addtime']);
            
            /* 状态 */
            $isgrap='0';
            $where=[
                'liveuid'=>$uid,
                'dripid'=>$v['id'],
            ];
            $grapinfo=$model->getGrap($where);
            if($grapinfo){
                $isgrap='1';
            }
            $v['isgrap']=$isgrap;
            
            $list[$k]=$v;
        }
        
        return $list;
    }

    /* 抢单 */
    public function grapDrip($uid,$dripid){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('已抢单'), 'info' => array());
        
        $where2=[
            'id'=>$dripid,
        ];
        
        $info=$this->getDrip($where2);
        
        if(!$info){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('订单信息不存在');
			return $rs;
        }
        
        if($info['status']!=0){
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T('订单已结束，无法抢单');
			return $rs;
        }
        
		$livelimit=\App\getBanstatus($info['uid'],"0");
        if($livelimit  && $livelimit['isbanorder']=='1'){
            $rs['code'] = 1008;
			$rs['msg'] = \PhalApi\T('对方截止到'.$livelimit['endtime'].'已被禁止接单');
			return $rs;
        }
		
        $model = new Model_Drip();
        $where=[
            'liveuid'=>$uid,
            'dripid'=>$dripid,
        ];
        $grapinfo=$model->getGrap($where);
        if($grapinfo){
            $rs['code']=1006;
			$rs['msg']=\PhalApi\T('已经抢单了~');
			return $rs;
        }
        
        
        $Domain_Skill = new Domain_Skill();
        $where2=[
            'uid'=>$uid,
            'skillid'=>$info['skillid'],
            'status'=>1,
            'switch'=>1,
        ];
        
        $auth=$Domain_Skill->getAuthInfo($where2);
        if(!$auth){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('相关技能未认证或未开启');
			return $rs;
        }
        
        if($info['sex']!=0 && $auth['sex']!=$info['sex']){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('不符合订单要求，无法抢单');
			return $rs;
        }
        
        if($info['levelid']> $auth['levelid']){
            $rs['code']=1005;
			$rs['msg']=\PhalApi\T('不符合订单要求，无法抢单');
			return $rs;
        }
        
		$res = $model->grapDrip($uid,$info['id'],$info['skillid']);
        if(!$res){
            $rs['code']=1007;
			$rs['msg']=\PhalApi\T('抢单失败，请重试');
			return $rs;
        }
        
        return $rs;
    }
    
    
    
    /* 取消原因 */
    public function getDripCancel(){
        
        $model = new Model_Drip();
		$list = $model->getDripCancel();
        
        return $list;
    }
    
    /* 取消订单 */
    public function cancelDrip($uid,$dripid,$content){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $where2=[
            'id'=>$dripid,
        ];
        
        $info=$this->getDrip($where2);
        
        if(!$info){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('订单信息不存在');
			return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('无权操作');
			return $rs;
        }
        
        if($info['status']!=0){
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T('订单已结束，无法取消');
			return $rs;
        }
        
        $model = new Model_Drip();
        
        $where=[
            'id'=>$dripid,
            'status'=>0,
        ];
        $data=[
            'status'=>-1,
            'reason'=>$content,
        ];
		$res = $model->upDrip($where,$data);
        if(!$res){
            $rs['code']=1007;
			$rs['msg']=\PhalApi\T('操作失败，请重试');
			return $rs;
        }
        
        return $rs;
    }

    /* 滴滴订单提示 */
    public function getDripTips($uid){
        
        $where2=[
            'uid'=>$uid,
            'status'=>0,
        ];
        
        $info=$this->getDrip($where2);
        
        if(!$info){
			return 0;
        }
        
        
        $model = new Model_Drip();
        
        $where="dripid={$info['id']}";
        
        $list=$model->getLiveid($where);
        if(!$list){
            return 0;
        }
        
        return 1;
    }
}
