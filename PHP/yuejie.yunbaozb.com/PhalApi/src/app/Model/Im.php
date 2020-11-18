<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Im extends NotORM {
    
	/* 官方公告 */
	public function getSysNotice($uid,$p) {
		
        /* if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
        
		$list=\PhalApi\DI()->notorm->sys_notice
					->select("*")
					->limit($start,$pnum)
                    ->order('id desc')
					->fetchAll();
		
		return $list; */
		if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$uid;
		//读取用户创建时间
		$userinfo=\PhalApi\DI()->notorm->user
				->select("create_time")
				->where('id =? ',$uid)
				->fetchOne();
		$list=\PhalApi\DI()->notorm->sys_notice
					->select("*")
					->where("addtime > ?",$userinfo['create_time'])								
					->limit($start,$pnum)
                    ->order('id desc')
					->fetchAll();
		//记录最后一次访问该接口的时间
		if($list){
			$isexist=\PhalApi\DI()->notorm->sys_notice_readtime
				->where('uid =? ',$uid)
				->fetchOne();
			if($isexist){
				/* $one=\PhalApi\DI()->notorm->sys_notice
							->where("addtime > ?",$isexist['addtime'])
							->fetchOne(); */
				/* if($one){
					$rs['isread']="0";//是否已读：0：未读；1：已读
				} */
				\PhalApi\DI()->notorm->sys_notice_readtime
						->where("uid=?",$uid)
						->update(array("addtime"=>time()));
			}else{
				// $rs['isread']="0";
				$data=array(
					"uid"=>$uid,
					"addtime"=>time()
				);
				\PhalApi\DI()->notorm->sys_notice_readtime
							->insert($data);
			}
		}
		/*//读取用户创建时间
		$userinfo=\PhalApi\DI()->notorm->user
				->select("create_time")
				->where('id =? ',$uid)
				->fetchOne();	 
		 $list=\PhalApi\DI()->notorm->sys_notice
					->select("*")
					->where("addtime > ?",$userinfo['create_time'])								
					->limit($start,$pnum)
                    ->order('id desc')
					->fetchAll(); */
		// $rs['list']=$list;
		return $list;

	}	

	/* 官方公告：读取状态 */
	public function getStatus($uid) {
		$rs=array("status"=>0,"content"=>"","time"=>"");
		//status：是否已读：0：未读；1：已读
		//读取用户创建时间
		$userinfo=\PhalApi\DI()->notorm->user
				->select("create_time")
				->where('id =? ',$uid)
				->fetchOne();
		//记录最后一次访问该接口的时间
		$isexist=\PhalApi\DI()->notorm->sys_notice_readtime
				->where('uid =? ',$uid)
				->fetchOne();
		
		if($isexist){
			$one=\PhalApi\DI()->notorm->sys_notice
						//->where("addtime > ?",$isexist['addtime'])
						->order("addtime desc")
						->fetchOne();
			 if($one){
				
				if($one['addtime']>$isexist['addtime']){
					$rs['status']="1";
				}else{
					$rs['status']="0";
				}
				$rs['content']=$one['content'];
				$rs['time']=date("Y-m-d H:i:s",$one['addtime']);
			}
		}else{
			$one=\PhalApi\DI()->notorm->sys_notice
						->where("addtime > ?",$userinfo['create_time'])
						->order("addtime desc")
						->fetchOne();
			if($one){
				$rs['status']="1";
				$rs['content']=$one['content'];
				$rs['time']=date("Y-m-d H:i:s",$one['addtime']);										   
			}
			else{
				$rs['status']="0";
				$rs['content']="欢迎来到陪玩世界！！！";
				$rs['time']=date("Y-m-d H:i:s",time());
			}
		}
		return $rs;
	}		

}
