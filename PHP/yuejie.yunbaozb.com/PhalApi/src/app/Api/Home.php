<?php
namespace App\Api;

use PhalApi\Api;
use App\Domain\Guide as Domain_Guide;
use App\Domain\Home as Domain_Home;
use App\Domain\Skill as Domain_Skill;
use App\Domain\Dynamic as Domain_Dynamic;
/**
 * 首页
 */

class Home extends Api {

	public function getRules() {
        return array(
            'getUsers' => array(
                'p' => array('name' => 'p', 'type' => 'int','default'=>'1', 'desc' => '页码'),
            ),
            'search' => array(
                'keyword' => array('name' => 'keyword', 'type' => 'string', 'desc' => '搜索内容'),
            ),
            
            'searchMore' => array(
                'keyword' => array('name' => 'keyword', 'type' => 'string', 'desc' => '搜索内容'),
                'p' => array('name' => 'p', 'type' => 'int','default'=>'1', 'desc' => '页码'),
            ),
        );
	}
	
    /**
     * 网站信息
     * @desc 用于获取网站基本信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].site_name 网站名称
     * @return string info[0].name_coin 消费币名称
     * @return string info[0].apk_ver APK版本号
     * @return string info[0].apk_des APK更新说明
     * @return string info[0].apk_url APK下载链接
     * @return string info[0].ipa_ver IPA版本号
     * @return string info[0].ios_shelves IPA上架版本号
     * @return string info[0].ipa_des IPA更新说明
     * @return string info[0].ipa_url IPA下载链接
     * @return array info[0].login_type 登录方式
     * @return array info[0].share_type 分享方式
     * @return array info[0].admin 私信管理员账号
     * @return array info[0].im_admin_drip 抢单大厅的IM管理员
     * @return array info[0].admin_dispatch 派单的IM管理员
     * @return string msg 提示信息
     */
	public function getConfig() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $info=\App\getConfigPub();
        unset($info['site_url']);
        unset($info['site_seo_title']);
        unset($info['site_seo_keywords']);
        unset($info['site_seo_description']);
        unset($info['copyright']);
        unset($info['qr_url']);
        
        $info_pri=\App\getConfigPri();
        
        
		$login_type=$info_pri['login_type'];
        foreach ($login_type as $k => $v) {
            if($v=='ios'){
                unset($login_type[$k]);
                break;
            }
        }

        $login_type=array_values($login_type);
		
		$info['login_type']=$login_type;
		
        $info['login_type_ios']=$info_pri['login_type'];
        $info['share_type']=$info_pri['share_type'];
        $info['admin']=$info_pri['im_admin'];
        $info['im_admin_drip']=$info_pri['im_admin_drip'];
        $info['admin_dispatch']=$info_pri['im_admin_dispatch'];
		$info['admin_upservice']=$info_pri['im_admin_upservice'];
        /* 引导页 */
        $domain = new Domain_Guide();
		$guide_info = $domain->getGuide();
        
        $info['guide']=$guide_info;
		

        $rs['info'][0] = $info;
		
