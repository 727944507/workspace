$(function(){
	/*点击提交审核*/
	var is_submit=0;
	$(".auth_ok").click(function(){
		if(is_submit==1){
			return;
		}

		var realname=$("#realname").val();
		var phone=$("#phone").val();
		var cardno=$("#cardno").val();
		var front=$("#front").val();
		var back=$("#back").val();
		var hand=$("#hand").val();

		if(realname==''){
			layer.msg(Lang("请输入姓名"));
			return;
		}
        
        if(phone==''){
			layer.msg(Lang("请输入手机号码"));
			return;
		}
        
        if(cardno==''){
			layer.msg(Lang("请输入身份证号码"));
			return;
		}
        
        if(front==''){
			layer.msg(Lang("请上传证件正面"));
			return;
		}
        
        if(back==''){
			layer.msg(Lang("请上传证件反面"));
			return;
		}
        
        if(hand==''){
			layer.msg(Lang("请上传手持证件正面照"));
			return;
		}

		is_submit=1;

		$.ajax({
			url: '/Appapi/Auth/auth_save',
			type: 'POST',
			dataType: 'json',
			data: {uid:uid,token:token,realname: realname,phone:phone,cardno:cardno,front:front,back:back,hand:hand},
			success:function(data){
                is_submit=0;
				var code=data.code;
				if(code!=0){
					layer.msg(data.msg);
					return;
				}else{
					layer.msg(Lang('提交成功'), {time:1000},function(){
						// location.reload();
						window.location.href="/Appapi/Auth/index&uid="+uid+"&token="+token;
					});


				}
			},
			error:function(e){
                is_submit=0;
				console.log(e);
			}
		});
		
	});
});