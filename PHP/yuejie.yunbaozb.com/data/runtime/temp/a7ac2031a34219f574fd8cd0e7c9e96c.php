<?php /*a:2:{s:80:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/main/index.html";i:1603704624;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->


    <link href="/themes/admin_simpleboot3/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/admin_simpleboot3/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        form .input-order {
            margin-bottom: 0px;
            padding: 0 2px;
            width: 42px;
            font-size: 12px;
        }

        form .input-order:focus {
            outline: none;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .form-required {
            color: red;
        }
    </style>
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "static/js/",
            APP: '<?php echo app('request')->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/themes/admin_simpleboot3/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="/static/js/wind.js"></script>
    <script src="/themes/admin_simpleboot3/public/assets/js/bootstrap.min.js"></script>
    <script>
        Wind.css('artDialog');
        Wind.css('layer');
        $(function () {
            $("[data-toggle='tooltip']").tooltip({
                container:'body',
                html:true,
            });
            $("li.dropdown").hover(function () {
                $(this).addClass("open");
            }, function () {
                $(this).removeClass("open");
            });
        });
    </script>
    <?php if(APP_DEBUG): ?>
        <style>
            #think_page_trace_open {
                z-index: 9999;
            }
        </style>
    <?php endif; ?>
<link rel="stylesheet" type="text/css" href="/static/admin/css/index.css">
<style>
    .home-info li em {
        float: left;
        width: 120px;
        font-style: normal;
        font-weight: bold;
    }

    .home-info ul {
        padding: 0;
        margin: 0;
    }

    .panel {
        margin-bottom: 0;
    }

    .grid-sizer {
        width: 10%;
    }

    .grid-item {
        margin-bottom: 5px;
        padding: 5px;
    }

    .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
        padding-left: 5px;
        padding-right: 5px;
        float: none;
    }

</style>
<?php 
    \think\facade\Hook::listen('admin_before_head_end',null,false);
 ?>
</head>
<body>
<div class="wrap">
    <div class="home-grid">
        <!-- width of .grid-sizer used for columnWidth -->
        <div class="grid-sizer"></div>
        <div class="list">
            <div class="list_title list_title1">充值统计</div>
            <div class="list_bd">
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">今日支付金额</p>
                        <p class="bd_pt_top_n"><?php echo $pay_money_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $pay_money_rate_c; ?>"><?php echo $pay_money_rate; ?></span></p>
                        <p>昨日：<span><?php echo $pay_money_y; ?></span></p>
                        <p>总支付金额：<span><?php echo $pay_money; ?></span></p>
                    </div>
                </div>
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">今日支付订单数</p>
                        <p class="bd_pt_top_n"><?php echo $pay_orders_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $pay_orders_rate_c; ?>"><?php echo $pay_orders_rate; ?></span></p>
                        <p>昨日：<span><?php echo $pay_orders_y; ?></span></p>
                        <p>总支付订单数：<span><?php echo $pay_orders; ?></span></p>
                    </div>
                </div>
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">今日支付人数</p>
                        <p class="bd_pt_top_n"><?php echo $pay_users_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $pay_users_rate_c; ?>"><?php echo $pay_users_rate; ?></span></p>
                        <p>昨日：<span><?php echo $pay_users_y; ?></span></p>
                        <p>总支付人数：<span><?php echo $pay_users; ?></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="list">
            <div class="list_title list_title2">注册统计</div>
            <div class="list_bd">
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">平台注册人数</p>
                        <p class="bd_pt_top_n"><?php echo $users_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $users_rate_c; ?>"><?php echo $users_rate; ?></span></p>
                        <p>昨日：<span><?php echo $users_y; ?></span></p>
                        <p>平台总注册人数：<span><?php echo $users; ?></span></p>
                    </div>
                </div>
                <!-- <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">代理注册人数</p>
                        <p class="bd_pt_top_n">1000.00</p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="up">10%</span></p>
                        <p>昨日：<span>900.00</span></p>
                        <p>代理总注册人数：<span>1900.00</span></p>
                    </div>
                </div> -->
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">邀请注册人数</p>
                        <p class="bd_pt_top_n"><?php echo $agent_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $agent_rate_c; ?>"><?php echo $agent_rate; ?></span></p>
                        <p>昨日：<span><?php echo $agent_y; ?></span></p>
                        <p>邀请总注册人数：<span><?php echo $agent; ?></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="list">
            <div class="list_title list_title3">消费统计</div>
            <div class="list_bd">
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">今日消费数</p>
                        <p class="bd_pt_top_n"><?php echo $coins_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $coins_rate_c; ?>"><?php echo $coins_rate; ?></span></p>
                        <p>昨日：<span><?php echo $coins_y; ?></span></p>
                        <p>总消费数：<span><?php echo $coins; ?></span></p>
                    </div>
                </div>
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">今日收益数</p>
                        <p class="bd_pt_top_n"><?php echo $votes_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $votes_rate_c; ?>"><?php echo $votes_rate; ?></span></p>
                        <p>昨日：<span><?php echo $votes_y; ?></span></p>
                        <p>总收益数：<span><?php echo $votes; ?></span></p>
                    </div>
                </div>
                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">今日提现数</p>
                        <p class="bd_pt_top_n"><?php echo $cash_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $cash_rate_c; ?>"><?php echo $cash_rate; ?></span></p>
                        <p>昨日：<span><?php echo $cash_y; ?></span></p>
                        <p>总提现数：<span><?php echo $cash; ?></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="list">
            <div class="list_title list_title4">审核统计</div>
            <div class="list_bd">

                <div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">信息认证未审核数</p>
                        <p class="bd_pt_top_n"><?php echo $auths_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $auths_rate_c; ?>"><?php echo $auths_rate; ?></span></p>
                        <p>昨日：<span><?php echo $auths_y; ?></span></p>
                        <p>总信息认证数：<span><?php echo $auths; ?></span></p>
                    </div>
                </div>
				
				<div class="bd_pt">
                    <div class="bd_pt_top">
                        <p class="bd_pt_top_t">技能认证未审核数</p>
                        <p class="bd_pt_top_n"><?php echo $skills_t; ?></p>
                    </div>
                    
                    <div class="bd_pt_bot">
                        <p>较于前一日：<span class="<?php echo $skills_rate_c; ?>"><?php echo $skills_rate; ?></span></p>
                        <p>昨日：<span><?php echo $skills_y; ?></span></p>
                        <p>总信息认证数：<span><?php echo $skills; ?></span></p>
                    </div>
                </div>
				
            </div>
        </div>
		
		<div class="list">
            <div class="list_title list_title5">技能订单统计</div>
            <div class="list_bd_bg">
				<div class="bd_bg_top">
					<ul class="type jndd_type">
						<li class="on" data-day='0' data-list="jnddlist">今日</li>
						<li data-day='1' data-list="jnddlist">昨日</li>
						<li data-day='2' data-list="jnddlist">近7日</li>
						<li data-day='3' data-list="jnddlist">近30日</li>
						<li data-day='4' data-list="jnddlist">总计</li>
					</ul>
					<div class="top_tome">
						 <div class="data_select">
							<input type="text" name="start_time" class="jnddlist_start_time  bg-control js-date date" value="" style="width: 120px;" autocomplete="off" placeholder="开始时间"> -
							<input type="text" class="jnddlist_end_time bg-control js-date date" name="end_time" value="" style="width: 120px;" autocomplete="off" placeholder="结束时间">
						</div>
						
						<div class="search jndd_search"></div>
					</div>
				</div>
				
				<div class="bd_bg_content">
					<ul class="list jnddlist">
					<?php if(is_array($jnddlist) || $jnddlist instanceof \think\Collection || $jnddlist instanceof \think\Paginator): $i = 0; $__LIST__ = $jnddlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<li>
							<div class="name"><?php echo $vo['name']; ?></div>
							<div class="num"><?php echo $vo['nums']; ?></div>
						</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
						
					</ul>
				
				</div>
				
            </div>
        </div>
		
		<div class="list">
            <div class="list_title list_title6">技能人数统计</div>
            <div class="list_bd_bg">
				<div class="bd_bg_top">
					<ul class="type jnrs_type">
						<li class="on" data-day='0'>今日</li>
						<li data-day='1'>昨日</li>
						<li data-day='2'>近7日</li>
						<li data-day='3'>近30日</li>
						<li data-day='4'>总计</li>
					</ul>
					<div class="top_tome">
						 <div class="data_select">
							<input type="text" name="start_time" class="jnrslist_start_time  bg-control js-date date" value="" style="width: 120px;" autocomplete="off" placeholder="开始时间"> -
							<input type="text" class="jnrslist_end_time bg-control js-date date" name="end_time" value="" style="width: 120px;" autocomplete="off" placeholder="结束时间">
						</div>
						
						<div class="search jnrs_search"></div>
					</div>
				</div>
				
				<div class="bd_bg_content">
					<ul class="list jnrslist">
					<?php if(is_array($jnrslist) || $jnrslist instanceof \think\Collection || $jnrslist instanceof \think\Paginator): $i = 0; $__LIST__ = $jnrslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<li>
							<div class="name"><?php echo $vo['name']; ?></div>
							<div class="num"><?php echo $vo['nums']; ?></div>
						</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
						
					</ul>
				
				</div>
				
            </div>
        </div>
		
		
		<div class="list">
            <div class="list_title list_title7">用户分析</div>
            <div class="list_bd_bg">
				<div class="bd_bg_top">
					<ul class="type yhfx_type">
						<li class="on" data-day='0'>今日</li>
						<li data-day='1'>昨日</li>
						<li data-day='2'>近7日</li>
						<li data-day='3'>近30日</li>
						<li data-day='4'>总计</li>
					</ul>
					<div class="top_tome">
						 <div class="data_select">
							<input type="text" name="start_time" class="yhfxlist_start_time  bg-control js-date date" value="" style="width: 120px;" autocomplete="off" placeholder="开始时间"> -
							<input type="text" class="yhfxlist_end_time bg-control js-date date" name="end_time" value="" style="width: 120px;" autocomplete="off" placeholder="结束时间">
						</div>
						
						<div class="search yhfx_search"></div>
					</div>
				</div>
				
				<div class="bd_bg_content yhfxlist">
					<ul class="list ">
						<li>
							<div class="name">男生注册人数</div>
							<div class="num"><?php echo $yhfx['nums0']; ?></div>
						</li>
						<li>
							<div class="name">女生注册人数</div>
							<div class="num"><?php echo $yhfx['nums1']; ?></div>
						</li>
						<li>
							<div class="name">总注册人数</div>
							<div class="num"><?php echo $yhfx['nums2']; ?></div>
						</li>
					</ul>
					
					<ul class="list ">
						<li>
							<div class="name">0~10岁</div>
							<div class="num"><?php echo $yhfx['year0']; ?></div>
						</li>
						<li>
							<div class="name">11~20岁</div>
							<div class="num"><?php echo $yhfx['year1']; ?></div>
						</li>
						<li>
							<div class="name">21~30岁</div>
							<div class="num"><?php echo $yhfx['year2']; ?></div>
						</li>
						<li>
							<div class="name">31~40岁</div>
							<div class="num"><?php echo $yhfx['year3']; ?></div>
						</li>
						<li>
							<div class="name">41岁以上</div>
							<div class="num"><?php echo $yhfx['year4']; ?></div>
						</li>
					</ul>
				
				</div>
				
            </div>
        </div>
		
		<div class="list">
            <div class="list_title list_title8">动态功能分析</div>
            <div class="list_bd_bg">
				<div class="bd_bg_top">
					<ul class="type dtgn_type">
						<li class="on" data-day='0'>今日</li>
						<li data-day='1'>昨日</li>
						<li data-day='2'>近7日</li>
						<li data-day='3'>近30日</li>
						<li data-day='4'>总计</li>
					</ul>
					<div class="top_tome">
						 <div class="data_select">
							<input type="text" name="start_time" class="dtgnlist_start_time  bg-control js-date date" value="" style="width: 120px;" autocomplete="off" placeholder="开始时间"> -
							<input type="text" class="dtgnlist_end_time bg-control js-date date" name="end_time" value="" style="width: 120px;" autocomplete="off" placeholder="结束时间">
						</div>
						
						<div class="search dtgn_search"></div>
					</div>
				</div>
				
				<div class="bd_bg_content ">
					<ul class="list dtgnlist">
						<li>
							<div class="name">动态数量</div>
							<div class="num"><?php echo $dtgn['dt0']; ?></div>
						</li>
						<li>
							<div class="name">评论数量</div>
							<div class="num"><?php echo $dtgn['dt1']; ?></div>
						</li>
						<li>
							<div class="name">点赞数量</div>
							<div class="num"><?php echo $dtgn['dt2']; ?></div>
						</li>
						<li>
							<div class="name">技能动态占比</div>
							<div class="num"><?php echo $dtgn['dt3']; ?>%</div>
						</li>
						<li>
							<div class="name">使用男女占比</div>
							<div class="num"><?php echo $dtgn['dt4']; ?>%-<?php echo $dtgn['dt5']; ?>%</div>
						</li>
					</ul>
					

				</div>
				
            </div>
        </div>
		
		<div class="list">
            <div class="list_title list_title9">滴滴下单功能分析</div>
            <div class="list_bd_bg">
				<div class="bd_bg_top">
					<ul class="type ddxd_type">
						<li class="on" data-day='0'>今日</li>
						<li data-day='1'>昨日</li>
						<li data-day='2'>近7日</li>
						<li data-day='3'>近30日</li>
						<li data-day='4'>总计</li>
					</ul>
					<div class="top_tome">
						 <div class="data_select">
							<input type="text" name="start_time" class="ddxdlist_start_time  bg-control js-date date" value="" style="width: 120px;" autocomplete="off" placeholder="开始时间"> -
							<input type="text" class="ddxdlist_end_time bg-control js-date date" name="end_time" value="" style="width: 120px;" autocomplete="off" placeholder="结束时间">
						</div>
						
						<div class="search ddxd_search"></div>
					</div>
				</div>
				
				<div class="bd_bg_content ">
					<ul class="list ddxdlist">
						<li>
							<div class="name">使用次数</div>
							<div class="num"><?php echo $ddxd['dd0']; ?></div>
						</li>
						<li>
							<div class="name">使用男女占比</div>
							<div class="num"><?php echo $ddxd['dd1']; ?>%-<?php echo $ddxd['dd2']; ?>%</div>
						</li>
						<li>
							<div class="name">平均抢单人数</div>
							<div class="num"><?php echo $ddxd['dd3']; ?></div>
						</li>
						<li>
							<div class="name">下单成功率</div>
							<div class="num"><?php echo $ddxd['dd4']; ?>%</div>
						</li>

					</ul>
					

				</div>
				
            </div>
        </div>

    </div>
</div>
<script src="/static/js/admin.js"></script>
<script src="/static/admin/js/admin.js"></script>
<?php 
    $lang_set=defined('LANG_SET')?LANG_SET:'';
    $thinkcmf_version=cmf_version();
 ?>
<script>


</script>
<?php 
    \think\facade\Hook::listen('admin_before_body_end',null,false);
 ?>
</body>
</html>