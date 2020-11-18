<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Rank as Domain_Rank;

/**
 * 排行榜
 */
 
class Rank extends Api {

	public function getRules() {
		return array(
            'getLiveContri' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
			),
		);
	}
    
	/**
	 * 主播贡献榜 
	 * @desc 用于获取某主播贡献榜
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
     * @return string info[].total 总数
	 * @return string msg 提示信息
	 */
	public function getLiveContri() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $liveuid=\App\checkNull($this->liveuid);
        
        if($liveuid<0 ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        
        $domain = new Domain_Rank();
		$list = $domain->getLiveContri($liveuid);
        
        $rs['info']=$list;
        
		return $rs;
	} 

	/**
	 * 全站魅力榜 
	 * @desc 用于获取全站魅力榜
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
     * @return string info[].total 总数
	 * @return string msg 提示信息
	 */
	public function getCharm() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        
        $domain = new Domain_Rank();
		$list = $domain->getCharm();
        
        $rs['info']=$list;
        
		return $rs;
	}   
    

}
