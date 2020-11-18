<?php /*a:2:{s:95:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\order_comment\index.html";i:1605250774;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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
        
        <form class="well form-inline margin-top-20" method="post" action="<?php echo url('orderComment/index'); ?>">
            技能：
            <select class="form-control" name="skillid" style="width: 100px;">
                <option value=''>全部</option>
                <?php if(is_array($skill) || $skill instanceof \think\Collection || $skill instanceof \think\Paginator): $i = 0; $__LIST__ = $skill;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $key; ?>" <?php if(input('request.skillid') != '' && input('request.skillid') == $key): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            
            用户ID：
            <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>" placeholder="用户ID">
            
            主播ID：
            <input class="form-control" type="text" name="liveuid" style="width: 200px;" value="<?php echo input('request.liveuid'); ?>" placeholder="主播ID">
            <input type="submit" class="btn btn-primary" value="搜索"/>
            <a class="btn btn-danger" href="<?php echo url('orderComment/index'); ?>">清空</a>
        </form>
    
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>评论人</th>
						<th>被评论人</th>
						<th>技能</th>
						<th>评分</th>
						<th style="width: 60%;">评论内容</th>
						<th>评论时间</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
					<tr>
						<td><?php echo $vo['userinfo']['user_nickname']; ?> ( <?php echo $vo['uid']; ?> )</td>
						<td><?php echo $vo['liveinfo']['user_nickname']; ?> ( <?php echo $vo['touid']; ?> )</td>
                        <td><?php echo $vo['skill']['name']; ?></td>
						<td><?php echo $vo['star']; ?></td>
						<td><?php echo $vo['content']; ?></td>
						<td><?php echo $vo['svctm']; ?></td>
                        <td>
                            <a class="btn btn-xs btn-primary" style="background-color: #18bc9c;border-color: #18bc9c;" href="javascript:parent.openIframeLayer('<?php echo url('orderComment/edit',array('id'=>$vo['id'])); ?>','编辑页面',{});">编辑</a>
							<a class="btn btn-xs btn-danger js-ajax-dialog-btn"
							           href="<?php echo url('orderComment/del',array('id'=>$vo['id'])); ?>"
							           data-msg="您确定要删除么？">删除</a>
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