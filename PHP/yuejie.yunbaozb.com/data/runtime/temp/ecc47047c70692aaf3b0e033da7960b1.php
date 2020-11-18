<?php /*a:4:{s:72:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/auth/status.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta content="telephone=no" name="format-detection" />
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">

    <!-- No Baidu Siteapp-->
    <meta http-equiv="Cache-Control" content="no-siteapp"/>

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->
    <link rel="icon" href="/favicon.ico" >
    <link rel="shortcut icon" href="/favicon.ico">
    <link href='/static/appapi/css/common.css?t=1573550405' rel="stylesheet" type="text/css" >

	
    <title><?php echo lang('实名认证'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/appapi/css/auth.css?t=1565083698">

</head>
<body>
    <div class="auth_status">
        <div class="status_img">
            <img src="/static/appapi/images/auth_status.png">
        </div>
        
        <div class="status_info">
            <?php if($info && $info['status'] == 2): ?>
            <p class="status_info_t no"><?php echo lang('身份信息审核未通过'); ?></p>
            <p class="status_info_d"><?php echo $info['reason']; ?></p>
            <?php else: ?>
            <p class="status_info_t"><?php echo lang('身份信息审核中...'); ?></p>
            <p class="status_info_d"><?php echo lang('3个工作日内会有审核结果，请耐心等待'); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if($info && $info['status'] == 2): ?>
        <div class="autharea">
            <a href="/appapi/auth/apply?uid=<?php echo $uid; ?>&token=<?php echo $token; ?>&reset=1"><?php echo lang('重新认证'); ?></a>
        </div>
        <?php endif; ?>
    </div>
    

    
<script type="text/javascript">
    var uid='<?php echo $uid; ?>';
    var token='<?php echo $token; ?>';
    var baseSize = 100;
    function setRem () {
        var scale = document.documentElement.clientWidth / 750;
        document.documentElement.style.fontSize = (baseSize * Math.min(scale, 3)) + 'px';
    }
    setRem();
    window.onresize = function () {
        setRem();
    }
</script>
<script src="/static/js/jquery.js"></script>
<script src="/static/js/layer/layer.js"></script>
<script src="/static/appapi/js/<?php echo $lang; ?>.js"></script>
<script src="/static/appapi/js/lang.js"></script>



</body>
</html>