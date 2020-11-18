<?php
namespace App\Domain;

use App\Model\Charge as Model_Charge;

class Charge {

    /* 充值规则 */
	public function getChargeRules() {

        $key='getChargeRules';
		$rules=\App\getcaches($key);
		if(!$rules){
            $model = new Model_Charge();
			$rules= $model->getChargeRules();
            if($rules){
                \App\setcaches($key,$rules);
            }
			
		}

		return $rules;
	}

    /* 生成订单 */
	public function setOrder($changeid,$orderinfo) {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$model = new Model_Charge();
        
        $charge = $model->getChargeRule($changeid);

        if(!$charge){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        
        if($charge['money']!=$orderinfo['money'] || ($charge['coin']!=$orderinfo['coin']  && $charge['coin_ios']!=$orderinfo['coin'] )){
			$rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
		}
		
		$orderinfo['coin_give']=$charge['give'];
        
		$rs = $model->setOrder($orderinfo);

		return $rs;
	}
	
}
