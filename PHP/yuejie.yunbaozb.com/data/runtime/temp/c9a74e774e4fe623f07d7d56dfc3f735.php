<?php /*a:4:{s:71:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/auth/apply.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
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

	
    <title><?php echo lang('实名认证'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/appapi/css/upload.css?t=5">
    <link rel="stylesheet" type="text/css" href="/static/appapi/css/auth_apply.css?t=1566211359">
</head>
<body>
    <div class="auth_top">
        *<?php echo lang('以下信息均为必填项，为保障您的利益，请如实填写'); ?>
    </div>
    <ul class="auth_list">
        <li>
            <p class="auth_list_title"><?php echo lang('真实姓名'); ?></p>
            <p class="auth_list_right">
                <input type="text" name="realname" id="realname" placeholder="<?php echo lang('请输入姓名'); ?>" value="<?php echo $info['name']; ?>">
            </p>
            <p class="clearboth"></p>
        </li>
        <li>
            <p class="auth_list_title"><?php echo lang('手机号码'); ?></p>
            <p class="auth_list_right">
                <input type="text" name="phone" id="phone" placeholder="<?php echo lang('请输入手机号码'); ?>" value="<?php echo $info['mobile']; ?>">
            </p>
            <p class="clearboth"></p>
        </li>
        <li>
            <p class="auth_list_title"><?php echo lang('身份证号'); ?></p>
            <p class="auth_list_right">
                <input type="text" name="cardno" id="cardno" placeholder="<?php echo lang('请输入身份证号码'); ?>" value="<?php echo $info['cer_no']; ?>">
            </p>
            <p class="clearboth"></p>
        </li>
    </ul>
    <div class="line_bd_u">
        <ul class="upload clearfix">
            <li>
                <div class="up_img" data-fileid="up_img2">
                    <?php if($info): ?>
                    <img src="<?php echo $info['front_view1']; ?>">
                    <?php else: ?>
                    <img src="/static/appapi/images/upload.png">
                    <?php endif; ?>
                    <div class="shadd">
                        <div class="progress_bd"><div class="progress_sp"></div></div>
                    </div>
                    <input type="hidden" class="img_input" name="front" id="front" value="<?php echo $info['front_view']; ?>">
                    <input type="file" id="up_img2" class="file_input" name="file"  accept="image/*" style="display:none;"/>
                </div>
                <div class="up_t">
                    <?php echo lang('证件正面'); ?>
                </div>
            </li>
            <li>
                <div class="up_img" data-fileid="up_img3">
                    <?php if($info): ?>
                    <img src="<?php echo $info['back_view1']; ?>">
                    <?php else: ?>
                    <img src="/static/appapi/images/upload.png">
                    <?php endif; ?>
                    <div class="shadd">
                        <div class="progress_bd"><div class="progress_sp"></div></div>
                    </div>
                    <input type="hidden" class="img_input" name="back" id="back" value="<?php echo $info['back_view']; ?>">
                    <input type="file" id="up_img3" class="file_input" name="file"  accept="image/*" style="display:none;"/>
                </div>
                <div class="up_t">
                    <?php echo lang('证件反面'); ?>
                </div>
            </li>
            <li>
                <div class="up_img" data-fileid="up_img4">
                    <?php if($info): ?>
                    <img src="<?php echo $info['handset_view1']; ?>">
                    <?php else: ?>
                    <img src="/static/appapi/images/upload.png">
                    <?php endif; ?>
                    <div class="shadd">
                        <div class="progress_bd"><div class="progress_sp"></div></div>
                    </div>
                    <input type="hidden" class="img_input" name="hand" id="hand" value="<?php echo $info['handset_view']; ?>">
                    <input type="file" id="up_img4" class="file_input" name="file"  accept="image/*" style="display:none;"/>
                </div>
                <div class="up_t">
                    <?php echo lang('手持证件正面照'); ?>
                </div>
            </li>
        </ul>
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
        <?php echo lang('提交认证'); ?>
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
		var protocol="<?php echo $protocol; ?>";
		var domain="<?php echo $domain; ?>";
		var upload_url="<?php echo $upload_url; ?>";

		var qiniu_expedite_url=protocol+'://'+domain+'/';
		var qiniu_upload_url=protocol+'://'+upload_url+'/';
    </script>
    <script src="/static/js/ajaxfileupload.js"></script>
    <script type="text/javascript" src="/static/appapi/js/upload.js?t=1577953340"></script>
    <script type="text/javascript" src="/static/appapi/js/auth.js?t=1"></script>

</body>
</html>