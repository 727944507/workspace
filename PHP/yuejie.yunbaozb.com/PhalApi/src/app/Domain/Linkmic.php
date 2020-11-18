<?php
namespace App\Domain;

use App\Model\Linkmic as Model_Linkmic;
use App\Domain\Live as Domain_Live;
use App\Domain\Dispatch as Domain_Dispatch;
use App\Domain\Skill as Domain_Skill;

class Linkmic {
    
    /* 申请上老板位 */
	public function apply($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $key2='sitting_'.$liveuid;
        
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if( $v==$uid){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('您已经在坐席上了');
                return $rs;
                break;
            }
        }
        
        $key='boss_'.$liveuid;
        
        $score=\App\zScore($key,$uid);
        if($score){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('您已经在申请了');
            return $rs;
        }
        
        $nowtime=\App\getMillisecond();
        
        \App\zAdd($key,$nowtime,$uid);
        
        
		return $rs;
	}

    /* 取消上老板位 */
	public function cancel($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $key='boss_'.$liveuid;
        
        \App\zRem($key,$uid);
        
        
		return $rs;
	}
    
    
    /* 老板位上麦 */
	public function setMic($uid,$touid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($uid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $key1='boss_'.$uid;
        
        $score=\App\zScore($key1,$touid);
        if(!$score){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('对方未申请上麦');
            return $rs;
        }
        
        $key2='sitting_'.$uid;
        /* 坐席上是否有人 */
        $sit_user=\App\hGet($key2,8);
        if($sit_user && $sit_user>0){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('坐席上已有人');
            return $rs;
        }
        
        \App\zRem($key1,$touid);
        
        \App\hSet($key2,8,$touid);
        
		return $rs;
	}


    /* 派单-上麦抢单 */
	public function upMic($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        /* 条件匹配 */
        $where=[
            'uid'=>$liveuid,
            'stream'=>$stream,
            'status'=>0,
        ];
        $Domain_Dispatch = new Domain_Dispatch();
		$dispatchinfo = $Domain_Dispatch->getDispatch($where);
        if(!$dispatchinfo){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('暂未派单，无法上麦');
            return $rs;
        }
        
        $skillid=$dispatchinfo['skillid'];
        $levelid=$dispatchinfo['levelid'];
        $sex=$dispatchinfo['sex'];
        $age=$dispatchinfo['age'];
        $coin=$dispatchinfo['coin'];
        
        $where2=[
            'uid'=>$uid,
            'skillid'=>$skillid,
            'status'=>1,
            'switch'=>1,
        ];
        if($coin!=0){
            $where2['coin<=?']=$coin;
        }
        
        if($levelid!=0){
            $where2['levelid>=?']=$levelid;
        }
        if($sex!=0){
            if($sex=1){
                $where2['sex']=1;
            }else{
                $where2['sex!=?']=1;
            }
        }
        
        $Domain_Skill = new Domain_Skill();
		$skillinfo = $Domain_Skill->getAuthInfo($where2);
        
        if(!$skillinfo){
            $rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('条件不符合，不能上麦');
            return $rs;
        }
        
        if($age!=0){

            $time=\App\getAges($age);
            if(!$time){
                $rs['code'] = 1005;
                $rs['msg'] = \PhalApi\T('条件不符合，不能上麦');
                return $rs;
            }
            
            $userinfo=\App\getUserInfo($uid);
            
            if(!$userinfo || $userinfo['birthday']<$time[0] || $userinfo['birthday']>=$time[1]){
                $rs['code'] = 1006;
                $rs['msg'] = \PhalApi\T('条件不符合，不能上麦');
                return $rs;
            }
            
        }
        
        
        /* 坐位信息 sitting */
        $key2='sitting_'.$liveuid;
        $list=\App\hGetAll($key2);
        
        foreach($list as $k=>$v){
            if($v > 0 && $v==$uid){
                $rs['code'] = 1007;
                $rs['msg'] = \PhalApi\T('你已经在坐席上了');
                return $rs;
                break;
            }
        }
        $sitid=0;
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if($k<8 && $v == 0 ){
                $sitid=$k;
                \App\hSet($key2,$k,$uid);
                break;
            }
        }
        
        if($sitid==0){
            $rs['code'] = 1008;
            $rs['msg'] = \PhalApi\T('坐席已满');
            return $rs;
        }
        
        $info=[
            'sitid'=>(string)$sitid
        ];
        
        $rs['info'][0]=$info;
        
		return $rs;
	}
    
    /* 交友-申请上麦  */
	public function jy_apply($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $key2='sitting_'.$liveuid;
        
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if( $v==$uid){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('您已经在坐席上了');
                return $rs;
                break;
            }
        }
        
        $userinfo=\App\getUserInfo($uid);
        
        $sex=$userinfo['sex'];
        
        if($sex!=1){
            $sex=2;
        }
        
        $key='jy_'.$liveuid.'_'.$sex;
        
        $score=\App\zScore($key,$uid);
        if($score){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('您已经在申请了');
            return $rs;
        }
        
        $nowtime=\App\getMillisecond();
        
        \App\zAdd($key,$nowtime,$uid);
        
        
		return $rs;
	}

    /* 交友-取消申请 */
	public function jy_cancel($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $userinfo=\App\getUserInfo($uid);
        
        $sex=$userinfo['sex'];
        
        if($sex!=1){
            $sex=2;
        }
        
        $key='jy_'.$liveuid.'_'.$sex;
        
        \App\zRem($key,$uid);
        
        
		return $rs;
	}
    
    
    /* 交友-上麦 */
	public function jy_setMic($uid,$touid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
		
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($uid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
		$key_step='jy_step';
		$jy_step_r=\App\hGet($key_step,$uid);
		if($jy_step_r && $jy_step_r==2){
			$rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('心动选择环节不能上麦');
            return $rs;
		}
			
        $userinfo=\App\getUserInfo($touid);
        
        $sex=$userinfo['sex'];
        
        if($sex!=1){
            $sex=2;
        }
        
        $key1='jy_'.$uid.'_'.$sex;
        
        $score=\App\zScore($key1,$touid);
        if(!$score){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('对方未申请上麦');
            return $rs;
        }
        
        $key2='sitting_'.$uid;
        
        $sitid=0;
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if($sex==1){
                if(in_array($k,[1,2,5,6]) && $v==0){
                    $sitid=$k;
                    \App\hSet($key2,$k,$touid);
                    break;
                }
            }else{
                if(in_array($k,[3,4,7,8]) && $v==0){
                    $sitid=$k;
                    \App\hSet($key2,$k,$touid);
                    break;
                }
            }
        }
        
        if($sitid ==0){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('坐席上已满员');
            return $rs;
        }
        
        \App\zRem($key1,$touid);
        
        $info=[
            'sitid'=>(string)$sitid
        ];
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 交友-心动选择 */
	public function setHeart($uid,$liveuid,$tositid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
		
		if($liveinfo['type']!=2){
			$rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
		}
        
        $key='sitting_'.$liveuid;
        
		$mysit=0;
		$touid=0;
		
        $list=\App\hGetAll($key);
        foreach($list as $k=>$v){
            if($v==$uid){
				$mysit=$k;
			}
			if($k==$tositid){
				$touid=$v;
			}
        }
		
		if($mysit==0){
			$rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('无权操作');
            return $rs;
		}
		
		if( !(in_array($mysit,[1,2,5,6]) && in_array($tositid,[3,4,7,8])) && !(in_array($tositid,[1,2,5,6]) && in_array($mysit,[3,4,7,8])) ){
			$rs['code'] = 1006;
            $rs['msg'] = \PhalApi\T('选择错误');
            return $rs;
		}
		
		if($touid==0){
			$rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('不能选择空位');
            return $rs;
		}
        
		$key2='heart_'.$liveuid;
		
        \App\hSet($key2,$mysit.'_'.$uid,$tositid.'_'.$touid);
        
		return $rs;
	}

    /* 交友-心动选择结果 */
	public function getHeart($uid) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($uid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
		
		if($liveinfo['type']!=2){
			$rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
		}
        
		$list_heart=[];
		$list_ok=[];
        $key='heart_'.$uid;
		
        $list=\App\hGetAll($key);
		
        foreach($list as $k=>$v){
			$h_user=explode('_',$k);
			$h_touser=explode('_',$v);
			
            $userinfo=\App\getUserInfo($h_user[1]);
            $touserinfo=\App\getUserInfo($h_touser[1]);
			
			$data=[
				'u_id'=>$userinfo['id'],
				'u_user_nickname'=>$userinfo['user_nickname'],
				'u_avatar'=>$userinfo['avatar'],
				'u_avatar_thumb'=>$userinfo['avatar_thumb'],
				'u_sex'=>$userinfo['sex'],
				'to_id'=>$touserinfo['id'],
				'to_user_nickname'=>$touserinfo['user_nickname'],
				'to_avatar'=>$touserinfo['avatar'],
				'to_avatar_thumb'=>$touserinfo['avatar_thumb'],
				'to_sex'=>$touserinfo['sex'],
			];
			
			$list_heart[]=$data;
			
			if(isset($list[$v]) && $list[$v]==$k){
				/* 互选 */
				if(!isset($list_ok[$v])){
					$list_ok[$k]=$v;
				}
				
			}
        }
		$list_s=[];
		
		foreach($list_ok as $k=>$v){
			$h_user=explode('_',$k);
			$h_touser=explode('_',$v);
			
            $userinfo=\App\getUserInfo($h_user[1]);
            $touserinfo=\App\getUserInfo($h_touser[1]);
			
			$nan=$userinfo;
			$nv=$touserinfo;
			if($userinfo['sex']!=1){
				$nan=$touserinfo;
				$nv=$userinfo;
			}
			
			$data=[
				'man_id'=>$nan['id'],
				'man_user_nickname'=>$nan['user_nickname'],
				'man_avatar'=>$nan['avatar'],
				'man_avatar_thumb'=>$nan['avatar_thumb'],
				'man_sex'=>$nan['sex'],
				'woman_id'=>$nv['id'],
				'woman_user_nickname'=>$nv['user_nickname'],
				'woman_avatar'=>$nv['avatar'],
				'woman_avatar_thumb'=>$nv['avatar_thumb'],
				'woman_sex'=>$nv['sex'],
			];
			
			$list_s[]=$data;
		}
		
		$info=[
			'heart'=>$list_heart,
			'hand'=>$list_s,
		];
		
		$rs['info'][0]=$info;
        
		return $rs;
	}

    /* 闲谈-申请上麦  */
	public function chat_apply($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $key2='sitting_'.$liveuid;
        
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if( $v==$uid){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('您已经在坐席上了');
                return $rs;
                break;
            }
        }
        
        $key='chat_'.$liveuid;
        
        $score=\App\zScore($key,$uid);
        if($score){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('您已经在申请了');
            return $rs;
        }
        
        
        $nowtime=\App\getMillisecond();
        
        \App\zAdd($key,$nowtime,$uid);
        
        
		return $rs;
	}

    /* 闲谈-取消申请 */
	public function chat_cancel($uid,$liveuid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        
        $key='chat_'.$liveuid;
        
        \App\zRem($key,$uid);
        
        
		return $rs;
	}
    
    
    /* 闲谈-上麦 */
	public function chat_setMic($uid,$touid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($uid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        $key1='chat_'.$uid;
        
        $score=\App\zScore($key1,$touid);
        if(!$score){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('对方未申请上麦');
            return $rs;
        }
        
        $key2='sitting_'.$uid;
        
        $sitid=0;
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if( $v==0){
                $sitid=$k;
                \App\hSet($key2,$k,$touid);
                break;
            }
        }
        
        if($sitid ==0){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('坐席上已满员');
            return $rs;
        }
        
        \App\zRem($key1,$touid);
        
        $info=[
            'sitid'=>(string)$sitid
        ];
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 点歌-上麦 */
	public function song_setMic($uid,$touid,$stream,$sitid) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_Live = new Domain_Live();
		$liveinfo = $Domain_Live->getLiveInfo($uid);
        if(!$liveinfo || $liveinfo['islive']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('聊天室未开启');
            return $rs;
        }
        
        
        $key2='sitting_'.$uid;
        
        $list=\App\hGetAll($key2);
        foreach($list as $k=>$v){
            if( $v==$touid){
                $rs['code'] = 1003;
                $rs['msg'] = \PhalApi\T('对方已经在席位上了');
                return $rs;
                break;
            }
        }
        
        $isexist=\App\hGet($key2,$sitid);
        if($isexist>0){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('该席位上已经有人了');
            return $rs;
        }
        
        \App\hSet($key2,$sitid,$touid);
        
		return $rs;
	}
}
