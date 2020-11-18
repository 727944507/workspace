<?php /*a:4:{s:70:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/agent/one.html";i:1603704624;s:65:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/head.html";i:1603704624;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;s:68:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/scripts.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<title>一级好友</title>
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

	
		<link href='/static/appapi/css/agent_profit.css' rel="stylesheet" type="text/css" >
	</head>
    <body >

	<div class="profit">
		<ul>
			<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
			<li>
				<img class="thumb" src="<?php echo $v['userinfo']['avatar']; ?>">
				<div class="info">
					<p class="name"><?php echo $v['userinfo']['user_nickname']; ?></p>
					<p class="id">ID: <?php echo $v['userinfo']['id']; ?></p>
				</div>
				<div class="info2">
					<p class="icon"><img src="/static/appapi/images/coin.png" class="votes"></p>
					<p class="coin"><?php echo $v['coin']; ?></p>
				</div>
			</li>
			<?php endforeach; endif; else: echo "" ;endif; if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?>
            <div class="empty"></div>
            <?php endif; ?>
		</ul>
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



    <script>
        var uid='<?php echo $uid; ?>';
        var token='<?php echo $token; ?>';
     	$(function(){
            function getlistmore(){
                $.ajax({
                    url:'/appapi/agent/one_more',
                    data:{'page':page,'uid':uid,'token':token},
                    type:'post',
                    dataType:'json',
                    success:function(data){
                        if(data.nums>0){
                            var nums=data.nums;
                            var list=data.list.data;
                            var html='';
                            for(var i=0;i<nums;i++){
                                var v=list[i];
                                html+='<li>\
                                    <img class="thumb" src="'+v['userinfo']['avatar']+'">\
                                    <div class="info">\
                                        <p class="name">'+v['userinfo']['user_nickname']+'</p>\
                                        <p class="id">ID: '+v['userinfo']['id']+'</p>\
                                    </div>\
                                    <div class="info2">\
                                        <p class="icon"><img src="/static/appapi/images/coin.png" class="votes"></p>\
                                        <p class="coin">'+v['coin']+'</p>\
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

            var scroll_obj=$(window);
            scroll_obj.scroll(function(){  
                    var srollPos = scroll_obj.scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  		
                    var totalheight = parseFloat(scroll_obj.height()) + parseFloat(srollPos);  
                    if(($(document).height()-50) <= totalheight  && isscroll) {  
                            isscroll=false;
                            getlistmore()
                    }  
            });  


        })
     </script>
</body>
</html>