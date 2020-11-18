<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Home extends NotORM {
	/* 轮播 */
	public function getSilide($id) {
		
		$list=\PhalApi\DI()->notorm->slide_item
				->select('id,title,image,url')
                ->where('status=1 and slide_id=?',$id)
				->order('list_order asc')
				->fetchAll();

		return $list;
	}

	/* 用户列表 */
	public function getUsers($uid,$p) {
        
        if($p<1){
            $p=1;
        }
        
        $nums=20;
        $start=($p-1) * $nums;

		$list=\PhalApi\DI()->notorm->user
				->select('id,user_nickname,avatar,avatar_thumb,sex,birthday,profession,addr')
                ->where('user_type=2 and user_status!=0 and isswitch=1 and id!=?',$uid)
				->order('online desc,orders desc')
                ->limit($start,$nums)
				->fetchAll();

		return $list;
	}

	/* 搜索技能 */
	public function searchSkill($keyword) {
        
		$list=\PhalApi\DI()->notorm->skill
				->select('*')
                ->where('name like ?','%'.$keyword.'%')
				->order('list_order asc')
				->fetchAll();

		return $list;
	}	
    
    /* 搜索用户 */
	public function searchUser($keyword,$p) {
        
        if($p<1){
            $p=1;
        }
        
        $nums=20;
        $start=($p-1) * $nums;

		$list=\PhalApi\DI()->notorm->user
				->select('id,user_nickname,avatar,avatar_thumb,sex,birthday')
                // ->where('user_type=2 and user_status!=0 and user_status!=3 ')
                ->where('user_type=2 and user_status not in(0,3) ')
                ->where('id like ? or user_nickname like ?','%'.$keyword.'%','%'.$keyword.'%')
				->order('online desc,orders desc')
                ->limit($start,$nums)
				->fetchAll();

		return $list;
	}
	
	public function getActive($limit){
		$list=\PhalApi\DI()->notorm->skill_auth
				->order('stars desc,orders desc')
				->group('uid')
				->limit($limit)
				->fetchAll();
		$arr=array();
		foreach($list as $v){
			$userinfo=\App\getUserInfo($v['uid']);
			$arr['userInfo'.$v['uid']]['avatar'] = $userinfo['avatar'];
			$arr['userInfo'.$v['uid']]['avatar_thumb'] = $userinfo['avatar_thumb'];
		}
		return $arr;
	}
			

}
