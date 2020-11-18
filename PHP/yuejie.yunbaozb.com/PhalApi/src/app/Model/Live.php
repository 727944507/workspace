<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Live extends NotORM {
    
	/* 背景列表 */
	public function getBg() {

		$list=\PhalApi\DI()->notorm->live_bg
                    ->select("id,name,thumb")
                    ->order('list_order asc')
					->fetchAll();
		
		return $list;

	}
    
    /* 列表 */
	public function getLists($p,$where,$order) {
		
        if($p<1){
            $p=1;
        }
		$pnum=50;
		$start=($p-1)*$pnum;
        
		$list=\PhalApi\DI()->notorm->live
                    ->select("*")
                    ->where($where)
					->limit($start,$pnum)
                    ->order($order)
					->fetchAll();
		
		return $list;

	}

	/* 信息 */
	public function getInfo($where) {
        
		$info=\PhalApi\DI()->notorm->live
					->select("*")
                    ->where($where)
					->fetchOne();
		
		return $info;

	}

	/* 写入信息 */
	public function setInfo($data) {
        
		$rs=\PhalApi\DI()->notorm->live
					->insert($data);
        
		return $rs;

	}
    
    /* 设置信息 */
	public function upInfo($where,$data) {
        
		$rs=\PhalApi\DI()->notorm->live
                    ->where($where)
					->update($data);
        
		return $rs;

	}
    
    /* 更新数量 */
	public function upNums($where,$field,$nums=1,$type=0) {
        
        if($type==1){
            $rs=\PhalApi\DI()->notorm->live
                    ->where($where)
					->update( array("{$field}" => new \NotORM_Literal("{$field} - {$nums}") ) );
        }else{
            $rs=\PhalApi\DI()->notorm->live
                    ->where($where)
					->update( array("{$field}" => new \NotORM_Literal("{$field} + {$nums}") ) );
        }
		
        
		return $rs;

	}
    
    
    /* 写入直播记录 */
	public function setLiverecord($data) {
        
		$rs=\PhalApi\DI()->notorm->live_record
					->insert($data);
		
		return $rs;

	}

    /* 获取直播记录信息 */
	public function getStopInfo($where) {
        
		$info=\PhalApi\DI()->notorm->live_record
                    ->select('*')
                    ->where($where)
					->fetchOne();
		
		return $info;

	}
	

}
