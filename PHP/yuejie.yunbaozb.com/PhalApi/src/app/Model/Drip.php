<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Drip extends NotORM {
    
	/* 获取订单信息 */
	public function getDrip($where) {

		$info= \PhalApi\DI()->notorm->drip
                            ->select('*')
                            ->where($where)
                            ->order('id desc')
                            ->fetchOne();

		return $info;
	}
    
    /* 生成订单 */
	public function setDrip($data) {

		$result= \PhalApi\DI()->notorm->drip->insert($data);

		return $result;
	}
    
    /* 更新订单 */
	public function upDrip($where,$data) {

		$rs= \PhalApi\DI()->notorm->drip
                            ->where($where)
                            ->update($data);

		return $rs;
	}

    /* 滴滴订单 */
	public function getMyDrip($uid,$p) {
        
        if($p<1){
            $p=1;
        }
        
        $nums=20;
        $start=($p-1) * $nums;
        
		$list= \PhalApi\DI()->notorm->drip
                    ->select('*')
                    ->where('uid=?',$uid)
                    ->order('id desc')
                    ->limit($start,$nums)
                    ->fetchAll();

		return $list;
	}

    /* 选择大神 */
	public function getLiveid($where) {
        
        $nums=20;
        
		$list= \PhalApi\DI()->notorm->drip_grap
                    ->select('*')
                    ->where($where)
                    ->order('addtime desc')
                    ->limit($nums)
                    ->fetchAll();

		return $list;
	}

    /* 抢单列表 */
	public function getDripList($where) {
        
        $nums=20;
        
		$list= \PhalApi\DI()->notorm->drip
                    ->select('*')
                    ->where($where)
                    ->order('id desc')
                    ->limit($nums)
                    ->fetchAll();

		return $list;
	}

    /* 抢单记录 */
	public function getGrap($where) {
        
        $info=\PhalApi\DI()->notorm->drip_grap
                ->select('*')
                ->where($where)
                ->fetchOne();

		return $info;
	}
    
    /* 抢单 */
	public function grapDrip($uid,$dripid,$skillid) {
        
        $ifok=\PhalApi\DI()->notorm->drip_grap
                ->insert(['liveuid'=>$uid,'dripid'=>$dripid,'skillid'=>$skillid,'addtime'=>time()]);
        if($ifok){
            \PhalApi\DI()->notorm->drip
                    ->where('id=?',$dripid)
                    ->update( array('count' => new \NotORM_Literal("count + 1") ) );
        }

		return $ifok;
	}
    
    /* 取消 */
	public function getDripCancel() {
        
		$list= \PhalApi\DI()->notorm->drip_cancel
                    ->select('*')
                    ->order('list_order asc')
                    ->fetchAll();

		return $list;
	}

}
