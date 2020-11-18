<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Livetalk extends NotORM {
	
	/* 信息 */
	public function getInfo($uid) {
		$info=\PhalApi\DI()->notorm->live_talk
					->select("*")
                    ->where("islive=1 and uid=?",$uid)
					->fetchOne();
		return $info;
	}

	/* 写入信息 */
	public function setInfo($data) {
		$uid=$data['uid'];//房间用户ID
		$isexist=\PhalApi\DI()->notorm->live_talk
                    ->where("uid=?",$uid)
					->fetchOne();
		if($isexist){
			$rs=\PhalApi\DI()->notorm->live_talk
					->where("uid=?",$uid)
					->update($data);
		}else{
			$rs=\PhalApi\DI()->notorm->live_talk
					->insert($data);
		}
		return $rs;
	}
	
    
    /* 设置信息 */
	public function upInfo($uid,$data) {
	
		$rs=\PhalApi\DI()->notorm->live_talk
                    ->where("uid=?",$uid)
					->update($data);
		return $rs;
	}
    

    /* 获取直播记录信息 */
	public function getStopInfo($uid) {
		$info=\PhalApi\DI()->notorm->live_talk
                    ->select('*')
                    ->where("uid=?",$uid)
					->fetchOne();
		return $info;
	}
	
	

}
