<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use app\admin\model\UserModel;
use app\admin\model\UserAuthModel;
use think\Db;
use think\db\Query;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {
		
		$data = $this->request->param();
        $map=[];
		$start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['a.create_time','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['a.create_time','<=',strtotime($end_time) + 60*60*24];
        }
		
        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }
		
        $list = Db::name('user')
			->alias("a")
			->join('user_auth i', 'a.id = i.uid')
			->where($map)
            ->where('a.user_type=2')
            ->where(function (Query $query) {
                $data = $this->request->param();
                if (!empty($data['uid'])) {
                    $query->where('a.id', intval($data['uid']));
                }

                if (!empty($data['keyword'])) {
                    $keyword = $data['keyword'];
                    $query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
                }
				if (!empty($data['source'])) {
                    $query->where('a.source', $data['source']);
                }

            })
            ->order("a.create_time DESC")
            ->paginate(10);
		$nums=Db::name("user")
				->alias("a")
				->join('user_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where(function (Query $query) {
					$data = $this->request->param();
					if (!empty($data['uid'])) {
						$query->where('a.id', intval($data['uid']));
					}

					if (!empty($data['keyword'])) {
						$keyword = $data['keyword'];
						$query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
					}
					if (!empty($data['source'])) {
						$query->where('a.source', $data['source']);
					}

				})
				->count();
		//在线总人数
		$online=Db::name("user")
				->alias("a")
				->join('user_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where('a.online!=0')
				->where(function (Query $query) {
					$data = $this->request->param();
					if (!empty($data['uid'])) {
						$query->where('a.id', intval($data['uid']));
					}
		
					if (!empty($data['keyword'])) {
						$keyword = $data['keyword'];
						$query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
					}
					if (!empty($data['source'])) {
						$query->where('a.source', $data['source']);
					}
		
				})
				->count();		
        // 获取分页显示
        $list->each(function($v,$k){
            $v['user_login']=m_s($v['user_login']);
            $v['user_email']=m_s($v['user_email']);
            $v['mobile']=m_s($v['mobile']);
			
			$map2[]=['uid','=',$v['id']];
			$map2[]=['type','=',0];//支出明细
			$map2[]=['action','=',3];//赠送礼物
			$lists = Db::name("user_coinrecord")
			    ->where($map2)
				->order("id desc")
				->select();
				
			$map3[]=['uid','=',$v['id']];
			$map3[]=['status','=',-2];//支出明细
			$lists2 = Db::name('orders')
			    ->where($map3)
			    ->order("id desc")
			    ->select();	
			
			$total=0;
			$total2=0;
			foreach($lists as $j){
				$total+=$j['nums']*$j['total'];
			}
			foreach($lists2 as $g){
				$total2+=$g['nums']*$g['total'];
			}
			
			$v['total2']=$total;
			$v['total3']=$total2;
            return $v;
        });
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
		$this->assign('nums', $nums);
		$this->assign('online', $online);
        // 渲染模板输出
        return $this->fetch();
    }
	
	/**
	 * 后台本站用户修改
	 * @adminMenu(
	 *     'name'   => '本站用户',
	 *     'parent' => 'default1',
	 *     'display'=> true,
	 *     'hasView'=> true,
	 *     'order'  => 10000,
	 *     'icon'   => '',
	 *     'remark' => '本站用户',
	 *     'param'  => ''
	 * )
	 */
	public function update()
	{
		$content = hook_one('user_admin_update_view');
		
		if (!empty($content)) {
		    return $content;
		}
		
		$id      = $this->request->param('user_id', 0, 'intval');
		if(!empty($id)){
			$user_id = $id;
		}else{
			$this->redirect('user/adminIndex/index');
		}
		
		$sex = [
			'0'=>'保密',
			'1'=>'男',
			'2'=>'女'
		];
		$this->assign('sex', $sex);
		
		$result = Db::name('user')
			->alias("a")
			->join('user_auth i', 'a.id = i.uid')
		    ->where('a.user_type=2')
			->where('a.id', intval($user_id))
		    ->find();
			
		//$result  = Db::name('slideItem')->where('slide_id', $slideId)->select();
		$this->assign('slide_id', $id);
		$this->assign('result', $result);
		return $this->fetch();
	}
	
	public function updatePost()
	{
		$data = $this->request->param('post');
		$id = intval($this->request->param('id'));
		
		$data['sex'] = intval($data['sex']);
		if(trim($data['avatar']) == ''){
			unset($data['avatar']);
		}
		
		if($id<=0){
			$this->error("数据有误");
		}
		
		$userResult = Db::name('user')->where('id',intval($data['id']))->strict(false)->update($data);
		$userAuthResult = Db::name('user_auth')->where('uid',intval($data['id']))->strict(false)->update($data);
		
		$this->success("保存成功！");
	}
	
    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            $result = Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 0);
            if ($result) {
                $this->success("会员拉黑成功！", "adminIndex/index");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            $this->success("会员启用成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }

    /* 删除用户 */
    public function del()
    {
        $id = input('param.id', 0, 'intval');
        if ($id) {
            Db::name("user")->where(["id" => $id])->delete();
            
            /* 删除用户认证 */
            Db::name("user_auth")->where(["uid" => $id])->delete();
            
            /* 删除技能认证 */
            Db::name("skill_auth")->where(["uid" => $id])->delete();
            
            /* 粉丝、关注 */
            Db::name("user_attention")->where("uid={$id} or touid={$id}")->delete();
            
            /* token */
            Db::name("user_token")->where(["user_id" => $id])->delete();
            
            /* 评价 */
            Db::name("user_comment")->where(["touid" => $id])->delete();
            
            /* 技能标签 */
            Db::name("label_count")->where(["uid" => $id])->delete();
            
            $this->success("删除会员成功！", '');
        } else {
            $this->error('数据传入失败！');
        }
    }
    
    public function setHost()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $ishost = $this->request->param('ishost', 0, 'intval');
        
        $nowtime=time();
        
        $rs=DB::name("user")->where("id={$id}")->update(['ishost'=>$ishost]);
        if($rs===false){
            $this->error("操作失败");
        }
        
        $this->success("操作成功");        
    }
	/* 推荐 */
    function setrecommend(){
        
        $id = $this->request->param('id', 0, 'intval');
        $isrecommend = $this->request->param('isrecommend', 0, 'intval');
		
		$data=[
			'isrecommend'=>$isrecommend,
			'recommend_time'=>time(),
		];
		
		if($isrecommend==0){
			$data['recommend_time']='0';
		}
		$rs = DB::name('user')->where("id={$id}")->update($data);
		if(!$rs){
			$this->error("操作失败！");
		}
        $this->success("操作成功！",url('adminPlay/index'));
            
	}
}
