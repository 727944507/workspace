<?php
namespace App\Api;

use PhalApi\Api;
use App\Domain\User as Domain_User;
use App\Domain\Skill as Domain_Skill;
use App\Domain\Foot as Domain_Foot;
use App\Domain\Liveapply as Domain_Liveapply;

/**
 * 用户信息
 */
class User extends Api {
    public function getRules() {
        return array(
            'getBaseInfo' => array(
                'ios_version' => array('name' => 'ios_version', 'type' => 'string', 'default'=>'', 'desc' => 'IOS版本号'),
            ),
			
			'getNearby'=>array(
				'uid' =>array('name'=>'uid','type'=>'int','default'=>0,'desc'=>'用户id'),
				'lng' =>array('name'=>'lng','desc'=>'经度'),
				'lat' =>array('name'=>'lat','desc'=>'纬度'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
			),
            
            'setUserinfo' => array(
                'fields' => array('name' => 'fields', 'type' => 'string', 'default'=>'', 'desc' => '修改信息json串'),
            ),
            
            'upUserInfo' => array(
                'fields' => array('name' => 'fields', 'type' => 'string', 'default'=>'', 'desc' => '修改信息json串'),
            ),
            
            'setAttent' => array(
                'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
            ),
            
            'getFollow' => array(
                'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
            ),
            
            'getFans' => array(
                'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
            ),
            
            'getHome' => array(
                'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
            ),
            'checkAttent' => array(
                'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
            ),
			'setReport' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '被举报用户ID'),
				'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token did content'),
			),
			'setBlack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			'blackList' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
			),
			'checkBlack' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'),
			),
			'getRecom' => array(
                'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'defaulf'=>0, 'desc' => '技能ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
            ),
			'getFollowlist' => array(
				'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'defaulf'=>0, 'desc' => '技能ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
            ),
			'getAuthlist' => array(
				'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'defaulf'=>0, 'desc' => '技能ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
            ),
        );
    }

	/**
	 * 判断token
	 * @desc 用于判断token
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function iftoken() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);

		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
		return $rs;
	}
    
	/**
	 * 获取用户信息
	 * @desc 用于获取单个用户基本信息
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return array info[0] 用户信息
	 * @return int info[0].id 用户ID
	 * @return string info[0].follows 关注数
	 * @return string info[0].fans 粉丝数
	 * @return string info[0].auth_nums 开启的技能数
	 * @return string info[0].profession 职业
	 * @return string info[0].school 学校
	 * @return string info[0].hobby 兴趣
	 * @return string info[0].voice 语音
	 * @return string info[0].age 年龄
	 * @return string info[0].constellation 星座
	 * @return string info[0].visitnums 来访量
	 * @return string info[0].viewnums 浏览量
	 * @return string info[0].newnums 新增来访量
	 * @return array  info[0].list 
	 * @return string info[0].list[].id 
	 * @return string info[0].list[].name 名称
	 * @return string info[0].list[].thumb 图标
	 * @return string info[0].list[].href H5链接 
	 * @return string msg 提示信息
	 */
	public function getBaseInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_User();
		$info = $domain->getBaseInfo($uid);
        if(!$info){
            $rs['code'] = 700;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
        }

		$configpub=\App\getConfigPub();
		$configpri=\App\getConfigPri();

		$ios_shelves=$configpub['ios_shelves'];
		

		/* 个人中心菜单 */
        $ios_version=\App\checkNull($this->ios_version);

		$list=array();
		$shelves=1;
		if($ios_version && $ios_version==$ios_shelves){
			$shelves=0;
		}
        $auth_nums='0';
        
        
        $isauth=$domain->isauth($uid);
        
        $Domain_Liveapply = new Domain_Liveapply();
        $isapply=-1;
        $apply_info=$Domain_Liveapply->getInfo($uid);
        if($apply_info){
            $isapply=$apply_info['status'];
        }
        
        $Domain_Skill = new Domain_Skill();
        $where=[];
        $where['uid']=$uid;
        $where['status']='1';
        
        $isskillauth=$Domain_Skill->getSkillAuth($where);
        
        
        $list[]=array('id'=>'1','name'=>\PhalApi\T('订单中心'),'thumb'=>\App\get_upload_path("/static/app/person/order.png"),'href'=>'' );
        $list[]=array('id'=>'2','name'=>\PhalApi\T('我的钱包'),'thumb'=>\App\get_upload_path("/static/app/person/wallet.png"),'href'=>'' );
        if($isskillauth){
            
            foreach($isskillauth as $k=>$v){
                if($v['switch']==1){
                    $auth_nums++;
                }
            }

            $list[]=array('id'=>'5','name'=>\PhalApi\T('我的技能'),'thumb'=>\App\get_upload_path("/static/app/person/skill.png"),'href'=>'' );
        }elseif($isauth==1){
            $list[]=array('id'=>'4','name'=>\PhalApi\T('申请大神'),'thumb'=>\App\get_upload_path("/static/app/person/skill.png"),'href'=>'' );
        }else{
            $list[]=array('id'=>'3','name'=>\PhalApi\T('实名认证'),'thumb'=>\App\get_upload_path("/static/app/person/auth.png"),'href'=>\App\get_upload_path("/appapi/auth/index") );
        }
        

		$list[]=array('id'=>'7','name'=>\PhalApi\T('我的动态'),'thumb'=>\App\get_upload_path("/static/app/person/dynamic.png") ,'href'=>'');
		$list[]=array('id'=>'8','name'=>\PhalApi\T('我的相册'),'thumb'=>\App\get_upload_path("/static/app/person/photo.png") ,'href'=>'');
        
        if($isapply==1){
            $list[]=array('id'=>'11','name'=>\PhalApi\T('开启聊天室'),'thumb'=>\App\get_upload_path("/static/app/person/chatroom.png") ,'href'=>'');
        }else if($isauth){
            $list[]=array('id'=>'10','name'=>\PhalApi\T('开启聊天室'),'thumb'=>\App\get_upload_path("/static/app/person/chatroom.png") ,'href'=>'');
        }else{
            //$list[]=array('id'=>'3','name'=>\PhalApi\T('开启聊天室'),'thumb'=>\App\get_upload_path("/static/app/person/chatroom.png") ,'href'=>\App\get_upload_path("/appapi/auth/index"));
        }
		
		$Liveapply = new Domain_Liveapply();
		$res = $Liveapply->getInfo($uid);
		if($res){
		    $list['statusLive']=$res['status'];
		    $list['reasonLive']=$res['reason'];
		}else{
			$list['statusLive']=0;
			$list['reasonLive']=0;
		}
		
        
		$list[]=array('id'=>'9','name'=>\PhalApi\T('邀请赚钱'),'thumb'=>\App\get_upload_path("/static/app/person/agent.png") ,'href'=>\App\get_upload_path("/appapi/agent/index"));
		$list[]=array('id'=>'6','name'=>\PhalApi\T('设置'),'thumb'=>\App\get_upload_path("/static/app/person/setting.png") ,'href'=>'');

		$info['list']=$list;
        
        $info['auth_nums']=(string)$auth_nums;
        
        /* 足迹 */
        $Domain_Foot = new Domain_Foot();
        $visitnums=$Domain_Foot->getVisitNums($uid);
        $viewnums=$Domain_Foot->getViewNums($uid);
        $newnums=$Domain_Foot->getNewNums($uid);
        
        $info['visitnums']=$visitnums;
        $info['viewnums']=$viewnums;
        $info['newnums']=$newnums;
        
		$rs['info'][0] = $info;

		return $rs;
	}
    
    /**
     * 兴趣爱好
     * @desc 用于获取兴趣爱好
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 
     * @return string info[].name 名称
     * @return string msg 提示信息
     */
    public function getHobby() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$list = \App\getHobby();

        $rs['info']=$list;

        return $rs;
    }
    
    
    /**
	 * 设置资料
	 * @desc 用于用户设置资料
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setUserinfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $fields=$this->fields;
        
        if($fields==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $fields_a=json_decode($fields,true);
        if(!$fields_a){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        if( !isset($fields_a['avatar']) || $fields_a['avatar']==''  ){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('请上传头像');
            return $rs;
        }
        
        if( !isset($fields_a['user_nickname']) || $fields_a['user_nickname']==''  ){
            $rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('请设置您的昵称');
            return $rs;
        }
        
        if( !isset($fields_a['birthday']) || $fields_a['birthday']==''  ){
            $rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('请选择出生日期');
            return $rs;
        }
        
        if( !isset($fields_a['sex']) || !$fields_a['sex']  ){
            $rs['code'] = 1006;
            $rs['msg'] = \PhalApi\T('请选择您的性别');
            return $rs;
        }

		$domain = new Domain_User();
		$info = $domain->upUserInfo($uid,$fields_a);
	 
		return $info;
	}

    /**
	 * 更新基本信息
	 * @desc 用于用户更新基本信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function upUserInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $fields=$this->fields;
        
        if($fields==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $fields_a=json_decode($fields,true);
        if(!$fields_a){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        

        unset($fields_a['sex']);
        
		$domain = new Domain_User();
		$info = $domain->upUserInfo($uid,$fields_a);
	 
		return $info;
	}
    
    /**
	 * 设置、取消关注
	 * @desc 用于设置、取消关注
	 * @return int code 操作码，0表示成功
	 * @return array info 
     * @return string info[0].isattent 是否关注，0否1是
	 * @return string msg 提示信息
	 */
	public function setAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);

        if($touid<1){
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

        if($uid==$touid){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('不能关注自己');
			return $rs;
        }
        
        $domain = new Domain_User();
		$isattent = $domain->setAttent($uid,$touid);

        $info['isattent']=$isattent;
        $msg=\PhalApi\T('取消成功');
        if($isattent==1){
            $msg=\PhalApi\T('关注成功');
        }
        
        $rs['msg']=$msg;
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /**
	 * 关注列表
	 * @desc 用于获取用户关注列表
	 * @return int code 操作码，0表示成功
	 * @return array info
     * @return object info[] 用户基本信息
     * @return string info[].isattent 是否关注，0否1是
	 * @return string msg 提示信息
	 */
	public function getFollow() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $touid=\App\checkNull($this->touid);
        $p=\App\checkNull($this->p);
        if($touid<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $domain = new Domain_User();
		$list = $domain->getFollow($uid,$touid,$p);

        $rs['info']=$list;
        
		return $rs;
	}

    /**
	 * 粉丝列表
	 * @desc 用于获取用户粉丝列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return object info[] 用户基本信息
	 * @return string info[].isattent 是否关注，0否1是
	 * @return string msg 提示信息
	 */
	public function getFans() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $touid=\App\checkNull($this->touid);
        $p=\App\checkNull($this->p);
        if($touid<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $domain = new Domain_User();
		$list = $domain->getFans($uid,$touid,$p);

        $rs['info']=$list;

		return $rs;
	}

    /**
	 * 个人主页
	 * @desc 用于个人主页信息
	 * @return int code 操作码，0表示成功
	 * @return array info 用户基本信息
	 * @return string info[].isattent 是否关注，0否1是
	 * @return string info[].des 信息描述
	 * @return string info[].fans 粉丝数
	 * @return array  info[].list 技能列表
	 * @return string msg 提示信息
	 */
	public function getHome() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $touid=\App\checkNull($this->touid);

        if($uid<1 || $touid<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $domain = new Domain_User();
		$info = $domain->getHome($uid,$touid);

        $rs['info'][0]=$info;
        
        /* 足迹 */
        if($uid!=$touid){
            $Domain_Foot = new Domain_Foot();
            $Domain_Foot->addFoot($uid,$touid);
        }
        

		return $rs;
	}

    /**
	 * 检测关系
	 * @desc 用于检测两个用户间的关注情况
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].status 状态，0表示未关注，1表示我关注对方，2表示对方关注我，3表示互关
	 * @return string msg 提示信息
	 */
	public function checkAttent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);

        if($uid<1 || $token=='' || $touid<1){
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

        $domain = new Domain_User();
		$status = $domain->checkAttent($uid,$touid);

        $rs['info'][0]['status']=$status;

		return $rs;
	}
    
	/**
	 * 判断认证
	 * @desc 用于判断是否认证
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isauth 是否认证，0否1是
	 * @return string msg 提示信息
	 */
	public function ifauth() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);

		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_User();
		$isauth = $domain->isauth($uid);
        
        $info['isauth']=$isauth;
        
        $rs['info'][0]=$info;
        
		return $rs;
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
        
        $domain = new Domain_User();
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
        $touid=\App\checkNull($this->touid);
        $content=\App\checkNull($this->content);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $touid < 1 || $content == ''  || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'touid'=>$touid,
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
        
        
        $domain = new Domain_User();
		$res = $domain->setReport($uid,$touid,$content);
        
		return $res;
	}
	/**
	 * 拉黑/取消拉黑
	 * @desc 用于拉黑/取消拉黑
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isblack 拉黑信息,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function setBlack() {
		$rs = array('code' => 0, 'msg' => '已拉黑', 'info' => array());
		
		$domain = new Domain_User();
		$info = $domain->setBlack($this->uid,$this->touid);
		if($info=='0'){
			$rs['msg']="取消拉黑成功";
		}
		$rs['info'][0]['isblack']=(string)$info;
		return $rs;
	}	
	
	/**
	 * 黑名单列表 
	 * @desc 用于 黑名单列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function blackList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
		$p=\App\checkNull($this->p);
     
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
        
        
        $domain = new Domain_User();
		$res = $domain->blackList($uid,$p);
        $rs['info']=$res;
		return $rs;
	}
	
	/**
	 * 检测拉黑状态
	 * @desc 用于私信聊天时判断私聊双方的拉黑状态
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].u2t  是否拉黑对方,0表示未拉黑，1表示已拉黑
	 * @return string info[0].t2u  是否被对方拉黑,0表示未拉黑，1表示已拉黑
	 * @return string msg 提示信息
	 */
	public function checkBlack() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$u2t = \App\isBlack($this->uid,$this->touid);
		$t2u = \App\isBlack($this->touid,$this->uid);
	 
		//判断对方是否已注销
		$is_destroy=\App\checkIsDestroyByUid($this->touid);
		if($is_destroy){
			$rs['code']=1001;
			$rs['msg']=\PhalApi\T('对方账号已注销');
			return $rs;
		}
		
		$rs['info'][0]['u2t']=(string)$u2t;
		$rs['info'][0]['t2u']=(string)$t2u;
		return $rs;
	}	
	
	/** 推荐列表
	 * @desc 用于获取用户 推荐列表
	 * @return int code 操作码，0表示成功
	 * @return array info
     * @return object info[] 用户基本信息
     * @return string info[].isattent 是否关注，0否1是
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
		$type=0;
        $domain = new Domain_User();
		
		$list = $domain->getList($uid,$sex,$age,$skillid,$p,$type);

        $rs['info']=$list;
        
		return $rs;
	}
	
	/** 附近人列表
	 * @desc 用于获取用户 推荐列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return object info[] 用户基本信息
	 * @return string info[].isattent 是否关注，0否1是
	 * @return string msg 提示信息
	 */
	
	public function getNearby() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=\App\checkNull($this->uid);
		$token=\App\checkNull($this->token);
		$lng=\App\checkNull($this->lng);
		$lat=\App\checkNull($this->lat);
		$p=\App\checkNull($this->p);
		
		if($uid=='' || $token==''){
		    $rs['code'] = 1001;
		    $rs['msg'] = \PhalApi\T('信息错误');
		    return $rs;
		}
		
		if($lng=='' || $lat==''){
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
		
		$domain = new Domain_User();
		$list = $domain->getNearby($uid,$lng,$lat,$p);
		
		$rs['info'] = $list;
		
		return $rs;
	}
	
	/**
	 * 关注列表
	 * @desc 用于获取用户关注列表
	 * @return int code 操作码，0表示成功
	 * @return array info
     * @return object info[] 用户基本信息
     * @return string info[].isattent 是否关注，0否1是
	 * @return string msg 提示信息
	 */
	public function getFollowlist() {
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
        $domain = new Domain_User();
		$list = $domain->getList($uid,$sex,$age,$skillid,$p,$type);

        $rs['info']=$list;
        
		return $rs;
	}
	/**
	 * 最近三天认证用户列表
	 * @desc 用于获取 最近三天认证用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info
     * @return object info[] 用户基本信息
     * @return string info[].isattent 是否关注，0否1是
	 * @return string msg 提示信息
	 */
	public function getAuthlist() {
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
        $domain = new Domain_User();
		$list = $domain->getList($uid,$sex,$age,$skillid,$p,$type);

        $rs['info']=$list;
        
		return $rs;
	}	 
} 
