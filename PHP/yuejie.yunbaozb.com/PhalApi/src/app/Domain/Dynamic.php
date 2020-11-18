<?php
namespace App\Domain;

use App\Model\Dynamic as Model_Dynamic;
use App\Domain\Skill as Domain_Skill;
use App\Domain\User as Domain_User;

class Dynamic {

    /* 发布动态 */
	public function setDynamic($data) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('发布成功，等待审核'), 'info' => array());
        
        $type=0;
        $thumbs=$data['thumbs'];
        if($thumbs){
            $thumbs_a=array_filter(preg_split('/,|，/',$thumbs));
            $nums=count($thumbs_a);
            if($nums>9){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('最多选择9张图片');
                return $rs;
            }
            
            $type=1;
        }
        
        $video=$data['video'];
        if($video){
            if($type!=0){
                $rs['code'] = 1003;
                $rs['msg'] = \PhalApi\T('不能同时使用图片、视频、语音');
                return $rs;
            }
            
            $type=2;
        }else{
            $data['video_t']='';
        }
        
        $voice=$data['voice'];
        if($voice){
            if($type!=0){
                $rs['code'] = 1003;
                $rs['msg'] = \PhalApi\T('不能同时使用图片、视频、语音');
                return $rs;
            }
            
            $voice_l=$data['voice_l'];
            if($voice_l<=0){
                $rs['code'] = 1003;
                $rs['msg'] = \PhalApi\T('语音时长不正确');
                return $rs;
            }
            
            $type=3;
        }else{
            $data['voice_l']=0;
        }
        
        $skillid=$data['skillid'];
        if($skillid){
            $where=[
                'uid'=>$data['uid'],
                'skillid'=>$data['skillid'],
            ];
            $domain_s = new Domain_Skill();
            $info = $domain_s->getAuthInfo($where);
            if(!$info){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('该技能还未认证');
                return $rs;
            }
            
            if($info['status']!=1){
                $rs['code'] = 1005;
                $rs['msg'] = \PhalApi\T('该技能认证还未通过审核');
                return $rs;
            }
            
        }else{
           $data['skillid']=0; 
        }
        
        $configpri=\App\getConfigPri();
        if($configpri['dynamic_switch']==0){
            $data['status']=1;
            $rs['msg'] = \PhalApi\T('发布成功');
        }

        $data['type']=$type;
        $data['addtime']=time();
        
        $model = new Model_Dynamic();
        $res= $model->setDynamic($data);
        
        if(!$res){
            $rs['code'] = 1006;
            $rs['msg'] = \PhalApi\T('发布失败，请重试');
            return $rs;
        }

		return $rs;
	}
    
    /* 动态内容 */
	public function getDynamic($did) {

        $model = new Model_Dynamic();
        
        $info=$model->getDynamic($did);
    
        return $info;

    }

    /* 点赞 */
	public function addLike($uid,$did) {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $model = new Model_Dynamic();
        
        $info=$model->getDynamic($did);
        if(!$info){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('动态不存在');
			return $rs;
        }
        
        if($info['uid']==$uid){
            $rs['code'] = 1003;
			$rs['msg'] = \PhalApi\T('自己的动态不能点赞');
			return $rs;
        }
        
        $iflike= \App\dynamic_isLike($uid,$did);
        
        if($iflike){
            /* 取赞 */
            $model->delLike($uid,$did);
            $islike='0';
        }else{
            /* 点赞 */
            $model->setLike($uid,$did);
            $islike='1';
        }
        
        $info=$model->getDynamic($did);
        
        $info2['islike']=$islike;
        $info2['likes']=\App\NumberFormat($info['likes']);
        
        $rs['info'][0]=$info2;
        
        return $rs;
    }

    /* 删除动态 */
	public function del($uid,$did) {
        $rs = array('code' => 0, 'msg' => \PhalApi\T('删除成功~'), 'info' => array());
        
        $where=[
            'uid'=>$uid,
            'id'=>$did,
        ];
        $model = new Model_Dynamic();
        $ifok= $model->del($where);
        
        if(!$ifok){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('删除失败');
            return $rs;
        }
        
        $where2=[
            'did'=>$did,
        ];
        
        $model->delOnlyLike($where2);
        $model->delComment($where2);
        $model->delOnlyCommentLike($where2);
        
        return $rs;
    }

    /* 获取评论 */
	public function getComments($uid,$did,$lastid) {
        
        if($lastid==0){
            $where="did={$did} and pid=0";
        }else{
            $where="did={$did} and pid=0 and id<{$lastid}";
        }
        
        $nums=20;
        $model = new Model_Dynamic();
        $list= $model->getComments($where,$nums);
        
        foreach($list as $k=>$v){
            $v=$this->handelComment($uid,$v);
            $list[$k]=$v;
        }

        return $list;
    }
    
    /* 获取回复 */
	public function getReplys($uid,$cid,$lastid) {

        if($lastid==0){
            $where="cid={$cid} and pid!=0";
            $nums=3;
        }else{
            $where="cid={$cid} and pid!=0 and id<{$lastid}";
            $nums=20;
        }
        
        $model = new Model_Dynamic();
        $list= $model->getComments($where,$nums);
        
        foreach($list as $k=>$v){
            $v=$this->handelComment($uid,$v);
            $list[$k]=$v;
        }
        
        return $list;
    }
    
	/* 获取回复评论总数 */
	public function getReplyscount($cid) {
      
		$where="cid={$cid}";
		
        $model = new Model_Dynamic();
        $count= $model->getCommentscount($where);
        
        return $count;
    }
    /* 处理评论 */
    protected function handelComment($uid,$v){
        $userinfo=\App\getUserInfo($v['uid']);
        
        $user=[
            'id'=>$userinfo['id'],
            'user_nickname'=>$userinfo['user_nickname'],
            'avatar'=>$userinfo['avatar'],
        ];
        
        $v['userinfo']=$user;
        
        $v['datetime']=\App\offtime($v['addtime']);
        $v['likes']=\App\NumberFormat($v['likes']);
        
        $reply=[];
        $islike='0';
        $touid='0';
        $touserinfo=(object)array();
        
        if($v['pid']==0){
            /* 评论动态 */
            $islike=\App\dynamic_comment_isLike($uid,$v['id']);
            
            /* 回复列表 */
            $reply=$this->getReplys($uid,$v['id'],0);
            $replycount=$this->getReplyscount($v['id']);
            
        }else if($v['cid']==$v['pid']){
            /* 评论评论 */
            
        }else{
            /* 回复 */
            $touid=$v['touid'];
            $touser=\App\getUserInfo($v['touid']);
            
            $touserinfo=[
                'id'=>$touser['id'],
                'user_nickname'=>$touser['user_nickname'],
                'avatar'=>$touser['avatar'],
            ];
            
        }
        
        $v['reply']=$reply;
        $v['replycount']=$replycount;
        $v['islike']=$islike;
        $v['touid']=$touid;
        $v['touserinfo']=$touserinfo;
        
        return $v;
    }

    /* 评论、回复 */
	public function setComment($data) {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        /* if($data['uid']==$data['touid']){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('不能评论自己~');
            return $rs;
        } */
        
        if($data['cid']==0 && $data['pid']!=0){
            $data['cid']=$data['pid'];
        }
        
        $data['addtime']=time();
        
        $model = new Model_Dynamic();
        $res= $model->setComment($data);
        
        if(!$res){
            $rs['code'] = 1006;
            $rs['msg'] = \PhalApi\T('发布失败，请重试');
            return $rs;
        }
        
        return $rs;
    }
    
    /* 评论点赞 */
	public function addCommnetLike($uid,$did,$cid) {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $model = new Model_Dynamic();
        $iflike= \App\dynamic_comment_isLike($uid,$cid);
        
        if($iflike){
            /* 取赞 */
            $model->delCommentLike($uid,$cid);
            $islike='0';
        }else{
            /* 点赞 */
            $model->setCommentLike($uid,$did,$cid);
            $islike='1';
        }
        
        $where=[
            'id'=>$cid
        ];
        $likes='0';
        $comment=$model->getComment($where);
        if($comment){
            $likes=$comment['likes'];
        }
        
        $info['islike']=$islike;
        $info['likes']=\App\NumberFormat($likes);
        
        $rs['info'][0]=$info;
        
        return $rs;
    }
    
    /* 处理动态信息 */
    protected function handelInfo($uid,$v){
        if($v['video']){
            $v['video']=\App\get_upload_path($v['video']);
            $v['video_t']=\App\get_upload_path($v['video_t']);
        }else{
            $v['video_t']='';
        }
        
        if($v['voice']){
            $v['voice']=\App\get_upload_path($v['voice']);
        }
        
        if($v['thumbs']){
            $thumbs_a=array_filter(preg_split('/,|，/',$v['thumbs']));
            
            foreach($thumbs_a as $k1=>$v1){
                $v1=\App\get_upload_path($v1);
                $thumbs_a[$k1]=$v1;
            }
            $v['thumbs']=$thumbs_a;
        }else{
            $v['thumbs']=[];
        }
        
        if($v['skillid']){
            
            $domain = new Domain_Skill();
            
            
            $where=[
                'uid'=>$v['uid'],
                'skillid'=>$v['skillid'],
                'status'=>1,
                'switch'=>1,
            ];
            $authinfo = $domain->getSkillAuth($where);
            if($authinfo){
                $auth=$authinfo[0];
                
                $skillinfo=$domain->getSkill($auth['skillid']);

                $skill['method']=isset($skillinfo['method']) ? $skillinfo['method'] : '';
                $skill['name']=isset($skillinfo['name']) ? $skillinfo['name'] : '';
                $skill['thumb']=isset($skillinfo['thumb']) ? $skillinfo['thumb'] : '';
                
                $skill['coin']=$auth['coin'];
				$skill['n_coin']=$auth['n_coin'];
                $skill['level']=$auth['level'];
                $skill['orders']=$auth['orders'];
                $skill['auth_thumb']=$auth['auth_thumb'];
                
                $v['skillinfo']=$skill;
                
            }else{
                $v['skillid']='0';
                $v['skillinfo']=(object)[];
            }
            
        }else{
            $v['skillinfo']=(object)[];
        }
        
        $userinfo=\App\getUserInfo($v['uid']);
        
        $v['user_nickname']=$userinfo['user_nickname'];
        $v['avatar']=$userinfo['avatar'];
        $v['age']=$userinfo['age'];
        $v['sex']=$userinfo['sex'];
        $v['addr']=$userinfo['addr'];
		$v['isAttention']=\App\isAttention($uid,$v['uid']);											 
        
        $v['likes']=\App\NumberFormat($v['likes']);
        
        if($uid==$v['uid']){
            $v['islike']='0';
            $v['isattent']='0';
			$v['isblack']='0';
        }else{
           $v['islike']=\App\dynamic_isLike($uid,$v['id']);
            $v['isattent']=\App\isAttent($uid,$v['uid']); 
			$v['isblack']=\App\isBlack($uid,$v['uid']);
        }
        
        $v['datatime']=\App\datetime($v['addtime']);
        
        unset($v['status']);
        unset($v['addtime']);
        unset($v['recoms']);
        
        return $v;
    }
        
    /* 动态列表
    * type 类型 0推荐 1关注 2最新
    */
	public function getList($uid,$sex,$age,$skillid,$p,$type=0) {
        
        $uids_s='';
        $attent_a=[];
        if($type==1){
            $where1=[
                'uid'=>$uid,
            ];
            
            $domain = new Domain_User();
            $attentlist = $domain->getAllAttention($where1);
            if(!$attentlist){
                return [];
            }
            
            $attent_a=array_column($attentlist,'touid');
            $attent_s=implode(',',$attent_a);
            
            $uids_s=$attent_s;
        }
        
        //$where='status=1 and (type=1 or type=2)';
		$where='status=1';			  
        
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
                return [];
            }
            
            $where2.=" and birthday>={$time[0]} and birthday<{$time[1]}";
            $isuser=1;
        }
        
        if($isuser){
            $domain = new Domain_User();
            $users = $domain->getUsers($where2);
            if(!$users){
                return [];
            }
            
            $uids_a=array_column($users,'id');
            $uids_user=implode(',',$uids_a);
            
            if($type==1){
                $uids_a2=array_intersect($attent_a,$uids_a);
                $uids_s=implode(',',$uids_a2);
            }else{
                $uids_s=$uids_user;
            }
            
            if(!$uids_s){
                return [];
            }
            
        }
        
        if($uids_s){
            $where.=" and uid in ({$uids_s})";
        }
        
        if($skillid!=0){
            $where.=' and skillid='.$skillid;
        }
        
        $order='id desc';
        
        $model = new Model_Dynamic();
        if($type!=0){
            $list= $model->getDynamicList($where,$order,$p);
        }else{
            $list= $model->getRecom($where,$p);
        }
        
        
        foreach($list as $k=>$v){
			$isblack=\App\isBlack($v['uid'],$uid);
			$isblack1=\App\isBlack($uid,$v['uid']);
			if($isblack || $isblack1){
				unset($list[$k]);
				continue;
			}
            $v=$this->handelInfo($uid,$v);
            $list[$k]=$v;
        }
		$list=array_values($list);
        
        return $list;
    }

    /* 动态详情 */
	public function getDynamicDetail($uid,$id) {
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $model = new Model_Dynamic();

        $info= $model->getDynamic($id);
        
        if(!$info || $info['status']!=1){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('动态不存在');
            return $rs;
        }

        $info=$this->handelInfo($uid,$info);
        
        $rs['info'][0]=$info;
        
        return $rs;
    }
    
    /* 个人主页动态列表 */
	public function getDynamics($uid,$liveuid,$p) {
        

        $where="status=1 and uid={$liveuid}";
        
        $order='id desc';
        
        $model = new Model_Dynamic();

        $list= $model->getDynamicList($where,$order,$p);
        
        foreach($list as $k=>$v){
			$isblack=\App\isBlack($v['uid'],$uid);
			$isblack1=\App\isBlack($uid,$v['uid']);
			if($isblack || $isblack1){
				unset($list[$k]);
				continue;
			}
            $v=$this->handelInfo($uid,$v);
            $list[$k]=$v;
        }
        $list=array_values($list);
        return $list;
    }

    /* 我的动态 */
	public function getMyDynamics($uid,$p) {
        

        $where="status=1 and uid={$uid}";
        
        $order='id desc';
        
        $model = new Model_Dynamic();

        $list= $model->getDynamicList($where,$order,$p);
        
        foreach($list as $k=>$v){
            $v=$this->handelInfo($uid,$v);
            $list[$k]=$v;
        }
        
        return $list;
    }

    /* 举报列表 */
	public function getReport() {
        
        $model = new Model_Dynamic();

        $list= $model->getReport();
        
        return $list;
    }

    /* 举报内容 */
	public function setReport($uid,$did,$content) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('举报成功'), 'info' => array());
         
        $info=$this->getDynamic($did);
        if(!$info){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('动态不存在');
            return $rs;
        }
        
        $data=[
            'uid'=>$uid,
            'touid'=>$info['uid'],
            'did'=>$did,
            'content'=>$content,
            'addtime'=>time(),
        ];
        $model = new Model_Dynamic();

        $res= $model->setReport($data);
        if(!$res){
            $rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('举报失败，请重试');
            return $rs;
        }
        return $rs;
    }
	
}
