<?php
namespace App\Domain;

use App\Model\User as Model_User;
use App\Domain\Skill as Domain_Skill;

class User {

    /* 用户基本信息 */
    public function getBaseInfo($uid) {
        
        $model = new Model_User();
        $info = $model->getBaseInfo($uid);
        
        if($info){
            //$birthday=$info['birthday'];
            $info=\App\handleUser($info);
            $info['follows']=\App\getFollowNum($uid);
            $info['fans']=\App\getFansNum($uid);
            //$info['birthday']=date('Y-m-d',$birthday);
            unset($info['birthday']);
        }

        return $info;
    }

    /* 是否实名认证 */
    public function isauth($uid) {
        
        $model = new Model_User();
        $rs = $model->isauth($uid);

        return $rs;
    }
    
    /* 更新基本信息 */
    public function upUserInfo($uid,$fields=[]) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());

        $model = new Model_User();
        $data=[];
        $info=[];
        /* 头像 */
        if( isset($fields['avatar']) && $fields['avatar']!=''  ){
            $avatar_q=$fields['avatar'];
            
            if(!strstr($avatar_q,'?')){
                $avatar=  $avatar_q.'?imageView2/2/w/600/h/600'; //600 X 600
                $avatar_thumb=  $avatar_q.'?imageView2/2/w/200/h/200'; // 200 X 200
            }else{
                $avatar=$avatar_q;
                $avatar_thumb=$avatar_q;
            }
            


            $data['avatar']=$avatar;
            $data['avatar_thumb']=$avatar_thumb;

            $info['avatar']=\App\get_upload_path($avatar);
            $info['avatar_thumb']=\App\get_upload_path($avatar_thumb);
            
        }
        
        
        /* 昵称 */
        if( isset($fields['user_nickname']) && $fields['user_nickname']!=''  ){
            $name=$fields['user_nickname'];
            $count=mb_strlen($name);
            if($count>10){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('昵称最多10个字');
                return $rs;
            }
            
            $isexist = $model->checkNickname($uid,$name);
            if($isexist){
                $rs['code'] = 1003;
                $rs['msg'] = \PhalApi\T('昵称已存在');
                return $rs;
            }
            
            $data['user_nickname']=$name;
            $info['user_nickname']=$name;
        }
        /* 声音 */
        if( isset($fields['voice']) && $fields['voice']!=''  ){
            $voice=$fields['voice'];
            $voice_l=$fields['voice_l'];
            
            $data['voice']=$voice;
            $data['voice_l']=$voice_l;
            $info['voice']=\App\get_upload_path($voice);
            $info['voice_l']=$voice_l;
        }else{
			$data['voice']="";
            $data['voice_l']=0;
            $info['voice']="";
            $info['voice_l']=0;
		}
        /* 生日 年龄 星座 */
        if( isset($fields['birthday']) && $fields['birthday']!=''  ){
            $birthday=strtotime($fields['birthday']);
            $age=\App\getAge($birthday);
            $constellation=\App\getConstellation($birthday);
            
            $data['birthday']=$birthday;

            $info['birthday']=$birthday;
            $info['age']=$age;
            $info['constellation']=$constellation;
        }
        
        /* 性别 */
        if( isset($fields['sex']) && $fields['sex']!=''  ){
            $sex=$fields['sex'];
            
            $isexist = $model->checkSex($uid);
            if(!$isexist){
                $data['sex']=$sex;
                $info['sex']=$sex;
            }
        }
        
        /* 家乡 */
        if( isset($fields['addr'])){
			if($fields['addr']!=''){
				$addr=$fields['addr'];
            
				$data['addr']=$addr;
				$info['addr']=$addr;
			}else{
				$data['addr']="";
				$info['addr']="";
			}
            
        }
        
        /* 签名 */
        if( isset($fields['signature'])){
			if($fields['signature']!=''){
				 $signature=$fields['signature'];
            
				$data['signature']=$signature;
				$info['signature']=$signature;
			}else{
				$data['signature']="";
				$info['signature']="";
			}
           
        }
        
        /* 兴趣 */
        if( isset($fields['hobby']) ){
			if($fields['hobby']!=''){
				$hobbyid='';
				$hobby='';
				
				$hobby_a=preg_split('/,|，/',$fields['hobby']);
				$hobby_a=array_filter($hobby_a);
				
				$nums=count($hobby_a);
				if($nums>5){
					$rs['code']=1003;
					$rs['msg']=\PhalApi\T('最多选择五个兴趣');
					return $rs;
				}
				
				$list = \App\getHobby();
				foreach($hobby_a as $k=>$v){
					foreach($list as $k1=>$v1){
						if($v==$v1['id']){
							$hobbyid.=$v1['id'].',';
							// $hobby.=$v1['name'].' ';
							$hobby.=$v1['name'].';';
						}
					}
				}
				if($hobbyid){
					$data['hobby']=$hobbyid;
					$info['hobby']=$hobby;
				}
			}
        }
        
        /* 职业 */
        if( isset($fields['profession']) ){
			if($fields['profession']!=''){
				$profession=$fields['profession'];
            
				$data['profession']=$profession;
				$info['profession']=$profession;
			}else{
				$data['profession']="";
				$info['profession']="";
			}
            
        }
        
        /* 学校 */
        if( isset($fields['school'])){
			if($fields['school']!=''){
				$school=$fields['school'];
            
				$data['school']=$school;
				$info['school']=$school;
			}else{
				$data['school']="";
				$info['school']="";
			}
        }
        
        if(!$data){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs; 
        }
        
        $result = $model->upUserInfo($uid,$data);
        
        \App\delcache("userinfo_".$uid);
        
        $rs['info'][0]=$info;
        return $rs;
    }

    /* 更新星级 */
    public function upStar($uid,$stars,$nums){
        
        $model = new Model_User();
        

        $res=$model->upStar($uid,$stars,$nums);

        return $res;
    }
    
    /* 关注、取关 */
    public function setAttent($uid,$touid){
        
        $model = new Model_User();
        
        $isattent=\App\isAttent($uid,$touid);
        if($isattent){
            /* 已关注 取消 */
            $model->delAttent($uid,$touid);
            $isattent='0';
        }else{
            /* 未关注 关注 */
            $model->setAttent($uid,$touid);
            $isattent='1';
        }
        
        return $isattent;
    }

    /* 关注列表 */
    public function getFollow($uid,$touid,$p){
        
        $where=[
            'uid'=>$touid
        ];

        $model = new Model_User();
        $list = $model->getAttention($where,$p);
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($v['touid']);
            unset($userinfo['birthday']);
            $isattent='0';
            if($uid==$touid){
                $isattent='1';
            }else{
                $isattent=\App\isAttent($uid,$v['touid']);
            }
            $userinfo['isattent']=$isattent;
            $list[$k]=$userinfo;
        }
        
        return $list;
    }
 /* 用户列表
    * type 类型 0推荐 1关注 2最新
    */
	public function getList($uid,$sex,$age,$skillid,$p,$type=0) {
        $uids_s='';
        $attent_a=[];
		$model = new Model_User();
		$where='user_status=1 and user_type=2 ';
        if($type==1){
            $where1=[
                'uid'=>$uid,
            ];
			$order1='addtime desc';
            $attentlist = $model->getAllAttention($where1,$order1);
			
            if(!$attentlist){
                return [];
            }
            $attent_a=array_column($attentlist,'touid');
            $attent_s=implode(',',$attent_a);
            $uids_s=$attent_s;
			if($uids_s){
				$where.=" and id in ({$uids_s})";
			}
        }
		
        // $where='status=1 and (type=1 or type=2)';
        $Domain_Skill = new Domain_Skill();
        if($sex!=0){
            if($sex==1){
                $where.=' and sex=1';
            }else{
                $where.=' and sex!=1';
            }
        }
        if($age!=0){
            $time=\App\getAges($age);
            if(!$time){
                return [];
            }
            $where.=" and birthday>={$time[0]} and birthday<{$time[1]}";
        }
        
        
        if($skillid!=0){
			$where3="skillid={$skillid} and status=1 and switch=1";
			
			$skillids=$Domain_Skill->getByidslist($where3);
			
			
			if(!$skillids){
                return [];
            }
            $killuser_a=array_column($skillids,'uid');
            $killuser_s=implode(',',$killuser_a);
            $uids_s1=$killuser_s;
			if($uids_s1){
				$where.=" and id in ({$uids_s1})";
			}
		
			/* $where.=" and skillid={$skillid}";
			$where.=" and id in ({$uids_s})"; */
        }
       
        
        if($type==0){//推荐
			$order='recommend_time desc,orders desc';
            
        }else if($type==2){//最新认证成功的用户（最近三天）
			$threedaystime=time()-3*24*60*60;
            $where2="status=1 and uptime > {$threedaystime}";
		
			$order2='uptime desc';
          
            $authlist = $model->getAuthlist($where2,$order2);
			
            if(!$authlist){
                return [];
            }
            $auth_a=array_column($authlist,'uid');
            $auth_s=implode(',',$auth_a);
            $uids_s2=$auth_s;
			if($uids_s2){
				$where.=" and id in ({$uids_s2})";
			}
			
        }
        $list= $model->getList($where,$order,$p);
		
		
        foreach($list as $k=>$v){
			$labelArr=$model->getLabel($v['id']);
			$skill_list=$Domain_Skill->getMyskillList($v['id']);
            $v['skill_list']=$skill_list;
			$v['skill'] = $labelArr;
            $list[$k]=$v;
        }
        return $list;
    }
	
	/*附近人列表*/
	public function getNearby($uid,$lng,$lat,$p) {
		
		$model = new Model_User();
		$Domain_Skill = new Domain_Skill();
		
		$list = $model->getNearby($uid,$lng,$lat,$p);
		foreach($list as $k=>$v){
			$distance = \App\calcDistance($lat,$lng,$v['lat'],$v['lng']);
			$labelArr=$model->getLabel($v['id']);
			$skill_list=$Domain_Skill->getMyskillList($v['id']);
		    $v['skill_list']=$skill_list;
			$v['skill'] = $labelArr;
			$v['distance'] = sprintf("%.2f", $distance).'km';
		    $list[$k]=$v;
		}
		
		return $list;
	}
	
	

    /* 所有关注、粉丝用户 */
    public function getAllAttention($where){
        
        $model = new Model_User();
        $list = $model->getAllAttention($where);
        
        return $list;
    }
    
    /* 粉丝列表 */
    public function getFans($uid,$touid,$p){
        
        $where=[
            'touid'=>$touid
        ];
        
        $model = new Model_User();
        $list = $model->getAttention($where,$p);
        
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($v['uid']);
            unset($userinfo['birthday']);
            $isattent=\App\isAttent($uid,$v['uid']);
            
            $userinfo['isattent']=$isattent;
            $list[$k]=$userinfo;
        }
        
        return $list;
        
    }

    /* 个人主页 */
    public function getHome($uid,$touid){


        $info=\App\getUserInfo($touid);
        
        $isattent=\App\isAttent($uid,$touid);
		$isblack=\App\isBlack($uid,$touid);
            
        $info['isattent']=$isattent;
		$info['isblack']=$isblack;
        
        $info['fans']=\App\getFansNum($touid);

        $des=[];
        if($info['addr']){
            $des[]=\PhalApi\T('来自{n}',[ 'n'=>$info['addr'] ]);
        }
        
        if($info['birthday']){
            $y=date('y',$info['birthday']);
            $y0=substr($y,0,1).'0';
            $sex=\PhalApi\T('女生');
            if($info['sex']==1){
                $sex=\PhalApi\T('男生');
            }
            $des[]=\PhalApi\T('{n}后',['n'=>$y0]).$info['constellation'].$sex;
        }
        unset($info['birthday']);
        
        if($info['hobby']){
            $hobby=explode(';',$info['hobby']);
            $hobby=array_filter($hobby);
            $des[]=\PhalApi\T('喜欢{n}',[ 'n'=>implode('/',$hobby) ]);
        }
        
        if($info['profession']){
            $des[]=\PhalApi\T('从事{n}',[ 'n'=>$info['profession'] ]);
        }
        
        if($info['school']){
            $des[]=\PhalApi\T('毕业于{n}',[ 'n'=>$info['school'] ]);
        }
        
        $info['des']=implode('，',$des);
        
        /* 技能 */
        $where=[
            'uid'=>$touid,
            'status'=>'1',
            'switch'=>'1',
        ];
        
        $order='id desc';
        if($info['user_status']=='3'){
			$info['list']=array();
		}else{
			$Domain_Skill = new Domain_Skill();
			$list = $Domain_Skill->getSkillAuth($where);
			$info['list']=$list;
        }
        
        
        return $info;
        
    }
    
    /* 根据条件获取用户ID */
    public function getUsers($where){
        
        $model = new Model_User();
        $list = $model->getUsers($where);
        
        return $list;
    }

    /* 检测关系 */
    public function checkAttent($uid,$touid){
        
        $status='0';
        
        $isattent1=\App\isAttent($uid,$touid);
        if($isattent1){
            $status='1';
        }
        $isattent2=\App\isAttent($touid,$uid);
        
        if($isattent2){
            $status='2';
        }
        
        if($isattent1 && $isattent2){
            $status='3';
        }
        
        return $status;
    }
    
    /* 检测是否主持人 */
    public function ishost($uid){
        
        $model = new Model_User();
        $rs = $model->ishost($uid);
        
        return $rs;
    }
	/* 举报列表 */
	public function getReport() {
        
        $model = new Model_User();

        $list= $model->getReport();
        
        return $list;
    }
	
	/* 举报内容 */
	public function setReport($uid,$touid,$content) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('举报成功'), 'info' => array());
       
        $info=\App\getUserInfo($touid);
        if(!$info){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('用户不存在');
            return $rs;
        }
        
        $data=[
            'uid'=>$uid,
            'touid'=>$touid,
            'content'=>$content,
            'addtime'=>time(),
        ]; 
        $model = new Model_User();

        $res= $model->setReport($data);
        if(!$res){
            $rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('举报失败，请重试');
            return $rs;
        }
        return $rs;
    }
	//拉黑/取消用户
	public function setBlack($uid,$touid) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->setBlack($uid,$touid);

		return $rs;
	}
	
	//黑名单
	public function blackList($uid,$p) {
		$where=[
            'uid'=>$uid
        ];
        
        $model = new Model_User();
        $list = $model->blackList($where,$p);
       
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($v['touid']);
            $list[$k]=$userinfo;
        }
        
        return $list;
        
	}
}
