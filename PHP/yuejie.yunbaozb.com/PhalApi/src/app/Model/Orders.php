<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Orders extends NotORM {
	/* 进行中 */
	public function getOrdersing($where,$order='id desc') {
		
        $nowtime=time();
        $addtime=$nowtime - 60*60*24*30*3;
        
		$list=\PhalApi\DI()->notorm->orders
				->select('*')
                ->where($where)
                ->where('addtime >= ?',$addtime)
				->order($order)
				->fetchAll();

		return $list;
	}
    
    /* 订单列表 */
	public function getOrders($p,$where,$order='id desc') {
        
        $nowtime=time();
        $addtime=$nowtime - 60*60*24*30*3;

        if($p<1){
            $p=1;
        }
        
        $nums=20;
        $start=($p-1) * $nums;
		
		$list=\PhalApi\DI()->notorm->orders
				->select('*')
                ->where($where)
                ->where('addtime >= ?',$addtime)
				->order($order)
				->limit($start,$nums)
				->fetchAll();

		return $list;
	}
    
    /* 取消原因 */
    public function getCancelList(){
        $list=\PhalApi\DI()->notorm->orders_cancel
				->select('*')
				->order('list_order asc')
				->fetchAll();

		return $list;
    }
    
    /* 获取订单信息 */
	public function getOrderInfo($where) {
		
		$info=\PhalApi\DI()->notorm->orders->select('*')->where($where)->order("id asc")->fetchOne();

		return $info;
	}
	
	/* 获取部分订单信息 */
	public function getSomeOrderInfo($where) {
		
		$info=\PhalApi\DI()->notorm->orders->select('id,svctm,uid,liveuid,status,recept_status,order_type')->where($where)->order("id asc")->fetchOne();

		return $info;
	}	

	/* 生成订单 */
	public function setOrder($orderinfo) {

		$result= \PhalApi\DI()->notorm->orders->insert($orderinfo);

		return $result;
	}

	/* 更新订单 */
	public function upOrder($where,$data) {

		$result= \PhalApi\DI()->notorm->orders->where($where)->update($data);

		return $result;
	}			

}
