<?php
namespace App\Domain;

use App\Model\Live as Model_Live;
use App\Domain\User as Domain_User;
use App\Domain\Liveapply as Domain_Liveapply;
use App\Domain\Agent as Domain_Agent;

class Live {
    
    /* 背景列表 */
	public function getBg() {

        $key='live_bg';
		$list=\App\getcaches($key);
		if(!$list){
            $model = new Model_Live();
			$list= $model->getBg();
            if($list){
                \App\setcaches($key,$list);
            }
		}
        
        foreach($list as $k=>$v){
            $v['thumb']=\App\get_upload_path($v['thumb']);
            $list[$k]=$v;
        }

		return $list;
	}

    /* 某背景图片 */
	public function getBgByid($bgid) {
        $thumb='';
        $list=$this->getBg();
        
        foreach($list as $k=>$v){
            if($bgid==$v['id']){
                $thumb=$v['thumb'];
                break;
            }
        }

		return $thumb;
	}
    
    protected function handleRoom($v){
        unset($v['deviceinfo']);
        unset($v['starttime']);
        $v['thumb']=\App\get_upload_path($v['thumb']);
        // if($v['isvideo']!='1'){
			$v['pull']=\App\PrivateKeyA('http',$v['uid']);
		/* }else{
			$v['pull']="";
		} */
        $v['type_v']=\App\getLiveType($v['type']);
        $v['bg']=$this->getBgByid($v['bgid']);
        
        $userinfo=\App\getUserInfo($v['uid']);
        $v['user_nickname']=$userinfo['user_nickname'];
        $v['avatar']=$userinfo['avatar'];
        $v['avatar_thumb']=$userinfo['avatar_thumb'];
        
        return $v;
    }
    
    /* 列表 */
	public function getLists($type,$p,$uid) {
        
        $where=['islive'=>1];
        if($type!=0){
            $where['type']=$type;
        }
        $order='nums desc';

        $model = new Model_Live();
        $list= $model->getLists($p,$where,$order);

        foreach($list as $k=>$v){
			$isblack=\App\isBlack($v['uid'],$uid);
			$isblack1=\App\isBlack($uid,$v['uid']);
			if($isblack || $isblack1){
				unset($list[$k]);
				continue;
			}
			
			
            $v=$this->handleRoom($v);
            
            $list[$k]=$v;
        }
		$list=array_values($list);
		return $list;
	}

    /* 关注聊天列表 */
	public function getListsAtten($uid,$p) {
        
        $where1=['uid'=>$uid];
        
        $Domain_User = new Domain_User();
        $uids = $Domain_User->getAllAttention($where1);
        if(!$uids){
            return [];
        }
        
        $uids_a=array_column($uids,'touid');
        $uids_s=implode(',',$uids_a);
        
        $where=[
            'islive'=>1,
            'uid in (?)'=>$uids_s,
        ];
        $order='nums desc';

        $model = new Model_Live();
        $list= $model->getLists($p,$where,$order);

        foreach($list as $k=>$v){
            $v=$this->handleRoom($v);
            
            $list[$k]=$v;
        }
		return $list;
	}

    /* 信息 */
	public function getInfo($liveuid) {
        
        $info2=[
            'type'=>'0',
            'title'=>'',
            'des'=>'',
            'thumb'=>'',
            'thumb_p'=>'',
            'bgid'=>'0',
            'bg'=>'',
        ];
        
        $where=['uid'=>$liveuid];
        $model = new Model_Live();
        $info= $model->getInfo($where);

        if($info){
            
            $info2['type']=$info['type'];
            $info2['title']=$info['title'];
            $info2['des']=$info['des'];
            $info2['thumb']=$info['thumb'];
            $info2['bgid']=$info['bgid'];
            $info2['thumb_p']=\App\get_upload_path($info['thumb']);
            $info2['bg']=$this->getBgByid($info['bgid']);
        }
		return $info2;
	}
    
    
    /* 直播信息 */
	public function getLiveInfo($liveuid) {
        
        $where=['uid'=>$liveuid];
        $model = new Model_Live();
        $info= $model->getInfo($where);

        if($info){
            $info['thumb_p']=\App\get_upload_path($info['thumb']);
            $info['pull']=\App\PrivateKeyA('http',$info['uid']);
			$info['bg']=$this->getBgByid($info['bgid']);
        }
		return $info;
	}
    

    /* 设置信息 */
	public function setInfo($uid,$data) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $Domain_User = new Domain_User();
        $ishost = $Domain_User->ishost($uid);
        
        $Domain_Liveapply = new Domain_Liveapply();
        $isapply = $Domain_Liveapply->getInfo($uid);
        
        if(!$ishost && (!$isapply || $isapply['status']!=1)){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('您还不是主持人，无权操作');
            return $rs;
        }
        
