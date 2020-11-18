<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Live as Domain_Live;
use App\Domain\Skill as Domain_Skill;
use App\Domain\Dispatch as Domain_Dispatch;
use App\Domain\User as Domain_User;
use App\Domain\Liveapply as Domain_Liveapply;
use App\Domain\Home as Domain_Home;
use App\Domain\Agent as Domain_Agent;

/**
 * 聊天室
 */
 
class Live extends Api {

	public function getRules() {
		return array(
            'getLists' => array(
				'type' => array('name' => 'type', 'type' => 'int', 'default'=>0, 'desc' => '类型，0热门1派单2交友3闲谈4点歌'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getListsAtten' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getInfo' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
			),
            
			'setInfo' => array(
				'des' => array('name' => 'des', 'type' => 'string', 'desc' => '描述'),
			),
            
            'start' => array(
				'title' => array('name' => 'title', 'type' => 'string', 'desc' => '标题'),
				'des' => array('name' => 'des', 'type' => 'string', 'desc' => '描述'),
				'thumb' => array('name' => 'thumb', 'type' => 'string', 'desc' => '封面'),
                'type' => array('name' => 'type', 'type' => 'int', 'desc' => '类型，1派单2交友3闲谈4点歌'),
				'bgid' => array('name' => 'bgid', 'type' => 'int', 'desc' => '背景ID'),
				'deviceinfo' => array('name' => 'deviceinfo', 'type' => 'string', 'desc' => '设备信息'),
			),
            
            'stop' => array(
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'stopInfo' => array(
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'changeLive' => array(
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '状态，0关播1直播'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'enter' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
			),
            
            'upnums' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '方式0减1加'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
            
            'getUserNums' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
            ),
            
            'getUserList' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
            ),
            
            'getPop' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
            ),
		);
	}
    
	/**
	 * 聊天室列表轮播
	 * @desc 用于获取聊天室列表轮播
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
     * @return string info[].image 图片
     * @return string info[].url 链接
	 * @return string msg 提示信息
	 */
	public function getSlides() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$id=2;
        $Domain_Home = new Domain_Home();
		$list = $Domain_Home->getSilide($id);

