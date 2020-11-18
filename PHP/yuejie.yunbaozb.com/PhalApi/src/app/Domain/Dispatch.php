<?php
namespace App\Domain;

use App\Model\Dispatch as Model_Dispatch;
use App\Domain\User as Domain_User;
use App\Domain\Live as Domain_Live;

class Dispatch {
    
    /* 派单 */
	public function send($uid,$data) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_User = new Domain_User();
		$ishost = $Domain_User->ishost($uid);
        if(!$ishost){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('您还不是主持人，无权操作');
            return $rs;
        }
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($uid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启，无法操作');
            return $rs;
        }
        
        $skillid=$data['skillid'];
        $levelid=$data['levelid'];
        $sex=$data['sex'];
        $age=$data['age'];
        $coin=$data['coin'];
        
        $uids_s='';
        $where='status=1 and switch=1 ';
        
        $isuser=0;
        $where2='user_type=2';
        if($sex!=0){
            if($sex==1){
                $where2.=' and sex=1';
            }else{
                $where2.=' and sex!=1';
            }
            $isuser=1;
        }
        
        if($age!=0){
            $time=\App\getAges($age);
            if(!$time){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('该条件下无用户');
                return $rs;
            }
            
            $where2.=" and birthday>={$time[0]} and birthday<{$time[1]}";
            $isuser=1;
        }
        
        if($isuser){
            $domain = new Domain_User();
            $users = $domain->getUsers($where2);
            if(!$users){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('该条件下无用户');
                return $rs;
            }
            
            $users_a=array_column($users,'id');
            $users_s=implode(',',$users_a);
            
            $uids_s=$users_s;
            
            if(!$uids_s){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('该条件下无用户');
                return $rs;
            }
            
        }
        
        if($uids_s){
            $where.=" and uid in ({$uids_s})";
        }
        
        if($skillid!=0){
            $where.=' and skillid='.$skillid;
        }
        
        if($levelid!=0){
            $where.=' and levelid>='.$levelid;
        }
        
        if($coin!=0){
            $where.=' and coin<='.$coin;
        }
        
        
        $model = new Model_Dispatch();
        $list=$model->getSkillAuth($where);
        
        if(!$list){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('该条件下无用户');
            return $rs;
        }
        
        $data['uid']=$uid;
        $data['addtime']=time();
        
        $res=$model->setDispatch($data);
        
        if(!$res){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('操作失败，请重试');
            return $rs;
        }
        
        
		return $rs;
	}
    
    /* 订单信息 */
    public function getDispatch($where){
        
        $model = new Model_Dispatch();
        $info=$model->getDispatch($where);
        
        return $info;
    }
    
    /* 更新信息 */
	public function upInfo($where,$data) {
        
        if(!$where || !$data){
            return 0;
        }
        
        $model = new Model_Dispatch();

        $rs= $model->upInfo($where,$data);
        
		return $rs;
	}
	
}
