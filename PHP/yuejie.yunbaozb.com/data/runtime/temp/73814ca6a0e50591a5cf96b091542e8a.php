<?php /*a:2:{s:86:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\skill\edit.html";i:1604717821;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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
			<li ><a href="<?php echo url('skill/index'); ?>">列表</a></li>
			<li class="active"><a ><?php echo lang('EDIT'); ?></a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form margin-top-20" action="<?php echo url('skill/editPost'); ?>">
            <div class="form-group">
				<label for="input-classid" class="col-sm-2 control-label"><span class="form-required">*</span>分类</label>
				<div class="col-md-6 col-sm-10">
                    <select class="form-control" name="classid" style="width: 100px;">
                        <?php if(is_array($class) || $class instanceof \think\Collection || $class instanceof \think\Paginator): $i = 0; $__LIST__ = $class;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $v['id']; ?>" <?php if($data['classid'] == $v['id']): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
				</div>
			</div>
            <div class="form-group">
				<label for="input-name" class="col-sm-2 control-label"><span class="form-required">*</span>名称</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-name" name="name" value="<?php echo $data['name']; ?>">
				</div>
			</div>
            
            <div class="form-group" style="display:none;">
				<label for="input-name_en" class="col-sm-2 control-label"><span class="form-required">*</span>名称-英文</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-name_en" name="name_en" value="<?php echo $data['name_en']; ?>">
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-user_login" class="col-sm-2 control-label"><span class="form-required">*</span>封面</label>
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
                    <input type="button" class="btn btn-sm btn-cancel-thumbnail" value="取消图片"> 建议尺寸： 100 X 100
				</div>
			</div>
			
			<div class="form-group">
				<label for="input-user_login" class="col-sm-2 control-label"><span class="form-required">*</span>栏目封面</label>
				<div class="col-md-6 col-sm-10">
					<input type="hidden" name="thumb2" id="thumbnail2" value="<?php echo $data['thumb2']; ?>">
			        <a href="javascript:uploadOneImage('图片上传','#thumbnail2');">
			            <?php if(empty($data['thumb2'])): ?>
			            <img src="/public/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png"
			                     id="thumbnail2-preview"
			                     style="cursor: pointer;max-width:100px;max-height:100px;"/>
			            <?php else: ?>
			            <img src="<?php echo cmf_get_image_preview_url($data['thumb2']); ?>"
			                 id="thumbnail2-preview"
			                 style="cursor: pointer;max-width:100px;max-height:100px;"/>
			            <?php endif; ?>
			        </a>
			        <input type="button" class="btn btn-sm btn-cancel-thumbnail" value="取消图片"> 建议尺寸： 100 X 100
				</div>
			</div>
			
			<div class="form-group">
				<label for="input-user_login" class="col-sm-2 control-label"><span class="form-required">*</span>技能示例图</label>
				<div class="col-md-6 col-sm-10">
					<input type="hidden" name="thumb3" id="thumbnail3" value="<?php echo $data['thumb3']; ?>">
			        <a href="javascript:uploadOneImage('图片上传','#thumbnail3');">
			            <?php if(empty($data['thumb3'])): ?>
			            <img src="/public/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png"
			                     id="thumbnail3-preview"
			                     style="cursor: pointer;max-width:100px;max-height:100px;"/>
			            <?php else: ?>
			            <img src="<?php echo cmf_get_image_preview_url($data['thumb3']); ?>"
			                 id="thumbnail3-preview"
			                 style="cursor: pointer;max-width:100px;max-height:100px;"/>
			            <?php endif; ?>
			        </a>
			        <input type="button" class="btn btn-sm btn-cancel-thumbnail" value="取消图片"> 建议尺寸： 100 X 100
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-method" class="col-sm-2 control-label"><span class="form-required">*</span>收费方式</label>
				<div class="col-md-6 col-sm-10">
					<select class="form-control" id="method" name="method" style="width: 100px;">
                        <?php if(is_array($method) || $method instanceof \think\Collection || $method instanceof \think\Paginator): $i = 0; $__LIST__ = $method;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $v; ?>" <?php if($data['method'] == $v): ?>selected<?php endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
				</div>
			</div>
			<div class="form-group"  id="methodminutes_bd" <?php if($data['method'] == '半小时' OR $data['method'] == '小时'): ?> style="display:none;"<?php endif; ?> >
					<label for="input-methodminutes" class="col-sm-2 control-label"><span class="form-required">*</span>陪玩时长</label>
					<div class="col-md-6 col-sm-10">
						<select class="form-control" name="methodminutes" style="width: 100px;">
							<?php if(is_array($methodminutes) || $methodminutes instanceof \think\Collection || $methodminutes instanceof \think\Paginator): $i = 0; $__LIST__ = $methodminutes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $v; ?>" <?php if($data['methodminutes'] == $v): ?>selected<?php endif; ?>><?php echo $v; ?>分钟</option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
            
            <div class="form-group">
				<label for="input-colour_font" class="col-sm-2 control-label"><span class="form-required">*</span>颜色-字体</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control js-color" name="colour_font" style="display:inline-block;width:200px;" value="<?php echo $data['colour_font']; ?>"> 
                    <input  class="form-control colour_block" style="display:inline-block;width:50px;background:<?php echo $data['colour_font']; ?>;" disabled/>
				</div>
			</div>
            <div class="form-group">
				<label for="input-colour_bg" class="col-sm-2 control-label"><span class="form-required">*</span>颜色-背景</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control js-color" name="colour_bg" style="display:inline-block;width:200px;" value="<?php echo $data['colour_bg']; ?>"> 
                    <input  class="form-control colour_block" style="display:inline-block;width:50px;background:<?php echo $data['colour_bg']; ?>;" disabled/>
				</div>
			</div>
            
            <div class="form-group">
				<label for="input-auth_tip" class="col-sm-2 control-label"><span class="form-required">*</span>认证提示</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-auth_tip" name="auth_tip" value="<?php echo $data['auth_tip']; ?>">
				</div>
			</div>
            
            <div class="form-group" style="display:none;">
				<label for="input-auth_tip_en" class="col-sm-2 control-label"><span class="form-required">*</span>认证提示-英文</label>
				<div class="col-md-6 col-sm-10">
					<input type="text" class="form-control" id="input-auth_tip_en" name="auth_tip_en" value="<?php echo $data['auth_tip_en']; ?>">
				</div>
			</div>


            <div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
					<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo lang('EDIT'); ?></button>
					<a class="btn btn-default" href="javascript:history.back(-1);"><?php echo lang('BACK'); ?></a>
				</div>
			</div>

		</form>
	</div>
	<script src="/public/static/js/admin.js"></script>
    <script>
        (function(){
            Wind.use('colorpicker',function(){
                $('.js-color').each(function () {
                    var $this=$(this);
                    $this.ColorPicker({
                        livePreview:true,
                        onChange: function(hsb, hex, rgb) {
                            $this.val('#'+hex);
                            $this.siblings(".colour_block").css('background','#'+hex);
                        },
                        onBeforeShow: function () {
                            $(this).ColorPickerSetColor(this.value);
                        }
                    });
                });

            });
            $('.btn-cancel-thumbnail').click(function () {
                $('#thumbnail-preview').attr('src', '/public/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png');
                $('#thumbnail').val('');
            });
			$("#method").on('change',function(){
                var v=$("#method option:selected").val();
				if(v!="小时" && v!="半小时"){
					$("#methodminutes_bd").show();
				}else{
					$("#methodminutes_bd").hide();
				}
                
               
            });
        })()
    </script>
    <script>
        
      </script>
</body>
</html>