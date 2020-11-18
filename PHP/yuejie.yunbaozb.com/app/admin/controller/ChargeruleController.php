<?php

/* 充值规则 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ChargeruleController extends AdminBaseController
{

    public function index()
    {
        
        $list = Db::name('charge_rules')
            ->order("list_order asc")
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
            
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $money=$data['money'];
            if($money<=0){
                $this->error('请填写正确的价格');
            }
            $coin=$data['coin'];
            if($coin<=0){
                $this->error('请填写正确的钻石数');
            }
            
            $coin_ios=$data['coin_ios'];
            if($coin_ios==''){
                $this->error('请填写正确的苹果支付钻石数');
            }

            $id = DB::name('charge_rules')->insertGetId($data);
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
        
        $data=Db::name('charge_rules')
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

            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $money=$data['money'];
            if($money<=0){
                $this->error('请填写正确的价格');
            }
            $coin=$data['coin'];
            if($coin<=0){
                $this->error('请填写正确的钻石数');
            }
            
            $coin_ios=$data['coin_ios'];
            if($coin_ios==''){
                $this->error('请填写正确的苹果支付钻石数');
            }

            $rs = DB::name('charge_rules')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('charge_rules');
        parent::listOrders($model);
        $this->resetcache();
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('charge_rules')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        $this->resetcache();
        $this->success("删除成功！",url("Chargerule/index"));
    }


    protected function resetcache(){
        $key='getChargeRules';

        $level=DB::name('charge_rules')
                ->field('id,name,money,coin,coin_ios,product_id')
                ->order("list_order asc")
                ->select();
        if($level){
            setcaches($key,$level);
        }
    }
}