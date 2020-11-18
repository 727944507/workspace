<?php

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\db\Query;

class LevelController extends AdminBaseController
{
	protected function getClass($k=''){
	    $status=array(
			"1"=>"VIP等级",
			"2"=>"贵族等级",
			'3'=>'大神等级',
			'4'=>'魅力等级'
			);
	    if($k==''){
	        return $status;
	    }
	    return $status[$k];
	}
	
	public function vipList()
	{
		$data = $this->request->param('post');
		$list = Db::name('level')
			->where('type','1')
			->order('sort asc')
			->paginate(20);
			
		$list->each(function($v,$k){
			if($v['type']==1){
				$v['type'] = 'VIP等级';
			}elseif($v['type']==2){
				$v['type'] = '贵族等级';
			}elseif($v['type']==3){
				$v['type'] = '大神等级';
			}elseif($v['type']==4){
				$v['type'] = '魅力等级';
			}
			return $v;
		});

		if($data){
			foreach($data as $v){
				Db::name('level')
					->where('id',$v['id'])
					->update(['sort'=>$v['sort']]);
			}
			$this->success('修改成功');
		}
		
		$page = $list->render();
		
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function vipAdd()
	{
		$classArr = $this->getClass();
		
		$this->assign('class', $this->getCLass());
		return $this->fetch();
	}
	
	public function vipAddPost()
	{
		$data = $this->request->param('post');
		
		if(!$data){
			$this->error('数据错误');
		}
		$data['addtime'] = time();
		$re = Db::name('level')->insert($data);
		if(!$re){
			$this->error('数据错误');
		}
		$this->success('添加成功');
	}
	
	public function vipEdit()
	{
		$id = $this->request->param('id');
		$classArr = $this->getClass();
		if($id<1){
			$this->error('数据错误');
		}
		
		$data = Db::name('level')
			->where('id',$id)
			->find();
		
		$this->assign('class', $this->getCLass());
		$this->assign('data', $data);
		return $this->fetch();
	}
	
	public function vipEditPost()
	{
		$id = $this->request->param('id');
		$data = $this->request->param('post');
		if($id<1){
			$this->error('数据错误');
		}
		
		$re = Db::name('level')
			->where('id',$id)
			->update($data);
		if(!$re){
			$this->error('数据错误');
		}
		$this->success('修改成功');
	}
	
	public function nobleList()
	{
		$list = Db::name('level')
			->where('type','2')
			->order('sort asc')
			->paginate(20);
			
		$list->each(function($v,$k){
			if($v['type']==1){
				$v['type'] = 'VIP等级';
			}elseif($v['type']==2){
				$v['type'] = '贵族等级';
			}elseif($v['type']==3){
				$v['type'] = '大神等级';
			}elseif($v['type']==4){
				$v['type'] = '魅力等级';
			}
			return $v;
		});
		$page = $list->render();
		
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function masterList()
	{
		$list = Db::name('level')
			->where('type','3')
			->order('sort asc')
			->paginate(20);
			
		$list->each(function($v,$k){
			if($v['type']==1){
				$v['type'] = 'VIP等级';
			}elseif($v['type']==2){
				$v['type'] = '贵族等级';
			}elseif($v['type']==3){
				$v['type'] = '大神等级';
			}elseif($v['type']==4){
				$v['type'] = '魅力等级';
			}
			return $v;
		});
		$page = $list->render();
		
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function charmList()
	{
		$list = Db::name('level')
			->where('type','4')
			->order('sort asc')
			->paginate(20);
			
		$list->each(function($v,$k){
			if($v['type']==1){
				$v['type'] = 'VIP等级';
			}elseif($v['type']==2){
				$v['type'] = '贵族等级';
			}elseif($v['type']==3){
				$v['type'] = '大神等级';
			}elseif($v['type']==4){
				$v['type'] = '魅力等级';
			}
			return $v;
		});
		$page = $list->render();
		
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function vipDel()
	{
		$id = $this->request->param('id');
		if($id<1){
			$this->error('数据错误');
		}
		
		$re = Db::name('level')
			->where('id',$id)
			->delete();
		if(!$re){
			$this->error('数据错误');
		}
		$this->success('删除成功');
	}
}