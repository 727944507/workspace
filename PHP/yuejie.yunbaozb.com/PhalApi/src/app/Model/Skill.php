<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Skill extends NotORM {

    /* 技能分类 */
    public function getClass() {
        $list=\PhalApi\DI()->notorm->skill_class
				->select('id,name,name_en')
				->order('list_order asc')
				->fetchAll();
        return $list;
    }
    
    /* 技能列表 */
    public function getSkillList() {
        
        $list=\PhalApi\DI()->notorm->skill
				->select('*')
				->order('list_order asc, classid asc')
				->fetchAll();
        
        return $list;
    }

    /* 技能段位 */
    public function getLevel($skillid) {
        
        $list=\PhalApi\DI()->notorm->skill_level
				->select('levelid,name,name_en')
                ->where('skillid=?',$skillid)
				->order('levelid asc')
				->fetchAll();
        
        return $list;
    }
    
    /* 价格列表 */
    public function getCoinList() {
        
        $list=\PhalApi\DI()->notorm->skill_coin
				->select('*')
				->order('coin asc')
				->fetchAll();
        
        return $list;
    }
    
    /* 认证的技能 */
    public function getAuthInfo($where) {

        $info=\PhalApi\DI()->notorm->skill_auth
				->select('*')
                ->where($where)
				->fetchOne();
        
        return $info;
    }
    
    /* 认证的技能 */
    public function getSkillAuth($where,$order) {

        $list=\PhalApi\DI()->notorm->skill_auth
				->select('*')
                ->where($where)
				->order($order)
				->fetchAll();
        
        return $list;
    }
    
    /* 认证的技能 -智能排序 */
    public function getRecomAuth($where) {
        $configpri=\App\getConfigPri();
        
        $skill_recom_star=floatval($configpri['skill_recom_star']);
        $skill_recom_orders=floatval($configpri['skill_recom_orders']);
        $skill_recom_coin=floatval($configpri['skill_recom_coin']);

        $list=\PhalApi\DI()->notorm->skill_auth
				->select("*,floor( ( stars * {$skill_recom_star} + orders * {$skill_recom_orders} + coin * {$skill_recom_coin} ) * 100 ) as recom")
                ->where($where)
				->order('recom desc')
				->fetchAll();
        
        return $list;
    }
    
    /* 更新我的技能信息 */
    public function upSkill($where,$data) {

        $rs=\PhalApi\DI()->notorm->skill_auth
                ->where($where)
				->update($data);
        
        return $rs;
    }
    
    /* 更新星级、评论 */
    public function upStar($where,$star=1,$comments=1) {

        $rs=\PhalApi\DI()->notorm->skill_auth
                ->where($where)
				->update(['star' => new \NotORM_Literal("star + {$star}"),'comments' => new \NotORM_Literal("comments + {$comments}")]);
        if($rs){
            $info=\PhalApi\DI()->notorm->skill_auth
                ->select('star,comments')
                ->where($where)
                ->fetchOne();
                
            $stars=\App\getLevel($info['star'],$info['comments']);
            
            \PhalApi\DI()->notorm->skill_auth
                ->where($where)
				->update(['stars' => $stars]);
        }
        
        
        return $rs;
    }
    
    /* 更新订单数 */
    public function upOrsers($where,$orders=1) {

        $rs=\PhalApi\DI()->notorm->skill_auth
                ->where($where)
				->update(['orders' => new \NotORM_Literal("orders + {$orders}")]);
                
        \PhalApi\DI()->notorm->user
                ->where('id=?',$where['uid'])
				->update(['orders' => new \NotORM_Literal("orders + {$orders}")]);
        
        return $rs;
    }

    /* 更新用户表状态 */
    public function upUserSwitch($uid,$isswitch) {

        $rs=\PhalApi\DI()->notorm->user
                ->where('id=?',$uid)
				->update(['isswitch'=>$isswitch]);
        
        return $rs;
    }

    /* 技能标签 */
    public function getLabel($skillid) {

        $list=\PhalApi\DI()->notorm->label
				->select('id,name,name_en')
                ->where('skillid=?',$skillid)
				->order('list_order asc')
				->fetchAll();
        
        return $list;
    }
	
	/* 技能标签 */
	public function getAllLabel($uid) {
		
		$userList=\PhalApi\DI()->notorm->skill_auth
				->select('label')
				->where('uid',$uid)
				->fetchAll();
				
		$label = '';
		foreach($userList as $v){
			if($v['label']!=''){
				$label = $v['label'];
				break;
			}
		}
				
		$labelArr=explode(";",$label);
		
		$labelList=[];
		foreach($labelArr as $v){
			$list=\PhalApi\DI()->notorm->label
					->select('id,name,name_en')
			        ->where('id',$v)
					->fetch();
			$labelList[]=$list;
		}
	    
	    return $labelList;
	}

    /* 技能标签统计 */
    public function getLabelNums($uid,$skillid) {

        $list=\PhalApi\DI()->notorm->label_count
				->select('*')
                ->where('uid=? and skillid=?',$uid,$skillid)
				->order('nums desc')
				->fetchAll();
        
        return $list;
    }

    /* 更新标签统计 */
    public function upLabelNums($uid,$skillid,$labelid) {

        $list=\PhalApi\DI()->notorm->label_count
                ->where('uid=? and skillid=? and labelid=?',$uid,$skillid,$labelid)
				->update( array('nums' => new \NotORM_Literal("nums + 1") ) );
        if(!$list){
            \PhalApi\DI()->notorm->label_count->insert(['uid'=>$uid,'skillid'=>$skillid,'labelid'=>$labelid,'nums'=>1]);
        }
        return 1;
    }
    
	/* 技能详情 */
    public function getMyskillList($where,$order) {
        
		$list=\PhalApi\DI()->notorm->skill_auth
				->select('id,skillid,status,switch')
                ->where($where)
				->order($order)
				->fetchAll();
		foreach($list as $k1=>$v1){
			$skillinfo=\PhalApi\DI()->notorm->skill
				->where('id=?',$v1['skillid'])
				->fetchOne();
			if(!$skillinfo){
				unset($list[$k]);
				continue;
			}
			$v1['skill_name']=$skillinfo['name'];
			$v1['skill_thumb']=\App\get_upload_path($skillinfo['thumb']);
			$list[$k1]=$v1;
		}		
		$list=array_values($list);
        return $list;
    }
    
    /* 根据技能ID获取拥有用户ID集合 */
    public function getByidslist($where) {
        
		$list=\PhalApi\DI()->notorm->skill_auth
				->select('id,skillid,uid')
                ->where($where)
				->fetchAll();
        return $list;
    }
}
