<?php /*a:2:{s:85:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\guide\set.html";i:1603704624;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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

<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a>配置</a></li>
        <li><a href="<?php echo url('Guide/index'); ?>">管理</a></li>
    </ul>
    <form class="form-horizontal js-ajax-form margin-top-20" role="form" action="<?php echo url('Guide/setPost'); ?>" method="post">
        <fieldset>
            <div class="tabbable">
                <div class="tab-content">
                    <div class="tab-pane active" id="A">
                        <div class="form-group">
                            <label for="input-switch" class="col-sm-2 control-label">引导页开关</label>
                            <div class="col-md-6 col-sm-10">
                                <select class="form-control" name="post[switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-type" class="col-sm-2 control-label">引导页类型</label>
                            <div class="col-md-6 col-sm-10">
                                <select class="form-control" name="post[type]" id="type">
                                    <option value="0">图片</option>
                                    <option value="1" <?php if($config['type'] == '1'): ?>selected<?php endif; ?>>视频</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group" id="type_0" <?php if($config['type'] == 1): ?>style="display:none;"<?php endif; ?>>
                            <label for="input-time" class="col-sm-2 control-label"><span class="form-required">*</span>图片展示时间</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-time" name="post[time]" value="<?php echo (isset($config['time']) && ($config['time'] !== '')?$config['time']:'1'); ?>">秒,请填写大于0的整数 当类型选择图片时，每张图片的展示时间
                            </div> 
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="1">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </fieldset>
    </form>
</div>
<script src="/public/static/js/admin.js"></script>
<script>
(function(){
    $("#type").on('change',function(){
        var v=$(this).val();
        if(v==1){
            $("#type_0").hide();
        }else{
            $("#type_0").show();
        }
        
    })
    
})()  
</script>
</body>
</html>