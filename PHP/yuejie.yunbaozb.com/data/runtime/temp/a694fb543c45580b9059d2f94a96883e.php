<?php /*a:4:{s:76:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/skillauth/apply.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
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

	
    <title><?php echo $skillinfo['name']; ?></title>
    <link rel="stylesheet" type="text/css" href="/static/iosselect/iosselect.css">
    <link rel="stylesheet" type="text/css" href="/static/appapi/css/upload.css?t=5">
    <link rel="stylesheet" type="text/css" href="/static/appapi/css/skill_apply.css?t=1566207204">
</head>
<body>
    <div class="auth_title">
         <?php echo lang('上传图片'); ?><span>(<?php echo lang('禁止盗用他人图片，发现将会封号'); ?>)</span>
    </div>
    <div class="line_bd_u">
        <ul class="upload clearfix">
            <li>
                <div class="up_img" data-fileid="up_img2">
                    <?php if($info): ?>
                    <img src="<?php echo $info['thumb1']; ?>">
                    <?php else: ?>
                    <img src="/static/appapi/images/skillauth/skill_up.png">
                    <?php endif; ?>
                    <div class="up_tips" <?php if($info): ?>style="display:none;"<?php endif; ?>>
                        <?php echo $skillinfo['auth_tip']; ?>
                    </div>
                    <div class="shadd">
                        <div class="progress_bd"><div class="progress_sp"></div></div>
                    </div>
                    <input type="hidden" class="img_input" name="thumb" id="thumb" value="<?php echo $info['thumb']; ?>">
                    <input type="file" id="up_img2" class="file_input" name="file"  accept="image/*" style="display:none;"/>
                </div>
            </li>
        </ul>
    </div>
    <div class="auth_level clearfix">
        <div class="auth_level_l"><?php echo lang('段位'); ?></div>
        <div class="auth_level_r">
            <?php if($info): ?>
            <span id="level"><?php echo $info['level']; ?></span>
            <?php else: ?>
            <span id="level" class="no"><?php echo lang('必须与图片等级相符'); ?></span>
            <?php endif; ?>
            
        </div>
    </div>

    <?php if($info): if($info['status'] != 0): ?>
        <!-- <div class="autharea auth_ok">
            提交重审
        </div> -->
        <?php else: ?>
        <div class="autharea auth_no">
            <?php echo lang('信息审核中'); ?>
        </div>
        <?php endif; else: ?>
    <div class="autharea auth_ok">
        <?php echo lang('提交审核'); ?>
    </div>
    <?php endif; ?>
    
    

    
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
        var status='<?php echo $info['status']; ?>';
        var levelj='<?php echo $level; ?>';
        var skillid='<?php echo $skillid; ?>';
    </script>
    <script src="/static/js/ajaxfileupload.js"></script>
    <script type="text/javascript" src="/static/iosselect/iosselect.js"></script>
    <script type="text/javascript" src="/static/appapi/js/skillauth.js?t=1577953340"></script>

</body>
</html>