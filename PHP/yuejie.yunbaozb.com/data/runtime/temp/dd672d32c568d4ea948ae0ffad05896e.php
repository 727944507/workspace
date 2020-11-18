<?php /*a:2:{s:89:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\dynamic\index.html";i:1604910589;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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
        
        <form class="well form-inline margin-top-20" method="post" action="<?php echo url('dynamic/index'); ?>">
            <!-- 状态：
            <select class="form-control" name="status" style="width: 100px;">
                <option value=''>全部</option>
                <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $key; ?>" <?php if(input('request.status') != '' && input('request.status') == $key): ?>selected<?php endif; ?>><?php echo $v; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select> -->
            提交时间：
            <input class="form-control js-bootstrap-date" name="start_time" id="start_time" value="<?php echo input('request.start_time'); ?>" aria-invalid="false" style="width: 110px;"> - 
            <input class="form-control js-bootstrap-date" name="end_time" id="end_time" value="<?php echo input('request.end_time'); ?>" aria-invalid="false" style="width: 110px;">
            用户ID：
            <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>"
                   placeholder="用户ID">
            <input type="hidden" name="status" value="<?php echo $status2; ?>"/>
            <input type="submit" class="btn btn-primary" value="搜索"/>
            <a class="btn btn-danger" href="<?php echo url('dynamic/index',array('status'=>$status2)); ?>">清空</a>
        </form>
    
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>用户</th>
						<th>类型</th>
						<th width="10%">内容</th>
						<th width="30%">图片</th>
						<th>位置</th>
						<th>点赞数</th>
						<th>评论数</th>
						<th>推荐值</th>
						<th>状态</th>
						<th>提交时间</th>
						<th align="center"><?php echo lang('ACTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
					<tr>
						<td><?php echo $vo['id']; ?></td>
						<td><?php echo $vo['userinfo']['user_nickname']; ?> (<?php echo $vo['uid']; ?>)</td>
                        <td><?php echo $type[$vo['type']]; ?></td>
                        <td><?php echo $vo['content']; ?></td>
						<td>
                            <?php if($vo['thumb']): if(is_array($vo['thumb']) || $vo['thumb'] instanceof \think\Collection || $vo['thumb'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['thumb'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                <img class="imgtip" src="<?php echo $v; ?>" style="max-width:100px;max-height:100px;">
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php endif; if($vo['video_t']): ?><img class="imgtip" src="<?php echo $vo['video_t']; ?>" style="max-width:100px;max-height:100px;"><?php endif; ?>
                        
                        </td>
						<td><?php echo $vo['location']; ?></td>
						<td><?php echo $vo['likes']; ?></td>
						<td><?php echo $vo['comments']; ?></td>
						<td><?php echo $vo['recoms']; ?></td>
						<td><?php echo $status[$vo['status']]; ?></td>
                        <td><?php echo date('Y-m-d H:i',$vo['addtime']); ?></td>
						<td>
							<a class="btn btn-xs btn-primary" style="background-color: #18bc9c;border-color: #18bc9c;" href="javascript:parent.openIframeLayer('<?php echo url('dynamic/edit',array('id'=>$vo['id'])); ?>','编辑页面',{});">编辑</a>
                            <?php if($vo['type'] == 2): ?>
                            <a class="btn btn-xs btn-success view" data-id="<?php echo $vo['id']; ?>">查看视频</a>
                            <?php endif; if($vo['type'] == 3): ?>
                            <a class="btn btn-xs btn-success view" data-id="<?php echo $vo['id']; ?>">查看语音</a>
                            <?php endif; if($vo['status'] == 0): ?>
                                <a class="btn btn-xs btn-success js-ajax-dialog-btn" href="<?php echo url('dynamic/setstatus',array('id'=>$vo['id'],'status'=>1)); ?>">同意</a>
                                <a class="btn btn-xs btn-danger js-ajax-dialog-btn" href="<?php echo url('dynamic/setstatus',array('id'=>$vo['id'],'status'=>-1)); ?>">拒绝</a>
                            <?php endif; if($vo['status'] == 1): ?>
                                <a class="btn btn-xs btn-danger js-ajax-dialog-btn" href="<?php echo url('dynamic/setstatus',array('id'=>$vo['id'],'status'=>-1)); ?>">拒绝</a>
                            <?php endif; if($vo['status'] == -1): ?>
                                <a class="btn btn-xs btn-success js-ajax-dialog-btn" href="<?php echo url('dynamic/setstatus',array('id'=>$vo['id'],'status'=>1)); ?>">同意</a>
                            <?php endif; ?>
                            
                            <a class="btn btn-xs btn-info setrecom" data-id="<?php echo $vo['id']; ?>" data-recoms="<?php echo $vo['recoms']; ?>">设置推荐值</a>
                            
                            <a class="btn btn-xs btn-primary" href='<?php echo url("dynamiccom/index",array("did"=>$vo["id"])); ?>'>查看评论</a>
                            
                            <a class="btn btn-xs btn-danger js-ajax-delete" href="<?php echo url('dynamic/del',array('id'=>$vo['id'])); ?>"><?php echo lang('DELETE'); ?></a>
						</td>
					</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo $page; ?></div>

		</form>
	</div>
    <div id="enlarge_images" style="position:fixed;display:none;z-index:2;"></div>
	<script src="/public/static/js/admin.js"></script>
    <script>
        $(function(){
            
            Wind.use('layer');
            $('.setrecom').click(function(){
                var _this=$(this);
                var id=_this.data('id');
                var recoms=_this.data('recoms');
                
                layer.prompt({
                    formType: 0,
                    title: '推荐值',
                    value: recoms,
                    area: ['800px', '100px'] //自定义文本域宽高
                }, function(value, index, elem){
                    $.ajax({
                        url:'<?php echo url('Dynamic/setrecom'); ?>',
                        type:'POST',
                        data:{id:id,recoms:value},
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
                    
                });
                
            })
            
            $('.view').click(function(){
                var _this=$(this);
                var id=_this.data('id');

                layer.open({
                    type: 2,
                    title: '查看',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['500px', '100%'],
                    content: '/admin/Dynamic/see?id='+id
                }); 
                
            });
        })
    </script>
</body>
</html>