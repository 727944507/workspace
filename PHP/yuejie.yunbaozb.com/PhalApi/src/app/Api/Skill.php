<?php
namespace App\Api;

use PhalApi\Api;
use App\Domain\Skill as Domain_Skill;

/**
 * 技能
 */
class Skill extends Api {
    public function getRules() {
        return array(
            'setSwitch' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
                'isswitch' => array('name' => 'isswitch', 'type' => 'int', 'desc' => '开关，0关1开'),
            ),
            
            'getCoins' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
            ),
            
            'upSkill' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
                'fields' => array('name' => 'fields', 'type' => 'string', 'desc' => 'json'),
            ),
            
            'getLevel' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
            ),
            
            'getLabel' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
            ),
			
			'getAllLabel' => array(
			    'uid' => array('name' => 'uid', 'type' => 'int', 'desc' => '陪玩ID'),
			),
            
            'getUserList' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
                'order' => array('name' => 'order', 'type' => 'int', 'default'=>'0', 'desc' => '排序，0智能1最新'),
                'sex' => array('name' => 'sex', 'type' => 'int', 'default'=>'0', 'desc' => '性别，0不限1男2女'),
                'level' => array('name' => 'level', 'type' => 'int', 'default'=>'0', 'desc' => '段位ID,不选为0'),
                'voice' => array('name' => 'voice', 'type' => 'int', 'default'=>'0', 'desc' => '语音，0不限1有'),
            ),
            
            'getSkillHome' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
            ),
            
            'getSkillInfo' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
            ),
            
            'getSkillAuth' => array(
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
            ),
			'getMyskillList' => array(
                
            ),

        );
    }
    
    
    /**
     * 全部分类
     * @desc 用于获取全部分类
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 分类ID
     * @return string info[].name 分类名
     * @return array info[].list 技能列表
     * @return string info[].list[].id 技能ID
     * @return string info[].list[].name 技能名
     * @return string info[].list[].thumb 图标
     * @return string msg 提示信息
     */
    public function getAll() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $domain = new Domain_Skill();
		$list = $domain->getAll();
        
        $rs['info']=$list;


        return $rs;
    }

    /**
     * 选择技能认证
     * @desc 用于获取技能信息
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 分类ID
     * @return string info[].name 技能名
     * @return string info[].thumb 图标
     * @return string info[].status 状态 -1未申请 0审核中，1通过2拒绝
     * @return string msg 提示信息
     */
    public function getUserSkill() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);	
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Skill();
		$list = $domain->getUserSkill($uid);

        $rs['info']=$list;


        return $rs;
    }
    
    
    /**
     * 我的技能
     * @desc 用于获取技能信息
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 
     * @return string info[].skillid 技能ID 
     * @return string info[].switch 开关，0关1开
     * @return string info[].thumb 截图
     * @return string info[].coin 价格
     * @return string info[].voice 语音
     * @return string info[].des 描述
     * @return string info[].level 段位
     * @return array  info[].label_a 标签列表
     * @return string info[].label_a[] 标签
     * @return object info[].skill 技能信息
     * @return object info[].skill.name 技能名
     * @return object info[].skill.thumb 图标
     * @return object info[].skill.method 单位
     * @return string msg 提示信息
     */
    public function getMySkill() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);	
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Skill();
		$list = $domain->getMySkill($uid);

        $rs['info']=$list;


        return $rs;
    }

    /**
     * 技能开关
     * @desc 用于开启、关闭技能
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string msg 提示信息
     */
    public function setSwitch() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);	
        $skillid=\App\checkNull($this->skillid);
        $isswitch=\App\checkNull($this->isswitch);	
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $data=[
            'switch'=>$isswitch
        ];
        
        $domain = new Domain_Skill();
		$info = $domain->setSwitch($uid,$skillid,$data);

        return $info;
    }

    /**
	 * 价格列表
	 * @desc 用于获取价格列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getCoins() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $skillid=\App\checkNull($this->skillid);

      /*   $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
         */
		$domain = new Domain_Skill();
		$info = $domain->getCoins($uid,$skillid);
        
        $rs['info']=$info;
        
		return $rs;
	}
    
    /**
	 * 价格说明
	 * @desc 用于查看价格说明
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].coin 价格
	 * @return string info[].orders 接单数
	 * @return string msg 提示信息
	 */
	public function getCoinInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$domain = new Domain_Skill();
		$coinlist = $domain->getCoinList();
        
        $keys=array_keys($coinlist);
        
        $end=end($keys);
        
        $orders='0';
        $coin='0';
        $list=[];
        foreach($coinlist as $k=>$v ){
            $data=[];
            if($orders<$v['orders']){
                $data['coin']=(string)$coin;
                $data['orders']=(string)$orders;
                $list[]=$data;
            }
            
            if($k==$end){
                $data['coin']=(string)$v['coin'];
                $data['orders']=(string)$v['orders'];
                $list[]=$data;
            }
            
            $orders=$v['orders'];
            $coin=$v['coin'];
        }
        
        $rs['info']=$list;
		return $rs;
	}

    /**
     * 更新技能配置
     * @desc 用于更新技能配置
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string msg 提示信息
     */
    public function upSkill() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $skillid=\App\checkNull($this->skillid);
        $fields=$this->fields;

        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $fields_a=json_decode($fields,true);
        
        $domain = new Domain_Skill();
		$res = $domain->upSkill($uid,$skillid,$fields_a);

        return $res;
    }


    /**
     * 技能段位
     * @desc 用于获取技能段位
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].levelid 
     * @return string info[].name 段位名
     * @return string msg 提示信息
     */
    public function getLevel() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $skillid = \App\checkNull($this->skillid);
        
        if($skillid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $domain = new Domain_Skill();
		$list = $domain->getLevel($skillid);
        
        foreach($list as $k=>$v){
            $v['id']=$v['levelid'];
            $list[$k]=$v;
        }

        $rs['info']=$list;

        return $rs;
    }

    /**
     * 技能标签
     * @desc 用于获取技能标签
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 
     * @return string info[].name 标签
     * @return string msg 提示信息
     */
    public function getLabel() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $skillid = \App\checkNull($this->skillid);
        
        if($skillid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $domain = new Domain_Skill();
		$list = $domain->getLabel($skillid);

        $rs['info']=$list;

        return $rs;
    }
	
	/**
	 * 技能标签
	 * @desc 用于获取技能标签
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].id 
	 * @return string info[].name 标签
	 * @return string msg 提示信息
	 */
	
	public function getAllLabel() {
	    $rs = array('code' => 0, 'msg' => '', 'info' => array());
	    
	    $uid = \App\checkNull($this->uid);
	    if($uid<1){
	        $rs['code'] = 1001;
	        $rs['msg'] = \PhalApi\T('信息错误');
	        return $rs;
	    }
	    
	    $domain = new Domain_Skill();
		$list = $domain->getAllLabel($uid);
	
	    $rs['info']=$list;
	
	    return $rs;
	}

    /**
     * 某技能下用户
     * @desc 用于获取全部分类
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 
     * @return string info[].skillid 技能ID
     * @return string info[].method 单位
     * @return string info[].level 段位
     * @return string info[].star_level 星级
     * @return string info[].orders 接单量
     * @return object info[].userinfo 用户信息
     * @return array  info[].label_a 标签列表
     * @return string info[].label_a[] 标签
     * @return string msg 提示信息
     */
    public function getUserList() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = \App\checkNull($this->uid);
        $skillid = \App\checkNull($this->skillid);
        $order = \App\checkNull($this->order);
        $sex = \App\checkNull($this->sex);
        $level = \App\checkNull($this->level);
        $voice = \App\checkNull($this->voice);
        
        if($skillid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $domain = new Domain_Skill();
		$list = $domain->getUserList($uid,$skillid,$order,$sex,$level,$voice);
 
        $rs['info']=$list;

        return $rs;
    }

    /**
     * 技能主页
     * @desc 用于获取技能主页信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].isattent 是否关注0否1是
     * @return object info[0].skill 技能信息
     * @return string info[0].skill.method 单位
     * @return array  info[0].authinfo 技能认证信息
     * @return string info[0].authinfo.thumb 截图
     * @return string info[0].authinfo.level 段位
     * @return string info[0].authinfo.coin 价格
     * @return string info[0].authinfo.voice 语音介绍
     * @return string info[0].authinfo.des 介绍
     * @return string info[0].authinfo.orders 接单数
     * @return string info[0].authinfo.stars 星级数字
     * @return string info[0].authinfo.star_level 星级
     * @return array  info[0].authinfo.label_a 标签列表
     * @return string info[0].authinfo.label_a[] 标签
     * @return string info[0].comment_nums 评论总数
     * @return array  info[0].label_list 评论标签统计
     * @return string info[0].label_list[].label 标签
     * @return string info[0].label_list[].nums 数量
     * @return array  info[0].comment_list 评论列表
     * @return object info[0].comment_list[].userinfo 评论用户信息
     * @return string info[0].comment_list[].content 内容
     * @return string info[0].comment_list[].star 星级
     * @return string info[0].comment_list[].add_time 时间
     * @return array  info[0].comment_list[].label_a 评论标签
     * @return string info[0].comment_list[].label_a[] 标签
     * @return string msg 提示信息
     */
    public function getSkillHome() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = \App\checkNull($this->uid);
        $skillid = \App\checkNull($this->skillid);
        $liveuid = \App\checkNull($this->liveuid);

        
        if($skillid<1 || $liveuid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $domain = new Domain_Skill();
		$info = $domain->getSkillHome($uid,$liveuid,$skillid);

        return $info;
    }

    /**
     * 用户某技能信息
     * @desc 用于获取用户某技能信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return object info[0].skill 技能信息
     * @return string info[0].skill.method 单位
     * @return array  info[0].authinfo 技能认证信息
     * @return string info[0].authinfo.thumb 截图
     * @return string info[0].authinfo.level 段位
     * @return string info[0].authinfo.coin 价格
     * @return string info[0].authinfo.voice 语音介绍
     * @return string info[0].authinfo.des 介绍
     * @return string info[0].authinfo.orders 接单数
     * @return string info[0].authinfo.stars 星级数字
     * @return string info[0].authinfo.star_level 星级
     * @return array  info[0].authinfo.label_a 标签列表
     * @return string info[0].authinfo.label_a[] 标签
     * @return string msg 提示信息
     */
    public function getSkillInfo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $skillid = \App\checkNull($this->skillid);
        $liveuid = \App\checkNull($this->liveuid);

        
        if($skillid<1 || $liveuid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $domain = new Domain_Skill();
		$info = $domain->getSkillInfo($liveuid,$skillid);

        return $info;
    }

    /**
     * 用户技能列表
     * @desc 用于获取某用户技能列表
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[].skillid 技能ID
     * @return string info[].coin 价格
     * @return string info[].method 单位
     * @return string info[].skillname 技能名称
     * @return string info[].level 段位
     * @return string info[].orders 接单量

     * @return string msg 提示信息
     */
    public function getSkillAuth() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $liveuid = \App\checkNull($this->liveuid);

        
        if( $liveuid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $where=[
            'uid'=>$liveuid,
            'status'=>'1',
            'switch'=>'1',
        ];
        
        
        $domain = new Domain_Skill();
		$list = $domain->getSkillAuth($where);

        $rs['info']=$list;
        return $rs;
    }

    /** 我的技能列表
     * @desc 用于获取技能信息
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string msg 提示信息
     */
    public function getMyskillList() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);	
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Skill();
		$list = $domain->getMyskillList($uid);

        $rs['info']=$list;


        return $rs;
    }

} 
