<?php /*a:2:{s:78:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/gift/add.html";i:1603704624;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
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
			<li ><a href="<?php echo url('Gift/index'); ?>">列表</a></li>
			<li class="active"><a ><?php echo lang('ADD'); ?></a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form margin-top-20" action="<?php echo url('Gift/addPost'); ?>">

            <div class="form-group">
				<label for="input-type" class="col-sm-2 control-label"><span class="form-required">*</span>类型</label>
				<div class="col-md-6 col-sm-10" id="type">
                    <?php if(is_array($type) || $type instanceof \think\Collection || $type instanceof \think\Paginator): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<label class="radio-inline"><input type="radio" name="type" value="<?php echo $key; ?>" <?php if($i == 1): ?>checked<?php endif; ?>><?php echo $v; ?></label>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-giftname" class="col-sm-2 control-label"><span class="form-required">*</span>名称</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-giftname" name="giftname" style="width:300px;">
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-needcoin" class="col-sm-2 control-label"><span class="form-required">*</span>价格</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-needcoin" name="needcoin" style="width:300px;">
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-user_login" class="col-sm-2 control-label"><span class="form-required">*</span>图片</label>
				<div class="col-md-6 col-sm-10">
					<input type="hidden" name="gifticon" id="thumbnail" value="">
                    <a href="javascript:uploadOneImage('图片上传','#thumbnail');">
                        <img src="/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png"
                                 id="thumbnail-preview"
                                 style="cursor: pointer;max-width:100px;max-height:100px;"/>
                    </a>
                    <input type="button" class="btn btn-sm btn-cancel-thumbnail" value="取消图片"> 建议尺寸： 200 X 200
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-swftype" class="col-sm-2 control-label"><span class="form-required">*</span>动画类型</label>
				<div class="col-md-6 col-sm-10" id="swftype">
                    <?php if(is_array($swftype) || $swftype instanceof \think\Collection || $swftype instanceof \think\Paginator): $i = 0; $__LIST__ = $swftype;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<label class="radio-inline"><input type="radio" name="swftype" value="<?php echo $key; ?>" <?php if($i == 1): ?>checked<?php endif; ?>><?php echo $v; ?></label>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
            
            <div class="form-group" id="">
				<label for="input-gif" class="col-sm-2 control-label"><span class="form-required">*</span>GIF图片</label>
				<div class="col-md-6 col-sm-10">
                    <div id="swftype_bd_0">
                        <input type="hidden" name="gif" id="thumbnail2" value="">
                        <a href="javascript:uploadOneImage('图片上传','#thumbnail2');">
                            <img src="/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png"
                                     id="thumbnail2-preview"
                                     style="cursor: pointer;max-width:100px;max-height:100px;"/>
                        </a>
                        <input type="button" class="btn btn-sm btn-cancel-thumbnail2" value="取消图片"> 建议尺寸： 200 X 200
                    </div>
                    <div id="swftype_bd_1" style="display:none;">
                        <input class="form-control" id="js-file-input" type="text" name="svga" value="" style="width: 300px;display: inline-block;" title="文件名称">
                        <a href="javascript:uploadOne('文件上传','#js-file-input','file');">上传SVGA文件</a>
                    </div>
                    
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-swftime" class="col-sm-2 control-label">动画时长</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-swftime" name="swftime" value="0" style="width:300px;">秒 精度：小数点后两位
				</div>
			</div>
            
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo lang('ADD'); ?></button>
					<a class="btn btn-default" href="javascript:history.back(-1);"><?php echo lang('BACK'); ?></a>
				</div>
			</div>
            
		</form>
	</div>
	<script src="/static/js/admin.js"></script>
    <script>
        (function(){
            $('.btn-cancel-thumbnail').click(function () {
                $('#thumbnail-preview').attr('src', '/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png');
                $('#thumbnail').val('');
            });
            
            $('.btn-cancel-thumbnail2').click(function () {
                $('#thumbnail2-preview').attr('src', '/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png');
                $('#thumbnail2').val('');
            });
            
            
            $("#swftype label").on('click',function(){
                var v=$("input",this).val();
                var b=$("#swftype_bd_"+v);
                b.siblings().hide();
                b.show();
            })
        })()
    </script>
</body>
</html>