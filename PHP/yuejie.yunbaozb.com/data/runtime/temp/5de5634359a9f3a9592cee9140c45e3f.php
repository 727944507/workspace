<?php /*a:4:{s:71:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/cash/index.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
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

	
    <title><?php echo lang('提现记录'); ?></title>
	<link type="text/css" rel="stylesheet" href="/static/appapi/css/cash.css?t=1566262037"/> 
    
</head>
<body>
    <div class="list">
        <ul>
            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <li>
                <div class="list_l">
                    <p><span><img class="coin" src="/static/appapi/images/coin.png"></span><?php echo $v['votes']; ?></p>
                    <p class="money"><span><?php echo lang('金额'); ?>:</span><?php echo $v['money']; ?></p>
                </div>
                <div class="list_r">
                    <p><?php echo $v['status_name']; ?></p>
                    <p><?php echo $v['addtime']; ?></p>
                </div>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
	<script>
		var uid='<?php echo $uid; ?>';
		var token='<?php echo $token; ?>';
	</script>
	

    
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



	<script>
	$(function(){
		function getlistmore(){
			$.ajax({
				url:'/appapi/cash/getlistmore',
				data:{'page':page,'uid':uid,'token':token},
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.nums>0){
                        var nums=data.nums;
                        var list=data.data;
                        var html='';
                        for(var i=0;i<nums;i++){
                            var v=list[i];
                            html='<li>\
                                    <div class="list_l">\
                                        <p><span><img class="coin" src="/static/appapi/images/coin.png"></span>'+v['votes']+'}</p>\
                                        <p class="money"><span><?php echo lang('金额'); ?>:</span>'+v['money']+'}</p>\
                                    </div>\
                                    <div class="list_r">\
                                        <p>'+v['status_name']+'}</p>\
                                        <p>'+v['addtime']+'}</p>\
                                    </div>\
                                </li>';
                        }
						
						$(".list ul").append(html);
					}
					
					if(data.isscroll==1){
						page++;
						isscroll=true;
					}
				}
			})
		}

		var page=2; 
		var isscroll=true; 
        
        var scroll_list=$("body");

		scroll_list.scroll(function(){  
            var srollPos = scroll_list.scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  		
            var totalheight = parseFloat(scroll_list.height()) + parseFloat(srollPos);  
            if(($(document).height()-50) <= totalheight  && isscroll) {  
                    isscroll=false;
                    getlistmore()
            }  
		});  


	})
	</script>	
</body>
</html>