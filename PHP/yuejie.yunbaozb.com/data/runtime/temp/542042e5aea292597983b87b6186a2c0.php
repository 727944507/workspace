<?php /*a:4:{s:71:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/auth/index.html";i:1603852376;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
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

	
    <title><?php echo lang('成为认证大神'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/appapi/css/auth.css?t=1">
    
</head>
<body>

    <div class="auth_bg">
        <if condition="">
        <img src="/static/appapi/images/auth/auth_bg_<?php echo $lang; ?>.jpg">
    </div>
    <div class="auth_i_agrent">
        <div class="switch">
            <input type="checkbox" id="switch">
            <label for="switch">
                <img class="checked_no" src="/static/appapi/images/check_no.png">
                <img class="checked_ok" src="/static/appapi/images/check_ok.png">
            </label>
        </div>
        <?php echo lang('已阅读并同意'); ?> <a href="/appapi/page/detail?id=4">《<?php echo lang('大神认证协议'); ?>》</a>
    </div>
    <div class="auth_i_btn">
        <?php echo lang('立即认证'); ?>
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



    <script type="text/javascript">
        (function(){
            $('.auth_i_btn').click(function(){
                if(typeof($("input[type=checkbox]:checked").val()) == "undefined"){
                    layer.msg('<?php echo lang('请勾选大神认证协议'); ?>');
                    return !1;
                }
                location.href="/appapi/auth/apply?uid=<?php echo $uid; ?>&token=<?php echo $token; ?>";
            })
        })()
    </script>


</body>
</html>