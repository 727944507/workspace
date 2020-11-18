(function () {
	
	// 技能订单统计
	$('.jndd_type li').click(function() {
		$('.jndd_type li').removeClass('on');
		var day=$(this).data('day');
		$(this).addClass('on');
		
		$('.jnddlist_start_time').val('');
		$('.jnddlist_end_time').val('');
		
		jndd_lists(day);

	});
	
	$('.jndd_search').click(function() {
		var daystart=$('.jnddlist_start_time').val();
		var dayend=$('.jnddlist_end_time').val();
		
		if(!daystart || !dayend){
			alert('请选择时间');
			return;
		}
		jndd_lists();
		
		$('.jndd_type li').removeClass('on');

	});
	
	function jndd_lists(day=''){
		var daystart=$('.jnddlist_start_time').val();
		var dayend=$('.jnddlist_end_time').val();
		$.ajax({
			url: "/admin/main/jndd_lists",
			data: {day:day,daystart:daystart,dayend:dayend},
			type: "POST",
			dataType: "json",
			success: function(data) {
				
				var lists='';
				for(var i= 0; i < data.length; i++) {
					lists+='<li>\
						<div class="name">'+data[i]['name']+'</div>\
						<div class="num">'+data[i]['nums']+'</div>\
					</li>';
				}

				
				$('.jnddlist').html(lists);
			}
		});
	}
	
	
	// 技能人数统计
	$('.jnrs_type li').click(function() {
		$('.jnrs_type li').removeClass('on');
		var day=$(this).data('day');
		$(this).addClass('on');
		
		$('.jnrslist_start_time').val('');
		$('.jnrslist_end_time').val('');
		
		jnrs_lists(day);

	});
	
	$('.jnrs_search').click(function() {
		var daystart=$('.jnrslist_start_time').val();
		var dayend=$('.jnrslist_end_time').val();
		
		if(!daystart || !dayend){
			alert('请选择时间');
			return;
		}
		jnrs_lists();
		
		$('.jnrs_type li').removeClass('on');

	});
	
	function jnrs_lists(day=''){
		var daystart=$('.jnrslist_start_time').val();
		var dayend=$('.jnrslist_end_time').val();
		$.ajax({
			url: "/admin/main/jnrs_lists",
			data: {day:day,daystart:daystart,dayend:dayend},
			type: "POST",
			dataType: "json",
			success: function(data) {
				
				var lists='';
				for(var i= 0; i < data.length; i++) {
					lists+='<li>\
						<div class="name">'+data[i]['name']+'</div>\
						<div class="num">'+data[i]['nums']+'</div>\
					</li>';
				}
				
		
				
				
				$('.jnrslist').html(lists);
			}
		});
	}
	
	
	// 技能人数统计
	$('.yhfx_type li').click(function() {
		$('.yhfx_type li').removeClass('on');
		var day=$(this).data('day');
		$(this).addClass('on');
		
		$('.yhfxlist_start_time').val('');
		$('.yhfxlist_end_time').val('');
		
		yhfx_lists(day);

	});
	
	$('.yhfx_search').click(function() {
		var daystart=$('.yhfxlist_start_time').val();
		var dayend=$('.yhfxlist_end_time').val();
		
		if(!daystart || !dayend){
			alert('请选择时间');
			return;
		}
		yhfx_lists();
		
		$('.yhfx_type li').removeClass('on');

	});
	
	function yhfx_lists(day=''){
		var daystart=$('.yhfxlist_start_time').val();
		var dayend=$('.yhfxlist_end_time').val();
		$.ajax({
			url: "/admin/main/yhfx_lists",
			data: {day:day,daystart:daystart,dayend:dayend},
			type: "POST",
			dataType: "json",
			success: function(data) {
				
				var lists='<ul class="list ">\
						<li>\
							<div class="name">男生注册人数</div>\
							<div class="num">'+data['nums0']+'</div>\
						</li>\
						<li>\
							<div class="name">女生注册人数</div>\
							<div class="num">'+data['nums1']+'</div>\
						</li>\
						<li>\
							<div class="name">总注册人数</div>\
							<div class="num">'+data['nums2']+'</div>\
						</li>\
					</ul>\
					<ul class="list ">\
						<li>\
							<div class="name">0~10岁</div>\
							<div class="num">'+data['year0']+'</div>\
						</li>\
						<li>\
							<div class="name">11~20岁</div>\
							<div class="num">'+data['year1']+'</div>\
						</li>\
						<li>\
							<div class="name">21~30岁</div>\
							<div class="num">'+data['year2']+'</div>\
						</li>\
						<li>\
							<div class="name">31~40岁</div>\
							<div class="num">'+data['year3']+'</div>\
						</li>\
						<li>\
							<div class="name">41岁以上</div>\
							<div class="num">'+data['year4']+'</div>\
						</li>\
					</ul>';

			
				
				
				
				$('.yhfxlist').html(lists);
			}
		});
	}
	
	
	
	// 动态功能分析
	$('.dtgn_type li').click(function() {
		$('.dtgn_type li').removeClass('on');
		var day=$(this).data('day');
		$(this).addClass('on');
		
		$('.dtgnlist_start_time').val('');
		$('.dtgnlist_end_time').val('');
		
		dtgn_lists(day);

	});
	
	$('.dtgn_search').click(function() {
		var daystart=$('.dtgnlist_start_time').val();
		var dayend=$('.dtgnlist_end_time').val();
		
		if(!daystart || !dayend){
			alert('请选择时间');
			return;
		}
		dtgn_lists();
		
		$('.dtgn_type li').removeClass('on');

	});
	
	function dtgn_lists(day=''){
		var daystart=$('.dtgnlist_start_time').val();
		var dayend=$('.dtgnlist_end_time').val();
		$.ajax({
			url: "/admin/main/dtgn_lists",
			data: {day:day,daystart:daystart,dayend:dayend},
			type: "POST",
			dataType: "json",
			success: function(data) {
				
				var lists='<li>\
							<div class="name">动态数量</div>\
							<div class="num">'+data['dt0']+'</div>\
						</li>\
						<li>\
							<div class="name">评论数量</div>\
							<div class="num">'+data['dt1']+'</div>\
						</li>\
						<li>\
							<div class="name">点赞数量</div>\
							<div class="num">'+data['dt2']+'</div>\
						</li>\
						<li>\
							<div class="name">技能动态占比</div>\
							<div class="num">'+data['dt3']+'%</div>\
						</li>\
						<li>\
							<div class="name">使用男女占比</div>\
							<div class="num">'+data['dt4']+'%-'+data['dt5']+'%</div>\
						</li>';

				
				
				$('.dtgnlist').html(lists);
			}
		});
	}
	
	
	// 滴滴下单功能分析
	$('.ddxd_type li').click(function() {
		$('.ddxd_type li').removeClass('on');
		var day=$(this).data('day');
		$(this).addClass('on');
		
		$('.ddxdlist_start_time').val('');
		$('.ddxdlist_end_time').val('');
					
		ddxd_lists(day);

	});
	
	$('.ddxd_search').click(function() {
		var daystart=$('.ddxdlist_start_time').val();
		var dayend=$('.ddxdlist_end_time').val();
		
		if(!daystart || !dayend){
			alert('请选择时间');
			return;
		}
		ddxd_lists();
		
		$('.ddxd_type li').removeClass('on');

	});
	
	function ddxd_lists(day=''){
		var daystart=$('.ddxdlist_start_time').val();
		var dayend=$('.ddxdlist_end_time').val();
		$.ajax({
			url: "/admin/main/ddxd_lists",
			data: {day:day,daystart:daystart,dayend:dayend},
			type: "POST",
			dataType: "json",
			success: function(data) {
				
				var lists='<li>\
							<div class="name">使用次数</div>\
							<div class="num">'+data['dd0']+'</div>\
						</li>\
						<li>\
							<div class="name">使用男女占比</div>\
							<div class="num">'+data['dd1']+'%-'+data['dd2']+'%</div>\
						</li>\
						<li>\
							<div class="name">平均抢单人数</div>\
							<div class="num">'+data['dd3']+'</div>\
						</li>\
						<li>\
							<div class="name">下单成功率</div>\
							<div class="num">'+data['dd4']+'%</div>\
						</li>';

				
				$('.ddxdlist').html(lists);
			}
		});
	}
	
	

})()