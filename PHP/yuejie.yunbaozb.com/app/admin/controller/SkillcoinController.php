<?php

/* 技能价格 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SkillcoinController extends AdminBaseController
{

    public function index()
    {
        
        $list = Db::name('skill_coin')
            ->order("coin asc")
            ->paginate(20);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        return $this->fetch();
    }


    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $coin=$data['coin'];
            $orders=$data['orders'];
            if($coin == ''){
                $this->error('请填写价格');
            }else{
                $check = Db::name('skill_coin')->where("coin='{$coin}'")->find();
                if($check){
                    $this->error('同一价格已存在');
                }
            }

            if($orders==''){
                $this->error('请填写接单量');
            }
			
			if($data['first_price']==''){
				$this->error('请填首单价格');
			}

            $id = DB::name('skill_coin')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            $this->resetcache();
            $this->success("添加成功！");
        }
    }

    public function edit()
    {
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('skill_coin')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $id=$data['id'];
            $coin=$data['coin'];
            $orders=$data['orders'];
            if($coin == ''){
                $this->error('请填写价格');
            }else{
                $check = Db::name('skill_coin')->where("coin='{$coin}' and id !='{$id}'")->find();
                if($check){
                    $this->error('同一价格已存在');
                }
            }

            if($orders==''){
                $this->error('请填写接单量');
            }
			
			if($data['first_price']==''){
				$this->error('请填首单价格');
			}

            $rs = DB::name('skill_coin')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            /* 更新技能配置 */
            $coinlist=DB::name('skill_coin')
                        ->field('*')
                        ->order("coin desc")
                        ->select()->toArray();
                        
            $list=Db::name('skill_auth')->where('coinid',$id)->select()->toArray();

            foreach($list as $k=>$v){
                if($v['orders'] >= $orders){
                    Db::name('skill_auth')->where('id',$v['id'])->update( ['coin'=>$coin] );
                }else{
                    foreach($coinlist as $k1=>$v1){
                        if( $v['orders'] >= $v1['orders'] ){
                            Db::name('skill_auth')->where('id',$v['id'])->update(['coinid'=>$v1['id'],'coin'=>$v1['coin']]);
                            break;
                        }
                    }
                    
                }
            }
            /* 更新技能配置 */
            
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('skill_coin')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        /* 更新技能配置 */
        $coinlist=DB::name('skill_coin')
                    ->field('*')
                    ->order("coin desc")
                    ->select()->toArray();
                    
        $list=Db::name('skill_auth')->where('coinid',$id)->select()->toArray();

        foreach($list as $k=>$v){
            $isup='0';
            foreach($coinlist as $k1=>$v1){
                if( $v['orders'] >= $v1['orders'] ){
                    $isup='1';
                    Db::name('skill_auth')->where('id',$v['id'])->update(['coinid'=>$v1['id'],'coin'=>$v1['coin']]);
                    break;
                }
            }
            
            if(!$isup){
                Db::name('skill_auth')->where('id',$v['id'])->update(['switch'=>0,'coinid'=>0,'coin'=>0]);
            }
        }
        /* 更新技能配置 */
            
        $this->resetcache();
        $this->success("删除成功！",url("skillcoin/index"));
    }


    protected function resetcache(){

        $key='skill_coinlist';

        $list=DB::name('skill_coin')
                ->field('*')
                ->order("coin asc")
                ->select();
        if($list){
            setcaches($key,$list);
        }
    }
}