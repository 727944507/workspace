$(function(){
    /* html
    <ul class="upload clearfix">
        <li>
            <div class="up_img" data-fileid="up_img2">
                <if condition="$info">
                <img src="{$info['front_view1']}">
                <else/>
                <img src="__STATIC__/appapi/images/upload.png">
                </if>
                <div class="shadd">
                    <div class="progress_bd"><div class="progress_sp"></div></div>
                </div>
                <input type="hidden" class="img_input" name="front" id="front" value="{$info['front_view']}">
                <input type="file" id="up_img2" class="file_input" name="file"  accept="image/*" style="display:none;"/>
            </div>
            <div class="up_t">
                证件正面
            </div>
        </li>
    </ul>
    */
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
    function ajaxFileUploadBF() {
            var _this=upload_this;
            var iptt=$('.file_input',_this);
            if(iptt.val()==''){
                return !1;
            }
            var animate_div=$(".progress_sp",_this);
            var shadd=$(".shadd",_this);
            shadd.show();
            
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
	function ajaxFileUpload() {
            var _this=upload_this;
            var iptt=$('.file_input',_this);
            if(iptt.val()==''){
                return !1;
            }
            var animate_div=$(".progress_sp",_this);
            var shadd=$(".shadd",_this);
            shadd.show();
			animate_div.css({"width":"0px"});

            var id=_this.data('fileid');
			animate_div.animate({"width":"100%"},700,function(){
				$.ajax({url: "getuploadtoken", success: function(res){
					var resa=JSON.parse(res);
					var token = resa.token;
					var domain = resa.domain;
					var name = 'auth_'+ new Date().getTime()+'.jpg';
					var imgurl = qiniu_expedite_url+name; //加速域名模板上定义
					
					$.ajaxFileUpload({
						url: qiniu_upload_url, //模板上定义
						secureuri: false,
						fileElementId: id,
						data: { 'x:name':name,fname:name,key:name,token:token },
						dataType: 'json',
						success: function(data,status,xhr) {
							//七牛不返回ajaxFileUpload可使用的错误提示，只能自行访问图片尝试
							if(status=='success'){
								layer.msg(Lang("上传成功"));
								$("img",_this).attr("src",imgurl);
								$(".img_input",_this).attr("value",name);
								shadd.hide();
							}else{
								layer.msg(Lang("上传失败"));
								shadd.hide();
							}
						}
						
					})
				
				}
				});
				return true;
			})
			
    }   
});