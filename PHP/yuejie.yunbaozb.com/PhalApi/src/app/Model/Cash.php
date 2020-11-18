<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Cash extends NotORM {
	/* 我的：订单收益 */
	public function getProfit($uid){
		$info= \PhalApi\DI()->notorm->user
				->select("votes,votestotal")
				->where('id=?',$uid)
				->fetchOne();

		return $info;
	}
	/* 我的：礼物收益 */
	public function getGiftProfit($uid){
		$info= \PhalApi\DI()->notorm->user
				->select("votes_gift,votes_gifttotal")
				->where('id=?',$uid)
				->fetchOne();

		return $info;
	}
    
	/* 本月提现次数  */
	public function getCashNums($uid){
        $nowtime=time();
        //本月第一天
        $month=date('Y-m-d',strtotime(date("Ym",$nowtime).'01'));
        $month_start=strtotime(date("Ym",$nowtime).'01');

        //本月最后一天
        $month_end=strtotime("{$month} +1 month");
            
		$nums=\PhalApi\DI()->notorm->cash_record
                ->where('uid=? and addtime > ? and addtime < ?',$uid,$month_start,$month_end)
                ->count();
        return $nums;
	}

	/* 扣除：订单收益映票  */
	public function upVotes($uid,$votes){
        
		$rs=\PhalApi\DI()->notorm->user
                ->where('id = ? and votes>=?', $uid,$votes)
                ->update(array('votes' => new \NotORM_Literal("votes - {$votes}")) );     

		return $rs;
	}
	/* 扣除礼物收益映票  */
	public function upVotesgift($uid,$votes){
        
		$rs=\PhalApi\DI()->notorm->user
                ->where('id = ? and votes_gift>=?', $uid,$votes)
                ->update(array('votes_gift' => new \NotORM_Literal("votes_gift - {$votes}")) );     

		return $rs;
	}
	public function getVotes($uid){
		$info=\PhalApi\DI()->notorm->user
				->select("votes_gift,votes")
                ->where('id = ?',$uid)
				->fetchOne();
		return $info;
	}

	/* 提现  */
	public function setCash($data){
        
		$rs=\PhalApi\DI()->notorm->cash_record->insert($data);
		return $rs;
	}
    
    /* 提现账号列表 */
    public function getUserAccountList($uid){
        
        $list=\PhalApi\DI()->notorm->cash_account
                ->select("*")
                ->where('uid=?',$uid)
                ->order("addtime desc")
                ->fetchAll();
                
        return $list;
    }

    /* 提现账号详情 */
    public function getAccount($id){
        
        $info=\PhalApi\DI()->notorm->cash_account
                    ->select("*")
                    ->where('id=?',$id)
                    ->fetchOne();
                
        return $info;
    }

    /* 设置提账号 */
    public function setUserAccount($data){
        
        $rs=\PhalApi\DI()->notorm->cash_account
                ->insert($data);
                
        return $rs;
    }

    /* 删除提账号 */
    public function delUserAccount($data){
        
        $rs=\PhalApi\DI()->notorm->cash_account
                ->where($data)
                ->delete();
                
        return $rs;
    }
}
