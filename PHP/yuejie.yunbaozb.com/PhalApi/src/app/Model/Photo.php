<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Photo extends NotORM {
    
	/* 照片列表 */
	public function getPhotos($where,$order,$p) {
		
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
        
		$list=\PhalApi\DI()->notorm->photo
					->select("*")
					->where($where)
                    ->order($order)
                    ->limit($start,$pnum)
					->fetchAll();
		
		return $list;

	}
    
    /* 新加 */
	public function setPhoto($data) {
		
        
		$rs=\PhalApi\DI()->notorm->photo
					->insert($data);
		
		return $rs;

	}
    
    /* 照片信息 */
	public function getPhoto($where) {
		
		$info=\PhalApi\DI()->notorm->photo
                    ->select('*')
                    ->where($where)
					->fetchOne();
		
		return $info;

	}
    
    /* 删除照片 */
	public function delPhoto($where) {
		
		$info=\PhalApi\DI()->notorm->photo
                    ->where($where)
					->delete();
		
		return $info;

	}

}
