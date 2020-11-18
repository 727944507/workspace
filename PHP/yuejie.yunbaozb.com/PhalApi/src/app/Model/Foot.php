<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Foot extends NotORM {
    
	/* 足迹 */
	public function getFoot($where) {
		
		$pnum=50;
        
		$list=\PhalApi\DI()->notorm->foot
					->select("*")
                    ->where($where)
                    ->order('addtime desc')
                    ->limit(0,$pnum)
					->fetchAll();
		
		return $list;

	}
    
    /*添加足迹 */
	public function addFoot($uid,$liveuid) {
		
        $ifok=\PhalApi\DI()->notorm->foot
                           ->where('uid=? and liveuid=?',$uid,$liveuid)
                           ->update( array( 'nums' => new \NotORM_Literal("nums + 1"),'addtime'=>time() ) );
        if(!$ifok){
            $ifok=\PhalApi\DI()->notorm->foot
                           ->insert( array( 'uid' => $uid,'liveuid' => $liveuid,'nums' => 1,'addtime'=>time() ) );
        }
		
		return $ifok;

	}
    
    /*删除足迹 */
	public function delFoot($where,$type) {
		if($type=='1'){
			$uid=$where["uid"];
			$ifok=\PhalApi\DI()->notorm->foot
						   ->where("liveuid=? and type=?",$uid,$type)
                           ->delete();
			\PhalApi\DI()->notorm->foot_visit
                           ->where($where)
                           ->delete();
		}else{
			$ifok=\PhalApi\DI()->notorm->foot
						   ->where($where)
                           ->where("type=?",$type)
                           ->delete();
			\PhalApi\DI()->notorm->foot_view
                           ->where($where)
                           ->delete();
		}
	  
		return $ifok;

	}
    
    /*获取来访次数 */
	public function getVisitNums($uid) {
		
        $nums=0;
        $ifok=\PhalApi\DI()->notorm->foot_visit
                           ->select('nums')
                           ->where('uid=?',$uid)
                           ->fetchOne();
        if($ifok){
            $nums=$ifok['nums'];
        }
		
		return (string)$nums;

	}

    /*更新来访次数 */
	public function addVisit($uid,$liveuid,$type) {
		
		$ifok1=\PhalApi\DI()->notorm->foot
                           ->where('uid=? and liveuid=? and type=?',$uid,$liveuid,$type)
                           ->update( array( 'nums' => new \NotORM_Literal("nums + 1"),'addtime'=>time() ) );
        if(!$ifok1){
            $ifok1=\PhalApi\DI()->notorm->foot
                           ->insert( array( 'uid' => $uid,'liveuid' => $liveuid,'nums' => 1,'type' => $type,'addtime'=>time() ) );
        }
		
		
        $ifok=\PhalApi\DI()->notorm->foot_visit
                           ->where('uid=?',$liveuid)
                           ->update( array( 'nums' => new \NotORM_Literal("nums + 1") ) );
        if(!$ifok){
            $ifok=\PhalApi\DI()->notorm->foot_visit
                           ->insert( array( 'uid' => $liveuid,'nums' => 1 ) );
        }
		
		return $ifok;

	}
    
    /*获取访问次数 */
	public function getViewNums($uid) {
		
        $nums=0;
        $ifok=\PhalApi\DI()->notorm->foot_view
                           ->select('nums')
                           ->where('uid=?',$uid)
                           ->fetchOne();
        if($ifok){
            $nums=$ifok['nums'];
        }
		
		return (string)$nums;

	}
    
    /*更新访问次数 */
	public function addView($uid,$liveuid,$type) {
		
		$ifok1=\PhalApi\DI()->notorm->foot
                           ->where('uid=? and liveuid=? and type=?',$uid,$liveuid,$type)
                           ->update( array( 'nums' => new \NotORM_Literal("nums + 1"),'addtime'=>time() ) );
        if(!$ifok1){
            $ifok1=\PhalApi\DI()->notorm->foot
                           ->insert( array( 'uid' => $uid,'liveuid' => $liveuid,'nums' => 1,'type' => $type,'addtime'=>time() ) );
        }
		
		
        $ifok=\PhalApi\DI()->notorm->foot_view
                           ->where('uid=?',$uid)
                           ->update( array( 'nums' => new \NotORM_Literal("nums + 1") ) );
        if(!$ifok){
            $ifok=\PhalApi\DI()->notorm->foot_view
                           ->insert( array( 'uid' => $uid,'nums' => 1 ) );
        }
		
		return $ifok;

	}
    
    
    /*获取新增来访次数 */
	public function getNewNums($uid) {
		$nums=0;
        $ifok=\PhalApi\DI()->notorm->foot_new
                           ->select('nums')
                           ->where('uid=?',$uid)
                           ->fetchOne();
        if($ifok){
            $nums=$ifok['nums'];
        }
		
		return (string)$nums;
        
	}
    
    /*更新新增来访次数 */
	public function addNew($uid) {
		
        $ifok=\PhalApi\DI()->notorm->foot_new
                           ->where('uid=?',$uid)
                           ->update( array( 'nums' => new \NotORM_Literal("nums + 1") ) );
        if(!$ifok){
            $ifok=\PhalApi\DI()->notorm->foot_new
                           ->insert( array( 'uid' => $uid,'nums' => 1 ,'addtime'=>time() ) );
        }
		
		return $ifok;

	}
    
    /*重置新增来访次数 */
	public function resetNew($uid) {
		
        $ifok=\PhalApi\DI()->notorm->foot_new
                           ->where('uid=?',$uid)
                           ->update( array( 'nums' => 0,'addtime'=>time() ) );

		
		return $ifok;

	}
	/*删除来访记录 */
	public function clearVisit($where) {
		
        $ifok=\PhalApi\DI()->notorm->foot
                           ->where($where)
                           ->delete();
		\PhalApi\DI()->notorm->foot_visit
                           ->where($where)
                           ->delete();
						   
		return $ifok;

	}
}
