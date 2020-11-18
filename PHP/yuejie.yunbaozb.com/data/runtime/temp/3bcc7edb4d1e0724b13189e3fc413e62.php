<?php /*a:4:{s:72:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/page/detail.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page['post_title']; ?></title>
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

	
    <link href="/static/appapi/css/page.css?t=1556176544" rel="stylesheet" type="text/css">
</head>
<body>
     <!-- <div class="title"> -->
        <!-- <?php echo $page['post_title']; ?> -->
     <!-- </div> -->
     
     <div class="content">
        <?php echo (isset($page['post_content']) && ($page['post_content'] !== '')?$page['post_content']:''); ?>
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
