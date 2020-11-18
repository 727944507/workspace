<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Guide extends NotORM {
    
	/* 引导页 */
	public function getGuide() {
		
        $config=\PhalApi\DI()->notorm->option
            ->select('option_value')
            ->where("option_name='guide'")
            ->fetchOne();
            
        $config = json_decode($config['option_value'],true);
        
        $where="type={$config['type']}";
        
        $list=\PhalApi\DI()->notorm->guide
            ->select('thumb,href')
            ->where($where)
            ->order('list_order asc,uptime desc')
            ->fetchAll();
        foreach($list as $k=>$v){
            $v['thumb']=\App\get_upload_path($v['thumb']);
            $v['href']=urldecode($v['href']);
            $list[$k]=$v;
        }

        $config['list']=$list;
		return $config;
	}		

}
