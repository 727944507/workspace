<?php /*a:2:{s:86:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/user/admin_index/index.html";i:1603704624;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
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
        <li class="active"><a><?php echo lang('USER_INDEXADMIN_INDEX'); ?></a></li>
    </ul>
    <form class="well form-inline margin-top-20" method="post" action="<?php echo url('user/adminIndex/index'); ?>">
        用户ID：
        <input class="form-control" type="text" name="uid" style="width: 200px;" value="<?php echo input('request.uid'); ?>"
               placeholder="请输入用户ID">
        关键字：
        <input class="form-control" type="text" name="keyword" style="width: 200px;" value="<?php echo input('request.keyword'); ?>"
               placeholder="用户名/昵称/邮箱">
		设备来源：
        <select class="form-control" name="source">
            <option value="">全部</option>
                <option value="0" <?php if(input('request.source') != '' && input('request.source') == '0'): ?>selected<?php endif; ?>>PC</option>
                <option value="1" <?php if(input('request.source') != '' && input('request.source') == '1'): ?>selected<?php endif; ?>>安卓APP</option>
                <option value="2" <?php if(input('request.source') != '' && input('request.source') == '2'): ?>selected<?php endif; ?>>苹果APP</option>
                <option value="3" <?php if(input('request.source') != '' && input('request.source') == '3'): ?>selected<?php endif; ?>>小程序</option>
        </select>

        提交时间：
        <input class="form-control js-bootstrap-date" name="start_time" id="start_time" value="<?php echo input('request.start_time'); ?>" aria-invalid="false" style="width: 110px;"> - 
        <input class="form-control js-bootstrap-date" name="end_time" id="end_time" value="<?php echo input('request.end_time'); ?>" aria-invalid="false" style="width: 110px;">
            	   
        <input type="submit" class="btn btn-primary" value="搜索"/>
        <a class="btn btn-danger" href="<?php echo url('user/adminIndex/index'); ?>">清空</a>
		<br>
        <br>
		用户数：<?php echo $nums; ?>  (根据条件统计)
    </form>
    <form method="post" class="js-ajax-form">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th><?php echo lang('USERNAME'); ?></th>
                <th><?php echo lang('NICENAME'); ?></th>
                <th><?php echo lang('AVATAR'); ?></th>
                <th><?php echo lang('EMAIL'); ?></th>
                <th>余额</th>
                <th>累计消费</th>
                <th>订单收益余额</th>
                <th>订单收益总额</th>
                <th>礼物收益余额</th>
                <th>礼物收益总额</th>
                <th><?php echo lang('REGISTRATION_TIME'); ?></th>
                <th><?php echo lang('LAST_LOGIN_TIME'); ?></th>
                <th><?php echo lang('LAST_LOGIN_IP'); ?></th>
                <th><?php echo lang('STATUS'); ?></th>
                <th><?php echo lang('ACTIONS'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php 
                $user_statuses=array("0"=>lang('USER_STATUS_BLOCKED'),"1"=>lang('USER_STATUS_ACTIVATED'),"2"=>lang('USER_STATUS_UNVERIFIED'));
             if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$vo): ?>
                <tr>
                    <td><?php echo $vo['id']; ?></td>
                    <td><?php echo !empty($vo['user_login']) ? $vo['user_login'] : ($vo['mobile']?$vo['mobile']:lang('THIRD_PARTY_USER')); ?>
                    </td>
                    <td><?php echo !empty($vo['user_nickname']) ? $vo['user_nickname'] : lang('NOT_FILLED'); ?></td>
                    <td><img width="25" height="25" src="<?php echo get_upload_path($vo['avatar']); ?>"/></td>
                    <td><?php echo $vo['user_email']; ?></td>
                    <td><?php echo $vo['coin']; ?></td>
                    <td><?php echo $vo['consumption']; ?></td>
                    <td><?php echo $vo['votes']; ?></td>
                    <td><?php echo $vo['votestotal']; ?></td>
					<td><?php echo $vo['votes_gift']; ?></td>
                    <td><?php echo $vo['votes_gifttotal']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$vo['last_login_time']); ?></td>
                    <td><?php echo $vo['last_login_ip']; ?></td>
                    <td>
                        <?php switch($vo['user_status']): case "0": ?>
                                <span class="label label-danger"><?php echo $user_statuses[$vo['user_status']]; ?></span>
                            <?php break; case "1": ?>
                                <span class="label label-success"><?php echo $user_statuses[$vo['user_status']]; ?></span>
                            <?php break; case "2": ?>
                                <span class="label label-warning"><?php echo $user_statuses[$vo['user_status']]; ?></span>
                            <?php break; ?>
                        <?php endswitch; ?>
                    </td>
                    <td>
                        <?php if($vo['id'] != '1'): if(empty($vo['user_status']) || (($vo['user_status'] instanceof \think\Collection || $vo['user_status'] instanceof \think\Paginator ) && $vo['user_status']->isEmpty())): ?>
                                <a class="btn btn-xs btn-success js-ajax-dialog-btn"
                                   href="<?php echo url('adminIndex/cancelban',array('id'=>$vo['id'])); ?>"
                                   data-msg="<?php echo lang('ACTIVATE_USER_CONFIRM_MESSAGE'); ?>"><?php echo lang('ACTIVATE_USER'); ?></a>
                                <?php else: ?>
                                <a class="btn btn-xs btn-warning js-ajax-dialog-btn"
                                   href="<?php echo url('adminIndex/ban',array('id'=>$vo['id'])); ?>"
                                   data-msg="<?php echo lang('BLOCK_USER_CONFIRM_MESSAGE'); ?>"><?php echo lang('BLOCK_USER'); ?></a>
                            <?php endif; else: ?>
                            <a class="btn btn-xs btn-warning disabled"><?php echo lang('BLOCK_USER'); ?></a>
                        <?php endif; if($vo['isrecommend'] == '1'): ?>
							<a class="btn btn-xs btn-info js-ajax-dialog-btn"
								   href="<?php echo url('adminIndex/setrecommend',array('id'=>$vo['id'],'isrecommend'=>0)); ?>">取消推荐</a>
						<?php else: ?>
							<a class="btn btn-xs btn-info js-ajax-dialog-btn"
								   href="<?php echo url('adminIndex/setrecommend',array('id'=>$vo['id'],'isrecommend'=>1)); ?>">推荐</a>
						<?php endif; if($vo['ishost'] == 1): ?>
                            <a class="btn btn-xs btn-primary js-ajax-dialog-btn" href="<?php echo url('adminIndex/setHost',array('id'=>$vo['id'],'ishost'=>0)); ?>" >取消派单主持人</a>
                        <?php else: ?>
                            <a class="btn btn-xs btn-primary js-ajax-dialog-btn" href="<?php echo url('adminIndex/setHost',array('id'=>$vo['id'],'ishost'=>1)); ?>">设为派单主持人</a>
                        <?php endif; ?>
                        
                        <a class="btn btn-xs btn-danger js-ajax-dialog-btn"
                                   href="<?php echo url('adminIndex/del',array('id'=>$vo['id'])); ?>"
                                   data-msg="您确定要删除此用户么？"><?php echo lang('DELETE'); ?></a>
                    </td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <div class="pagination"><?php echo $page; ?></div>
    </form>
</div>
<script src="/static/js/admin.js"></script>
</body>
</html>