        if(!$data){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        if(isset($data['type'])){
            $type=isset($data['type'])?$data['type']:'';
            if($type==1 && !$ishost){
                $rs['code'] = 1011;
                $rs['msg'] = \PhalApi\T('您还不是主持人，无权操作');
                return $rs;
            }
        }
        
        if(isset($data['thumb'])){
            $thumb=isset($data['thumb'])?$data['thumb']:'';
            if($thumb==''){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('请上传封面');
                return $rs;
            }
        }
        
        if(isset($data['title'])){
            $title=isset($data['title'])?$data['title']:'';
            if($title==''){
                $rs['code'] = 1005;
                $rs['msg'] = \PhalApi\T('请输入标题');
                return $rs;
            }
            
            if(mb_strlen($title) > 20){
                $rs['code'] = 1006;
                $rs['msg'] = \PhalApi\T('标题最多20字');
                return $rs;
            }
        }
        
        if(isset($data['des'])){
            $des=isset($data['des'])?$data['des']:'';
            if($des==''){
                $rs['code'] = 1007;
                $rs['msg'] = \PhalApi\T('请输入公告');
                return $rs;
            }
            
            if(mb_strlen($des)>200){
                $rs['code'] = 1008;
                $rs['msg'] = \PhalApi\T('公告最多200字');
                return $rs;
            }
        }
        
        if(isset($data['bgid'])){
            $bgid=isset($data['bgid'])?$data['bgid']:0;
            if($bgid==0){
                $rs['code'] = 1009;
                $rs['msg'] = \PhalApi\T('请选择背景');
                return $rs;
            }
            $isexist=0;
            $bglist=$this->getBg();
            foreach($bglist as $k=>$v){
                if($v['id']==$bgid){
                    $isexist=1;
                    break;
                }
            }
            
            if(!$isexist){
                $rs['code'] = 1010;
                $rs['msg'] = \PhalApi\T('选择的背景不存在');
                return $rs;
            }
        }
        
        $where=['uid'=>$uid];
        
        $model = new Model_Live();

        $isexist= $model->getInfo($where);
        if($isexist){
            $info= $model->upInfo($where,$data);
        }else{
            $data['uid']=$uid;
            $info= $model->setInfo($data);
        }
        
        if(!$info){
            $rs['code'] = 1007;
            $rs['msg'] = \PhalApi\T('操作失败');
            return $rs;
        }
        
		return $rs;
	}

    /* 更新信息 */
	public function upInfo($where,$data) {
        
        if(!$where || !$data){
            return 0;
        }
        
        $model = new Model_Live();

        $rs= $model->upInfo($where,$data);
        
		return $rs;
	}

    /* 更新人数 */
	public function upNums($where,$field,$nums=1,$type=0) {
        
        if(!$where || !$field){
            return 0;
        }
        
        $model = new Model_Live();

        $rs= $model->upNums($where,$field,$nums,$type);
        
		return $rs;
	}

    /* 开启聊天室 */
	public function start($uid,$data) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $nowtime=time();
        
        $data['islive']=0;
		$data['isvideo']=0;
        $data['pull']='';			
        $data['starttime']=$nowtime;
        $data['showid']=$nowtime;
        $stream=$uid.'_'.$nowtime;
        
        $data['stream']=$stream;
        $data['nums']=0;
        $data['totalnums']=0;
        
        $deviceinfo=isset($data['deviceinfo'])?$data['deviceinfo']:'';
        if($deviceinfo==''){
            $data['deviceinfo']='';
        }
        
        
        $res= $this->setInfo($uid,$data);
        if($res['code']!=0){
            if($res['code']==1001){
                $res['msg']='您还不是主持人，无法开启聊天室';
            }
            if($res['code']==1011){
                $res['msg']='无法开启派单聊天室';
            }
            return $res;
        }
        
        $configpri=\App\getConfigPri();
        $info=[];
        $info['stream']=$stream;
        $info['showid']=(string)$nowtime;
        $info['chatserver']=$configpri['chatserver'];
        
        $uservotes=\App\getUserGiftVotestotal($uid);
        
        $info['votestotal']=$uservotes['votestotal'];
		
		/* 邀请码 */
		$Domain_Agent = new Domain_Agent();
        $agentcode= $Domain_Agent->getMyCode($uid);
		$info['agentcode']=$agentcode;
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 关闭聊天室 */
	public function stop($uid,$stream) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        

        $where=[
            'uid'=>$uid,
            'stream'=>$stream,
            'islive'=>1,
        ];
        
        $model = new Model_Live();
        $liveinfo= $model->getInfo($where);
        if(!$liveinfo){
            $res=$this->stopInfo($stream);
            return $res;
        }
        
        $data2=['islive'=>0];
        $model->upInfo($where,$data2);
        
        $nowtime=time();
        
        $nums=$liveinfo['totalnums'];
        
        $data=[
            'uid'=>$liveinfo['uid'],
            'type'=>$liveinfo['type'],
            'title'=>$liveinfo['title'],
            'des'=>$liveinfo['des'],
            'thumb'=>$liveinfo['thumb'],
            'bgid'=>$liveinfo['bgid'],
            'showid'=>$liveinfo['showid'],
            'stream'=>$liveinfo['stream'],
            'starttime'=>$liveinfo['starttime'],
            'nums'=>$nums,
        ];
        $data['endtime']=$nowtime;
        $length=$nowtime-$data['starttime'];
        $data['length']=$length;
        
        
        $res= $model->setLiverecord($data);
        
        $info=[];
        $info['length']=\App\handellength($length);
        $info['nums']=(string)$nums;
        $info['title']=$liveinfo['title'];
        
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 聊天室结束信息 */
	public function stopInfo($stream) {
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $info=[
            'title'=>'',
            'length'=>'',
            'nums'=>'',
        ];

        $where=[
            'stream'=>$stream,
        ];
        
        $model = new Model_Live();

        $liveinfo= $model->getStopInfo($where);
        if($liveinfo){
            $info['title']=$liveinfo['title'];
            $info['length']=\App\handellength($liveinfo['length']);
            $info['nums']=$liveinfo['nums'];
        }
        
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 修改聊天室状态 */
	public function changeLive($uid,$stream,$type) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        
        
        if($type==1){
            $where=['uid'=>$uid,'stream'=>$stream];
            $data=['islive'=>1];
            $model = new Model_Live();
            $res=$model->upInfo($where,$data);
        }else{
            $res=$this->stop($uid,$stream);
            return $res;
        }
        
        
		return $rs;
	}
    
	
}
