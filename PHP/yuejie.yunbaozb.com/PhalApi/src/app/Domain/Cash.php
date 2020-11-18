<?php
namespace App\Domain;

use App\Model\Cash as Model_Cash;

class Cash {
    /* 我的：订单收益 */
	public function getProfit($uid) {
			$rs = array();

			$model = new Model_Cash();
			$info = $model->getProfit($uid);
            
            $configpri=\App\getConfigPri();
		
            //提现比例
            $cash_rate=$configpri['cash_rate'];
            $cash_start=$configpri['cash_start'];
            $cash_end=$configpri['cash_end'];
            $cash_max_times=$configpri['cash_max_times'];
            //剩余票数
            $votes=$info['votes'];
            
            //总可提现数
            $total=(string)floor($votes/$cash_rate);
            //$tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放';
            $tips=$configpri['cash_tip'];

            
            $rs=array(
                "votes"=>$votes,
                "votestotal"=>$info['votestotal'],
                "total"=>$total,
                "cash_rate"=>$cash_rate,
                "tips"=>$tips,
            );

			return $rs;
	}
    /* 提现：订单收益 */
	public function setCash($data) {
        
			$rs = array('code' => 0, 'msg' => \PhalApi\T('提现成功'), 'info' => array());
            
            $nowtime=time();
        
            $uid=$data['uid'];
            $accountid=$data['accountid'];
            $cashvote=$data['cashvote'];
            
            $configpri=\App\getConfigPri();
            
            $cash_start=$configpri['cash_start'];
            $cash_end=$configpri['cash_end'];
            $cash_max_times=$configpri['cash_max_times'];
            
            $day=(int)date("d",$nowtime);
            
            if($day < $cash_start || $day > $cash_end){
                $rs['code'] = 1005;
                $rs['msg'] = \PhalApi\T('不在提现期限内，不能提现');
                return $rs;
            }
            
             //提现比例
            $cash_rate=$configpri['cash_rate'];
            /* 最低额度 */
            $cash_min=$configpri['cash_min'];
            
            //提现钱数
            $money=floor($cashvote/$cash_rate);
            
            if($money < $cash_min){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('提现最低额度为{n}元',['n'=>$cash_min]);
                return $rs;
            }
            
            
            $model = new Model_Cash();
            
            if($cash_max_times){
                $nums=$model->getCashNums($uid);
                if($nums >= $cash_max_times){
                    $rs['code'] = 1006;
                    $rs['msg'] = \PhalApi\T('每月只可提现{n}次,已达上限',['n'=>$cash_max_times]);
                    return $rs;
                }
            }
            
            /* 钱包信息 */
            $accountinfo=$model->getAccount($accountid);
            if(!$accountinfo){
                $rs['code'] = 1007;
                $rs['msg'] = \PhalApi\T('提现账号信息不正确');
                return $rs;
            }

            $cashvotes=$money*$cash_rate;
            
            $ifok=$model->upVotes($uid,$cashvotes);
            if(!$ifok){
                $rs['code'] = 1001;
                $rs['msg'] = \PhalApi\T('余额不足');
                return $rs;
            }
        
            $data=array(
                "uid"=>$uid,
                "money"=>$money,
                "votes"=>$cashvotes,
				"votes_type"=>'0',//提现类型：0：订单收益提现；1：礼物收益提现
                "orderno"=>$uid.'_'.date("ymdhis",$nowtime).rand(100,999),
                "status"=>0,
                "addtime"=>$nowtime,
                "uptime"=>$nowtime,
                "type"=>$accountinfo['type'],
                "account_bank"=>$accountinfo['account_bank'],
                "account"=>$accountinfo['account'],
                "name"=>$accountinfo['name'],
            );
        
			
			$res = $model->setCash($data);
            
            if(!$res){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('提现失败，请重试');
                return $rs;
            }
			$votesinfo=$model->getVotes($uid);
			$rs['info'][0]['votes']=$votesinfo['votes'];

			return $rs;
	}
	 /* 我的：礼物收益 */
	public function getGiftProfit($uid) {
			$rs = array();

			$model = new Model_Cash();
			$info = $model->getGiftProfit($uid);
            
            $configpri=\App\getConfigPri();
		
            //提现比例
            $system_rate=$configpri['system_rate'];
            $cash_start=$configpri['cash_start'];
            $cash_end=$configpri['cash_end'];
            $cash_max_times=$configpri['cash_max_times'];
            //剩余票数
            $votes=$info['votes_gift'];
            
            //总可提现数
            $total=(string)floor($votes/$system_rate);
            //$tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放';
            // $tips=$configpri['cash_tip'];

            
            $rs=array(
                "votes"=>$votes,
                "votestotal"=>$info['votes_gifttotal'],
                "total"=>$total,
                "cash_rate"=>$system_rate/100,
                "tips"=>"",
            );

			return $rs;
	}
	
