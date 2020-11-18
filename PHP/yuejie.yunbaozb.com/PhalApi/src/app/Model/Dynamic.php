<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Dynamic extends NotORM {
    
	/* 发布动态 */
	public function setDynamic($data) {
		
		$rs=\PhalApi\DI()->notorm->dynamic
					->insert($data);
		
		return $rs;
	}
    
    /* 动态内容 */
	public function getDynamic($id) {
		
		$info=\PhalApi\DI()->notorm->dynamic
                    ->select('*')
                    ->where('id=?',$id)
					->fetchOne();
		
		return $info;
	}
    
    /* 删除动态 */
	public function del($where) {
		
		$rs=\PhalApi\DI()->notorm->dynamic
					->where($where)
					->delete();
		return $rs;
	}
    
    /* 点赞 */
	public function setLike($uid,$did) {
        
		$ifok=\PhalApi\DI()->notorm->dynamic_like
					->insert(['uid'=>$uid,'did'=>$did,'addtime'=>time()]);
		
        if($ifok){
            \PhalApi\DI()->notorm->dynamic
                    ->where('id=?',$did)
					->update(['likes' => new \NotORM_Literal("likes + 1 ")]);
        }
        
		return 1;

	}
    
    /* 取赞 */
	public function delLike($uid,$did) {
		
		$ifok=\PhalApi\DI()->notorm->dynamic_like
					->where(['uid'=>$uid,'did'=>$did])
					->delete();
        if($ifok){
            \PhalApi\DI()->notorm->dynamic
                    ->where('id=? and likes>0',$did)
					->update(['likes' => new \NotORM_Literal("likes - 1 ")]);
        }
		
		return 1;

	}
    
    /* 删除点赞 */
	public function delOnlyLike($where) {
		
		$rs=\PhalApi\DI()->notorm->dynamic_like
					->where($where)
					->delete();
		return $rs;
	}
    
    /* 获取评论 */
	public function getComments($where,$nums) {
		
		$list=\PhalApi\DI()->notorm->dynamic_comment
                    ->select('*')
                    ->where($where)
                    ->order('id desc')
                    ->limit(0,$nums)
					->fetchAll();
		
		return $list;
	}
    /* 获取评论数量 */
	public function getCommentscount($where) {
		
		$count=\PhalApi\DI()->notorm->dynamic_comment
                    ->select('*')
                    ->where($where)
					->count();
		
		return $count;
	}
    /* 发布评论 */
	public function setComment($data) {
		
		$rs=\PhalApi\DI()->notorm->dynamic_comment
					->insert($data);
        if($rs){
            \PhalApi\DI()->notorm->dynamic
                    ->where('id=?',$data['did'])
					->update(['comments' => new \NotORM_Literal("comments + 1 ")]);
        }
		
		return $rs;
	}
    
    /* 获取评论 */
	public function getComment($where) {
		
		$info=\PhalApi\DI()->notorm->dynamic_comment
                    ->select('likes')
					->where($where)
					->fetchOne();
		return $info;
	}
    
    /* 删除评论 */
	public function delComment($where) {
		
		$rs=\PhalApi\DI()->notorm->dynamic_comment
					->where($where)
					->delete();
		return $rs;
	}
    
    /* 评论点赞 */
	public function setCommentLike($uid,$did,$cid) {
        
		$ifok=\PhalApi\DI()->notorm->dynamic_comment_like
					->insert(['uid'=>$uid,'did'=>$did,'cid'=>$cid,'addtime'=>time()]);
		
        if($ifok){
            \PhalApi\DI()->notorm->dynamic_comment
                    ->where('id=?',$cid)
					->update(['likes' => new \NotORM_Literal("likes + 1 ")]);
        }
        
		return 1;

	}
    
    /* 评论取赞 */
	public function delCommentLike($uid,$cid) {
		
		$ifok=\PhalApi\DI()->notorm->dynamic_comment_like
					->where(['uid'=>$uid,'cid'=>$cid])
					->delete();
        if($ifok){
            \PhalApi\DI()->notorm->dynamic_comment
                    ->where('id=? and likes>0',$cid)
					->update(['likes' => new \NotORM_Literal("likes - 1 ")]);
        }
		
		return 1;

	}
    
    /* 删除评论点赞 */
	public function delOnlyCommentLike($where) {
		
		$rs=\PhalApi\DI()->notorm->dynamic_comment_like
					->where($where)
					->delete();
		return $rs;
	}
    
    /* 推荐列表 */
	public function getRecom($where,$p) {
		
        if($p<1){
            $p=1;
        }
        
        $nums=20;
        $start=($p-1) * $nums;
        
        $configpri=\App\getConfigPri();
        $comment=$configpri['dynamic_recom_com'];
        $like=$configpri['dynamic_recom_like'];
		
		$list=\PhalApi\DI()->notorm->dynamic
                  //  ->select("*,(comments*{$comment} + likes*{$like}) as orders")
					 ->select("*,ceil(comments * ".$comment." + likes * ".$like.") as recomend")																		   
                    ->where($where)
                    //->order('recoms desc,orders desc')
					->order('recoms desc,recomend desc,addtime desc')										 
                    ->limit($start,$nums)
					->fetchAll();
					
		/*foreach($list as $k=>$v){
			$userList=\PhalApi\DI()->notorm->skill_auth
					->select('label')
					->where('uid',$$v['uid'])
					->fetchAll();
					
			$label = '';
			foreach($userList as $v){
				if($v['label']!=''){
					$label = $v['label'];
					break;
				}
			}
					
			$labelArr=explode(";",$label);
			
			$labelList=[];
			
			foreach($labelArr as $v){
				$labelResult=\PhalApi\DI()->notorm->label
						->select('id,name,name_en')
				        ->where('id',$v)
						->fetch();
				$str.=$labelResult['name'].';';
			}
			$list[$k]['tag'] = $str;
		}*/			
		
		return $list;
	}
    
    /* 动态列表 */
	public function getDynamicList($where,$order,$p) {
        
        if($p<1){
            $p=1;
        }
        
        $nums=20;
        $start=($p-1) * $nums;
		
		$list=\PhalApi\DI()->notorm->dynamic
                    ->select('*')
                    ->where($where)
                    ->order($order)
                    ->limit($start,$nums)
					->fetchAll();
		
		return $list;
	}
    
    /* 举报列表 */
    public function getReport() {
		
		$list=\PhalApi\DI()->notorm->dynamic_reportcat
                    ->select('*')
                    ->order('list_order')
					->fetchAll();
		
		return $list;
	}

    /* 举报 */
    public function setReport($data) {
		
		$list=\PhalApi\DI()->notorm->dynamic_report
					->insert($data);
		
		return $list;
	}

}
