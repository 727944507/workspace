<?php /*a:2:{s:88:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\liveing\edit.html";i:1603704624;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>

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
			<li ><a href="<?php echo url('Liveing/index'); ?>">列表</a></li>
			<li class="active"><a ><?php echo lang('EDIT'); ?></a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form margin-top-20" action="<?php echo url('Liveing/editPost'); ?>">
            <div class="form-group">
				<label for="input-uid" class="col-sm-2 control-label"><span class="form-required">*</span>用户ID</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-uid" name="uid" value="<?php echo $data['uid']; ?>" readonly>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-type" class="col-sm-2 control-label"><span class="form-required">*</span>背景图片</label>
				<div class="col-md-6 col-sm-10">
                    <select class="form-control" name="bgid">
                    	<option value="0" <?php if($data['bgid'] == '0'): ?>selected<?php endif; ?>>请选择背景图</option>
                        <?php if(is_array($bglist) || $bglist instanceof \think\Collection || $bglist instanceof \think\Paginator): $i = 0; $__LIST__ = $bglist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $key; ?>" <?php if($data['bgid'] > 0 and $data['bgid'] == $key): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-type" class="col-sm-2 control-label"><span class="form-required">*</span>聊天室类型</label>
				<div class="col-md-6 col-sm-10">
                    <select class="form-control" name="type" id="cdn">
                        <?php if(is_array($type) || $type instanceof \think\Collection || $type instanceof \think\Paginator): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $key; ?>" <?php if($data['type'] == $key): ?>selected<?php endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
				</div>
			</div>
            
            
            
           <!--  <div class="form-group">
				<label for="input-pull" class="col-sm-2 control-label"><span class="form-required">*</span>聊天播流地址</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-pull" name="pull" value="<?php echo $data['pull']; ?>">格式：flv
				</div>
			</div> -->
            
			<div class="form-group">
				<label for="input-title" class="col-sm-2 control-label"><span class="form-required">*</span>聊天室标题</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-title" name="title" value="<?php echo $data['title']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="input-des" class="col-sm-2 control-label"><span class="form-required">*</span>聊天室公告</label>
				<div class="col-md-6 col-sm-10">
					<textarea class="form-control" id="input-content" name="des" maxlength="200"><?php echo $data['des']; ?></textarea>
				</div>
			</div>
			
            <div class="form-group">
				<label for="input-user_login" class="col-sm-2 control-label"><span class="form-required">*</span>图片</label>
				<div class="col-md-6 col-sm-10">
					<input type="hidden" name="thumb" id="thumbnail" value="<?php echo $data['thumb']; ?>">
                    <a href="javascript:uploadOneImage('图片上传','#thumbnail');">
                        <?php if(empty($data['thumb'])): ?>
                        <img src="/public/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png"
                                 id="thumbnail-preview"
                                 style="cursor: pointer;max-width:100px;max-height:100px;"/>
                        <?php else: ?>
                        <img src="<?php echo cmf_get_image_preview_url($data['thumb']); ?>"
                             id="thumbnail-preview"
                             style="cursor: pointer;max-width:100px;max-height:100px;"/>
                        <?php endif; ?>
                    </a>
                    <input type="button" class="btn btn-sm btn-cancel-thumbnail" value="取消图片"> 
                    <p class="help-block">建议尺寸： 50 X 50 太大会造成加载图片加载慢等各种问题</p>
				</div>
			</div>
            
            <div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo lang('EDIT'); ?></button>
					<a class="btn btn-default" href="javascript:history.back(-1);"><?php echo lang('BACK'); ?></a>
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