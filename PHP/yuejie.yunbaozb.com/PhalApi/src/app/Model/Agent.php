<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Agent extends NotORM {
    
	/* 获取邀请关系 */
	public function getAgentInfo($uid) {
		
		$info=\PhalApi\DI()->notorm->agent
					->select("*")
					->where('uid=?',$uid)
					->fetchOne();
		
		return $info;

	}

	/* 判断是否有下级 */
	public function getAgentLower($uid) {
		
		$info=\PhalApi\DI()->notorm->agent
					->select("*")
					->where('one=?',$uid)
					->fetchOne();
		
		return $info;

	}

	/* 设置邀请码 */
	public function setCode($data) {
		
		$info=\PhalApi\DI()->notorm->agent_code
					->insert($data);
		
		return $info;

	}

	/* 获取邀请关系 */
	public function getCode($where) {
		
		$info=\PhalApi\DI()->notorm->agent_code
					->select("*")
					->where($where)
					->fetchOne();
		
		return $info;

	}

	/* 设置邀请关系 */
	public function setAgent($data) {
		
		$rs=\PhalApi\DI()->notorm->agent->insert($data);
		
		return $rs;

	}


	/* 获取收益 */
	public function getProfit($uid) {
		
		$rs=\PhalApi\DI()->notorm->agent_profit
                ->select('*')
                ->where('uid=?',$uid)
                ->fetchOne();
		
		return $rs;

	}

	/* 添加收益 */
	public function setProfit($data) {
		
		$rs=\PhalApi\DI()->notorm->agent_profit->insert($data);
		
		return $rs;

	}

	/* 更新贡献 */
	public function upContri($uid,$one=0) {
		
		$rs=\PhalApi\DI()->notorm->agent_profit
                ->where('uid=?',$uid)
                ->update( array('one' => new NotORM_Literal("one + {$one}") ));
		
		return $rs;

	}

	/* 更新收益 */
	public function upProfit($uid,$field,$value=0) {
		
		$rs=\PhalApi\DI()->notorm->agent_profit
                ->where('uid=?',$uid)
                ->update( array( "{$field}" => new NotORM_Literal("{$field} + {$one}") ));
		
		return $rs;

	}
    
}
