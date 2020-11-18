<?php /*a:2:{s:92:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\liverecord\index.html";i:1603704624;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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
		</ul>
		
		<form class="well form-inline margin-top-20" method="post" action="<?php echo url('liverecord/index'); ?>">
            时间：
			<input class="form-control js-bootstrap-date" name="start_time" id="start_time" value="<?php echo input('request.start_time'); ?>" aria-invalid="false" style="width: 110px;"> - 
            <input class="form-control js-bootstrap-date" name="end_time" id="end_time" value="<?php echo input('request.end_time'); ?>" aria-invalid="false" style="width: 110px;">
			管理员ID： 
            <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>"
                   placeholder="请输入会员id、靓号">
                   
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>		
		<form method="post" class="js-ajax-form" >

			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>编号</th>
						<th>会员（ID）</th>
						<th>标识</th>
						<th>流名</th>
						<th>类型</th>
						<th>标题</th>
						<th>封面</th>
						<th width="10%">描述</th>
                        <th>累计人数</th>
                        <th>时长</th>
						<th>直播开始时间</th>
						<th>直播结束时间</th>
					<!-- 	<th><?php echo lang('ACTIONS'); ?></th> -->
					</tr>
				</thead>
				<tbody>
					<?php $islive=array("0"=>"直播结束","1"=>"直播中"); if(is_array($lists) || $lists instanceof \think\Collection || $lists instanceof \think\Paginator): if( count($lists)==0 ) : echo "" ;else: foreach($lists as $key=>$vo): ?>
					<tr>
						<td><?php echo $vo['id']; ?></td>					
						<td><?php echo $vo['userinfo']['user_nickname']; ?> (<?php echo $vo['uid']; ?>) </td>
						<td><?php echo $vo['showid']; ?></td>
						<td><?php echo $vo['stream']; ?></td>
                        <td><?php echo $vo['type_val']; ?></td>
                        <td><?php echo $vo['title']; ?></td>
                        <td><img class="imgtip" src="<?php echo $vo['thumb']; ?>" style="max-width:100px;max-height:100px;"></td>
                        <td style="word-break:break-all"><?php echo $vo['des']; ?></td>
                        <td><?php echo $vo['nums']; ?></td>
                        <td><?php echo $vo['length_s']; ?></td>
						<td><?php echo date('Y-m-d H:i',$vo['starttime']); ?></td>
						<td><?php echo date('Y-m-d H:i',$vo['endtime']); ?></td>
						
						<!-- <td align="center">	
                            <a class="btn btn-xs btn-danger js-ajax-delete" href="<?php echo url('liverecord/del',array('id'=>$vo['id'])); ?>"><?php echo lang('DELETE'); ?></a>
						</td> -->
					</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo $page; ?></div>

		</form>
	</div>
    <div id="enlarge_images" style="position:fixed;display:none;z-index:2;"></div>
	<script src="/public/static/js/admin.js"></script>
</body>
</html>