<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Dispatch extends NotORM {
    
	/* 认证的技能的用户 */
    public function getSkillAuth($where) {

        $list=\PhalApi\DI()->notorm->skill_auth
				->select('*')
                ->where($where)
				->fetchAll();
        
        return $list;
    }
    
    
    /* 写入派单记录 */
    public function setDispatch($data) {

        $rs=\PhalApi\DI()->notorm->dispatch
				->insert($data);
        
        return $rs;
    }
    
    /* 获取派单记录 */
    public function getDispatch($where) {

        $info=\PhalApi\DI()->notorm->dispatch
				->select('*')
                ->where($where)
                ->order('id desc')
				->fetchOne();
        
        return $info;
    }

    /* 设置信息 */
	public function upInfo($where,$data) {
        
		$rs=\PhalApi\DI()->notorm->dispatch
                    ->where($where)
					->update($data);
        
		return $rs;

	}
}
