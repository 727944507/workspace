<?php /*a:2:{s:79:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/cash/edit.html";i:1603704624;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
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
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li ><a href="<?php echo url('Cash/index'); ?>"><?php echo $configpub['name_votes']; ?>提现记录列表</a></li>
			<li class="active"><a ><?php echo lang('EDIT'); ?></a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form margin-top-20" action="<?php echo url('Cash/editPost'); ?>">
            <div class="form-group">
				<label for="input-name" class="col-sm-2 control-label"><span class="form-required">*</span>会员</label>
				<div class="col-md-6 col-sm-10" style="padding-top:7px;">
					<?php echo $data['userinfo']['user_nicename']; ?> (<?php echo $data['uid']; ?>)
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-votes" class="col-sm-2 control-label"><span class="form-required">*</span>兑换<?php echo $configpub['name_votes']; ?></label>
				<div class="col-md-6 col-sm-10" style="padding-top:7px;">
					<?php echo $data['votes']; ?>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-money" class="col-sm-2 control-label"><span class="form-required">*</span>提现金额</label>
				<div class="col-md-6 col-sm-10" style="padding-top:7px;">
					<?php echo $data['money']; ?>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-name" class="col-sm-2 control-label"><span class="form-required">*</span>提现账号</label>
				<div class="col-md-6 col-sm-10" style="padding-top:7px;">
					<?php echo $type[$data['type']]; ?>  <br>
                    <?php echo $data['name']; ?>  <br>
                    <?php echo $data['account']; ?>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-trade_no" class="col-sm-2 control-label"><span class="form-required">*</span>第三方支付订单号</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-trade_no" name="trade_no" value="<?php echo $data['trade_no']; ?>">
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-trade_no" class="col-sm-2 control-label"><span class="form-required">*</span>状态</label>
				<div class="col-md-6 col-sm-10">
					<select class="form-control" name="status">
                        <?php if(is_array($status) || $status instanceof \think\Collection || $status instanceof \think\Paginator): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $key; ?>" <?php if($data['status'] == $key): ?>selected<?php endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
				</div>
			</div>
            
            <?php if($data['status'] == 0): ?>
            <div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
					<input type="hidden" name="uid" value="<?php echo $data['uid']; ?>" />
					<input type="hidden" name="votes" value="<?php echo $data['votes']; ?>" />
					<input type="hidden" name="votes_type" value="<?php echo $data['votes_type']; ?>" />
					<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo lang('EDIT'); ?></button>
					<a class="btn btn-default" href="javascript:history.back(-1);"><?php echo lang('BACK'); ?></a>
				</div>
			</div>
            <?php endif; ?>

		</form>
	</div>
	<script src="/static/js/admin.js"></script>
</body>
</html>