        return $rs;
	}

    /**
     * 首页
     * @desc 用于获取首页信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0].skilllist 技能列表 
     * @return string info[0].skilllist[].id
     * @return string info[0].skilllist[].name 技能名
     * @return string info[0].skilllist[].thumb 图标
     * @return array info[0].silidelist 轮播 
     * @return string info[0].silidelist[].image 图片
     * @return string info[0].silidelist[].url 链接
     * @return array info[0].dynamic_list 动态列表 
     * @return string info[0].dynamic_list[].user_nickname 昵称
     * @return string info[0].dynamic_list[].avatar 头像
     * @return string info[0].dynamic_list[].sex 性别
     * @return string info[0].dynamic_list[].age 年龄
     * @return string info[0].dynamic_list[].addr 地址
     * @return string info[0].dynamic_list[].isattent 是否关注,0否1是
     * @return string info[0].dynamic_list[].islike 是否点赞,0否1是
     * @return string info[0].dynamic_list[].type 动态类型，0纯文字，1图片，2视频，3语音
     * @return string info[0].dynamic_list[].content 文字内容
     * @return array  info[0].dynamic_list[].thumbs 图片集
     * @return string info[0].dynamic_list[].thumbs[] 图片链接
     * @return string info[0].dynamic_list[].video 视频链接
     * @return string info[0].dynamic_list[].video_t 视频封面
     * @return string info[0].dynamic_list[].voice 语音链接
     * @return string info[0].dynamic_list[].voice_l 语音时长
     * @return string info[0].dynamic_list[].location 位置
     * @return string info[0].dynamic_list[].datatime 时间
     * @return string info[0].dynamic_list[].skillid 技能ID，0为无技能
     * @return object info[0].dynamic_list[].skillinfo 技能信息
     * @return string info[0].dynamic_list[].skillinfo.method 方式
     * @return string info[0].dynamic_list[].skillinfo.name 技能名称
     * @return string info[0].dynamic_list[].skillinfo.thumb 技能图标
     * @return string info[0].dynamic_list[].skillinfo.coin 价格
     * @return array info[0].userlist 用户列表 
     * @return string info[0].userlist[].user_nickname 昵称
     * @return string info[0].userlist[].avatar 头像
     * @return string info[0].userlist[].sex 性别
     * @return string info[0].userlist[].age 年龄
     * @return string info[0].userlist[].addr 地址
     * @return string info[0].userlist[].profession 职业
     * @return array info[0].userlist[].list 技能
     * @return string info[0].userlist[].list[].name 名称
     * @return string info[0].userlist[].list[].colour_font 字颜色
     * @return string info[0].userlist[].list[].colour_bg 背景颜色
     * @return string msg 提示信息
     */
	public function getIndex() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = \App\checkNull($this->uid);
        
        /* 技能 */
        $Domain_skill = new Domain_Skill();
		$skilllist = $Domain_skill->getSkillList();
        
        $info['skilllist']=$skilllist;
        
        /* 轮播 */
        $domain = new Domain_Home();
		$silidelist = $domain->getSilide();
        $info['silidelist']=$silidelist;
        
        /* 动态列表 */
        $Domain_Dynamic = new Domain_Dynamic();
		$dynamic_list = $Domain_Dynamic->getList($uid,0,0,0,1,0);
        $info['dynamic_list']=$dynamic_list;
        
        /* 用户列表 */
        $list=$domain->getUsers($uid);
        $info['userlist']=$list;
		
		$active_list = $domain->getActive(3);
		$list=array_values($active_list);
		$info['active_list']=$list;
		
        $rs['info'][0] = $info;
		
        return $rs;
	}


    /**
     * 用户列表
     * @desc 用于获取用户列表(分页)
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[].user_nickname 昵称
     * @return string info[].avatar 头像
     * @return string info[].sex 性别
     * @return string info[].age 年龄
     * @return string info[].addr 地址
     * @return string info[].profession 职业
     * @return array  info[].list 技能
     * @return string info[].list[].name 名称
     * @return string info[].list[].colour_font 字颜色
     * @return string info[].list[].colour_bg 背景颜色
     * @return string msg 提示信息
     */
	public function getUsers() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid = \App\checkNull($this->uid);
        $p = \App\checkNull($this->p);
        
        /* 用户列表 */
        $domain = new Domain_Home();
        $list=$domain->getUsers($uid,$p);
        
        $rs['info'] = $list;
		
        return $rs;
	}

    /**
     * 搜索
     * @desc 用于获取搜索信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return array info[0].skilllist 技能列表
     * @return string info[0].skilllist[].id
     * @return string info[0].skilllist[].name 技能名
     * @return string info[0].skilllist[].thumb 图标
     * @return array info[0].list 用户列表
     * @return string info[0].list[].id 昵称
     * @return string info[0].list[].user_nickname 昵称
     * @return string info[0].list[].avatar 头像
     * @return string info[0].list[].sex 性别
     * @return string info[0].list[].age 年龄
     * @return string msg 提示信息
     */
	public function search() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $keyword = \App\checkNull($this->keyword);
        
        if($keyword==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('请输入关键词');
            return $rs;
        }
        
        /* 技能 */
        $Domain_skill = new Domain_Skill();
		$skilllist = $Domain_skill->getSkillList();
        
        foreach($skilllist as $k=>$v){
            if(!strstr($v['name'],$keyword)){
                unset($skilllist[$k]);
            }
        }
        $skilllist=array_values($skilllist);
        $info['skilllist']=$skilllist;
        
        /* 用户列表 */
        $domain = new Domain_Home();
        $list=$domain->searchUser($keyword);
        $info['list']=$list;
        
        $rs['info'][0] = $info;
		
        return $rs;
	}    
    
    /**
     * 搜索用户列表
     * @desc 用于获取搜索用户列表(分页)
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[].id 
     * @return string info[].user_nickname 昵称
     * @return string info[].avatar 头像
     * @return string info[].sex 性别
     * @return string info[].age 年龄
     * @return string msg 提示信息
     */
	public function searchMore() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $keyword = \App\checkNull($this->keyword);
        
        if($keyword==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('请输入关键词');
            return $rs;
        }
        
        /* 用户列表 */
        $domain = new Domain_Home();
        $list=$domain->searchUser($keyword);
        
        $rs['info'] = $list;
		
        return $rs;
	}

}
