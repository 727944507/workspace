<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\Menu;

class MainController extends AdminBaseController
{

    /**
     *  后台欢迎页
     */
    /*public function index(){
        $dashboardWidgets = [];
        $widgets          = cmf_get_option('admin_dashboard_widgets');

        $defaultDashboardWidgets = [
            '_SystemCmfHub'           => ['name' => 'CmfHub', 'is_system' => 1],
            '_SystemCmfDocuments'     => ['name' => 'CmfDocuments', 'is_system' => 1],
            '_SystemMainContributors' => ['name' => 'MainContributors', 'is_system' => 1],
            '_SystemContributors'     => ['name' => 'Contributors', 'is_system' => 1],
            '_SystemCustom1'          => ['name' => 'Custom1', 'is_system' => 1],
            '_SystemCustom2'          => ['name' => 'Custom2', 'is_system' => 1],
            '_SystemCustom3'          => ['name' => 'Custom3', 'is_system' => 1],
            '_SystemCustom4'          => ['name' => 'Custom4', 'is_system' => 1],
            '_SystemCustom5'          => ['name' => 'Custom5', 'is_system' => 1],
        ];

        if (empty($widgets)) {
            $dashboardWidgets = $defaultDashboardWidgets;
        } else {
            foreach ($widgets as $widget) {
                if ($widget['is_system']) {
                    $dashboardWidgets['_System' . $widget['name']] = ['name' => $widget['name'], 'is_system' => 1];
                } else {
                    $dashboardWidgets[$widget['name']] = ['name' => $widget['name'], 'is_system' => 0];
                }
            }

            foreach ($defaultDashboardWidgets as $widgetName => $widget) {
                $dashboardWidgets[$widgetName] = $widget;
            }


        }

        $dashboardWidgetPlugins = [];

        $hookResults = hook('admin_dashboard');

        if (!empty($hookResults)) {
            foreach ($hookResults as $hookResult) {
                if (isset($hookResult['width']) && isset($hookResult['view']) && isset($hookResult['plugin'])) { //验证插件返回合法性
                    $dashboardWidgetPlugins[$hookResult['plugin']] = $hookResult;
                    if (!isset($dashboardWidgets[$hookResult['plugin']])) {
                        $dashboardWidgets[$hookResult['plugin']] = ['name' => $hookResult['plugin'], 'is_system' => 0];
                    }
                }
            }
        }

        $smtpSetting = cmf_get_option('smtp_setting');

        $this->assign('dashboard_widgets', $dashboardWidgets);
        $this->assign('dashboard_widget_plugins', $dashboardWidgetPlugins);
        $this->assign('has_smtp_setting', empty($smtpSetting) ? false : true);

        return $this->fetch();
    }*/

