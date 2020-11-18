<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Rank extends NotORM {
    
	/* 主播贡献榜 */
    public function getLiveContri($liveuid) {

        $list=\PhalApi\DI()->notorm->user_votesrecord
				->select('fromid,sum(total) as totalall')
                ->where('type=1 and action=3 and uid=?',$liveuid)
                ->group('fromid')
                ->order('totalall desc')
                ->limit(0,20)
				->fetchAll();
        
        return $list;
    }
    
    /* 魅力榜 */
    public function getCharm() {

        $list=\PhalApi\DI()->notorm->user
				->select('id,votes_gifttotal as votestotal')
                ->where('user_type=2 and votes_gifttotal>0')
                ->order('votes_gifttotal desc')
                ->limit(0,20)
				->fetchAll();
        
        return $list;
    }
    
    

}
