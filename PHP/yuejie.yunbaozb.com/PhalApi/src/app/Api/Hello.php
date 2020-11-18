<?php

namespace App\Api;

use PhalApi\Api;

class Hello extends Api{
	
	public function getRules(){
		return array(
			'world' => array(
				'username' => array('name'=>'username','desc'=>'B站账号名称')
			),
		);
	}
	
	public function world(){
		
		return array(
			'content'=>'Hello'.$this->username
		);
	}
	
	
}