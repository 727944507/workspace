<?php /*a:2:{s:73:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/detail/index.html";i:1603959819;s:67:"/data/wwwroot/yuejie.yunbaozb.com/themes/default/appapi/footer.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="utf-8">
    <meta name="referrer" content="origin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="telephone=no" name="format-detection" />
    <link href='/static/appapi/css/common.css?t=1576565542' rel="stylesheet" type="text/css" >
	<link type="text/css" rel="stylesheet" href="/static/appapi/css/detail.css?t=1540544890"/> 
    <title>收支明细</title>
</head>
<body class="detail">
	<div class="profit_bg">
		<div class="tab">
			<ul>
				<li class="on">
                    消费明细
                    <div class="tab_line bg_default"></div>
                </li>
				<li>
                    收益明细
                    <div class="tab_line bg_default"></div>
                </li>
			</ul>
		</div>
        <div class="profit_line"></div>
		<div class="tab_b votesrecord ">
			<div class="profit_ul clear">
				<ul  class="list">
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<li>
						<div class="avatar">
							<img src="<?php echo $v['userinfo']['avatar_thumb']; ?>" onerror="this.src='/static/images/headicon.png'">
						</div>
						<div class="info">
							<div class="name">
								<p><?php echo $v['userinfo']['user_nickname']; ?></p>
								<p class="userid">ID:<?php echo $v['userinfo']['id']; ?></p>
							</div>
							<div class="recordtype">
								<p ><img src="<?php echo $v['record_thumb']; ?>" class="typeimg"></p>
								<p><?php echo $v['record_name']; ?></p>
							</div>
							<div class="contribute">
								<p class="record_typename"><?php echo $v['record_typename']; ?></p>
								<span class="contribute-nums">-<?php echo $v['total']; ?></span>
								<p class="record_time"><?php echo date('Y/m/d',$v['addtime']); ?></p>
							</div>
						</div>
						
					</li>		
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
		<div class="tab_b hide coinrecord">
			<div class="profit_ul clear">
				<ul  class="list">
					<?php if(is_array($list_coin) || $list_coin instanceof \think\Collection || $list_coin instanceof \think\Paginator): $i = 0; $__LIST__ = $list_coin;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
					<li>
						<div class="avatar">
							<img src="<?php echo $v['userinfo']['avatar_thumb']; ?>" onerror="this.src='/static/images/headicon.png'">
						</div>
						<div class="info">
							<div class="name">
								<p><?php echo $v['userinfo']['user_nickname']; ?></p>
								<p class="userid">ID:<?php echo $v['userinfo']['id']; ?></p>
							</div>
							<div class="recordtype">
								<p ><img src="<?php echo $v['record_thumb']; ?>" class="typeimg"></p>
								<p><?php echo $v['record_name']; ?></p>
							</div>
							<div class="contribute">
								<p class="record_typename"><?php echo $v['record_typename']; ?></p>
								<span class="contribute-nums">+<?php echo $v['total']; ?></span>
								<p class="record_time"><?php echo date('Y/m/d',$v['addtime']); ?></p>
							</div>
						</div>
						
					</li>		
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
	</div>
	

	<script>
		var uid='<?php echo (isset($uid) && ($uid !== '')?$uid:''); ?>';
		var token='<?php echo (isset($token) && ($token !== '')?$token:''); ?>';
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
	<script>
	$(function(){
		function getlistmore(){
			$.ajax({
				url:'/appapi/detail/voteslist_more',
				data:{'page':page,'uid':uid,'token':token},
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.nums>0){
							var nums=data.nums;
							var list=data.data;
							var html='';
							for(var i=0;i<nums;i++){
								html='<li>\
										<div class="avatar">\
										<img src="'+list[i]['userinfo']['avatar_thumb']+'">\
										</div>\
										<div class="info">\
											<div class="name">\
												<p>'+list[i]['userinfo']['user_nickname']+'</p>\
												<p class="userid">ID:'+list[i]['uid']+'</p>\
											</div>\
											<div class="recordtype">\
												<p ><img src="'+list[i]['record_thumb']+'" class="typeimg"></p>\
												<p>'+list[i]['record_name']+'</p>\
											</div>\
											<div class="contribute">\
												<p class="record_typename">'+list[i]['record_typename']+'</p>\
												<span class="contribute-nums">-'+list[i]['total']+'</span>\
												<p class="record_time">'+list[i]['addtime']+'</p>\
											</div>\
										</div>\
									</li>';
							}
						
						$(".votesrecord .profit_ul ul").append(html);
					}
					
					if(data.isscroll==1){
						page++;
						isscroll=true;
					}
				}
			})
		}
		
		function getcoinlistmore(){
			$.ajax({
				url:'/appapi/detail/coinlist_more',
				data:{'page':page,'uid':uid,'token':token},
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.nums>0){
							var nums=data.nums;
							var list=data.data;
							var html='';
							for(var i=0;i<nums;i++){
								html='<li>\
										<div class="avatar">\
										<img src="'+list[i]['userinfo']['avatar_thumb']+'">\
										</div>\
										<div class="info">\
											<div class="name">\
												<p>'+list[i]['userinfo']['user_nickname']+'</p>\
												<p class="userid">ID:'+list[i]['uid']+'</p>\
											</div>\
											<div class="recordtype">\
												<p ><img src="'+list[i]['record_thumb']+'" class="typeimg"></p>\
												<p>'+list[i]['record_name']+'</p>\
											</div>\
											<div class="contribute">\
												<p class="record_typename">'+list[i]['record_typename']+'</p>\
												<span class="contribute-nums">-'+list[i]['total']+'</span>\
												<p class="record_time">'+list[i]['addtime']+'</p>\
											</div>\
										</div>\
									</li>';
							}
						
						$(".coinrecord .profit_ul ul").append(html);
					}
					
					if(data.isscroll==1){
						page++;
						isscroll2=true;
					}
				}
			})
		}
		
		$(".tab ul li").on("click",function(){
			$(this).siblings().removeClass("on");
			$(this).addClass("on");
			$(".tab_b").hide().eq($(this).index()).show();
		})
		var page=2; 
		var isscroll=true; 
		var isscroll2=true; 

		$(".votesrecord .profit_ul").scroll(function(){  
				var srollPos = $(".votesrecord .profit_ul").scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  		
				var totalheight = parseFloat($(".votesrecord .profit_ul").height()) + parseFloat(srollPos);  
				if(($(document).height()-50) <= totalheight  && isscroll) {  
						isscroll=false;
						getlistmore()
				}  
		});  
		
		$(".coinrecord .profit_ul").scroll(function(){  
				var srollPos = $(".coinrecord .profit_ul").scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  		
				var totalheight = parseFloat($(".coinrecord .profit_ul").height()) + parseFloat(srollPos);  
				if(($(document).height()-50) <= totalheight  && isscroll2) {  
						isscroll2=false;
						getcoinlistmore()
				}  
		});  

	})
	</script>	
</body>
</html>