        $rs['info']=$list;
		return $rs;
	}

	/**
	 * 聊天室列表 
	 * @desc 用于获取聊天室列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].uid 主播ID
	 * @return string info[].title 标题
	 * @return string info[].thumb 封面
	 * @return string info[].user_nickname 昵称
	 * @return string info[].avatar 头像
	 * @return string info[].type 类型
	 * @return string info[].type_v 对应类型名
	 * @return string info[].bg 背景图
	 * @return string msg 提示信息
	 */
	public function getLists() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $type=\App\checkNull($this->type);
		$uid=\App\checkNull($this->uid);
        $p=\App\checkNull($this->p);
        
        $domain = new Domain_Live();
		$list = $domain->getLists($type,$p,$uid);

        $rs['info']=$list;
		return $rs;
	}

	/**
	 * 关注的聊天室列表 
	 * @desc 用于获取聊天室列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].uid 主播ID
	 * @return string info[].title 标题
	 * @return string info[].thumb 封面
	 * @return string info[].user_nickname 昵称
	 * @return string info[].avatar 头像
	 * @return string info[].type 类型
	 * @return string info[].type_v 对应类型名
	 * @return string msg 提示信息
	 */
	public function getListsAtten() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $p=\App\checkNull($this->p);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Live();
		$list = $domain->getListsAtten($uid,$p);

        $rs['info']=$list;
		return $rs;
	}
    
    
	/**
	 * 聊天室基本信息 
	 * @desc 用于用户获取聊天室信息
	 * @return int code 操作码，0表示成功
	 * @return array  info
	 * @return string info[0].type 类型
	 * @return string info[0].title 标题
	 * @return string info[0].thumb 封面
	 * @return string info[0].thumb_p 展示用封面
	 * @return string info[0].des 描述
	 * @return string info[0].bgid 背景ID
	 * @return string msg 提示信息
	 */
	public function getInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $liveuid=\App\checkNull($this->liveuid);
        if($liveuid<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $domain = new Domain_Live();
		$info = $domain->getInfo($liveuid);

        $rs['info'][0]=$info;
		return $rs;
	}

	/**
	 * 聊天室设置信息 
	 * @desc 用于主播开播前获取聊天室信息
	 * @return int code 操作码，0表示成功
	 * @return array  info
	 * @return string info[0].type 类型
	 * @return string info[0].title 标题
	 * @return string info[0].thumb 封面
	 * @return string info[0].thumb_p 展示用封面
	 * @return string info[0].des 描述
	 * @return string info[0].bgid 背景ID
	 * @return array  info[0].bglist 背景列表
	 * @return string info[0].bglist[].id 背景ID
	 * @return string info[0].bglist[].thumb 背景图
	 * @return array  info[0].typelist 类型列表
	 * @return string info[0].typelist[].id 类型ID
	 * @return string info[0].typelist[].name 类型名
	 * @return string msg 提示信息
	 */
	public function getSetInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $Domain_User = new Domain_User();
        $ishost = $Domain_User->ishost($uid);
        
        $Domain_Liveapply = new Domain_Liveapply();
        $isapply = $Domain_Liveapply->getInfo($uid);
        
        if(!$ishost && (!$isapply || $isapply['status']!=1)){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('您还不是主持人');
            return $rs;
        }
        
        $domain = new Domain_Live();
		$info = $domain->getInfo($uid);
        if(!$info){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $typelist=[];
        $type_list=\App\getLiveType();
        if(!$ishost){
            unset($type_list[1]);
        }
        
        foreach($type_list as $k=>$v){
            $data=[
                'id'=>''.$k,
                'name'=>$v,
            ];
            $typelist[]=$data;
        }
        
        
        $bglist=$domain->getBg();
        
        $info['typelist']=$typelist;
        $info['bglist']=$bglist;
        $rs['info'][0]=$info;
		return $rs;
	}
    
	/**
	 * 设置聊天室基本信息 
	 * @desc 用于设置聊天室信息
	 * @return int code 操作码，0表示成功
	 * @return string msg 提示信息
	 */
	public function setInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $des=\App\checkNull($this->des);

        if($uid<1 || $token=='' || $des==''){
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
        $data=[
            'des'=>$des,
        ];
        
        
        $domain = new Domain_Live();
		$res = $domain->setInfo($uid,$data);
        
		return $res;
	}
    
	/**
	 * 开启聊天室 
	 * @desc 用于开启聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].stream 流名
	 * @return string info[0].showid 直播标识
	 * @return string info[0].chatserver socket地址
	 * @return string info[0].votestotal 总映票
	 * @return string info[0].agentcode 邀请码
	 * @return string msg 提示信息
	 */
	public function start() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $title=\App\checkNull($this->title);
        $des=\App\checkNull($this->des);
        $thumb=\App\checkNull($this->thumb);
        $type=\App\checkNull($this->type);
        $bgid=\App\checkNull($this->bgid);
        $deviceinfo=\App\checkNull($this->deviceinfo);
        
        if($uid<1 || $token=='' || $title=='' || $des=='' || $thumb=='' || $type<1 || $type > 4 || $bgid<1){
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
        
        $data=[
            'title'=>$title,
            'des'=>$des,
            'thumb'=>$thumb,
            'type'=>$type,
            'bgid'=>$bgid,
        ];
        
        $domain = new Domain_Live();
		$res = $domain->start($uid,$data);
        
        
        if($res['code']==0){
            $userinfo=\App\getUserInfo($uid);

            $userinfo['usertype']='50';
            $userinfo['sign']='0'.'.0'.'1';
            $userinfo['livetype']=$type;
        
            \App\setcaches($token,$userinfo);
            
            /* 重置坐位信息 */
            $sitting=[
                '1'=>0,
                '2'=>0,
                '3'=>0,
                '4'=>0,
                '5'=>0,
                '6'=>0,
                '7'=>0,
                '8'=>0,
            ];
            $key='sitting_'.$uid;
            \App\delcache($key);
            \App\hMSet($key,$sitting);
            
            /* 清空心动选择 */
            $key6='heart_'.$uid;
            \App\delcache($key6);
			
			/* 清除交友环节 */
			$key_step='jy_step';
			\App\hDel($key_step,$uid);
			
			/* 清除交友环节 */
			$jy_step_time='jy_step_time';
			\App\hDel($jy_step_time,$uid);
			
			/* 清空老板位申请列表 */
            $key2='boss_'.$uid;
            \App\delcache($key2);
            
            /* 清空交友-男申请列表 */
            $key4='jy_'.$uid.'_1';
            \App\delcache($key4);
            
            /* 清空交友-女申请列表 */
            $key5='jy_'.$uid.'_2';
            \App\delcache($key5);
            
            /* 清除累计人数列表 */
            $key3='totalnums_'.$uid;
            \App\delcache($key3);
            
            /* 更新之前的派单状态 */
            $where=[
                'uid'=>$uid,
                'status'=>0,
            ];
            $data=['status'=>1];
            $Domain_Dispatch = new Domain_Dispatch();
            $dispatchinfo = $Domain_Dispatch->upInfo($where,$data);
        }
        
		return $res;
	}

	/**
	 * 关闭聊天室 
	 * @desc 用于关闭聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].title 标题
	 * @return string info[0].length 时长
	 * @return string info[0].nums 累计人数
	 * @return string msg 提示信息
	 */
	public function stop() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $stream=\App\checkNull($this->stream);
        
        if($uid<1 || $token=='' || $stream=='' ){
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
        
        $domain = new Domain_Live();
		$res = $domain->stop($uid,$stream);
        
		return $res;
	}

	/**
	 * 聊天室结束信息 
	 * @desc 用于获取聊天室结束信息
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].title 标题
	 * @return string info[0].length 时长
	 * @return string info[0].nums 累计人数
	 * @return string msg 提示信息
	 */
	public function stopInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $stream=\App\checkNull($this->stream);
        
        if($stream=='' ){
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
        
        $domain = new Domain_Live();
		$res = $domain->stopInfo($stream);
        
		return $res;
	}
    
	/**
	 * 修改直播状态 
	 * @desc 用于开启聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function changeLive() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $type=\App\checkNull($this->type);
        $stream=\App\checkNull($this->stream);
        
        if($uid<1 || $token=='' || $stream==''){
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
        
        $domain = new Domain_Live();
		$res = $domain->changeLive($uid,$stream,$type);
        
		return $res;
	}

	/**
	 * 进入聊天室
	 * @desc 用于进入聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].votestotal 主播总映票
	 * @return string info[0].chatserver socket地址
	 * @return string info[0].isattent 是否关注，0否1是
	 * @return string info[0].isdispatch 是否派单，0否1是
	 * @return string info[0].skillid 派单技能ID
	 * @return string info[0].jy_step 交友环节，1准备2选择3公布
	 * @return string info[0].jy_step_time 选择剩余时间(秒)
	 * @return string info[0].agentcode 邀请码
	 * @return array info[0].sits 坐位上用户列表
	 * @return string msg 提示信息
	 */
	public function enter() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if($uid<1 || $token=='' || $liveuid<1|| $stream==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
		if($liveuid==$uid){
			$rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('不能进入自己的聊天室');
			return $rs;
		}
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Live();
        $liveinfo=$domain->getLiveInfo($liveuid);
        if(!$liveinfo || $liveinfo['islive']!=1 || $liveinfo['stream']!=$stream){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('聊天室已关闭');
			return $rs;
        }
        
        $type=$liveinfo['type'];
        
        $userinfo=\App\getUserInfo($uid);
        
        
        $userinfo['usertype']='30';
        $userinfo['sign']='0'.'.0'.'1';
        $userinfo['livetype']=$type;
        
        \app\setcaches($token,$userinfo);
        
        $configpri=\App\getConfigPri();
        
        $livevotes=\App\getUserGiftVotestotal($liveuid);
        
        
        $info=[
            'votestotal'=>$livevotes['votestotal'],
			'bg'=>$liveinfo['bg'],
            'chatserver'=>$configpri['chatserver'],
        ];
        
        $info['isattent']=\App\isAttent($uid,$liveuid);
        
        /* 坐位信息 sitting */
        $list=\App\hGetAll('sitting_'.$liveuid);
        
        $sits=[];
        foreach($list as $k=>$v){
            if($v > 0){
                $vinfo=\App\getUserInfo($v);
                $sits[]=$vinfo;
            }else{
                $sits[]=(object)[];
            }
        }
        
        $info['sits']=$sits;
        
        /* 是否派单 */
        $isdispatch='0';
        $skillid='0';
        if($type==1){
            $where=[
                'uid'=>$liveuid,
                'stream'=>$stream,
                'status'=>0,
            ];
            $Domain_Dispatch = new Domain_Dispatch();
            $dispatchinfo = $Domain_Dispatch->getDispatch($where);
            if($dispatchinfo){
                $isdispatch='1';
                $skillid=$dispatchinfo['skillid'];
            }
            
            
        }
		
		$info['isdispatch']=(string)$isdispatch;
		$info['skillid']=(string)$skillid;
		
		/* 交友房间-当前环节 */
		$jy_step='1';
		$jy_step_time='0';
		if($type==2){
			$key_step='jy_step';
			$jy_step_r=\App\hGet($key_step,$liveuid);
			if($jy_step_r){
				$jy_step=$jy_step_r;
			}
			if($jy_step==2){
				/* 选择阶段 */
				$key_time='jy_step_time';
				$time=\App\hGet($key_time,$liveuid);
				if($time){
					$cha=5*60-(time()-$time);
					if($cha>0){
						$jy_step_time=$cha;
					}
				}
			}
		}
		
		$info['jy_step']=(string)$jy_step;
		$info['jy_step_time']=(string)$jy_step_time;
        
        /* 累计人数处理 */
        $key2='totalnums_'.$liveuid;
        $isexist=\App\hGet($key2,$uid);
        if(!$isexist){
            \App\hSet($key2,$uid,1);
            
            $where2=['uid'=>$liveuid,'islive'=>1];
            $field='totalnums';
            $domain->upNums($where2,$field);
        }
        
		
		/* 邀请码 */
		$Domain_Agent = new Domain_Agent();
        $agentcode= $Domain_Agent->getMyCode($uid);
		$info['agentcode']=$agentcode;
		
        $rs['info'][0]=$info;
        
		return $rs;
	}

	/**
	 * 更新人数
	 * @desc 用于更新聊天室人数
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function upnums() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        $type=\App\checkNull($this->type);
        $sign=\App\checkNull($this->sign);
        
        // file_put_contents('./upnums.txt',date('Y-m-d H:i:s').' 提交参数信息 REQUEST:'.json_encode($_REQUEST)."\r\n",FILE_APPEND);
        
        if($uid<1 || $token=='' || $liveuid<1 || $stream=='' || $sign==''){
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
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'liveuid'=>$liveuid,
            'stream'=>$stream,
            'type'=>$type,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $domain = new Domain_Live();
        $where2=['uid'=>$liveuid,'stream'=>$stream,'islive'=>1];
        
        if($type==0){
            $where2['nums>=?']=1;
            $type=1;
        }else{
            $type=0;
        }
        
        $field='nums';
        $nums=1;
        $domain->upNums($where2,$field,$nums,$type);
        // file_put_contents('./upnums.txt',date('Y-m-d H:i:s').' 提交参数信息 where2:'.json_encode($where2)."\r\n",FILE_APPEND);

        
		return $rs;
	}
    
	/**
	 * 用户列表
	 * @desc 用于获取用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[] 用户信息
	 * @return string info[].sittype 状态，0普通1在麦上2主持
	 * @return string msg 提示信息
	 */
	public function getUserList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        $p=\App\checkNull($this->p);
        
        if( $liveuid<1 || $stream==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        if($p<1){
            $p=1;
        }
        
        $list=[];
        
        $sitlist=[];
        if($p==1){
            $liveinfo=\App\getUserInfo($liveuid);
            $liveinfo['sittype']='2';
            
            $list[]=$liveinfo;
            
            /* 坐位信息 sitting */
            $sitlist=\App\hGetAll('sitting_'.$liveuid);
            
            foreach($sitlist as $k=>$v){
                if($v > 0){ 
                    $vinfo=\App\getUserInfo($v);
                    $vinfo['sittype']='1';
                    $list[]=$vinfo;
                }
            }
        }
        
        
		$pnum=20;
		$start=($p-1)*$pnum;
        $key="getUserLists_".$stream.'_'.$p;
		$userlist=\App\getcaches($key);
		if(!$userlist){ 
            $userlist=array();

            $uidlist=\App\zRevRange('user_'.$stream,$start,$pnum,true);
            foreach($uidlist as $k=>$v){
                /* 去除在麦上的 */
                $issit='0';
                foreach($sitlist as $k1=>$v1){
                    if($v1==$k){
                        $issit='1';
                        break;
                    }
                }
                if(!$issit){
                    $userinfo=\App\getUserInfo($k);
                    $userinfo['sittype']='0';
                
                    $userlist[]=$userinfo; 
                }
            }
            
            if($userlist){
                \App\setcaches($key,$userlist,5);
            }
		}
        
        if($userlist){
            $list=array_merge($list,$userlist);
        }
        
        
        $rs['info']=$list;
        
		return $rs;
	}

	/**
	 * 直播间人数
	 * @desc 用于获取直播间人数
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].nums 数量
	 * @return string msg 提示信息
	 */
	public function getUserNums() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $liveuid=\App\checkNull($this->liveuid);
        $stream=\App\checkNull($this->stream);
        
        if( $liveuid<1 || $stream==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $nums=\App\zCard('user_'.$stream);
        if(!$nums){
            $nums=0;
        }

        $info['nums']=(string)$nums;
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

	/**
	 * 用户弹窗
	 * @desc 用于获取用户弹窗内容
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0] 用户信息
	 * @return string info[0].isattent 是否关注，0否1是
	 * @return string info[0].fans 粉丝数
	 * @return object info[0].skillinfo 技能信息，无是为空对象
	 * @return string info[0].skillnames 认证技能名称，无为空
	 * @return string info[0].skill_firstid 第一个技能ID，无为0
	 * @return string msg 提示信息
	 */
	public function getPop() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $liveuid=\App\checkNull($this->liveuid);
        $touid=\App\checkNull($this->touid);
        $skillid=\App\checkNull($this->skillid);
        
        if( $liveuid<1 || $touid<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        

        $userinfo=\App\getUserInfo($touid);
        
        $userinfo['isattent']=\App\isAttent($uid,$touid);
        $userinfo['fans']=\App\getFansNum($touid);

        $skillinfo=(object)[];
		$skillnames='';
		$skill_firstid='0';
        /* 技能信息 */
        if($skillid>0){
            $where=[
                'uid'=>$touid,
                'skillid'=>$skillid,
                'status'=>1,
                'switch'=>1,
            ];
        
            $Domain_Skill = new Domain_Skill();
            $list = $Domain_Skill->getSkillAuth($where);
            if($list){
                $skillinfo=$list[0];
            }
        }else{
			$where=[
                'uid'=>$touid,
                'status'=>1,
                'switch'=>1,
            ];
			
			$order='orders desc';
        
            $Domain_Skill = new Domain_Skill();
            $list = $Domain_Skill->getSkillAuth($where);
            foreach($list as $k=>$v){
				if($k==0){
					$skill_firstid=$v['skillid'];
					$skillnames.=$v['skillname'];
				}else{
					$skillnames.='/'.$v['skillname'];
				}
				
				
			}
		}
        
        
        
        $userinfo['skillinfo']=$skillinfo;
        $userinfo['skillnames']=$skillnames;
        $userinfo['skill_firstid']=(string)$skill_firstid;
        
        
        $rs['info'][0]=$userinfo;
        
		return $rs;
	}

}
