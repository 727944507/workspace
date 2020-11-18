<?php /*a:2:{s:92:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/user\admin_play\update.html";i:1604977508;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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
    <!--<ul class="nav nav-tabs">
        <li><a href="<?php echo url('SlideItem/index',['slide_id'=>$slide_id]); ?>">幻灯片页面列表</a></li>
        <li class="active"><a href="<?php echo url('SlideItem/add',['slide_id'=>$slide_id]); ?>">添加幻灯片页面</a></li>
    </ul>-->
    <form action="<?php echo url('adminPlay/updatePost',array('id'=>$result['id'])); ?>" method="post" class="form-horizontal js-ajax-form margin-top-20">
        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr>
                        <th>接单量</th>
                        <td>
                            <input class="form-control" type="text" name="post[orders]" id="source" value="<?php echo $result['orders']; ?>"
                                       style="width: 400px" placeholder="请输入描述"></td>
                        </td>
                    </tr>
					<tr>
					    <th>粉丝量</th>
					    <td>
					        <input class="form-control" type="text" name="post[funs]" id="source" value="<?php echo $result['funs']; ?>"
					                   style="width: 400px" placeholder="请输入描述"></td>
					    </td>
					</tr>
                    <tr>
                        <th>评分</th>
                        <td><input class="form-control" type="text" name="post[stars]" id="source" value="<?php echo $result['stars']; ?>"
                                   style="width: 400px" placeholder="请输入描述"></td>
                    </tr>
					<!--<tr>
					    <th>登录密码</th>
					    <td><input class="form-control" type="text" name="post[description]" id="source" value=""
					               style="width: 400px" placeholder="请输入描述"></td>
					</tr>
					<tr>
					    <th>职业</th>
					    <td><input class="form-control" type="text" name="post[description]" id="source" value=""
					               style="width: 400px" placeholder="请输入描述"></td>
					</tr>
					<tr>
					    <th>学校</th>
					    <td><input class="form-control" type="text" name="post[description]" id="source" value=""
					               style="width: 400px" placeholder="请输入描述"></td>
					</tr>
					<tr>
					    <th>兴趣爱好</th>
					    <td><input class="form-control" type="text" name="post[description]" id="source" value=""
					               style="width: 400px" placeholder="请输入描述"></td>
					</tr>
					<tr>
					    <th>详细地址</th>
					    <td><input class="form-control" type="text" name="post[description]" id="source" value=""
					               style="width: 400px" placeholder="请输入描述"></td>
					</tr>
                    <tr>
                        <th>个性签名</th>
                        <td>
                            <textarea class="form-control" name="post[content]" id="description"
                                      style="width: 47%; height: 100px;" placeholder="请填写幻灯片内容"></textarea>
                        </td>
                    </tr>-->
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" name="post[slide_id]" value="<?php echo $slide_id; ?>">
                <button type="submit" class="btn btn-primary js-ajax-submit">编辑</button>
                <!--<a class="btn btn-default" href="<?php echo url('SlideItem/index',['slide_id'=>$slide_id]); ?>"><?php echo lang('BACK'); ?></a>-->
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="/public/static/js/admin.js"></script>
</body>
</html>