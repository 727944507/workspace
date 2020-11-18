$(function(){
    /* 段位选择 */
    var data=JSON.parse(levelj);
    var showBankDom = document.querySelector('#level');
    var bankIdDom = document.querySelector('#level');
    if(status==''){
        showBankDom.addEventListener('click', function () {
            var bankId = showBankDom.dataset['id'];
            var bankName = showBankDom.dataset['value'];

            var bankSelect = new IosSelect(1, 
                [data],
                {
                    container: '.container',
                    title: '',
                    itemHeight: 50,
                    itemShowCount: 3,
                    sureText: Lang('确定'),
                    closeText: Lang('取消'),
                    oneLevelId: bankId,
                    callback: function (selectOneObj) {
                        bankIdDom.innerHTML = selectOneObj.value;
                        bankIdDom.classList.remove("no");
                        //showBankDom.innerHTML = selectOneObj.value;
                        showBankDom.dataset['id'] = selectOneObj.id;
                        showBankDom.dataset['value'] = selectOneObj.value;
                    },
                    fallback: function () {
                        //console.log(1);
                    },
                    maskCallback: function () {
                        //console.log(2);
                    }
            });
        });        
    }

	/*点击提交审核*/
	var is_submit=0;
	$(".auth_ok").click(function(){
		if(is_submit==1){
			return;
		}

		var thumb=$("#thumb").val();
		var levelid=$("#level").data('id');

        if(thumb==''){
			layer.msg(Lang("请上传截图"));
			return;
		}
        
        if(levelid<1){
			layer.msg(Lang("请选择段位"));
			return;
		}
        
		is_submit=1;

		$.ajax({
			url: '/Appapi/skillauth/auth_save',
			type: 'POST',
			dataType: 'json',
			data: {uid:uid,token:token,skillid: skillid,thumb:thumb,levelid:levelid},
			success:function(data){
                is_submit=0;
				var code=data.code;
				if(code!=0){
					layer.msg(data.msg);
					return;
				}else{
					layer.msg(Lang('提交成功'), {time:1000},function(){
						location.reload();
					});
				}
			},
			error:function(e){
                is_submit=0;
				console.log(e);
			}
		});
		
	});
    var upload_this;
    $(".up_img img").click(function(){
        //if(status!='' && status==0){
        if(status!=''){
            return !1;
        }
        upload_this=$(this).parent();
        upload();
    })

    function upload() {

			var iptt=$('.file_input',upload_this)[0];
            //var iptt=document.getElementById(index);
			if(window.addEventListener) { // Mozilla, Netscape, Firefox
                iptt.removeEventListener('change',ajaxFileUpload,false);
                iptt.addEventListener('change',ajaxFileUpload,false);
			}else{
                iptt.detachEvent('onchange',ajaxFileUpload);
                iptt.attachEvent('onchange',ajaxFileUpload);
			}
			iptt.click();
    }
    function ajaxFileUpload() {
        var _this=upload_this;
            var iptt=$('.file_input',_this);
            if(iptt.val()==''){
                return !1;
            }
            var animate_div=$(".progress_sp",_this);
            var shadd=$(".shadd",_this);
            shadd.show();
            
            var up_tips=$(".up_tips",_this);
            
			animate_div.css({"width":"0px"});

            var id=_this.data('fileid');
			animate_div.animate({"width":"100%"},700,function(){
					$.ajaxFileUpload
					(
						{
							url: '/Appapi/upload/upload',
							secureuri: false,
							fileElementId: id,
							data: {},
							dataType: 'json',
							success: function(data) {
                                var str=data;
                                //console.log(str);
                                if(str.code==200){
                                    $("img",_this).attr("src",str.data.preview_url);
                                    $(".img_input",_this).attr("value",str.data.filepath);
                                    shadd.hide();
                                    up_tips.hide();
                                }else{
                                    layer.msg(str.msg);
                                    shadd.hide();
                                }
							},
							error: function(data) {
                                layer.msg(Lang("上传失败"));
                                shadd.hide();
							}
						}
					)
					return true;
			});
    }
});