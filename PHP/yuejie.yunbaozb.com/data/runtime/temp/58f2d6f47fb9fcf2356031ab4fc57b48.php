<?php /*a:2:{s:82:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/refund/index.html";i:1603704624;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
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
</head>
<link rel="stylesheet"  type="text/css" href="/static/js/layer/theme/default/layer.css"></script>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a >列表</a></li>
		</ul>
        
        <form class="well form-inline margin-top-20" method="post" action="<?php echo url('Refund/index'); ?>">
            状态：
            <select class="form-control" name="status" style="width: 100px;">
                <option value=''>全部</option>
                <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $key; ?>" <?php if(input('request.status') != '' && input('request.status') == $key): ?>selected<?php endif; ?>><?php echo $v; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            退款时间：
            <input class="form-control js-bootstrap-date" name="start_time" id="start_time" value="<?php echo input('request.start_time'); ?>" aria-invalid="false" style="width: 110px;"> - 
            <input class="form-control js-bootstrap-date" name="end_time" id="end_time" value="<?php echo input('request.end_time'); ?>" aria-invalid="false" style="width: 110px;">
            退款用户ID：
            <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>"
                   placeholder="退款用户ID">
            接单用户ID：
            <input class="form-control" type="text" name="touid" style="width: 200px;" value="<?php echo input('request.touid'); ?>"
                   placeholder="接单用户ID">
            订单ID：
            <input class="form-control" type="text" name="orderid" style="width: 200px;" value="<?php echo input('request.orderid'); ?>"
                   placeholder="动态ID">
            <input type="submit" class="btn btn-primary" value="搜索"/>
            <a class="btn btn-danger" href="<?php echo url('Refund/index'); ?>">清空</a>
        </form>
    
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>订单ID</th>
						<th>退款用户</th>
						<th>接单用户</th>
						<th width="50%">内容</th>
						<th>状态</th>
						<th>退款时间</th>
						
						<th align="center"><?php echo lang('ACTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
					<tr>
						<td><?php echo $vo['id']; ?></td>
						<td><?php echo $vo['userinfo']['user_nickname']; ?> (<?php echo $vo['uid']; ?>)</td>
						<td><?php echo $vo['touserinfo']['user_nickname']; ?> (<?php echo $vo['liveuid']; ?>)</td>
						<td><?php echo $vo['refundinfo']['content']; ?></td>
						<td><?php echo $status[$vo['status']]; ?></td>
                        <td><?php echo date('Y-m-d H:i',$vo['refundtime']); ?></td>
						<td>
							<?php if($vo['status'] == '3' OR $vo['status'] == '6'): ?>
								<a class="btn btn-xs btn-success js-ajax-dialog-btn" href="<?php echo url('Refund/setRefund',array('id'=>$vo['id'],'status'=>'5')); ?>" data-msg="确定同意退款吗？">同意退款</a>
								
								<a class="btn btn-xs btn-danger js-ajax-dialog-btn" href="<?php echo url('Refund/setRefund',array('id'=>$vo['id'],'status'=>'4')); ?>" data-msg="确定拒绝退款吗？">拒绝退款</a>
							<?php endif; ?>
						
						</td>
					</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo $page; ?></div>

		</form>
	</div>
	<input type="hidden"  id="_selectBanval" value="">
	<script src="/static/js/admin.js"></script>
	<script src="/static/js/layer/layer.js"></script>
	<script type="text/javascript">
		
		Wind.use('layer');
		/*禁止接单*/
		function setbanorder(touid,reportid){
			layer.open({
				type: 2,
				title: '禁单时长（小时）',
				shadeClose: true,
				shade: 0.8,
				area: ['30%', '26%'],
				btn:['确定','取消'],
				content: '/admin/userrepot/setbanorder.html&touid='+touid,
				btn1:function(index,layero){
					var selectBanval=$("#_selectBanval").val();
					//设置禁单时间
					$.ajax({
						url: '/admin/userrepot/setBan.html',
						type: 'POST',
						dataType: 'json',
						data: {touid:touid,selectBanval: selectBanval,reportid:reportid},
						success:function(data){
							var code=data.code;
							if(code!=0){
								layer.msg(data.msg);
								return;
							}
							layer.msg("设置成功",{icon: 1,time:1000},function(){
								layer.closeAll();
								location.reload();
							});
						},
						error:function(e){
							console.log(e);
						}
					});
					
				}
			}); 
		}
	</script>
</body>
</html>