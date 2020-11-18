<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Liveapply extends NotORM {
    
	/* 申请信息 */
	public function getInfo($where) {
		
		$info=\PhalApi\DI()->notorm->live_apply
					->select("*")
					->where($where)
					->fetchOne();
		
		return $info;

	}

	/* 新增 */
	public function set($data) {
        
		$rs=\PhalApi\DI()->notorm->live_apply
					->insert($data);
		
		return $rs;

	}
    
    /* 更新 */
	public function up($where,$data) {
        
		$rs=\PhalApi\DI()->notorm->live_apply
                    ->where($where)
					->update($data);
		
		return $rs;

	}

}
