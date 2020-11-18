<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Linkmic as Domain_Linkmic;

/**
 * 连麦
 */
 
class Linkmic extends Api {

	public function getRules() {
		return array(
            'getList' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'apply' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'cancel' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'setMic' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            
            'upMic' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            
            'getJyList' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
				'sex' => array('name' => 'sex', 'type' => 'int', 'desc' => '性别，1男2女'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'jy_apply' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'jy_cancel' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'jy_setMic' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'getChatList' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'chat_apply' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'chat_cancel' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'chat_setMic' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'song_setMic' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
				'sitid' => array('name' => 'sitid', 'type' => 'int', 'desc' => '坐位号'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
			
			'setHeart' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'tositid' => array('name' => 'tositid', 'type' => 'int', 'desc' => '对方座位号'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
		);
	}
    
	/**
	 * 老板位-申请列表
	 * @desc 用于获取老板位申请列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return array info[0].list 列表
	 * @return object info[0].list[] 用户信息
	 * @return string info[0].list[].isme 是否自己，0否1是
	 * @return string info[0].nums 申请总人数
	 * @return string info[0].rank 我的顺位
	 * @return string info[0].isapply 是否申请，0否1是
	 * @return string msg 提示信息
	 */
	public function getList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        $p=\App\checkNull($this->p);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if($p<1){
            $p=1;
        }
        
        $key='boss_'.$liveuid;
        
        $pnum=20;
		$start=($p-1)*$pnum;
        
        $userlist=[];
        
        $list=\App\zRange($key,$start,$pnum,true);
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($k);
            $isme='0';
            if($k==$uid){
                $isme='1';
            }
            $userinfo['isme']=$isme;
            
            $userlist[]=$userinfo;
        }
        
        $nums=\App\zCard($key);
        
        $isapply=0;
        $rank=0;
        if($uid!=$liveuid){
            $score=\App\zScore($key,$uid);
            if($score){
                $isapply=1;
                $rank=\App\zCount($key,0,$score);
            }
        }
        
        $info=[
            'list'=>$userlist,
            'nums'=>$nums,
            'rank'=>(string)$rank,
            'isapply'=>(string)$isapply,
        ];

        $rs['info'][0]=$info;
        
		return $rs;
	}

	/**
	 * 老板位-申请上麦
	 * @desc 用于申请上老板位
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function apply() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->apply($uid,$liveuid,$stream);
        
		return $res;
	}

	/**
	 * 老板位-取消上麦
	 * @desc 用于取消上老板位
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function cancel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->cancel($uid,$liveuid,$stream);
        
		return $res;
	}

	/**
	 * 老板位-上麦 
	 * @desc 用于主播控制用户上老板位
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function setMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $touid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->setMic($uid,$touid,$stream);
        
		return $res;
	}

	/**
	 * 派单-上麦抢单
	 * @desc 用于派单聊天室接单人上麦抢单
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[0].sitid 坐位号
	 * @return string msg 提示信息
	 */
	public function upMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->upMic($uid,$liveuid,$stream);
        
		return $res;
	}
    
    
	/**
	 * 交友-申请列表
	 * @desc 用于获取交友申请列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return array info[0].list 列表
	 * @return object info[0].list[] 用户信息
	 * @return string info[0].list[].isme 是否自己，0否1是
	 * @return string info[0].nums 申请总人数
	 * @return string info[0].rank 我的顺位
	 * @return string info[0].isapply 是否申请，0否1是
	 * @return string msg 提示信息
	 */
	public function getJyList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        $sex=\App\checkNull($this->sex);
        $p=\App\checkNull($this->p);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if($p<1){
            $p=1;
        }
        
        $key='jy_'.$liveuid.'_'.$sex;
        
        $pnum=20;
		$start=($p-1)*$pnum;
        
        $userlist=[];
        
        $list=\App\zRange($key,$start,$pnum,true);
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($k);
            $isme='0';
            if($k==$uid){
                $isme='1';
            }
            $userinfo['isme']=$isme;
            
