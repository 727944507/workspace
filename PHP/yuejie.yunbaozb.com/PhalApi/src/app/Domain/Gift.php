<?php
namespace App\Domain;

use App\Model\Gift as Model_Gift;

class Gift {

    /* 礼物列表 */
	public function getList() {
        
        $key='getGiftList';
        $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Gift();
            $list=$model->getList();
            if($list){
                \App\setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            $v['gifticon']=\App\get_upload_path($v['gifticon']);
            $v['swf']=\App\get_upload_path($v['swf']);
            $list[$k]=$v;
        }
        
		return $list;
	}

    /* 赠送礼物 */
	public function sendGift($uid,$touid_a,$giftid,$nums) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('赠送成功'), 'info' => array());
        
        $list=$this->getList();
        $giftinfo=[];
        foreach($list as $k=>$v){
            if($giftid==$v['id']){
                $giftinfo=$v;
                break;
            }
        }
        
        if(!$giftinfo){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('礼物信息错误');
            return $rs;
        }
        
        $type=0;
        $action=3;
        $actionid=$giftid;
        $total=$giftinfo['needcoin'] * $nums;
        $nowtime=time();
        
        $count=count($touid_a);
        
        $total_all=$total * $count;
        
        $res=\App\upCoin($uid,$total_all);
        if(!$res){
            $rs['code']=1008;
            $rs['msg']=\PhalApi\T('余额不足');
            return $rs;
        }
        $gifts=[];
        foreach($touid_a as $k=>$v){
            
            $touid=$v;
            /* 用户消费记录 */
            $record=[
                'type'=>$type,
                'action'=>$action,
                'uid'=>$uid,
                'touid'=>$touid,
                'actionid'=>$actionid,
                'nums'=>$nums,
                'total'=>$total,
                'addtime'=>$nowtime,
            ];
            \App\addCoinRecord($record);
        
            /* 对象用户加票 */
            $ifok=\App\addGiftVotes($touid,$total,$total);
            if($ifok){
                $record=[
                    'type'=>'1',
                    'action'=>$action,
                    'uid'=>$touid,
                    'fromid'=>$uid,
                    'actionid'=>$actionid,
                    'nums'=>$nums,
                    'total'=>$total,
                    'addtime'=>$nowtime,
                ];
                \App\addVotesRecord($record);
            }
			/* 分销:消费者上级收取提成 */
			\App\setAgentProfit($uid,$total,'1');
            
            $touserinfo=\App\getUserInfo($v);
            
            $data=[
                'giftid'=>$giftinfo['id'],
                'type'=>$giftinfo['type'],
                'giftname'=>$giftinfo['giftname'],
                'gifticon'=>$giftinfo['gifticon'],
                'swftime'=>$giftinfo['swftime'],
                'swftype'=>$giftinfo['swftype'],
                'swf'=>$giftinfo['swf'],
                'nums'=>$nums,
                'total'=>$total,
                
                'touid'=>$touid,
                'toname'=>$touserinfo['user_nickname'],
            ];
            
            $gifts[]=$data;
            
        }
        
        
        $gifttoken=md5( md5( json_encode($gifts).$nowtime.rand(100,999) ) );
        
        \App\setcaches($gifttoken,$gifts);
        
        $usercoin=\App\getUserCoin($uid);
        
        
        $info=[
            'gifttoken'=>$gifttoken,
            'coin'=>$usercoin['coin'],
        ];
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 个人主页赠送礼物 */
	public function sendGiftHome($uid,$touid,$giftid,$nums) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('赠送成功'), 'info' => array());
        
        $list=$this->getList();
        $giftinfo=[];
        foreach($list as $k=>$v){
            if($giftid==$v['id']){
                $giftinfo=$v;
                break;
            }
        }
        
        if(!$giftinfo){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('礼物信息错误');
            return $rs;
        }
        
        $type=0;
        $action=3;
        $actionid=$giftid;
        $total=$giftinfo['needcoin'] * $nums;
        $nowtime=time();
        
        
        $res=\App\upCoin($uid,$total);
        if(!$res){
            $rs['code']=1008;
            $rs['msg']=\PhalApi\T('余额不足');
            return $rs;
        }
        

        /* 用户消费记录 */
        $record=[
            'type'=>$type,
            'action'=>$action,
            'uid'=>$uid,
            'touid'=>$touid,
            'actionid'=>$actionid,
            'nums'=>$nums,
            'total'=>$total,
            'addtime'=>$nowtime,
        ];
        \App\addCoinRecord($record);
    
        /* 对象用户加票 */
        $ifok=\App\addGiftVotes($touid,$total,$total);
        if($ifok){
            $record=[
                'type'=>'1',
                'action'=>$action,
                'uid'=>$touid,
                'fromid'=>$uid,
                'actionid'=>$actionid,
                'nums'=>$nums,
                'total'=>$total,
                'addtime'=>$nowtime,
            ];
            \App\addVotesRecord($record);
        }
        
        $usercoin=\App\getUserCoin($uid);
        
        
        $info=[
            'coin'=>$usercoin['coin'],
        ];
        
        $rs['info'][0]=$info;
        
		return $rs;
	}
	
}
