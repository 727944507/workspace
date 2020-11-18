<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {

	/* 用户全部信息 */
	public function getBaseInfo($uid) {
		$info=\PhalApi\DI()->notorm->user
				->select("id,user_nickname,avatar,avatar_thumb,sex,signature,coin,votes,consumption,votestotal,votes_gift,votes_gifttotal,birthday,profession,school,hobby,voice,voice_l,addr,stars,star_nums")
				->where('id=?  and user_type=2',$uid)
				->fetchOne();
		return $info;
	}
    
    /* 是否认证 */
    public function isauth($uid){
        $isexist=\PhalApi\DI()->notorm->user_auth
				->select("uid")
				->where('uid =?  and status=1',$uid)
				->fetchOne();
        if($isexist){
            return '1';
        }
        
        return '0';
    }
    
    /* 检测用户昵称 */
    public function checkNickname($uid,$name){
        $isexist=\PhalApi\DI()->notorm->user
				->select("id")
				->where('id!=?  and user_nickname=?',$uid,$name)
				->fetchOne();
        if($isexist){
            return 1;
        }
        
        return 0;
    }

    /* 检测性别 */
    public function checkSex($uid){
        $isexist=\PhalApi\DI()->notorm->user
				->select("sex")
				->where('id!=?',$uid)
				->fetchOne();
        if($isexist && $isexist['sex']!=0){
            return 1;
        }
        
        return 0;
    }
    
    /* 用户信息更新 */
	public function upUserInfo($uid,$data){
        $rs=0;
        if($data){
            $rs=\PhalApi\DI()->notorm->user
                ->where('id=? ',$uid)
                ->update($data);
        }
        
        return $rs;
	}
    
    /* 更新星级 */
    public function upStar($uid,$stars,$nums) {

		$rs=\PhalApi\DI()->notorm->user
                ->where('id=?',$uid)
				->update(['stars' => new \NotORM_Literal("stars + {$stars}"),'star_nums' => new \NotORM_Literal("star_nums + {$nums}")]);
        
        $key='userinfo_'.$uid;
        
        $info=\App\hGetAll("userinfo_".$uid);
        if($info){
            \App\hIncrByFloat($key,'stars',$stars);
            \App\hIncrByFloat($key,'star_nums',$nums);
        }
        
            
        return $rs;
	}
    
    /* 设置关注 */
    public function setAttent($uid,$touid){
        
        $data=[
            'uid'=>$uid,
            'touid'=>$touid,
            'addtime'=>time(),
        ];
        \PhalApi\DI()->notorm->user_black
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
        $list=\PhalApi\DI()->notorm->user_attention
				->insert($data);
        
        return $list;
    }

    /* 取消关注 */
    public function delAttent($uid,$touid){
        
        $where=[
            'uid'=>$uid,
            'touid'=>$touid,
        ];
        
        $list=\PhalApi\DI()->notorm->user_attention
                ->where($where)
				->delete();
        
        return $list;
    }

    /* 关注列表 */
    public function getAttention($where,$p){
        
        if($p<1){
            $p=1;
        }
        
        $nums=50;
        $start=($p-1) * $nums;
        
        $list=\PhalApi\DI()->notorm->user_attention
				->select('*')
                ->where($where)
				->order('addtime desc')
                ->limit($start,$nums)
				->fetchAll();
        
        return $list;
    }
    
    /* 所有关注、粉丝用户 */
    public function getAllAttention($where,$order=''){
        
        $list=\PhalApi\DI()->notorm->user_attention
				->select('*')
                ->where($where)
				->order($order)
				->fetchAll();
        
        return $list;
    }
    
    /* 根据条件获取用户ID */
    public function getUsers($where){
        
        $list=\PhalApi\DI()->notorm->user
				->select('id')
                ->where($where)
				->fetchAll();
        
        return $list;
    }
    
    /* 是否派单主持人 */
    public function ishost($uid){
        $isexist=\PhalApi\DI()->notorm->user
				->select("id")
				->where('id =?  and ishost=1',$uid)
				->fetchOne();
        if($isexist){
            return '1';
        }
        
        return '0';
    }
	/* 举报列表 */
    public function getReport() {
		
		$list=\PhalApi\DI()->notorm->user_reportcat
                    ->select('*')
                    ->order('list_order')
					->fetchAll();
		
		return $list;
	}

    /* 举报 */
    public function setReport($data) {
		
		$list=\PhalApi\DI()->notorm->user_report
					->insert($data);
		
		return $list;
	}
	/* 拉黑/取消用户 */
	public function setBlack($uid,$touid){
		$isexist=\PhalApi\DI()->notorm->user_black
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			\PhalApi\DI()->notorm->user_black
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
			return 0;
		}else{
			\PhalApi\DI()->notorm->user_attention
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
			\PhalApi\DI()->notorm->user_black
				->insert(array("uid"=>$uid,"touid"=>$touid));

			return 1;
		}			 
	}
	
	/* 标签查询 */
	public function getLabel($uid){
		
		$skill_auth=\PhalApi\DI()->notorm->skill_auth
					->select("label,coin,skillid,voice")
					->where('uid=? and label != ""',$uid)
					->fetchOne();
					
		$skill=\PhalApi\DI()->notorm->skill
					->select("method")
					->where('id=?',$skill_auth['skillid'])
					->fetchOne();
					
		if($skill_auth){
			$label= $skill_auth['label'];
		}else{
			$label = '';
		}
		
		$newLabelArr = [];
		if($label!=''){
			$labelArr = explode(';',$label);
			foreach($labelArr as $v){
				$labelInfo=\PhalApi\DI()->notorm->label
							->select('id,name')
							->where('id=?',$v)
							->fetchOne();
				if($labelInfo){
					$newLabelArr['tag']['skillAuth'.$labelInfo['id']] = $labelInfo['name'];
				}
			}
			$newLabelArr['price']['price'] = $skill_auth['coin'].'币/'.$skill['method'];
			//$newLabelArr['voice']['voice'] = $skill_auth['voice'];
		}	
		return $newLabelArr;	 
	}
	
	/* 附近人查询 */
	public function getNearby($uid,$lng,$lat,$p){
						
			if($p<1){
				$p=1;
			}
			$nums=50;
			$start=($p-1) * $nums;
			$radius = 10000;//10000m
			$scope = \App\calcScope($lat, $lng, $radius);   // 调用范围计算函数，获取最大最小经纬度
			$where="user_status=1 and user_type=2 ";
			//var_dump($scope);die;
			/* 用户获取到的经纬度保存到数据库*/
			$userInfo = \PhalApi\DI()->notorm->user
						->where('id',$uid)
						->update(['lat'=>$lat,'lng'=>$lng]);
			
			/** 查询经纬度在 $radius 范围内的电站的详细地址 */
			$list=\PhalApi\DI()->notorm->user
						->select('id')
						->where('`lat` < '.$scope['maxLat'].' and `lat` > '.$scope['minLat'].' and `lng` < '.$scope['maxLng'].' and `lng` > '.$scope['minLng'])
						->where($where)
						->where('id !=?',$uid)
						->limit($start,$nums)
						->fetchAll();
				
			foreach($list as $k=>$v){
				$userinfo=\App\getUserInfo($v['id']);
				
				if($userinfo){
					unset($userinfo['birthday']);
					$isattent='0';
					if($uid==$touid){
						$isattent='1';
					}else{
						$isattent=\App\isAttent($uid,$v['touid']);
					}
					$userinfo['isattent']=$isattent;
					$list[$k]=$userinfo;
				}else{
					unset($list[$k]);
					continue;
				}
			}
			$list=array_values($list);
			
			return $list;
	}
	
	
	/* 黑名单列表 */
    public function blackList($where,$p){
        
        if($p<1){
            $p=1;
        }
        
        $nums=50;
        $start=($p-1) * $nums;
        
        $list=\PhalApi\DI()->notorm->user_black
				->select('*')
                ->where($where)
                ->limit($start,$nums)
				->fetchAll();
        
        return $list;
    }
	/* 用户列表：type：0：推荐；1：关注；2：最新 */
    public function getList($where,$order,$p){
        
        if($p<1){
            $p=1;
        }
        $nums=50;
        $start=($p-1) * $nums;
	
        $list=\PhalApi\DI()->notorm->user
				->select('id')
                ->where($where)
				->order($order)
                ->limit($start,$nums)
				->fetchAll();
	
        foreach($list as $k=>$v){
			$userinfo=\App\getUserInfo($v['id']);
			
			if($userinfo){
				unset($userinfo['birthday']);
				$isattent='0';
				if($uid==$touid){
					$isattent='1';
				}else{
					$isattent=\App\isAttent($uid,$v['touid']);
				}
				$userinfo['isattent']=$isattent;
				$list[$k]=$userinfo;
			}else{
				unset($list[$k]);
				continue;
			}
		}
		$list=array_values($list);
		//var_dump($list);die;
        return $list;
    }
	
	/* 最新认证三天的用户 */
    public function getAuthlist($where,$order){
       
        $list=\PhalApi\DI()->notorm->user_auth
				->select('*')
                ->where($where)
				->order($order)
				->fetchAll();
        return $list;
    }
}
