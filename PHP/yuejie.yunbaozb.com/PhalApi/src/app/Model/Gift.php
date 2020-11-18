<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Gift extends NotORM {
    
	/* 礼物列表 */
	public function getList() {
		
        
		$list=\PhalApi\DI()->notorm->gift
					->select("id,type,giftname,needcoin,gifticon,swftype,swf,swftime")
                    ->order('list_order asc,id desc')
					->fetchAll();
		
		return $list;

	}		

}