            $userlist[]=$userinfo;
        }
        if($uid==$liveuid){
			$m_nums=\App\zCard('jy_'.$liveuid.'_1');
			$f_nums=\App\zCard('jy_'.$liveuid.'_2');
			$nums=$m_nums+$f_nums;
		}else{
			$nums=\App\zCard($key);
		}
        
        
        $isapply=0;
        $rank=0;
        if($uid!=$liveuid){
            $score=\App\zScore($key,$uid);
            if($score){
                $isapply=1;
                $rank=\App\zCount($key,0,$score);
            }
        }
        
        $info=[
            'list'=>$userlist,
            'nums'=>(string)$nums,
            'rank'=>(string)$rank,
            'isapply'=>(string)$isapply,
        ];

        $rs['info'][0]=$info;
        
		return $rs;
	}

	/**
	 * 交友-申请上麦 
	 * @desc 用于交友聊天室申请上麦
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function jy_apply() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->jy_apply($uid,$liveuid,$stream);
        
		return $res;
	}

	/**
	 * 交友-取消申请
	 * @desc 用于交友聊天室取消上麦
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function jy_cancel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->jy_cancel($uid,$liveuid,$stream);
        
		return $res;
	}

	/**
	 * 交友-上麦 
	 * @desc 用于主播控制用户上麦
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[0].sitid 坐位号
	 * @return string msg 提示信息
	 */
	public function jy_setMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $touid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->jy_setMic($uid,$touid,$stream);
        
		return $res;
	}

	/**
	 * 交友-心动选择 
	 * @desc 用于用户进行心动选择
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function setHeart() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $tositid=\App\checkNull($this->tositid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<1 || $token=='' || $liveuid<1 || $tositid<1 || $tositid>8 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->setHeart($uid,$liveuid,$tositid,$stream);
        
		return $res;
	}

	/**
	 * 交友-心动选择结果 
	 * @desc 用于获取心动选择结果
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return array info[0].heart 心动选择
	 * @return string info[0].heart[].u_id 用户ID
	 * @return string info[0].heart[].u_user_nickname 用户昵称
	 * @return string info[0].heart[].u_avatar 用户头像
	 * @return string info[0].heart[].u_avatar_thumb 用户小头像
	 * @return string info[0].heart[].u_sex 用户性别
	 * @return string info[0].heart[].to_id 对方ID
	 * @return string info[0].heart[].to_user_nickname 对方昵称
	 * @return string info[0].heart[].to_avatar 对方头像
	 * @return string info[0].heart[].to_avatar_thumb 对方小头像
	 * @return string info[0].heart[].to_sex 对方性别
	 * @return array info[0].hand 牵手成功列表
	 * @return string info[0].hand[].man_id 男生ID
	 * @return string info[0].hand[].man_user_nickname 男生昵称
	 * @return string info[0].hand[].man_avatar 男生头像
	 * @return string info[0].hand[].man_avatar_thumb 男生小头像
	 * @return string info[0].hand[].man_sex 男生性别
	 * @return string info[0].hand[].woman_id 女生ID
	 * @return string info[0].hand[].woman_user_nickname 女生昵称
	 * @return string info[0].hand[].woman_avatar 女生头像
	 * @return string info[0].hand[].woman_avatar_thumb 女生小头像
	 * @return string info[0].hand[].woman_sex 女生性别
	 * @return string msg 提示信息
	 */
	public function getHeart() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        if($uid<1 || $token==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->getHeart($uid);
        
		return $res;
	}
    
	/**
	 * 闲谈-申请列表
	 * @desc 用于获取闲谈申请列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return array info[0].list 列表
	 * @return object info[0].list[] 用户信息
	 * @return string info[0].list[].isme 是否自己，0否1是
	 * @return string info[0].nums 申请总人数
	 * @return string info[0].rank 我的顺位
	 * @return string info[0].isapply 是否申请，0否1是
	 * @return string msg 提示信息
	 */
	public function getChatList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        $p=\App\checkNull($this->p);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if($p<1){
            $p=1;
        }
        
        $key='chat_'.$liveuid;
        
        $pnum=20;
		$start=($p-1)*$pnum;
        
        $userlist=[];
        
        $list=\App\zRange($key,$start,$pnum,true);
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($k);
            $isme='0';
            if($k==$uid){
                $isme='1';
            }
            $userinfo['isme']=$isme;
            
            $userlist[]=$userinfo;
        }
        
        $nums=\App\zCard($key);
        
        $isapply=0;
        $rank=0;
        if($uid!=$liveuid){
            $score=\App\zScore($key,$uid);
            if($score){
                $isapply=1;
                $rank=\App\zCount($key,0,$score);
            }
        }
        
        $info=[
            'list'=>$userlist,
            'nums'=>(string)$nums,
            'rank'=>(string)$rank,
            'isapply'=>(string)$isapply,
        ];

        $rs['info'][0]=$info;
        
		return $rs;
	}

	/**
	 * 闲谈-申请上麦 
	 * @desc 用于闲谈申请上麦
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function chat_apply() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->chat_apply($uid,$liveuid,$stream);
        
		return $res;
	}

	/**
	 * 闲谈-取消申请
	 * @desc 用于闲谈取消上麦
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function chat_cancel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $liveuid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->chat_cancel($uid,$liveuid,$stream);
        
		return $res;
	}

	/**
	 * 闲谈-上麦 
	 * @desc 用于闲谈主播控制用户上麦
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[0].sitid 坐位号
	 * @return string msg 提示信息
	 */
	public function chat_setMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<0 || $token=='' || $touid<1 || $stream=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->chat_setMic($uid,$touid,$stream);
        
		return $res;
	}

	/**
	 * 点歌-歌手上麦 
	 * @desc 用于点歌主播控制用户上歌手位
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function song_setMic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $stream=\App\checkNull($this->stream);
        $sitid=\App\checkNull($this->sitid);
        
        if($uid<0 || $token=='' || $touid<1 || $stream=='' || $sitid<1 || $sitid>7 ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Linkmic();
		$res = $domain->song_setMic($uid,$touid,$stream,$sitid);
        
		return $res;
	}

}
