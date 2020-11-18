<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Dynamic as Domain_Dynamic;

/**
 * 动态
 */
 
class Dynamic extends Api {

	public function getRules() {
		return array(
            'getRecom' => array(
				'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'defaulf'=>0, 'desc' => '技能ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getFollow' => array(
                'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'defaulf'=>0, 'desc' => '技能ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getNew' => array(
                'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'defaulf'=>0, 'desc' => '技能ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getDynamicDetail' => array(
				'id' => array('name' => 'id', 'type' => 'int', 'desc' => '动态ID'),
			),
            
            'getDynamics' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getMyDynamics' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'setDynamic' => array(
				'content' => array('name' => 'content', 'type' => 'string', 'desc' => '文字内容'),
				'thumbs' => array('name' => 'thumbs', 'type' => 'string', 'desc' => '图片链接，多张图片以,拼接'),
				'video' => array('name' => 'video', 'type' => 'string', 'desc' => '视频链接'),
				'video_t' => array('name' => 'video_t', 'type' => 'string', 'desc' => '视频封面'),
				'voice' => array('name' => 'voice', 'type' => 'string', 'desc' => '语音链接'),
				'voice_l' => array('name' => 'voice_l', 'type' => 'int', 'desc' => '语音时长'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '关联技能ID'),
                'location' => array('name' => 'location', 'type' => 'string', 'desc' => '位置'),
                'city' => array('name' => 'city', 'type' => 'string', 'desc' => '城市'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token content thumbs  video video_t voice skillid'),
			),
			'addLike' => array(
				'did' => array('name' => 'did', 'type' => 'int', 'desc' => '动态ID'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token did'),
			),
            
            'del' => array(
				'did' => array('name' => 'did', 'type' => 'int', 'desc' => '动态ID'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token did'),
			),
            
            'getComments' => array(
				'did' => array('name' => 'did', 'type' => 'int', 'desc' => '动态ID'),
				'lastid' => array('name' => 'lastid', 'type' => 'int', 'desc' => '最后一条评论ID,没有为0'),
			),
            
            'getReplys' => array(
				'cid' => array('name' => 'cid', 'type' => 'int', 'desc' => '动态ID'),
				'lastid' => array('name' => 'lastid', 'type' => 'int', 'desc' => '最后一条回复ID，没有为0'),
			),
            
            'setComment' => array(
                'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID，评论动态为动态所有者ID，回复评论为评论发布者uid'),
                'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
				'did' => array('name' => 'did', 'type' => 'int', 'desc' => '动态ID'),
				'cid' => array('name' => 'cid', 'type' => 'int', 'defaulf'=>0, 'desc' => '评论ID,评论动态为0，回复评论为评论的cid'),
				'pid' => array('name' => 'pid', 'type' => 'int','defaulf'=>0, 'desc' => '评论ID,评论动态为0，回复评论为评论的id'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token touid content did cid pid'),
			),
            
            'addCommnetLike' => array(
				'did' => array('name' => 'did', 'type' => 'int', 'desc' => '动态ID'),
				'cid' => array('name' => 'cid', 'type' => 'int', 'desc' => '评论ID'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token did cid'),
			),
            
            'setReport' => array(
				'did' => array('name' => 'did', 'type' => 'int', 'desc' => '动态ID'),
				'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token did content'),
			),
			
		);
	}
	/**
	 * 推荐动态列表 
	 * @desc 用于获取推荐动态列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getRecom() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $sex=\App\checkNull($this->sex);
        $age=\App\checkNull($this->age);
        $skillid=\App\checkNull($this->skillid);
        $p=\App\checkNull($this->p);

        /*if($uid=='' || $token=='' || $sex<0 || $sex>2 || $age<0 || $age>5 ){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}*/
        
        $type=0;
        $domain = new Domain_Dynamic();
		$list = $domain->getList($uid,$sex,$age,$skillid,$p,$type);
        
		$rs['info']=$list;
		return $rs;
	}
    
    /**
	 * 关注动态列表 
	 * @desc 用于获取用户关注的主播的动态列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getFollow() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $sex=\App\checkNull($this->sex);
        $age=\App\checkNull($this->age);
        $skillid=\App\checkNull($this->skillid);
        $p=\App\checkNull($this->p);

        if($uid=='' || $token=='' || $sex<0 || $sex>2 || $age<0 || $age>5 ){
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
        
        $type=1;
        $domain = new Domain_Dynamic();
		$list = $domain->getList($uid,$sex,$age,$skillid,$p,$type);
        
		$rs['info']=$list;
		return $rs;
	}
    
    /**
	 * 最新动态列表 
	 * @desc 用于获取动态推荐列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getNew() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $sex=\App\checkNull($this->sex);
        $age=\App\checkNull($this->age);
        $skillid=\App\checkNull($this->skillid);
        $p=\App\checkNull($this->p);

        if($uid=='' || $token=='' || $sex<0 || $sex>2 || $age<0 || $age>5 ){
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
        
        $type=2;
        $domain = new Domain_Dynamic();
		$list = $domain->getList($uid,$sex,$age,$skillid,$p,$type);
        
        $rs['info']=$list;
		return $rs;
	}
    
    /**
	 * 动态详情 
	 * @desc 用于获取某个动态详情
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getDynamicDetail() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $id=\App\checkNull($this->id);

        if($uid=='' || $token=='' || $id<1 ){
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
        
        $domain = new Domain_Dynamic();
		$res = $domain->getDynamicDetail($uid,$id);
        
		return $res;
	}
    
    /**
	 * 个人主页动态列表 
	 * @desc 用于获取个人主页动态列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getDynamics() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $p=\App\checkNull($this->p);

        if($uid=='' || $token=='' || $liveuid<1 ){
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
        
        $domain = new Domain_Dynamic();
		$list = $domain->getDynamics($uid,$liveuid,$p);
        
        $rs['info']=$list;
		return $rs;
	}
    
    /**
	 * 我的动态 
	 * @desc 用于获取我的动态
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getMyDynamics() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $p=\App\checkNull($this->p);

        if($uid=='' || $token==''){
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
        
        $domain = new Domain_Dynamic();
		$list = $domain->getMyDynamics($uid,$p);
        
        $rs['info']=$list;
		return $rs;
	}
    
	/**
	 * 发布动态 
	 * @desc 用于发布动态
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function setDynamic() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $content=$this->content;
        $thumbs=\App\checkNull($this->thumbs);
        $video=\App\checkNull($this->video);
        $video_t=\App\checkNull($this->video_t);
        $voice=\App\checkNull($this->voice);
        $voice_l=\App\checkNull($this->voice_l);
        $skillid=\App\checkNull($this->skillid);
        $location=\App\checkNull($this->location);
        $city=\App\checkNull($this->city);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || ( $content=='' && $thumbs=='' &&  $video=='' && $voice=='' ) || $sign==''){
            $rs['code'] = 1000;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'content'=>$content,
            'thumbs'=>$thumbs,
            'video'=>$video,
            'video_t'=>$video_t,
            'voice'=>$voice,
            'skillid'=>$skillid,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $data=[
            'uid'=>$uid,
            'content'=>$content,
            'thumbs'=>$thumbs,
            'video'=>$video,
            'video_t'=>$video_t,
            'voice'=>$voice,
            'voice_l'=>$voice_l,
            'skillid'=>$skillid,
            'location'=>$location,
            'city'=>$city,
        ];
        
        
        $domain = new Domain_Dynamic();
		$res = $domain->setDynamic($data);
        
		return $res;
	}
    
	/**
	 * 点赞 
	 * @desc 用于用户对动态点赞
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].islike 是否点赞，0否1是
	 * @return string info[].likes 点赞数
	 * @return string msg 提示信息
	 */
	public function addLike() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $did=\App\checkNull($this->did);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $did < 1  || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'did'=>$did,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Dynamic();
		$res = $domain->addLike($uid,$did);
        
		return $res;
	}

