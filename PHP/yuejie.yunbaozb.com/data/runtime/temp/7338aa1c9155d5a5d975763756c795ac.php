<?php /*a:2:{s:82:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/orders/index.html";i:1603961156;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
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
<style>
    p{
       margin:0;
       padding:0;
    }
    .label{
        display:inline-block;
        margin-bottom:5px;
        padding:5px 10px;
        border-radius:50px;
    }
    .imgtip{
        margin-bottom:5px;
    }
</style>
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a >列表</a></li>
		</ul>
        
        <form class="well form-inline margin-top-20" method="post" action="<?php echo url('Orders/index'); ?>">
            技能：
            <select class="form-control" name="skillid" style="width: 100px;">
                <option value=''>全部</option>
                <?php if(is_array($skill) || $skill instanceof \think\Collection || $skill instanceof \think\Paginator): $i = 0; $__LIST__ = $skill;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $key; ?>" <?php if(input('request.skillid') != '' && input('request.skillid') == $key): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            
            状态：
            <select class="form-control" name="status" style="width: 100px;">
                <option value=''>全部</option>
                <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $key; ?>" <?php if(input('request.status') != '' && input('request.status') == $key): ?>selected<?php endif; ?>><?php echo $v; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            提交时间：
            <input class="form-control js-bootstrap-date" name="start_time" id="start_time" value="<?php echo input('request.start_time'); ?>" aria-invalid="false" style="width: 110px;"> - 
            <input class="form-control js-bootstrap-date" name="end_time" id="end_time" value="<?php echo input('request.end_time'); ?>" aria-invalid="false" style="width: 110px;">
            用户ID：
            <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>" placeholder="用户ID">
            
            主播ID：
            <input class="form-control" type="text" name="liveuid" style="width: 200px;" value="<?php echo input('request.liveuid'); ?>" placeholder="主播ID">
            <input type="submit" class="btn btn-primary" value="搜索"/>
            <a class="btn btn-danger" href="<?php echo url('Orders/index'); ?>">清空</a>
        </form>
    
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>用户</th>
						<th>主播</th>
						<th>技能</th>
						<th>服务时间</th>
						<th>服务结束时间</th>
						<th>数量</th>
						<th>总价</th>
						<th>备注</th>
						<th>支付方式</th>
						<th>状态</th>
						<th>时间</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
					<tr>
						<td><?php echo $vo['userinfo']['user_nickname']; ?> ( <?php echo $vo['uid']; ?> )</td>
						<td><?php echo $vo['liveinfo']['user_nickname']; ?> ( <?php echo $vo['liveuid']; ?> )</td>
                        <td><?php echo $vo['skill']['name']; ?></td>
						<td><?php echo $vo['svctm']; ?></td>
						<td><?php echo $vo['overtime']; ?></td>
						<td><?php echo $vo['nums']; ?>*<?php echo $vo['skill']['method']; ?></td>
						<td><?php echo $vo['total']; ?></td>
						<td><?php echo $vo['des']; ?></td>
						<td><?php echo $paytype[$vo['type']]; ?></td>
						<td><?php echo $status[$vo['status']]; ?></td>
                        <td>
                            提交时间:<?php echo date('Y-m-d H:i',$vo['addtime']); ?><br>
                            <?php if($vo['paytime'] != 0): ?>
                            付款时间:<?php echo date('Y-m-d H:i',$vo['paytime']); ?><br>
                            <?php endif; if($vo['status'] == 2 || $vo['status'] == -2): ?>
                            接单时间:<?php echo date('Y-m-d H:i',$vo['receipttime']); ?><br>
                            <?php endif; if($vo['status'] == -3): ?>
                            拒接时间:<?php echo date('Y-m-d H:i',$vo['receipttime']); ?><br>
                            <?php endif; if($vo['status'] == -2): ?>
                            完成时间:<?php echo date('Y-m-d H:i',$vo['oktime']); ?><br>
                            <?php endif; if($vo['status'] == -1): ?>
                            取消时间:<?php echo date('Y-m-d H:i',$vo['canceltime']); ?><br>
                            原因:<?php echo $vo['reason']; ?>
                            <?php endif; ?>
                        </td>


					</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo $page; ?></div>

		</form>
	</div>
	<script src="/static/js/admin.js"></script>

</body>
</html>