<?php /*a:2:{s:80:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/auth/index.html";i:1603788539;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
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
        
        <form class="well form-inline margin-top-20" method="post" action="<?php echo url('Auth/index'); ?>">
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
            <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>"
                   placeholder="用户ID">
            关键词：
            <input class="form-control" type="text" name="keyword" style="width: 200px;" value="<?php echo input('request.keyword'); ?>"
                   placeholder="姓名、电话">
            <input type="submit" class="btn btn-primary" value="搜索"/>
            <a class="btn btn-danger" href="<?php echo url('Auth/index'); ?>">清空</a>
        </form>
    
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>用户</th>
						<th>姓名</th>
						<th>电话</th>
						<th>身份证号</th>
						<th width="10%">证件正面</th>
						<th width="10%">证件背面</th>
						<th width="10%">手持证件正面照</th>
						<th>审核意见</th>
						<th>状态</th>
						<th>提交时间</th>
						<th>处理时间</th>
						<th align="center"><?php echo lang('ACTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
					<tr>
						<td><?php echo $vo['userinfo']['user_nickname']; ?> ( <?php echo $vo['uid']; ?> )</td>	
						<td><?php echo $vo['name']; ?></td>
						<td><?php echo $vo['mobile']; ?></td>
						<td><?php echo $vo['cer_no']; ?></td>
						<td><?php if($vo['front_view']): ?><img class="imgtip" src="<?php echo $vo['front_view']; ?>" style="max-width:100px;"><?php endif; ?></td>
						<td><?php if($vo['back_view']): ?><img class="imgtip" src="<?php echo $vo['back_view']; ?>" style="max-width:100px;"><?php endif; ?></td>
						<td><?php if($vo['handset_view']): ?><img class="imgtip" src="<?php echo $vo['handset_view']; ?>" style="max-width:100px;"><?php endif; ?></td>
						<td><?php echo $vo['reason']; ?></td>
						<td><?php echo $status[$vo['status']]; ?></td>
                        <td><?php echo date('Y-m-d H:i',$vo['addtime']); ?></td>
                        <td><?php echo date('Y-m-d H:i',$vo['uptime']); ?></td>
						<td>
                            <?php if($vo['status'] == 0): ?>
                                <a class="btn btn-xs btn-success setstatus" data-uid="<?php echo $vo['uid']; ?>" data-status="1">同意</a>
                                <a class="btn btn-xs btn-danger setstatus" data-uid="<?php echo $vo['uid']; ?>" data-status="2">拒绝</a>
                            <?php endif; if($vo['status'] == 1): ?>
                                <a class="btn btn-xs btn-danger setstatus" data-uid="<?php echo $vo['uid']; ?>" data-status="2">拒绝</a>
                            <?php endif; if($vo['status'] == 2): ?>
                                <a class="btn btn-xs btn-success setstatus" data-uid="<?php echo $vo['uid']; ?>" data-status="1">同意</a>
                            <?php endif; ?>
						</td>
					</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo $page; ?></div>

		</form>
	</div>
    <div id="enlarge_images" style="position:fixed;display:none;z-index:2;"></div>
	<script src="/static/js/admin.js"></script>
    <script>
        $(function(){
            Wind.use('layer');
            $('.setstatus').click(function(){
                var _this=$(this);
                var uid=_this.data('uid');
                var status=_this.data('status');
                var value='';
                if(status==1){
                    value=' ';
                }
               
                layer.prompt({
                    formType: 2,
                    title: '审核意见',
                    value: value,
                    area: ['800px', '100px'], //自定义文本域宽高
					yes: function(index, layero){
						// 获取文本框输入的值
						var value = layero.find(".layui-layer-input").val();
						if (value) {
							$.ajax({
								url:'<?php echo url('Auth/setstatus'); ?>',
								type:'POST',
								data:{uid:uid,status:status,reason:value},
								dataType:'json',
								success:function(data){
									var code=data.code;
									if(code==0){
										layer.msg(data.msg);
										return !1;
									}
									layer.msg(data.msg,{},function(){
										layer.closeAll();
										reloadPage(window);
									});
									
								},
								error:function(){
									layer.msg('操作失败，请重试')
								}
							});
						
						}else{
							layer.msg("请输入审核意见");
							return !1;
						}
					}
                }, function(value, index, elem){
				
                });
                
            })
        })
    </script>
</body>
</html>