	/**
	 * 删除 
	 * @desc 用于用户删除动态
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function del() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $did=\App\checkNull($this->did);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $did < 1  || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'did'=>$did,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Dynamic();
		$res = $domain->del($uid,$did);
        
		return $res;
	}
    
	
    /**
	 * 获取评论 
	 * @desc 用于获取动态评论
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].nums 评论总数
	 * @return array  info[].list[] 评论列表
	 * @return string info[].list[].id 评论ID
	 * @return object info[].list[].userinfo 用户信息
	 * @return string info[].list[].datetime 时间
	 * @return string info[].list[].likes 点赞数
	 * @return string info[].list[].touid 回复用户ID，touid>0为回复信息
	 * @return object info[].list[].touserinfo 回复用户信息
	 * @return array  info[].list[].reply 回复列表
	 * @return string msg 提示信息
	 */
	public function getComments() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $did=\App\checkNull($this->did);
        $lastid=\App\checkNull($this->lastid);
        
        
        $domain = new Domain_Dynamic();
		$info = $domain->getDynamic($did);
        
        $nums='0';
        $list=[];
        if($info){
            $nums=$info['comments'];
            $list = $domain->getComments($uid,$did,$lastid);
        }
        
		
        
        $rs['info'][0]['nums']=$nums;
        $rs['info'][0]['list']=$list;
		return $rs;
	}
    
    /**
	 * 获取回复 
	 * @desc 用于获取评论回复
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].id 评论ID
	 * @return object info[].userinfo 用户信息
	 * @return string info[].datetime 时间
	 * @return string info[].likes 点赞数
	 * @return string info[].touid 回复用户ID，touid>0为回复信息
	 * @return object info[].touserinfo 回复用户信息
	 * @return string msg 提示信息
	 */
	public function getReplys() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $cid=\App\checkNull($this->cid);
        $lastid=\App\checkNull($this->lastid);
        
        $domain = new Domain_Dynamic();
		$list = $domain->getReplys($uid,$cid,$lastid);
        
        $rs['info']=$list;
		return $rs;
	}
    
    /**
	 * 评论、回复 
	 * @desc 用于用户对动态评论、回复评论
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].content 信息内容
	 * @return string msg 提示信息
	 */
	public function setComment() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $content=\App\checkNull($this->content);
        $did=\App\checkNull($this->did);
        $cid=\App\checkNull($this->cid);
        $pid=\App\checkNull($this->pid);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $content=='' || $touid < 1 || $did < 1 || $sign==''  ){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'content'=>$content,
            'touid'=>$touid,
            'did'=>$did,
            'cid'=>$cid,
            'pid'=>$pid,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $data=[
            'uid'=>$uid,
            'content'=>$content,
            'touid'=>$touid,
            'did'=>$did,
            'cid'=>$cid,
            'pid'=>$pid,
        ];
        
        $domain = new Domain_Dynamic();
		$res = $domain->setComment($data);
        
		return $res;
	}
    
	/**
	 * 评论点赞 
	 * @desc 用于用户对动态点赞
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].islike 是否点赞，0否1是
	 * @return string info[].likes 点赞数
	 * @return string msg 提示信息
	 */
	public function addCommnetLike() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $did=\App\checkNull($this->did);
        $cid=\App\checkNull($this->cid);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $did < 1 || $cid < 1  || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'did'=>$did,
            'cid'=>$cid,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Dynamic();
		$res = $domain->addCommnetLike($uid,$did,$cid);
        
		return $res;
	}

	/**
	 * 举报列表 
	 * @desc 用于获取举报列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].name 内容
	 * @return string msg 提示信息
	 */
	public function getReport() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $domain = new Domain_Dynamic();
		$list = $domain->getReport();
        
        $rs['info']=$list;
		return $rs;
	}

	/**
	 * 举报 
	 * @desc 用于用户举报动态
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function setReport() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $did=\App\checkNull($this->did);
        $content=\App\checkNull($this->content);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $did < 1 || $content == ''  || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'did'=>$did,
            'content'=>$content,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Dynamic();
		$res = $domain->setReport($uid,$did,$content);
        
		return $res;
	}


}
