<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Comment extends NotORM {
    
    /* 设置主播评论 */
	public function setComment($data) {

		$result= \PhalApi\DI()->notorm->skill_comment->insert($data);

		return $result;
	}

    /* 设置用户评论 */
	public function setEvaluate($data) {

		$result= \PhalApi\DI()->notorm->user_comment->insert($data);

		return $result;
	}

    /* 技能评论总数 */
    public function getCommentNums($where){
        
        $nums=\PhalApi\DI()->notorm->skill_comment
                ->where($where)
                ->count();
        return $nums;
    }
    
    /* 技能评论 */
    public function getComment($p,$where,$order){
        
        if($p<1){
            $p=1;
        }
        $nums=20;
        $start=($p-1) * $nums;
        
        $list=\PhalApi\DI()->notorm->skill_comment
                ->select('*')
                ->where($where)
                ->order($order)
                ->limit($start,$nums)
                ->fetchAll();
        
        return $list;
    }

    /* 评论内容 */
    public function getCommentInfo($where){
        
        $info=\PhalApi\DI()->notorm->skill_comment
                ->select('*')
                ->where($where)
                ->fetchOne();
        
        return $info;
    }

    /* 评论内容 */
    public function getEvaluateInfo($where){
        
        $info=\PhalApi\DI()->notorm->user_comment
                ->select('*')
                ->where($where)
                ->fetchOne();
        
        return $info;
    }
}
