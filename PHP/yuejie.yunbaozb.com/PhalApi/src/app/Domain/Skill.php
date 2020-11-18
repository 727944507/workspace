<?php
namespace App\Domain;

use App\Model\Skill as Model_Skill;
use App\Domain\Comment as Domain_Comment;

class Skill {
    /* 分类 */
    public function getClass() {
        
        $key='getSkillclass';
        $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Skill();
            $list=$model->getClass();
            if($list){
                \App\setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
                $v['name']=$v['name_en'];
            }
            unset($v['name_en']);
            $list[$k]=$v;
        }

        return $list;
    }

    /* 技能列表 */
    public function getSkillList() {
        $key='getSkilllist';
        $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Skill();
            $list=$model->getSkillList();
            if($list){
                \App\setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            $v['thumb']=\App\get_upload_path($v['thumb']);
            
            if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
                $v['name']=$v['name_en'];
            }
            unset($v['name_en']);
            
            $v['method']=\PhalApi\T($v['method']);
            unset($v['auth_tip']);
            unset($v['auth_tip_en']);
            unset($v['list_order']);
            $list[$k]=$v;
        }
        
        return $list;
    }
    
    /* 全部分类 */
    public function getAll(){
        $class = $this->getClass();
		$skilllist = $this->getSkillList();
        
        foreach($class as $k=>$v){
            $list=[];
            foreach($skilllist as $k1=>$v1){
                if($v['id']==$v1['classid']){
                    unset($v1['list_order']);
                    unset($v1['colour_font']);
                    unset($v1['colour_bg']);
                    unset($v1['classid']);
                    $list[]=$v1;
                }
            }
            $v['list']=$list;
            
            $class[$k]=$v;
        }
        
        return $class;
    }
    
    /* 选择技能认证 */
    public function getUserSkill($uid){
        $class = $this->getClass();
		$skilllist = $this->getSkillList();
        
        $where=[];
        $where['uid']=$uid;
        $order='id desc';
        
        $model = new Model_Skill();
        $authlist=$model->getSkillAuth($where,$order);
        
        foreach($class as $k=>$v){
            $list=[];
            foreach($skilllist as $k1=>$v1){
                if($v['id']==$v1['classid']){
                    $status='-1';
                    foreach($authlist as $k2=>$v2){
                        if($v2['skillid']==$v1['id']){
                            $status=$v2['status'];
                        }
                    }
                    $v1['status']=$status;
                    
                    unset($v1['list_order']);
                    unset($v1['colour_font']);
                    unset($v1['colour_bg']);
                    unset($v1['classid']);
                    $list[]=$v1;
                }
            }
            $v['list']=$list;
            
            $class[$k]=$v;
        }
        
        return $class;
    }

    /* 我的技能 */
    public function getMySkill($uid){
        
        $skilllist = $this->getSkillList();

        $where=[];
        $where['uid']=$uid;
        $where['status']=1;
        $order='id desc';
        
        $list=$this->getSkillAuth($where,$order);
        
        foreach($list as $k=>$v){
            foreach($skilllist as $k1=>$v1){
                if($v['skillid']==$v1['id']){
                    unset($v1['list_order']);
                    unset($v1['colour_font']);
                    unset($v1['colour_bg']);
                    $skill=$v1;
                }
            }
            $v['skill']=$skill;
            
            $list[$k]=$v;
        }
        
        return $list;
    }

    /* 价格列表 */
    public function getCoinList(){
        
        $key='skill_coinlist';
        // $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Skill();
            $list=$model->getCoinList();
            if($list){
                \App\setcaches($key,$list);
            }
        }

        return $list;
    }
    
    /* 某价格信息 */
    public function getCoin($id){
        $info=[];
        $list=$this->getCoinList();
        
        foreach($list as $k=>$v){
            if($v['id']==$id){
                $info=$v;
                break;
            }
        }

        return $info;
    }
    
    /* 可选价格 */
    public function getCoins($uid,$skillid){
        
        $list=$this->getCoinList();
        
        $where=[];
        $where['uid']=$uid;
        $where['skillid']=$skillid;
        
        $order='id desc';
        
        $orders='0';
        
        $model = new Model_Skill();
        $authlist=$model->getSkillAuth($where,$order);
        if($authlist){
            $orders=$authlist[0]['orders'];
        }
        
        foreach($list as $k=>$v){
            $canselect='0';
            if($v['orders']<=$orders){
                $canselect='1';
            }
            $v['canselect']=$canselect;
            $list[$k]=$v;
        }
        
        return $list;
        
    }
    
    /* 更新技能开关 */
    public function setSwitch($uid,$skillid,$data){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        
        $where=[];
        $where['uid']=$uid;
        $where['skillid']=$skillid;
        
        $order='id desc';
        
        $model = new Model_Skill();
        $authlist=$model->getSkillAuth($where,$order);
        if(!$authlist){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        $auth=$authlist[0];
        
        if($auth['status']!=1){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('请等待技能认证通过');
            return $rs;
        }
        
        if($auth['coin']==0){
            $rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('请先设置价格');
            return $rs;
        }
        
        $authlist=$model->upSkill($where,$data);
        
        $this->upUserSwitch($uid,$data['switch']);
        
        return $rs;
    }
    
    /* 更新用户表状态 */
    public function upUserSwitch($uid,$isswitch){
        
        $model = new Model_Skill();
        if($isswitch==0){
            
            $where=[];
            $where['uid']=$uid;
            $where['switch']=1;
            
            $order='id desc';
            
            
            $list=$model->getSkillAuth($where,$order);
            if($list){
                $isswitch=1;
            }
        }
        
        $res=$model->upUserSwitch($uid,$isswitch);
        
        return $res;
    }

    /* 更新技能信息 */
    public function upSkill($uid,$skillid,$fields){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $data=[];
        $info=[];
        /* 价格 */
        if( isset($fields['coin']) && $fields['coin']!=''  ){
            $coinid=$fields['coin'];
            
            $where=[];
            $where['uid']=$uid;
            $where['skillid']=$skillid;
            
            $order='id desc';
            
            $orders='0';
            
            $model = new Model_Skill();
            $authlist=$model->getSkillAuth($where,$order);
            if($authlist){
                $orders=$authlist[0]['orders'];
            }
            
            $coin='0';
            $list=$this->getCoinList();
            foreach($list as $k=>$v){
                if($v['id']==$coinid && $v['orders']<=$orders){
                    $coin=$v['coin'];
                    break;
                }
            }
            
            if(!$coin){
                $rs['code']=1003;
                $rs['msg']=\PhalApi\T('请选择正确的价格');
                return $rs;
            }

            if($coin){
                $data['coinid']=$coinid;
                $data['coin']=$coin;
                $info['coin']=$coin;
            }
        }
        
        /* 标签 */
        if( isset($fields['label']) && $fields['label']!=''  ){
            $label='';
            $label_a2=[];
            
            $label_a=preg_split('/,|，/',$fields['label']);
            $label_a=array_filter($label_a);
            
            $nums=count($label_a);
            if($nums>3){
                $rs['code']=1003;
                $rs['msg']=\PhalApi\T('最多选择三个标签');
                return $rs;
            }
            
            $list = $this->getLabel($skillid);
            foreach($label_a as $k=>$v){
                foreach($list as $k1=>$v1){
                    if($v==$v1['id']){
                        $label.=$v1['id'].';';
                        $label_a2[]=$v1['name'];
                    }
                }
            }
            if($label){
                $data['label']=$label;
                $info['label_a']=$label_a2;
            }
        }
        
        /* 段位 */
        if( isset($fields['level']) && $fields['level']!=''  ){
            $levelid2=$fields['level'];
            
            $levelinfo=$this->getLevelInfo($skillid,$levelid2);
        
            $level=isset($levelinfo['name']) ? $levelinfo['name'] : '';
            $levelid=isset($levelinfo['levelid']) ? $levelinfo['levelid'] : '0';
            
            if($levelid){
                $data['levelid']=$levelid;
                $info['levelid']=$levelid;
                $info['level']=$level;
            }
        }
        
        /* 截图 */
        if( isset($fields['thumb']) && $fields['thumb']!=''  ){
            $thumb=$fields['thumb'];

            $data['thumb']=$thumb;
            $info['thumb']=\App\get_upload_path($thumb);

        }
        
        /* 语音 */
        if( isset($fields['voice']) && $fields['voice']!=''  ){
            $voice=$fields['voice'];
            $voice_l=$fields['voice_l'];

            $data['voice']=$voice;
            $data['voice_l']=$voice_l;
            $info['voice']=\App\get_upload_path($voice);
            $info['voice_l']=$voice_l;

        }
        
        /* 介绍 */
        if( isset($fields['des']) && $fields['des']!=''  ){
            $des=$fields['des'];
            
            if(mb_strlen($des)>30){
                $rs['code']=1003;
                $rs['msg']=\PhalApi\T('介绍最多30个字');
                return $rs;
            }

            $data['des']=$des;
            $info['des']=$des;

        }
        
        if(!$data){
            return $rs;
        }
        
        $where=[];
        $where['uid']=$uid;
        $where['skillid']=$skillid;
        $where['status']='1';

        
        $model = new Model_Skill();
        $authlist=$model->upSkill($where,$data);
        
        $rs['info'][0]=$info;
        
        return $rs;
    }
    
    
    /* 更新星级、评论 */
    public function upStar($where,$star=1,$comments=1) {
        
        if(!$where){
            return 0;
        }
        $model = new Model_Skill();
        $list=$model->upStar($where,$star,$comments);

        return $rs;
    }
    
    /* 更新订单数 */
    public function upOrsers($where,$orders=1) {
        
        if(!$where){
            return 0;
        }
        
        $model = new Model_Skill();
        $list=$model->upOrsers($where,$orders);

        return 1;
    }
    
    /* 技能信息 */
    public function getSkill($id) {
        $info=[];
        $list=$this->getSkillList();
        
        foreach($list as $k=>$v){
            if($v['id']==$id){
                $info=$v;
                break;
            }
        }

        return $info;
    }

    /* 技能段位 */
    public function getLevel($skillid) {
        
        $key='skillLevel_'.$skillid;
        $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Skill();
            $list=$model->getLevel($skillid);
            if($list){
                \App\setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
                $v['name']=$v['name_en'];
            }
            unset($v['name_en']);
            $list[$k]=$v;
        }
        
        return $list;
    }
    
    /* 某段位信息 */
    public function getLevelInfo($skillid,$levelid) {
        
        $levellist=$this->getLevel($skillid);
        
        $level=[];
        foreach($levellist as $k=>$v){
            if($levelid==$v['levelid']){
                $level=$v;
                break;
            }
        }
        
        return $level;
    }

    /* 技能标签 */
    public function getLabel($skillid,$ifunset=1) {
        
        $key='skillLabel_'.$skillid;
        $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Skill();
            $list=$model->getLabel($skillid);
            if($list){
                \App\setcaches($key,$list);
            }
        }
        if($ifunset){
            foreach($list as $k=>$v){
                if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
                    $v['name']=$v['name_en'];
                }
                unset($v['name_en']);
                $list[$k]=$v;
            }
        }
        

        return $list;
    }
	
	/* 技能标签 */
	public function getAllLabel($uid,$ifunset=1) {
	    
	    $key='AllLabel_'.$uid;
	    $list=\App\getcaches($key);
	    if(!$list){
	        $model = new Model_Skill();
	        $list=$model->getAllLabel($uid);
	        if($list){
	            \App\setcaches($key,$list);
	        }
	    }
	    if($ifunset){
	        foreach($list as $k=>$v){
	            if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
	                $v['name']=$v['name_en'];
	            }
	            unset($v['name_en']);
	            $list[$k]=$v;
	        }
	    }
	    
	    return $list;
	}

    /* 技能标签统计 */
    public function getLabelNums($uid,$skillid) {
        
        $model = new Model_Skill();
        $list=$model->getLabelNums($uid,$skillid);
        
        $labellist=$this->getLabel($skillid);
        foreach($list as $k=>$v){
            $label='';
            foreach($labellist as $k1=>$v1){
                if($v['labelid']==$v1['id']){
                    $label=$v1['name'];
                }
            }
            $v['label']=$label;
            
            $list[$k]=$v;
        }
        return $list;
    }

    /* 更新技能标签统计 */
    public function upLabelNums($uid,$skillid,$labelid) {
        
        $model = new Model_Skill();
        $list=$model->upLabelNums($uid,$skillid,$labelid);
        
        return $list;
    }

    /* 用户单技能信息 */
    public function getAuthInfo($where) {
        
        $model = new Model_Skill();
        $info=$model->getAuthInfo($where);
        
        return $info;
    }

    /* 用户技能列表 */
    public function getSkillAuth($where,$order='id desc') {
        
        $model = new Model_Skill();
        $list=$model->getSkillAuth($where,$order);
        
        foreach($list as $k=>$v){
            $v=$this->handelAuth($v);
            $list[$k]=$v;
        }
        
        return $list;
    }

    /* 技能下用户列表 */
    public function getUserList($uid,$skillid,$order='0',$sex='0',$level='0',$voice='0') {
        
        $where=[];
        
        $where['status']='1';
        $where['switch']='1';
        $where['skillid']=$skillid;
        $where['uid!=?']=$uid;
        
        if($sex!=0){
            $where['sex']=$sex;
        }
        if($level!='0'){
            $where['levelid']=$level;
        }
        if($voice!=0){
            $where['voice !=?']='';
        }
        
        $model = new Model_Skill();
        
        if($order!=0){
            $order='uptime desc';
            $list=$model->getSkillAuth($where,$order);
        }else{
            $list=$model->getRecomAuth($where);
        }
        
        foreach($list as $k=>$v){
            $v=$this->handelAuth($v);
            $userinfo=\App\getUserInfo($v['uid']);
			if($userinfo['user_status']=='3'){
				unset($list[$k]);
				continue;
			}
            unset($userinfo['birthday']);
            $v['userinfo']=$userinfo;
            unset($v['thumb']);
            unset($v['switch']);
            unset($v['label']);
            unset($v['voice']);
            unset($v['des']);
            unset($v['recom']);

            $list[$k]=$v;
        }
        $list=array_values($list);
        return $list;
    }
    

    /* 技能主页 */
    public function getSkillHome($uid,$liveuid,$skillid){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $skillinfo=$this->getSkill($skillid);
        
        if(!$skillinfo){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('技能不存在');
            return $rs;
        }
        
        $where_a=[];
        $where_a['uid']=$liveuid;
        $where_a['skillid']=$skillid;
        $where_a['switch']='1';
        
        $order_a='id desc';
        
        $auth=$this->getSkillAuth($where_a,$order_a);
        if(!$auth){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('对方未认证此技能');
            return $rs;
        }
        
        $authinfo=$auth[0];
        
        $label=$this->getLabelNums($liveuid,$skillid);
        
        $Domain_Comment=new Domain_Comment();
        $where_c=[];
        $where_c['liveuid']=$liveuid;
        $where_c['skillid']=$skillid;
        
        $order_c='id desc';
        
        $comment_nums=$Domain_Comment->getCommentNums($where_c);
        
        $comment_list=$Domain_Comment->getComment(1,$where_c,$order_c);
        
        
        $info=\App\getUserInfo($liveuid);
        
        unset($info['birthday']);
        
        $isattent=\App\isAttent($uid,$liveuid);
            
        $info['isattent']=$isattent;
        
        $info['skill']=$skillinfo;
        $info['authinfo']=$auth[0];
        
        $info['label_list']=$label;
        $info['comment_nums']=$comment_nums;
        $info['comment_list']=$comment_list;
        
        
        $rs['info'][0]=$info;
        return $rs;
    }

    protected function handelAuth($v){
        $v['thumb']=\App\get_upload_path($v['thumb']);
        $v['voice']=\App\get_upload_path($v['voice']);
        $v['star_level']=(string)round($v['stars']);
        

        $label_a=preg_split('/;|；/',$v['label']);
        $label_a=array_filter($label_a);
        $label=[];
        $labellist=$this->getLabel($v['skillid']);
        foreach($label_a as $k1=>$v1){
            foreach($labellist as $k2=>$v2){
                if($v1==$v2['id']){
                    $label[]=$v2['name'];
                }
            }
        }
        
        $v['label_a']=$label;
        
        $skillinfo=$this->getSkill($v['skillid']);
        
        $v['method']=isset($skillinfo['method']) ? $skillinfo['method'] : '';
        $v['skillname']=isset($skillinfo['name']) ? $skillinfo['name'] : '';
        
        /* 段位 */
        $levelinfo=$this->getLevelInfo($v['skillid'],$v['levelid']);
        
        $level=isset($levelinfo['name']) ? $levelinfo['name'] : '';
        $v['level']=$level;
        
        unset($v['label']);
        unset($v['status']);
        unset($v['reason']);
        unset($v['addtime']);
        unset($v['uptime']);
        unset($v['star']);
        unset($v['comments']);
        
        return $v;
    }
    
    /* 某技能认证信息 */
    public function getSkillInfo($liveuid,$skillid){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $skillinfo=$this->getSkill($skillid);
        
        if(!$skillinfo){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('技能不存在');
            return $rs;
        }
        
        $where_a=[];
        $where_a['uid']=$liveuid;
        $where_a['skillid']=$skillid;
        $where_a['status']='1';
        
        $order_a='id desc';
        
        $auth=$this->getSkillAuth($where_a,$order_a);
        if(!$auth){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('对方未认证此技能');
            return $rs;
        }
        
        $authinfo=$auth[0];
        
        /* if($authinfo['switch']!=1){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('对方未开启此技能');
            return $rs;
        } */
        
        $info['skill']=$skillinfo;
        $info['authinfo']=$auth[0];
        
        $rs['info'][0]=$info;
        return $rs;
    }
	/* 我的技能列表 */
    public function getMyskillList($uid){
        $where=[];
        $where['uid']=$uid;
        $where['status']=1;
        $where['switch']=1;
        $order='id desc';
        $model = new Model_Skill();
        $list=$model->getMyskillList($where,$order);
        return $list;
    }
	 /* 我的技能列表 */
    public function getByidslist($where){
        $model = new Model_Skill();
        $list=$model->getByidslist($where);
       
        return $list;
    }
}
