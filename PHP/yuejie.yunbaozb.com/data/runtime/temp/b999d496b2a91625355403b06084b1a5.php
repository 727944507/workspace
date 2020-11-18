<?php /*a:2:{s:92:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/user\admin_index\index.html";i:1605599704;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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


    <link href="/public/themes/admin_simpleboot3/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="/public/themes/admin_simpleboot3/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
            ROOT: "/public/",
            WEB_ROOT: "/public/",
            JS_ROOT: "static/js/",
            APP: '<?php echo app('request')->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/public/themes/admin_simpleboot3/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="/public/static/js/wind.js"></script>
    <script src="/public/themes/admin_simpleboot3/public/assets/js/bootstrap.min.js"></script>
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
<style>
	.bg-gray {
	    color: #000;
	    background-color: #d2d6de !important;
	}
	.bg-red, .callout.callout-danger, .alert-danger, .alert-error, .modal-danger .modal-body {
	    background-color: #e74c3c !important;
	}
</style>
</head>
<body>
<div class="wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a><?php echo lang('USER_INDEXADMIN_INDEX'); ?></a></li>
    </ul>
    <form class="well form-inline margin-top-20" method="post" action="<?php echo url('user/adminIndex/index'); ?>">
        用户ID：
        <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>"
               placeholder="请输入用户ID">
        关键字：
        <input class="form-control" type="text" name="keyword" style="width: 200px;" value="<?php echo input('request.keyword'); ?>"
               placeholder="用户名/昵称/邮箱">
		设备来源：
        <select class="form-control" name="source">
            <option value="">全部</option>
                <option value="0" <?php if(input('request.source') != '' && input('request.source') == '0'): ?>selected<?php endif; ?>>PC</option>
                <option value="1" <?php if(input('request.source') != '' && input('request.source') == '1'): ?>selected<?php endif; ?>>安卓APP</option>
                <option value="2" <?php if(input('request.source') != '' && input('request.source') == '2'): ?>selected<?php endif; ?>>苹果APP</option>
                <option value="3" <?php if(input('request.source') != '' && input('request.source') == '3'): ?>selected<?php endif; ?>>小程序</option>
        </select>

        提交时间：
        <input class="form-control js-bootstrap-date" name="start_time" id="start_time" value="<?php echo input('request.start_time'); ?>" aria-invalid="false" style="width: 110px;"> - 
        <input class="form-control js-bootstrap-date" name="end_time" id="end_time" value="<?php echo input('request.end_time'); ?>" aria-invalid="false" style="width: 110px;">
            	   
        <input type="submit" class="btn btn-primary" value="搜索"/>
        <a class="btn btn-danger" href="<?php echo url('user/adminIndex/index'); ?>">清空</a>
		<br>
        <br>
		<a href="javascript:;" class="btn btn-default" style="font-size:14px;color:#ea5858;">
			<i class="fa fa-user fa-fw"></i>
			<span class="extend">
				用户总人数：<span id="personnum"><?php echo $nums; ?></span>
			</span>
		</a>
		<a href="javascript:;" class="btn btn-default" style="font-size:14px;color:dodgerblue;">
			<i class="fa fa-user fa-fw"></i>
			<span class="extend">
				在线总人数：<span id="personnum"><?php echo $online; ?></span>
			</span>
		</a>
    </form>
    <form method="post" class="js-ajax-form">
		<div style="overflow:scroll;">
        <table style="width: 225rem;" class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th><?php echo lang('USERNAME'); ?></th>
                <th><?php echo lang('NICENAME'); ?></th>
                <th><?php echo lang('AVATAR'); ?></th>
                <!--<th><?php echo lang('EMAIL'); ?></th>-->
				<!--新增栏目开始-->
				<th>在线状态</th>
				<th>性别</th>
				<th>系统</th>
				<th>实名</th>
				<th>真实姓名</th>
				<th>身份证</th>
				<th>登录渠道</th>
				<!--新增栏目结束-->
                <th>余额</th>
                <th>累计消费</th>
				<th>订单消费总额</th>
				<th>礼物消费总额</th>
                <!--<th>订单收益余额</th>
                <th>订单收益总额</th>
                <th>礼物收益余额</th>
                <th>礼物收益总额</th>-->
                <th><?php echo lang('REGISTRATION_TIME'); ?></th>
                <th><?php echo lang('LAST_LOGIN_TIME'); ?></th>
                <th><?php echo lang('LAST_LOGIN_IP'); ?></th>
                <th><?php echo lang('STATUS'); ?></th>
                <th><?php echo lang('ACTIONS'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php 
                $user_statuses=array("0"=>lang('USER_STATUS_BLOCKED'),"1"=>lang('USER_STATUS_ACTIVATED'),"2"=>lang('USER_STATUS_UNVERIFIED'));
             if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                <tr>
                    <td><?php echo $vo['id']; ?></td>
                    <td><?php echo !empty($vo['user_login']) ? $vo['user_login'] : ($vo['mobile']?$vo['mobile']:lang('THIRD_PARTY_USER')); ?>
                    </td>
                    <td><?php echo !empty($vo['user_nickname']) ? $vo['user_nickname'] : lang('NOT_FILLED'); ?></td>
                    <td><img width="25" height="25" src="<?php echo get_upload_path($vo['avatar']); ?>"/></td>
                    <!--<td><?php echo $vo['user_email']; ?></td>-->
					<!--新增栏目开始-->
					<td><?php echo !empty($vo['online']) ? '<span class="label bg-red">在线</span>' : '<span class="label bg-gray">离线</span>'; ?></td>
					<td>
						<?php if($vo['sex']==0): ?>
							保密
						<?php elseif($vo['sex']==1): ?>
							<span style="color:#cc2020;">男</span>
						<?php elseif($vo['sex']==2): ?>
							<span style="color:green">女</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if($vo['source']==0): ?>
							web
						<?php elseif($vo['source']==1): ?>
							android
						<?php elseif($vo['source']==2): ?>
							ios
						<?php elseif($vo['source']==3): ?>
							小程序
						<?php endif; ?>
					</td>
					<td>
						<?php if($vo['isauth']==0): ?>
							未认证
						<?php else: ?>
							<span style="color:#cc2020">
							已认证
							</span>
						<?php endif; ?>
					</td>
					<td><?php echo $vo['name']=='' ? '-' : $vo['name']; ?></td>
					<td><?php echo $vo['cer_no']=='' ? '-' : $vo['cer_no']; ?></td>
					<td>
						<?php if($vo['login_type']==0): ?>
							PC
						<?php elseif($vo['login_type']==1): ?>
							QQ
						<?php elseif($vo['login_type']==2): ?>
							微信
						<?php elseif($vo['login_type']==3): ?>
							新浪
						<?php elseif($vo['login_type']==4): ?>
							facebook
						<?php elseif($vo['login_type']==5): ?>
							twitter
						<?php elseif($vo['login_type']==6): ?>
							ios
						<?php endif; ?>
					</td>
					<!--新增栏目结束-->
                    <td><?php echo $vo['coin']; ?></td>
                    <td><?php echo $vo['consumption']; ?></td>
					<td><?php echo $vo['total3']; ?></td>
					<td><?php echo $vo['total2']; ?></td>
                    <!--<td><?php echo $vo['votes']; ?></td>
                    <td><?php echo $vo['votestotal']; ?></td>
					<td><?php echo $vo['votes_gift']; ?></td>
                    <td><?php echo $vo['votes_gifttotal']; ?></td>-->
                    <td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$vo['last_login_time']); ?></td>
                    <td><?php echo $vo['last_login_ip']; ?></td>
                    <td>
                        <?php switch($vo['user_status']): case "0": ?>
                                <span class="label label-danger"><?php echo $user_statuses[$vo['user_status']]; ?></span>
                            <?php break; case "1": ?>
                                <span class="label label-success"><?php echo $user_statuses[$vo['user_status']]; ?></span>
                            <?php break; case "2": ?>
                                <span class="label label-warning"><?php echo $user_statuses[$vo['user_status']]; ?></span>
                            <?php break; ?>
                        <?php endswitch; ?>
                    </td>
                    <td>
						<a class="btn btn-xs btn-primary" style="background-color: #18bc9c;border-color: #18bc9c;" href="javascript:parent.openIframeLayer('<?php echo url('adminIndex/update',array('user_id'=>$vo['id'])); ?>','管理 <?php echo $vo['name']; ?> 页面',{});">编辑</a>
                        <?php if($vo['id'] != '1'): if(empty($vo['user_status']) || (($vo['user_status'] instanceof \think\Collection || $vo['user_status'] instanceof \think\Paginator ) && $vo['user_status']->isEmpty())): ?>
                                <a class="btn btn-xs btn-success js-ajax-dialog-btn"
                                   href="<?php echo url('adminIndex/cancelban',array('id'=>$vo['id'])); ?>"
                                   data-msg="<?php echo lang('ACTIVATE_USER_CONFIRM_MESSAGE'); ?>"><?php echo lang('ACTIVATE_USER'); ?></a>
                                <?php else: ?>
                                <a class="btn btn-xs btn-warning js-ajax-dialog-btn"
                                   href="<?php echo url('adminIndex/ban',array('id'=>$vo['id'])); ?>"
                                   data-msg="<?php echo lang('BLOCK_USER_CONFIRM_MESSAGE'); ?>"><?php echo lang('BLOCK_USER'); ?></a>
                            <?php endif; else: ?>
                            <a class="btn btn-xs btn-warning disabled"><?php echo lang('BLOCK_USER'); ?></a>
                        <?php endif; ?>
                         <!--<?php if($vo['isrecommend'] == '1'): ?>
							<a class="btn btn-xs btn-info js-ajax-dialog-btn"
								   href="<?php echo url('adminIndex/setrecommend',array('id'=>$vo['id'],'isrecommend'=>0)); ?>">取消推荐</a>
						<?php else: ?>
							<a class="btn btn-xs btn-info js-ajax-dialog-btn"
								   href="<?php echo url('adminIndex/setrecommend',array('id'=>$vo['id'],'isrecommend'=>1)); ?>">推荐</a>
						<?php endif; ?>-->
                        <?php if($vo['ishost'] == 1): ?>
                            <a class="btn btn-xs btn-primary js-ajax-dialog-btn" href="<?php echo url('adminIndex/setHost',array('id'=>$vo['id'],'ishost'=>0)); ?>" >取消派单主持人</a>
                        <?php else: ?>
                            <a class="btn btn-xs btn-primary js-ajax-dialog-btn" href="<?php echo url('adminIndex/setHost',array('id'=>$vo['id'],'ishost'=>1)); ?>">设为派单主持人</a>
                        <?php endif; ?>
                        
                        <a class="btn btn-xs btn-danger js-ajax-dialog-btn"
                                   href="<?php echo url('adminIndex/del',array('id'=>$vo['id'])); ?>"
                                   data-msg="您确定要删除此用户么？"><?php echo lang('DELETE'); ?></a>
                    </td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
		</div>
        <div class="pagination"><?php echo $page; ?></div>
    </form>
</div>
<script src="/public/static/js/admin.js"></script>
</body>
</html>