	/* 提现：礼物收益 */
	public function setCashgift($data) {
        
			$rs = array('code' => 0, 'msg' => \PhalApi\T('提现成功'), 'info' => array());
            
            $nowtime=time();
        
            $uid=$data['uid'];
            $accountid=$data['accountid'];
            $cashvote=$data['cashvote'];
            $cashmoney=$data['money'];
            
            $configpri=\App\getConfigPri();
            
            $cash_start=$configpri['cash_start'];
            $cash_end=$configpri['cash_end'];
            $cash_max_times=$configpri['cash_max_times'];
            
            $day=(int)date("d",$nowtime);
            
            if($day < $cash_start || $day > $cash_end){
                $rs['code'] = 1005;
                $rs['msg'] = \PhalApi\T('不在提现期限内，不能提现');
                return $rs;
            }
            
            //提现比例：礼物收益；系统提成比例
            $cash_rate=$configpri['system_rate'];
            /* 最低额度 */
            $cash_min=$configpri['cash_min'];
            
            //提现钱数
            $money=floor($cashvote*$cash_rate/100);
            
            if($money < $cash_min){
                $rs['code'] = 1004;
                $rs['msg'] = \PhalApi\T('提现最低额度为{n}元',['n'=>$cash_min]);
                return $rs;
            }
            if($money != $cashmoney){
                $rs['code'] = 1008;
                $rs['msg'] = \PhalApi\T('可到账金额不正确，请重试');
                return $rs;
            }
            
            $model = new Model_Cash();
            
            if($cash_max_times){
                $nums=$model->getCashNums($uid);
                if($nums >= $cash_max_times){
                    $rs['code'] = 1006;
                    $rs['msg'] = \PhalApi\T('每月只可提现{n}次,已达上限',['n'=>$cash_max_times]);
                    return $rs;
                }
            }
            
            /* 钱包信息 */
            $accountinfo=$model->getAccount($accountid);
            if(!$accountinfo){
                $rs['code'] = 1007;
                $rs['msg'] = \PhalApi\T('提现账号信息不正确');
                return $rs;
            }

            $cashvotes=$money/$cash_rate*100;
            
            $ifok=$model->upVotesgift($uid,$cashvotes);
            if(!$ifok){
                $rs['code'] = 1001;
                $rs['msg'] = \PhalApi\T('余额不足');
                return $rs;
            }
        
            $data=array(
                "uid"=>$uid,
                "money"=>$money,
                "votes"=>$cashvotes,
                "votes_type"=>'1',//提现类型：0：订单收益提现；1：礼物收益提现
                "orderno"=>$uid.'_'.date("ymdhis",$nowtime).rand(100,999),
                "status"=>0,
                "addtime"=>$nowtime,
                "uptime"=>$nowtime,
                "type"=>$accountinfo['type'],
                "account_bank"=>$accountinfo['account_bank'],
                "account"=>$accountinfo['account'],
                "name"=>$accountinfo['name'],
            );
        
			
			$res = $model->setCash($data);
            
            if(!$res){
                $rs['code'] = 1002;
                $rs['msg'] = \PhalApi\T('提现失败，请重试');
                return $rs;
            }
			$votesinfo=$model->getVotes($uid);
			$rs['info'][0]['votes']=$votesinfo['votes_gift'];
			return $rs;
	}
    /* 账号列表 */
	public function getUserAccountList($uid) {
        $rs = array();
                
        $model = new Model_Cash();
        $rs = $model->getUserAccountList($uid);

        return $rs;
    }	
    /* 设置账号 */
	public function setUserAccount($data) {
        $rs = array();
                
        $model = new Model_Cash();
        $rs = $model->setUserAccount($data);

        return $rs;
    }
    /* 删除账号 */
	public function delUserAccount($data) {
        $rs = array();
                
        $model = new Model_Cash();
        $rs = $model->delUserAccount($data);

        return $rs;
    }	
}