    public function dashboardWidget(){
        $dashboardWidgets = [];
        $widgets          = $this->request->param('widgets/a');
        if (!empty($widgets)) {
            foreach ($widgets as $widget) {
                if ($widget['is_system']) {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 1]);
                } else {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 0]);
                }
            }
        }

        cmf_set_option('admin_dashboard_widgets', $dashboardWidgets, true);

        $this->success('更新成功!');

    }
	
	 public function index(){
        $nowtime=time();
        //当天0点
        $today=date("Ymd",$nowtime);
        $today_start=strtotime($today);
        //当天 23:59:59
        $today_end=strtotime("{$today} + 1 day");
        
        $yesterday_start=$today_start - 60*60*24;
        
        
        /* 充值统计 */
        /* 支付金额 */
        $pay_money=Db::name('charge_user')->where("status=1")->sum('money');
        if(!$pay_money){
            $pay_money=0;
        }
        
        $pay_money_t=Db::name('charge_user')->where("status=1 and addtime >= {$today_start} and addtime < {$today_end}")->sum('money');
        if(!$pay_money_t){
            $pay_money_t=0;
        }
        
        $pay_money_y=Db::name('charge_user')->where("status=1 and addtime >= {$yesterday_start} and addtime < {$today_start}")->sum('money');
        if(!$pay_money_y){
            $pay_money_y=0;
        }
        
        $pay_money_rate='0%';
        $pay_money_rate_c='';
        if($pay_money_t==0 && $pay_money_y==0){
            
        }else if($pay_money_t==0){
            $pay_money_rate=$pay_money_y.'%';
            $pay_money_rate_c='down';
        }else if($pay_money_y==0){
            $pay_money_rate=$pay_money_t.'%';
            $pay_money_rate_c='up';
        }else{
            $rate=floor(($pay_money_t - $pay_money_y)/$pay_money_y*100);
            if($rate>0){
                $pay_money_rate_c='up';
            }else if($rate<0){
                $pay_money_rate_c='down';
            }
            $pay_money_rate=abs($rate).'%';
        }
        
        $this->assign('pay_money', $pay_money);
        $this->assign('pay_money_t', $pay_money_t);
        $this->assign('pay_money_y', $pay_money_y);
        $this->assign('pay_money_rate', $pay_money_rate);
        $this->assign('pay_money_rate_c', $pay_money_rate_c);
        
        /* 支付订单 */
        $pay_orders=Db::name('charge_user')->where("status=1")->count();
        if(!$pay_orders){
            $pay_orders=0;
        }
        
        $pay_orders_t=Db::name('charge_user')->where("status=1 and addtime >= {$today_start} and addtime < {$today_end}")->count();
        if(!$pay_orders_t){
            $pay_orders_t=0;
        }
        
        $pay_orders_y=Db::name('charge_user')->where("status=1 and addtime >= {$yesterday_start} and addtime < {$today_start}")->count();
        if(!$pay_orders_y){
            $pay_orders_y=0;
        }
        
        $pay_orders_rate='0%';
        $pay_orders_rate_c='';
        if($pay_orders_t==0 && $pay_orders_y==0){
            
        }else if($pay_orders_t==0){
            $pay_orders_rate=$pay_orders_y.'%';
            $pay_orders_rate_c='down';
        }else if($pay_orders_y==0){
            $pay_orders_rate=$pay_orders_t.'%';
            $pay_orders_rate_c='up';
        }else{
            $rate=floor(($pay_orders_t - $pay_orders_y)/$pay_orders_y*100);
            if($rate>0){
                $pay_orders_rate_c='up';
            }else if($rate<0){
                $pay_orders_rate_c='down';
            }
            $pay_orders_rate=abs($rate).'%';
        }
        
        $this->assign('pay_orders', $pay_orders);
        $this->assign('pay_orders_t', $pay_orders_t);
        $this->assign('pay_orders_y', $pay_orders_y);
        $this->assign('pay_orders_rate', $pay_orders_rate);
        $this->assign('pay_orders_rate_c', $pay_orders_rate_c);
        
        /* 支付人数 */
        $pay_users=Db::name('charge_user')->where("status=1")->group('uid')->count();
        if(!$pay_users){
            $pay_users=0;
        }
        
        $pay_users_t=Db::name('charge_user')->where("status=1 and addtime >= {$today_start} and addtime < {$today_end}")->group('uid')->count();
        if(!$pay_users_t){
            $pay_users_t=0;
        }
        
        $pay_users_y=Db::name('charge_user')->where("status=1 and addtime >= {$yesterday_start} and addtime < {$today_start}")->group('uid')->count();
        if(!$pay_users_y){
            $pay_users_y=0;
        }
        
        $pay_users_rate='0%';
        $pay_users_rate_c='';
        if($pay_users_t==0 && $pay_users_y==0){
            
        }else if($pay_users_t==0){
            $pay_users_rate=$pay_users_y.'%';
            $pay_users_rate_c='down';
        }else if($pay_users_y==0){
            $pay_users_rate=$pay_users_t.'%';
            $pay_users_rate_c='up';
        }else{
            $rate=floor(($pay_users_t - $pay_users_y)/$pay_users_y*100);
            if($rate>0){
                $pay_users_rate_c='up';
            }else if($rate<0){
                $pay_users_rate_c='down';
            }
            $pay_users_rate=abs($rate).'%';
        }
        
        $this->assign('pay_users', $pay_users);
        $this->assign('pay_users_t', $pay_users_t);
        $this->assign('pay_users_y', $pay_users_y);
        $this->assign('pay_users_rate', $pay_users_rate);
        $this->assign('pay_users_rate_c', $pay_users_rate_c);
        
        /* 注册统计 */
        /* 注册人数 */
        $users=Db::name('user')->where("user_type=2")->count();
        if(!$users){
            $users=0;
        }
        
        $users_t=Db::name('user')->where("user_type=2 and create_time >= {$today_start} and create_time < {$today_end}")->count();
        if(!$users_t){
            $users_t=0;
        }
        
        $users_y=Db::name('user')->where("user_type=2 and create_time >= {$yesterday_start} and create_time < {$today_start}")->count();
        if(!$users_y){
            $users_y=0;
        }
        
        $users_rate='0%';
        $users_rate_c='';
        if($users_t==0 && $users_y==0){
            
        }else if($users_t==0){
            $users_rate=$users_y.'%';
            $users_rate_c='down';
        }else if($users_y==0){
            $users_rate=$users_t.'%';
            $users_rate_c='up';
        }else{
            $rate=floor(($users_t - $users_y)/$users_y*100);
            if($rate>0){
                $users_rate_c='up';
            }else if($rate<0){
                $users_rate_c='down';
            }
            $users_rate=abs($rate).'%';
        }
        
        $this->assign('users', $users);
        $this->assign('users_t', $users_t);
        $this->assign('users_y', $users_y);
        $this->assign('users_rate', $users_rate);
        $this->assign('users_rate_c', $users_rate_c);
        
        /* 代理 */
        /* 邀请 */
        $agent=Db::name('agent')->count();
        if(!$agent){
            $agent=0;
        }
        
        $agent_t=Db::name('agent')->where("addtime >= {$today_start} and addtime < {$today_end}")->count();
        if(!$agent_t){
            $agent_t=0;
        }
        
        $agent_y=Db::name('agent')->where("addtime >= {$yesterday_start} and addtime < {$today_start}")->count();
        if(!$agent_y){
            $agent_y=0;
        }
        
        $agent_rate='0%';
        $agent_rate_c='';
        if($agent_t==0 && $agent_y==0){
            
        }else if($agent_t==0){
            $agent_rate=$agent_y.'%';
            $agent_rate_c='down';
        }else if($agent_y==0){
            $agent_rate=$agent_t.'%';
            $agent_rate_c='up';
        }else{
            $rate=floor(($agent_t - $agent_y)/$agent_y*100);
            if($rate>0){
                $agent_rate_c='up';
            }else if($rate<0){
                $agent_rate_c='down';
            }
            $agent_rate=abs($rate).'%';
        }
        
        $this->assign('agent', $agent);
        $this->assign('agent_t', $agent_t);
        $this->assign('agent_y', $agent_y);
        $this->assign('agent_rate', $agent_rate);
        $this->assign('agent_rate_c', $agent_rate_c);
        
        /* 消费统计 */
        /* 消费 */
        $coins=Db::name('user_coinrecord')->sum('total');
        if(!$coins){
            $coins=0;
        }
        
        $coins_t=Db::name('user_coinrecord')->where("type=0 and addtime >= {$today_start} and addtime < {$today_end}")->sum('total');
        if(!$coins_t){
            $coins_t=0;
        }
        
        $coins_y=Db::name('user_coinrecord')->where("type=0 and addtime >= {$yesterday_start} and addtime < {$today_start}")->sum('total');
        if(!$coins_y){
            $coins_y=0;
        }
        
        $coins_rate='0%';
        $coins_rate_c='';
        if($coins_t==0 && $coins_y==0){
            
        }else if($coins_t==0){
            $coins_rate=$coins_y.'%';
            $coins_rate_c='down';
        }else if($coins_y==0){
            $coins_rate=$coins_t.'%';
            $coins_rate_c='up';
        }else{
            $rate=floor(($coins_t - $coins_y)/$coins_y*100);
            if($rate>0){
                $coins_rate_c='up';
            }else if($rate<0){
                $coins_rate_c='down';
            }
            $coins_rate=abs($rate).'%';
        }
        
        $this->assign('coins', $coins);
        $this->assign('coins_t', $coins_t);
        $this->assign('coins_y', $coins_y);
        $this->assign('coins_rate', $coins_rate);
        $this->assign('coins_rate_c', $coins_rate_c);
        
        /* 收益 */
        $votes=Db::name('user_coinrecord')->where("type=0 and action!=2")->sum('total');
        if(!$votes){
            $votes=0;
        }
        
        $votes_t=Db::name('user_coinrecord')->where("type=0 and action!=2 and addtime >= {$today_start} and addtime < {$today_end}")->sum('total');
        if(!$votes_t){
            $votes_t=0;
        }
        
        $votes_y=Db::name('user_coinrecord')->where("type=0 and action!=2  and addtime >= {$yesterday_start} and addtime < {$today_start}")->sum('total');
        if(!$votes_y){
            $votes_y=0;
        }
        
        $votes_rate='0%';
        $votes_rate_c='';
        if($votes_t==0 && $votes_y==0){
            
        }else if($votes_t==0){
            $votes_rate=$votes_y.'%';
            $votes_rate_c='down';
        }else if($votes_y==0){
            $votes_rate=$votes_t.'%';
            $votes_rate_c='up';
        }else{
            $rate=floor(($votes_t - $votes_y)/$votes_y*100);
            if($rate>0){
                $votes_rate_c='up';
            }else if($rate<0){
                $votes_rate_c='down';
            }
            $votes_rate=abs($rate).'%';
        }
        
        $this->assign('votes', $votes);
        $this->assign('votes_t', $votes_t);
        $this->assign('votes_y', $votes_y);
        $this->assign('votes_rate', $votes_rate);
        $this->assign('votes_rate_c', $votes_rate_c);
        
        /* 提现 */
        $cash=Db::name('cash_record')->where("status=1 ")->sum('money');
        if(!$cash){
            $cash=0;
        }
        
        $cash_t=Db::name('cash_record')->where("status=1 and uptime >= {$today_start} and uptime < {$today_end}")->sum('money');
        if(!$cash_t){
            $cash_t=0;
        }
        
        $cash_y=Db::name('cash_record')->where("status=1 and uptime >= {$yesterday_start} and uptime < {$today_start}")->sum('money');
        if(!$cash_y){
            $cash_y=0;
        }
        
        $cash_rate='0%';
        $cash_rate_c='';
        if($cash_t==0 && $cash_y==0){
            
        }else if($cash_t==0){
            $cash_rate=$cash_y.'%';
            $cash_rate_c='down';
        }else if($cash_y==0){
            $cash_rate=$cash_t.'%';
            $cash_rate_c='up';
        }else{
            $rate=floor(($cash_t - $cash_y)/$cash_y*100);
            if($rate>0){
                $cash_rate_c='up';
            }else if($rate<0){
                $cash_rate_c='down';
            }
            $cash_rate=abs($rate).'%';
        }
        $this->assign('cash', $cash);
        $this->assign('cash_t', $cash_t);
        $this->assign('cash_y', $cash_y);
        $this->assign('cash_rate', $cash_rate);
        $this->assign('cash_rate_c', $cash_rate_c);
        
        
        /* 审核统计 */
        
        /* 认证 */
        $auths=Db::name('user_auth')->where("status=0 ")->count();
        if(!$auths){
            $auths=0;
        }
        
        $auths_t=Db::name('user_auth')->where("status=0 and addtime >= {$today_start} and addtime < {$today_end}")->count();
        if(!$auths_t){
            $auths_t=0;
        }
        
        $auths_y=Db::name('user_auth')->where("status=0 and addtime >= {$yesterday_start} and addtime < {$today_start}")->count();
        if(!$auths_y){
            $auths_y=0;
        }
        
        $auths_rate='0%';
        $auths_rate_c='';
        if($auths_t==0 && $auths_y==0){
            
        }else if($auths_t==0){
            $auths_rate=$auths_y.'%';
            $auths_rate_c='down';
        }else if($auths_y==0){
            $auths_rate=$auths_t.'%';
            $auths_rate_c='up';
        }else{
            $rate=floor(($auths_t - $auths_y)/$auths_y*100);
            if($rate>0){
                $auths_rate_c='up';
            }else if($rate<0){
                $auths_rate_c='down';
            }
            $auths_rate=abs($rate).'%';
        }
        $this->assign('auths', $auths);
        $this->assign('auths_t', $auths_t);
        $this->assign('auths_y', $auths_y);
        $this->assign('auths_rate', $auths_rate);
        $this->assign('auths_rate_c', $auths_rate_c);
		
		
		
		/* 技能 */
        $skills=Db::name('skill_auth')->where("status=0")->count();
        if(!$skills){
            $skills=0;
        }
        
        $skills_t=Db::name('skill_auth')->where("status=0 and addtime >= {$today_start} and addtime < {$today_end}")->count();
        if(!$skills_t){
            $skills_t=0;
        }
        
        $skills_y=Db::name('skill_auth')->where("status=0 and addtime >= {$yesterday_start} and addtime < {$today_start}")->count();
        if(!$skills_y){
            $skills_y=0;
        }
        
        $skills_rate='0%';
        $skills_rate_c='';
        if($skills_t==0 && $skills_y==0){
            
        }else if($skills_t==0){
            $skills_rate=$skills_y.'%';
            $skills_rate_c='down';
        }else if($skills_y==0){
            $skills_rate=$skills_t.'%';
            $skills_rate_c='up';
        }else{
            $rate=floor(($skills_t - $skills_y)/$skills_y*100);
            if($rate>0){
                $skills_rate_c='up';
            }else if($rate<0){
                $skills_rate_c='down';
            }
            $skills_rate=abs($rate).'%';
        }
        $this->assign('skills', $skills);
        $this->assign('skills_t', $skills_t);
        $this->assign('skills_y', $skills_y);
        $this->assign('skills_rate', $skills_rate);
        $this->assign('skills_rate_c', $skills_rate_c);
		
		//技能订单统计
		$jnddlist = Db::name('skill')
			->field('id,name')
            ->order("list_order asc")
            ->select()
			->toArray();

		$jnrslist=$jnddlist;
		
		foreach($jnddlist as $k=>$v){
			
			$jndd_nums=Db::name('orders')->where("status=-2 and skillid={$v['id']} and addtime >= {$today_start} and addtime < {$today_end}")->count();
		
			

            $v['nums']=$jndd_nums;
	
            $jnddlist[$k]=$v;
		}
		
		$this->assign('jnddlist', $jnddlist);
		
		//技能人数统计
		foreach($jnrslist as $k=>$v){
			$jndd_nums=Db::name('skill_auth')->where("status=1 and skillid={$v['id']} and addtime >= {$today_start} and addtime < {$today_end}")->count();
			if(!$jndd_nums){
				$jndd_nums='0';
			}

            $v['nums']=$jndd_nums;
            $jnrslist[$k]=$v;
		}
		
		$this->assign('jnrslist', $jnrslist);
		
		
		//用户分析
		$yhfx=['nums0'=>0,'nums1'=>0,'nums2'=>0,'year0'=>0,'year1'=>0,'year2'=>0,'year3'=>0,'year4'=>0];
		
		//男
		$nums0=Db::name('user')->where("user_type=2 and sex=1 and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($nums0){
			$yhfx['nums0']=$nums0;
		}
		
		//女
		$nums1=Db::name('user')->where("user_type=2 and sex!=1 and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($nums1){
			$yhfx['nums1']=$nums1;
		}
		
		//总
		$nums2=Db::name('user')->where("user_type=2  and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($nums2){
			$yhfx['nums2']=$nums2;
		}
		
		
		//0-10
		$today_start_0=time()-60*60*24*365*10;
		$today_end_0=time();
		$year0=Db::name('user')->where("user_type=2  and birthday >= {$today_start_0} and birthday < {$today_end_0} and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($year0){
			$yhfx['year0']=$year0;
		}
		//11-20
		$today_start_1=time()-60*60*24*365*20;
		$year1=Db::name('user')->where("user_type=2  and birthday >= {$today_start_1} and birthday < {$today_start_0} and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($year1){
			$yhfx['year1']=$year1;
		}
		
		//21-30
		$today_start_2=time()-60*60*24*365*30;
		$year2=Db::name('user')->where("user_type=2  and birthday >= {$today_start_2} and birthday < {$today_start_1} and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($year2){
			$yhfx['year2']=$year2;
		}
		//31-40
		$today_start_3=time()-60*60*24*365*40;
		$year3=Db::name('user')->where("user_type=2  and birthday >= {$today_start_3} and birthday < {$today_start_2} and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($year3){
			$yhfx['year3']=$year3;
		}
		
		//40上
		$year4=Db::name('user')->where("user_type=2  and  birthday < {$today_start_3} and create_time >= {$today_start} and create_time < {$today_end}")->count();
		if($year4){
			$yhfx['year4']=$year4;
		}
		
		$this->assign('yhfx', $yhfx);
		
		//动态功能分析
		$dtgn=['dt0'=>0,'dt1'=>0,'dt2'=>0,'dt3'=>0,'dt4'=>0,'dt5'=>0];
		
		
		//动态数量
		$dt0=Db::name('dynamic')
			->where("status=1 and addtime >= {$today_start} and addtime < {$today_end}")
			->field('id,uid')
			->select()
			->toArray();

	
		if(!empty($dt0)){
			$dt0s=count($dt0);
			$dtgn['dt0']=$dt0s;
			
			
			$ids=array_column($dt0,'id');
			$dids = implode(",", $ids);
	
		
			//评论数量
			$dt1=Db::name('dynamic_comment')->where("did in ({$dids}) and addtime >= {$today_start} and addtime < {$today_end}")->count();
			if($dt1){
				$dtgn['dt1']=$dt1;
			}
			
			
			//点赞数量
			$dt2=Db::name('dynamic_like')->where("did in ({$dids}) and addtime >= {$today_start} and addtime < {$today_end}")->count();
			if($dt2){
				$dtgn['dt2']=$dt2;
			}
			
			
			//技能动态占比
			$dt3=Db::name('dynamic')->where("status=1 and skillid!=0 and addtime >= {$today_start} and addtime < {$today_end}")->count();
			if($dt3){
				$dt3s=($dt3/$dt0s)*100;
				$dtgn['dt3']=(int)$dt3s;
				
			}
			

			$sex1=0; //男
			$sex2=0; //女
			foreach($dt0 as $k=>$v){
				$user_sex=Db::name('user')->where('id',$v['uid'])->value('sex');
				if($user_sex==1){
					$sex1=$sex1+1;
				}else{
					$sex2=$sex2+1;
				}
			}
			
			//男占用比例
			$sex1=($sex1/$dt0s)*100;
			$dtgn['dt4']=(int)$sex1;
			
			//女占用比例
			$sex2=($sex2/$dt0s)*100;
			$dtgn['dt5']=(int)$sex2;
			
		}
		

		$this->assign('dtgn', $dtgn);
		
		
		
		//滴滴下单功能分析
		$ddxd=['dd0'=>0,'dd1'=>0,'dd2'=>0,'dd3'=>0,'dd4'=>0];
		
		
		//动态数量
		$dd0=Db::name('drip')
			->where("addtime >= {$today_start} and addtime < {$today_end}")
			->field('id,uid')
			->select()
			->toArray();
	
	
		if(!empty($dd0)){
			$dd0s=count($dd0);
			$ddxd['dd0']=$dd0s;
			$ids=array_column($dd0,'id');
			$dids = implode(",", $ids);

			$sex1=0; //男
			$sex2=0; //女
			foreach($dd0 as $k=>$v){
				$user_sex=Db::name('user')->where('id',$v['uid'])->value('sex');
				if($user_sex==1){
					$sex1=$sex1+1;
				}else{
					$sex2=$sex2+1;
				}
			}
			
			//男占用比例
			$sex1=($sex1/$dd0s)*100;
			$ddxd['dd1']=(int)$sex1;
			
			//女占用比例
			$sex2=($sex2/$dd0s)*100;
			$ddxd['dd2']=(int)$sex2;
			
			//平均抢单人数
			$dd3=Db::name('drip')->where("status=1 and addtime >= {$today_start} and addtime < {$today_end}")->count();
			
			if($dd3){
				$dd3s=$dd0s/$dd3;
				$ddxd['dd3']=(int)$dd3s;
			}
			
			
			//下单成功率
			$dd4=Db::name('drip')->where("status=1 and addtime >= {$today_start} and addtime < {$today_end}")->count();
			if($dd4){
				$dt4s=($dd4/$dd0s)*100;
				$ddxd['dd4']=(int)$dt4s;
			}

		}
		

		$this->assign('ddxd', $ddxd);
		
		
		
		
		
		
        return $this->fetch();
    }
	
	
	//技能订单统计
	public function jndd_lists(){
		$data = $this->request->param();
		if($data['daystart']=='' && $data['dayend']==''){
			//查询类型
			switch ($data['day']) {
				case '0':
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));
					$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";

				break;
				
				case '1':
					//获取昨天开始结束时间
					$dayStart=strtotime(date("Y-m-d 0:0:0",strtotime("-1 day")));
					$dayEnd=strtotime(date("Y-m-d 0:0:0"));
					$where=" addtime >={$dayStart} and addtime<{$dayEnd} and ";

				break;
				
				case '2':
					$w=date('w'); 
					//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
					$first=1;
					//周一
					$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
					$week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

					//本周结束日期 
					//周天
					$week_end=strtotime("{$week} +1 week")-1;
					
					$where=" addtime >={$week_start} and addtime<={$week_end} and ";

				break;

				case '3':
					//本月第一天
					$month=date('Y-m-d',strtotime(date("Ym").'01'));
					$month_start=strtotime(date("Ym").'01');

					//本月最后一天
					$month_end=strtotime("{$month} +1 month")-1;

					$where=" addtime >={$month_start} and addtime<={$month_end} and ";

				break;

				case '4':
					$where="";
				break;
				
				default:
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));
					$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";
				break;
			}
		
		}else{
			$dayStart=strtotime($data['daystart']);
			$dayEnd=strtotime($data['dayend']);
			$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";
			
		}

		$list = Db::name('skill')
			->field('id,name')
            ->order("list_order asc")
            ->select()
			->toArray();
		
		
		foreach($list as $k=>$v){
			$nums=Db::name('orders')->where($where."status=-2 and skillid={$v['id']}")->count();
	

            $v['nums']=$nums;
            $list[$k]=$v;
		}
		echo json_encode($list);
		exit;
	}
	
	
	//技能人数统计
	public function jnrs_lists(){
		$data = $this->request->param();
		if($data['daystart']=='' && $data['dayend']==''){
			//查询类型
			switch ($data['day']) {
				case '0':
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));
					$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";

				break;
				
				case '1':
					//获取昨天开始结束时间
					$dayStart=strtotime(date("Y-m-d 0:0:0",strtotime("-1 day")));
					$dayEnd=strtotime(date("Y-m-d 0:0:0"));
					$where=" addtime >={$dayStart} and addtime<{$dayEnd} and ";

				break;
				
				case '2':
					$w=date('w'); 
					//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
					$first=1;
					//周一
					$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
					$week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

					//本周结束日期 
					//周天
					$week_end=strtotime("{$week} +1 week")-1;
					
					$where=" addtime >={$week_start} and addtime<={$week_end} and ";

				break;

				case '3':
					//本月第一天
					$month=date('Y-m-d',strtotime(date("Ym").'01'));
					$month_start=strtotime(date("Ym").'01');

					//本月最后一天
					$month_end=strtotime("{$month} +1 month")-1;

					$where=" addtime >={$month_start} and addtime<={$month_end} and ";

				break;

				case '4':
					$where="";
				break;
				
				default:
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));
					$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";
				break;
			}
		
		}else{
			$dayStart=strtotime($data['daystart']);
			$dayEnd=strtotime($data['dayend']);
			$where=" addtime >={$dayStart} and addtime<={$dayEnd} and ";
			
		}

		$list = Db::name('skill')
			->field('id,name')
            ->order("list_order asc")
            ->select()
			->toArray();
		
		
		foreach($list as $k=>$v){
			$nums=Db::name('skill_auth')->where($where."status=1 and skillid={$v['id']}")->count();
	

            $v['nums']=$nums;
            $list[$k]=$v;
		}
		echo json_encode($list);
		exit;
	}
	
	
	//用户分析
	public function yhfx_lists(){
		$data = $this->request->param();
		if($data['daystart']=='' && $data['dayend']==''){
			//查询类型
			switch ($data['day']) {
				case '0':
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));

				break;
				
				case '1':
					//获取昨天开始结束时间
					$dayStart=strtotime(date("Y-m-d 0:0:0",strtotime("-1 day")));
					$dayEnd=strtotime(date("Y-m-d 0:0:0"));
				break;
				
				case '2':
					$w=date('w'); 
					//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
					$first=1;
					//周一
					$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
					$dayStart=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

					//本周结束日期 
					//周天
					$dayEnd=strtotime("{$week} +1 week")-1;
				

				break;

				case '3':
					//本月第一天
					$month=date('Y-m-d',strtotime(date("Ym").'01'));
					$dayStart=strtotime(date("Ym").'01');

					//本月最后一天
					$dayEnd=strtotime("{$month} +1 month")-1;

				break;

				case '4':
					$dayStart='0';
				break;
				
				default:
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));

				break;
			}
		
		}else{
			$dayStart=strtotime($data['daystart']);
			$dayEnd=strtotime($data['dayend']);

			
		}
		
		
		$where="";
		if($dayStart>0){
			$where="  and create_time >= {$dayStart} and create_time < {$dayEnd}";
		}
		
		//用户分析
		$yhfx=['nums0'=>0,'nums1'=>0,'nums2'=>0,'year0'=>0,'year1'=>0,'year2'=>0,'year3'=>0,'year4'=>0];
		
		
		
		//男
		$nums0=Db::name('user')->where("user_type=2 and sex=1".$where)->count();
		if($nums0){
			$yhfx['nums0']=$nums0;
		}
		
		
		//女
		$nums1=Db::name('user')->where("user_type=2 and sex!=1".$where)->count();
		if($nums1){
			$yhfx['nums1']=$nums1;
		}
		
		//总
		$nums2=Db::name('user')->where("user_type=2".$where)->count();
		if($nums2){
			$yhfx['nums2']=$nums2;
		}
		
		
		
		//0-10
		$today_end_0=time();
		$today_start_0=$today_end_0-(60*60*24*365*10);
		
		
		$year0=Db::name('user')->where("user_type=2  and birthday >= {$today_start_0} and birthday < {$today_end_0}".$where)->count();
		if($year0){
			$yhfx['year0']=$year0;
		}
		
		
		
		//11-20
		$today_start_1=time()-60*60*24*365*20;
		$year1=Db::name('user')->where("user_type=2  and birthday >= {$today_start_1} and birthday < {$today_start_0}".$where)->count();
		if($year1){
			$yhfx['year1']=$year1;
		}
		
		//21-30
		$today_start_2=time()-60*60*24*365*30;
		$year2=Db::name('user')->where("user_type=2  and birthday >= {$today_start_2} and birthday < {$today_start_1}".$where)->count();
		if($year2){
			$yhfx['year2']=$year2;
		}
		//31-40
		$today_start_3=time()-60*60*24*365*40;
		$year3=Db::name('user')->where("user_type=2  and birthday >= {$today_start_3} and birthday < {$today_start_2}".$where)->count();
		if($year3){
			$yhfx['year3']=$year3;
		}
		
		//40上
		$year4=Db::name('user')->where("user_type=2  and  birthday < {$today_start_3}".$where)->count();
		if($year4){
			$yhfx['year4']=$year4;
		}
		
		echo json_encode($yhfx);
		exit;
	}


	//动态功能分析
	public function dtgn_lists(){
		$data = $this->request->param();
		if($data['daystart']=='' && $data['dayend']==''){
			//查询类型
			switch ($data['day']) {
				case '0':
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));

				break;
				
				case '1':
					//获取昨天开始结束时间
					$dayStart=strtotime(date("Y-m-d 0:0:0",strtotime("-1 day")));
					$dayEnd=strtotime(date("Y-m-d 0:0:0"));
				break;
				
				case '2':
					$w=date('w'); 
					//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
					$first=1;
					//周一
					$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
					$dayStart=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

					//本周结束日期 
					//周天
					$dayEnd=strtotime("{$week} +1 week")-1;
				

				break;

				case '3':
					//本月第一天
					$month=date('Y-m-d',strtotime(date("Ym").'01'));
					$dayStart=strtotime(date("Ym").'01');

					//本月最后一天
					$dayEnd=strtotime("{$month} +1 month")-1;

				break;

				case '4':
					$dayStart='0';
				break;
				
				default:
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));

				break;
			}
		
		}else{
			$dayStart=strtotime($data['daystart']);
			$dayEnd=strtotime($data['dayend']);
			
			
		}
		$where="";
		if($dayStart>0){
			$where="  and addtime >= {$dayStart} and addtime < {$dayEnd}";
		}

		//动态功能分析
		$dtgn=['dt0'=>0,'dt1'=>0,'dt2'=>0,'dt3'=>0,'dt4'=>0,'dt5'=>0];
		
		
		//动态数量
		$dt0=Db::name('dynamic')
			->where("status=1".$where)
			->field('id,uid')
			->select()
			->toArray();
			
		

		if(!empty($dt0)){
			$dt0s=count($dt0);
			$dtgn['dt0']=$dt0s;
			
			$ids=array_column($dt0,'id');
			$dids = implode(",", $ids);
	
		
			//评论数量
			$dt1=Db::name('dynamic_comment')->where("did in ({$dids})".$where)->count();
			if($dt1){
				$dtgn['dt1']=$dt1;
			}
			
			
			//点赞数量
			$dt2=Db::name('dynamic_like')->where("did in ({$dids})".$where)->count();
			if($dt2){
				$dtgn['dt2']=$dt2;
			}
			
			
			//技能动态占比
			$dt3=Db::name('dynamic')->where("status=1 and skillid!=0".$where)->count();
			if($dt3){
				$dt3s=($dt3/$dt0s)*100;
				$dtgn['dt3']=(int)$dt3s;
				
			}

			$sex1=0; //男
			$sex2=0; //女
			foreach($dt0 as $k=>$v){
				$user_sex=Db::name('user')->where('id',$v['uid'])->value('sex');
				if($user_sex==1){
					$sex1=$sex1+1;
				}else{
					$sex2=$sex2+1;
				}
			}
			
			//男占用比例
			$sex1=($sex1/$dt0s)*100;
			$dtgn['dt4']=(int)$sex1;
			
			//女占用比例
			$sex2=($sex2/$dt0s)*100;
			$dtgn['dt5']=(int)$sex2;
			
		}
		
		
		echo json_encode($dtgn);
		exit;
	}
	
	//滴滴下单功能分析
	public function ddxd_lists(){
		$data = $this->request->param();
		if($data['daystart']=='' && $data['dayend']==''){
			//查询类型
			switch ($data['day']) {
				case '0':
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));

				break;
				
				case '1':
					//获取昨天开始结束时间
					$dayStart=strtotime(date("Y-m-d 0:0:0",strtotime("-1 day")));
					$dayEnd=strtotime(date("Y-m-d 0:0:0"));
				break;
				
				case '2':
					$w=date('w'); 
					//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
					$first=1;
					//周一
					$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
					$dayStart=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

					//本周结束日期 
					//周天
					$dayEnd=strtotime("{$week} +1 week")-1;
				

				break;

				case '3':
					//本月第一天
					$month=date('Y-m-d',strtotime(date("Ym").'01'));
					$dayStart=strtotime(date("Ym").'01');

					//本月最后一天
					$dayEnd=strtotime("{$month} +1 month")-1;

				break;

				case '4':
					$dayStart='0';
				break;
				
				default:
					//获取今天开始结束时间
					$dayStart=strtotime(date("Y-m-d"));
					$dayEnd=strtotime(date("Y-m-d 23:59:59"));

				break;
			}
		
		}else{
			$dayStart=strtotime($data['daystart']);
			$dayEnd=strtotime($data['dayend']);
			
			
		}
		$where="";
		if($dayStart>0){
			$where="  and addtime >= {$dayStart} and addtime < {$dayEnd}";
			$where1="addtime >= {$dayStart} and addtime < {$dayEnd}";
			
			
		}
		
		$ddxd=['dd0'=>0,'dd1'=>0,'dd2'=>0,'dd3'=>0,'dd4'=>0];
		
		
		//动态数量
		$dd0=Db::name('drip')
			->where($where1)
			->field('id,uid')
			->select()
			->toArray();
	
	
		if(!empty($dd0)){
			$dd0s=count($dd0);
			$ddxd['dd0']=$dd0s;
			$ids=array_column($dd0,'id');
			$dids = implode(",", $ids);
			
		
			$sex1=0; //男
			$sex2=0; //女
			foreach($dd0 as $k=>$v){
				$user_sex=Db::name('user')->where('id',$v['uid'])->value('sex');
				if($user_sex==1){
					$sex1=$sex1+1;
				}else{
					$sex2=$sex2+1;
				}
			}
			
			//男占用比例
			$sex1=($sex1/$dd0s)*100;
			$ddxd['dd1']=(int)$sex1;
			
			//女占用比例
			$sex2=($sex2/$dd0s)*100;
			$ddxd['dd2']=(int)$sex2;
			
			//平均抢单人数
			$dd3=Db::name('drip')->where("status=1".$where)->count();
			if($dd3){
				$dd3s=$dd0s/$dd3;
				$ddxd['dd3']=(int)$dd3s;
			}
			
			
			//下单成功率
			$dd4=Db::name('drip')->where("status=1".$where)->count();
			if($dd4){
				$dt4s=($dd4/$dd0s)*100;
				$ddxd['dd4']=(int)$dt4s;
			}

		}
		
		
		echo json_encode($ddxd);
		exit;
	}
}
