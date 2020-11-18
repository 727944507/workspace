<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Charge extends NotORM {
	/* 充值规则列表 */
	public function getChargeRules() {
		
		$rules=\PhalApi\DI()->notorm->charge_rules
				->select('id,name,money,coin,coin_ios,product_id')
				->order('list_order asc')
				->fetchAll();

		return $rules;
	}
    
    /* 获取充值规则 */
	public function getChargeRule($changeid) {
		
		$charge=\PhalApi\DI()->notorm->charge_rules->select('*')->where('id=?',$changeid)->fetchOne();

		return $charge;
	}		

	/* 订单号 */
	public function setOrder($orderinfo) {

		$result= \PhalApi\DI()->notorm->charge_user->insert($orderinfo);

		return $result;
	}			

}
