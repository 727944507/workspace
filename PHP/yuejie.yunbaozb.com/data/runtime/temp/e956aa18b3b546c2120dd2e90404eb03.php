<?php /*a:2:{s:92:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\chargerule\index.html";i:1603704624;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a >列表</a></li>
			<li><a href="<?php echo url('Chargerule/add'); ?>">添加</a></li>
		</ul>
		<form method="post" class="js-ajax-form" action="<?php echo url('Chargerule/listOrder'); ?>">
            <div class="table-actions">
                <button class="btn btn-primary btn-sm js-ajax-submit" type="submit"><?php echo lang('SORT'); ?></button>
            </div>
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
                        <th width="50"><?php echo lang('SORT'); ?></th>
						<th>ID</th>
						<th>名称</th>
                        <th>价格(CNY)</th>
						<th><?php echo $configpub['name_coin']; ?></th>
						<th><?php echo $configpub['name_coin']; ?>(苹果支付)</th>
						<th>苹果项目ID</th>
						<th>Google Pay项目ID</th>
						<th align="center"><?php echo lang('ACTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
					<tr>
                        <td>
                            <input name="list_orders[<?php echo $vo['id']; ?>]" class="input-order" type="text" value="<?php echo $vo['list_order']; ?>">
                        </td>
						<td><?php echo $vo['id']; ?></td>
						<td><?php echo $vo['name']; ?></td>
						<td><?php echo $vo['money']; ?></td>
						<td><?php echo $vo['coin']; ?></td>
						<td><?php echo $vo['coin_ios']; ?></td>
						<td><?php echo $vo['product_id']; ?></td>
						<td><?php echo $vo['google_pid']; ?></td>
						<td>	
                            <a class="btn btn-xs btn-primary" href='<?php echo url("Chargerule/edit",array("id"=>$vo["id"])); ?>'><?php echo lang('EDIT'); ?></a>
							<a class="btn btn-xs btn-danger js-ajax-delete" href="<?php echo url('Chargerule/del',array('id'=>$vo['id'])); ?>"><?php echo lang('DELETE'); ?></a>
						</td>
					</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo $page; ?></div>

		</form>
	</div>
	<script src="/public/static/js/admin.js"></script>
</body>
</html>