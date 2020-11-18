<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Refund extends NotORM {
    
	/* 申请退款 */
	public function setRefund($data) {
		
		
		$rs=\PhalApi\DI()->notorm->refund->insert($data);
		
		return $rs;

	}
	/* 获取退款信息 */
	public function getRefundinfo($where) {
		
		$info=\PhalApi\DI()->notorm->refund->select('*')->where($where)->order("id asc")->fetchOne();
		
		
		return $info;
	}	

    /* 退款理由列表 */
    public function getRefundcat() {
		
		$list=\PhalApi\DI()->notorm->refundcat
                    ->select('*')
                    ->order('list_order')
					->fetchAll();
		
		return $list;
	}
}
