<?php /*a:4:{s:72:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/agent/index.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <title>全民赚钱</title>
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

	
    <link href='/static/appapi/css/agent.css?t=1577181718' type="text/css" rel="stylesheet">
</head>
<body>
    <div class="agent">
        <div class="agent_title">
            <img src="/static/appapi/images/agent/tips_title.png">
        </div>
        <div class="agent_tips">
            <img src="/static/appapi/images/agent/tips_top.png">
        </div>
        <div class="agent_code">
            <div class="agent_code_t">
                — 您的邀请码 —
            </div>
            <div class="agent_code_b">
                <?php if(is_array($code_a) || $code_a instanceof \think\Collection || $code_a instanceof \think\Paginator): $i = 0; $__LIST__ = $code_a;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <span><?php echo $v; ?></span>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="agent_code_c">
                <a href="copy://<?php echo $code; ?>">点击复制</a>
            </div>
        </div>
        <div class="agent_info">
            <div class="agent_info_bd">
                <div class="agent_info_bd_tab">
                    我的分享
                </div>
                <div class="agent_info_bd_list clearfix">
                    <ul>
                        <li>
                            <a href="/appapi/agent/one?uid=<?php echo $uid; ?>&token=<?php echo $token; ?>">
                            <div class="li_icon">
                                <img src="/static/appapi/images/agent/icon.png">
                            </div>
                            <div class="li_t">
                                一级好友
                            </div>
                            <div class="li_d">
                                (<?php echo $one_nums; ?>人)
                            </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="share_explain">
            <div class="agent_explain_tips">
                功能说明
            </div>
            <div class="share_explain_bd">
                <div class="share_explain_bd_t">
                    以花费100<?php echo $site_info['name_coin']; ?>为例说明
                </div>
                <div class="share_explain_bd_d">
                    <p>一级好友花费100<?php echo $site_info['name_coin']; ?>，你可获得<?php echo $configpri['agent_one']; ?><?php echo $site_info['name_votes']; ?>奖励；</p>
                </div>
            </div>
        </div>
    </div>
    <div class="share">
        <a href="shareagent://<?php echo $code; ?>"><div class="share_btn">
            分享赚钱
        </div></a>
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
