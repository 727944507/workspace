<?php /*a:2:{s:90:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\dynamiccom\add.html";i:1604914792;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>

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
		<form method="post" class="form-horizontal js-ajax-form margin-top-20" action="<?php echo url('dynamiccom/addPost',array('did'=>$did)); ?>">
            
			<div class="form-group">
				<label for="input-uid" class="col-sm-2 control-label"><span class="form-required">*</span>内容ID</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-uid" name="post[id]" value="<?php echo $data['id']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="input-uid" class="col-sm-2 control-label"><span class="form-required">*</span>用户ID</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-uid" name="post[uid]" value="<?php echo $data['uid']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="input-uid" class="col-sm-2 control-label"><span class="form-required">*</span>回复用户ID</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-uid" name="post[touid]" value="<?php echo $data['touid']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="input-uid" class="col-sm-2 control-label"><span class="form-required">*</span>内容</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-uid" name="post[content]" value="<?php echo $data['content']; ?>">
				</div>
			</div>
            
            <div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo lang('EDIT'); ?></button>
				</div>
			</div>

		</form>
	</div>
	<script src="/public/static/js/admin.js"></script>
    <script type="text/javascript">
    (function(){
		$("#cdn").on('change',function(){
			var v=$(this).val();
			var b=$("#cdn_switch_1");
			if(v==0){
				b.hide();
				$("#input-type_val").val('');
			}else{
				b.show();
			}
		})
	})()
    </script>
</body>
</html>