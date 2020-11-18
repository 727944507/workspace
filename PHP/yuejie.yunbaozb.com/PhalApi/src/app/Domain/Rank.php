<?php
namespace App\Domain;

use App\Model\Rank as Model_Rank;

class Rank {
    
    
    /* 直播间内贡献版 */
    public function getLiveContri($liveuid){
        
        $model = new Model_Rank();
        $list=$model->getLiveContri($liveuid);
        
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($v['fromid']);
            $userinfo['total']=\App\NumberFormat($v['totalall']);
            
            $list[$k]=$userinfo;
        }
        
        return $list;
    }

    /* 魅力榜 */
    public function getCharm(){
        
        $model = new Model_Rank();
        $list=$model->getCharm();
        
        foreach($list as $k=>$v){
            $userinfo=\App\getUserInfo($v['id']);
            $userinfo['total']=\App\NumberFormat($v['votestotal']);
            
            $list[$k]=$userinfo;
        }
        
        return $list;
    }
	
}
