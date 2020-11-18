<?php /*a:2:{s:87:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/admin/setting/configpri.html";i:1603704624;s:77:"/data/wwwroot/yuejie.yunbaozb.com/themes/admin_simpleboot3/public/header.html";i:1603704624;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->


    <link href="/themes/admin_simpleboot3/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/admin_simpleboot3/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        form .input-order {
            margin-bottom: 0px;
            padding: 0 2px;
            width: 42px;
            font-size: 12px;
        }

        form .input-order:focus {
            outline: none;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .form-required {
            color: red;
        }
    </style>
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "static/js/",
            APP: '<?php echo app('request')->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/themes/admin_simpleboot3/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="/static/js/wind.js"></script>
    <script src="/themes/admin_simpleboot3/public/assets/js/bootstrap.min.js"></script>
    <script>
        Wind.css('artDialog');
        Wind.css('layer');
        $(function () {
            $("[data-toggle='tooltip']").tooltip({
                container:'body',
                html:true,
            });
            $("li.dropdown").hover(function () {
                $(this).addClass("open");
            }, function () {
                $(this).removeClass("open");
            });
        });
    </script>
    <?php if(APP_DEBUG): ?>
        <style>
            #think_page_trace_open {
                z-index: 9999;
            }
        </style>
    <?php endif; ?>
<style>
.cdnhide{
	display:none;
}
</style>
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#A" data-toggle="tab">登录配置</a></li>
        <li><a href="#B" data-toggle="tab">支付配置</a></li>
        <li><a href="#C" data-toggle="tab">提现配置</a></li>
        <li><a href="#D" data-toggle="tab">IM配置</a></li>
        <li><a href="#E" data-toggle="tab">智能排序</a></li>
        <li><a href="#F" data-toggle="tab">动态配置</a></li>
        <li><a href="#G" data-toggle="tab">邀请赚钱</a></li>
        <li><a href="#H" data-toggle="tab">订单设置</a></li>
        <li><a href="#I" data-toggle="tab">聊天室</a></li>
    </ul>
    <form class="form-horizontal js-ajax-form margin-top-20" role="form" action="<?php echo url('setting/configpriPost'); ?>" method="post">
        <fieldset>
            <div class="tabbable">
                <div class="tab-content">
                    <div class="tab-pane active" id="A">
						<div class="form-group">
                            <label for="input-reg_reward" class="col-sm-2 control-label">注册奖励</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-reg_reward"
                                       name="options[reg_reward]" value="<?php echo (isset($config['reg_reward']) && ($config['reg_reward'] !== '')?$config['reg_reward']:''); ?>">
                                       <p class="help-block">新用户注册奖励（整数）</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-site-name" class="col-sm-2 control-label">登录方式</label>
                            <div class="col-md-6 col-sm-10">
                                <?php 
									$QQ='QQ';
									$wx='wx';
									$Facebook='Facebook';
									$ios='ios';
								 ?>
								<label class="checkbox-inline"><input type="checkbox" value="QQ" name="login_type[]" <?php if(in_array(($QQ), is_array($config['login_type'])?$config['login_type']:explode(',',$config['login_type']))): ?>checked="checked"<?php endif; ?>>QQ</label>
								<label class="checkbox-inline"><input type="checkbox" value="wx" name="login_type[]" <?php if(in_array(($wx), is_array($config['login_type'])?$config['login_type']:explode(',',$config['login_type']))): ?>checked="checked"<?php endif; ?>>微信</label>
								<label class="checkbox-inline"><input type="checkbox" value="Facebook" name="login_type[]" <?php if(in_array(($Facebook), is_array($config['login_type'])?$config['login_type']:explode(',',$config['login_type']))): ?>checked="checked"<?php endif; ?>>Facebook</label>
								<label class="checkbox-inline"><input type="checkbox" value="ios" name="login_type[]" <?php if(in_array(($ios), is_array($config['login_type'])?$config['login_type']:explode(',',$config['login_type']))): ?>checked="checked"<?php endif; ?>>ios</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-site-name" class="col-sm-2 control-label">分享方式</label>
                            <div class="col-md-6 col-sm-10">
                                <?php 
									$share_qq='qq';
									$share_qzone='qzone';
									$share_wx='wx';
									$share_wchat='wchat';
								 ?>
								<label class="checkbox-inline"><input type="checkbox" value="qq" name="share_type[]" <?php if(in_array(($share_qq), is_array($config['share_type'])?$config['share_type']:explode(',',$config['share_type']))): ?>checked="checked"<?php endif; ?>>QQ</label>
								<label class="checkbox-inline"><input type="checkbox" value="qzone" name="share_type[]" <?php if(in_array(($share_qzone), is_array($config['share_type'])?$config['share_type']:explode(',',$config['share_type']))): ?>checked="checked"<?php endif; ?>>QQ空间</label>
								<label class="checkbox-inline"><input type="checkbox" value="wx" name="share_type[]" <?php if(in_array(($share_wx), is_array($config['share_type'])?$config['share_type']:explode(',',$config['share_type']))): ?>checked="checked"<?php endif; ?>>微信</label>
								<label class="checkbox-inline"><input type="checkbox" value="wchat" name="share_type[]" <?php if(in_array(($share_wchat), is_array($config['share_type'])?$config['share_type']:explode(',',$config['share_type']))): ?>checked="checked"<?php endif; ?>>微信朋友圈</label>
								
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="input-sendcode_switch" class="col-sm-2 control-label">验证码开关</label>
                            <div class="col-md-6 col-sm-10">
                                <select class="form-control" name="options[sendcode_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['sendcode_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                                验证码开关,关闭后不再发送真实验证码，采用默认验证码123456
                            </div>
                        </div>
                        
                        
						<div class="form-group">
                            <label for="input-code_switch" class="col-sm-2 control-label">短信接口平台</label>
                            <div class="col-md-6 col-sm-10" id="cdn">
                                <label class="radio-inline"><input type="radio" value="1" name="options[code_switch]" <?php if(in_array(($config['code_switch']), explode(',',"1"))): ?>checked="checked"<?php endif; ?>>阿里云</label>
                                <label class="radio-inline"><input type="radio" value="2" name="options[code_switch]" <?php if(in_array(($config['code_switch']), explode(',',"2"))): ?>checked="checked"<?php endif; ?>>容联云</label>
                               
                            </div>
                        </div>

                        <div class="cdn_bd <?php if($config['code_switch'] != '1'): ?>cdnhide<?php endif; ?>" id="code_switch_1">
                            <div class="form-group">
								<label for="input-aly_keydi" class="col-sm-2 control-label">阿里云AccessKey ID</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-aly_keydi" name="options[aly_keydi]" value="<?php echo (isset($config['aly_keydi']) && ($config['aly_keydi'] !== '')?$config['aly_keydi']:''); ?>">  阿里云控制台==》云通信-》短信服务==》 AccessKey ID
								</div>
							</div>

							<div class="form-group">							
								<label for="input-aly_secret" class="col-sm-2 control-label">阿里云AccessKey Secret</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-aly_secret" name="options[aly_secret]" value="<?php echo (isset($config['aly_secret']) && ($config['aly_secret'] !== '')?$config['aly_secret']:''); ?>">  阿里云控制台==》云通信-》短信服务==》 AccessKey Secret
								</div>
							</div>

							<div class="form-group">
								<label for="input-aly_signName" class="col-sm-2 control-label">阿里云短信签名</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-aly_signName" name="options[aly_signName]" value="<?php echo (isset($config['aly_signName']) && ($config['aly_signName'] !== '')?$config['aly_signName']:''); ?>">  阿里云控制台==》云通信-》短信服务==》 短信签名
								</div>
							</div>

							<div class="form-group">							
								<label for="input-aly_templateCode" class="col-sm-2 control-label">阿里云短信模板ID</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-aly_templateCode" name="options[aly_templateCode]" value="<?php echo (isset($config['aly_templateCode']) && ($config['aly_templateCode'] !== '')?$config['aly_templateCode']:''); ?>">  阿里云控制台==》云通信-》短信服务==》 短信模板ID 
								</div>
							</div>
                        
                        </div>
                        <div class="cdn_bd <?php if($config['code_switch'] != '2'): ?>cdnhide<?php endif; ?>" id="code_switch_2">
							<div class="form-group">
								<label for="input-ccp_sid" class="col-sm-2 control-label">容联云ACCOUNT SID</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-ccp_sid" name="options[ccp_sid]" value="<?php echo (isset($config['ccp_sid']) && ($config['ccp_sid'] !== '')?$config['ccp_sid']:''); ?>">  容联云ACCOUNT SID 
								</div>
							</div>
							<div class="form-group">
								<label for="input-ccp_token" class="col-sm-2 control-label">容联云AUTH TOKEN</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-ccp_token" name="options[ccp_token]" value="<?php echo (isset($config['ccp_token']) && ($config['ccp_token'] !== '')?$config['ccp_token']:''); ?>">  容联云AUTH TOKEN 
								</div>
							</div>
							<div class="form-group">
								<label for="input-ccp_appid" class="col-sm-2 control-label">容联云应用APPID</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-ccp_appid" name="options[ccp_appid]" value="<?php echo (isset($config['ccp_appid']) && ($config['ccp_appid'] !== '')?$config['ccp_appid']:''); ?>">  容联云应用APPID 
								</div>
							</div>
							<div class="form-group">
								<label for="input-ccp_tempid" class="col-sm-2 control-label">容联云短信模板ID</label>
								<div class="col-md-6 col-sm-10">
									<input type="text" class="form-control" id="input-ccp_tempid" name="options[ccp_tempid]" value="<?php echo (isset($config['ccp_tempid']) && ($config['ccp_tempid'] !== '')?$config['ccp_tempid']:''); ?>">  容联云短信模板ID 
								</div>
							</div>
							
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="1">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="B">
                        <div class="form-group">
                            <label for="input-aliapp_switch" class="col-sm-2 control-label">APP-支付宝开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[aliapp_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['aliapp_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-aliapp_partner" class="col-sm-2 control-label">支付宝合作者身份ID</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-aliapp_partner" name="options[aliapp_partner]" value="<?php echo (isset($config['aliapp_partner']) && ($config['aliapp_partner'] !== '')?$config['aliapp_partner']:''); ?>"> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-aliapp_seller_id" class="col-sm-2 control-label">支付宝帐号</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-aliapp_seller_id" name="options[aliapp_seller_id]" value="<?php echo (isset($config['aliapp_seller_id']) && ($config['aliapp_seller_id'] !== '')?$config['aliapp_seller_id']:''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-aliapp_key" class="col-sm-2 control-label">支付宝开发者密钥</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-aliapp_key" name="options[aliapp_key]" ><?php echo (isset($config['aliapp_key']) && ($config['aliapp_key'] !== '')?$config['aliapp_key']:''); ?></textarea> 密钥使用PKCS8版本
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-wx_switch" class="col-sm-2 control-label">APP-微信开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[wx_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['wx_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-wx_appid" class="col-sm-2 control-label">微信开放平台移动应用AppID</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-wx_appid" name="options[wx_appid]" value="<?php echo (isset($config['wx_appid']) && ($config['wx_appid'] !== '')?$config['wx_appid']:''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-wx_appsecret" class="col-sm-2 control-label">微信开放平台移动应用appsecret</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-wx_appsecret" name="options[wx_appsecret]" value="<?php echo (isset($config['wx_appsecret']) && ($config['wx_appsecret'] !== '')?$config['wx_appsecret']:''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-wx_mchid" class="col-sm-2 control-label">微信商户号mchid</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-wx_mchid" name="options[wx_mchid]" value="<?php echo (isset($config['wx_mchid']) && ($config['wx_mchid'] !== '')?$config['wx_mchid']:''); ?>"> 微信商户号mchid（微信开放平台移动应用 对应的微信商户 商户号mchid）
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-wx_key" class="col-sm-2 control-label">微信密钥key</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-wx_key" name="options[wx_key]" value="<?php echo (isset($config['wx_key']) && ($config['wx_key'] !== '')?$config['wx_key']:''); ?>"> 微信密钥key（微信开放平台移动应用 对应的微信商户 密钥key）
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-google_switch" class="col-sm-2 control-label">APP-Google开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[google_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['google_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-google_key" class="col-sm-2 control-label">APP-Google公钥</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-google_key" name="options[google_key]" ><?php echo (isset($config['google_key']) && ($config['google_key'] !== '')?$config['google_key']:''); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-ios_switch" class="col-sm-2 control-label">APP-苹果开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[ios_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['ios_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- <div class="form-group">
                            <label for="input-ios_sandbox" class="col-sm-2 control-label">APP-苹果支付模式</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[ios_sandbox]">
                                    <option value="0">沙盒</option>
                                    <option value="1" <?php if($config['ios_sandbox'] == '1'): ?>selected<?php endif; ?>>生产</option>
                                </select> IOS上架成功后，需要把支付模式改为 生产
                            </div>
                        </div> -->

                        
                        
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="C">
                        <div class="form-group" style="display:none;">
                            <label for="input-cash_rate" class="col-sm-2 control-label">提现比例</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-cash_rate" name="options[cash_rate]" value="<?php echo (isset($config['cash_rate']) && ($config['cash_rate'] !== '')?$config['cash_rate']:''); ?>"> 提现一元人民币需要的票数
                            </div>
                        </div>
						<div class="form-group">
                            <label for="input-system_rate" class="col-sm-2 control-label">礼物收益：提现比例（%）</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-system_rate" name="options[system_rate]" value="<?php echo (isset($config['system_rate']) && ($config['system_rate'] !== '')?$config['system_rate']:''); ?>"> 礼物收益：提现票数后台抽成比例(单位：%)
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-cash_min" class="col-sm-2 control-label">提现最低额度（元）</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-cash_min" name="options[cash_min]" value="<?php echo (isset($config['cash_min']) && ($config['cash_min'] !== '')?$config['cash_min']:''); ?>"> 可提现的最小额度，低于该额度无法提现
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-cash_start" class="col-sm-2 control-label">每月提现期</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-cash_start" name="options[cash_start]" value="<?php echo (isset($config['cash_start']) && ($config['cash_start'] !== '')?$config['cash_start']:''); ?>" style="width:100px;display: inline-block;">
                                -
                                <input type="text" class="form-control" id="input-cash_end" name="options[cash_end]" value="<?php echo (isset($config['cash_end']) && ($config['cash_end'] !== '')?$config['cash_end']:''); ?>" style="width:100px;display: inline-block;"> 每月提现期限（日），不在时间段无法提现  例：10-15
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-cash_max_times" class="col-sm-2 control-label">每月提现次数</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-cash_max_times" name="options[cash_max_times]" value="<?php echo (isset($config['cash_max_times']) && ($config['cash_max_times'] !== '')?$config['cash_max_times']:'0'); ?>"> 每月可提现最大次数，0表示不限制
                            </div>
                        </div>
                        
                        <!-- <div class="form-group">
                            <label for="input-cash_tip" class="col-sm-2 control-label">温馨提示</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-cash_tip" name="options[cash_tip]" value="<?php echo (isset($config['cash_tip']) && ($config['cash_tip'] !== '')?$config['cash_tip']:''); ?>" maxlength='50'> 提现页面下部提示,最多50字
                            </div>
                        </div> -->
                        
                        
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="D">
                        <div class="form-group">
                            <label for="input-im_sdkappid" class="col-sm-2 control-label">腾讯云通信SdkAppId</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-im_sdkappid" name="options[im_sdkappid]" value="<?php echo (isset($config['im_sdkappid']) && ($config['im_sdkappid'] !== '')?$config['im_sdkappid']:''); ?>"> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-im_key" class="col-sm-2 control-label">腾讯云通信密钥</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-im_key" name="options[im_key]" value="<?php echo (isset($config['im_key']) && ($config['im_key'] !== '')?$config['im_key']:''); ?>">  HMAC-SHA256
                            </div>
                        </div>
                        

                        <div class="form-group">
                            <label for="input-im_admin" class="col-sm-2 control-label">腾讯云通信账号管理员</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-im_admin" name="options[im_admin]" value="<?php echo (isset($config['im_admin']) && ($config['im_admin'] !== '')?$config['im_admin']:''); ?>"> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-im_admin_drip" class="col-sm-2 control-label">腾讯云通信账号管理员--滴滴订单</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-im_admin_drip" name="options[im_admin_drip]" value="<?php echo (isset($config['im_admin_drip']) && ($config['im_admin_drip'] !== '')?$config['im_admin_drip']:''); ?>"> 用于发送滴滴订单信息
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-im_admin_dispatch" class="col-sm-2 control-label">腾讯云通信账号管理员--派单</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-im_admin_dispatch" name="options[im_admin_dispatch]" value="<?php echo (isset($config['im_admin_dispatch']) && ($config['im_admin_dispatch'] !== '')?$config['im_admin_dispatch']:''); ?>"> 用于发送派单信息
                            </div>
                        </div>
						<div class="form-group">
                            <label for="input-im_admin_upservice" class="col-sm-2 control-label">腾讯云通信账号管理员--立即服务状态</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-im_admin_upservice" name="options[im_admin_upservice]" value="<?php echo (isset($config['im_admin_upservice']) && ($config['im_admin_upservice'] !== '')?$config['im_admin_upservice']:''); ?>"> 用于发送派单信息
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="E">
                        <div class="form-group">
                            <label for="input-skill_recom_star" class="col-sm-2 control-label">星级比重</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-skill_recom_star" name="options[skill_recom_star]" value="<?php echo (isset($config['skill_recom_star']) && ($config['skill_recom_star'] !== '')?$config['skill_recom_star']:''); ?>">  最多4位小数
                            </div>
                        </div>
                        

                        <div class="form-group">
                            <label for="input-skill_recom_orders" class="col-sm-2 control-label">订单数比重</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-skill_recom_orders" name="options[skill_recom_orders]" value="<?php echo (isset($config['skill_recom_orders']) && ($config['skill_recom_orders'] !== '')?$config['skill_recom_orders']:''); ?>"> 最多4位小数
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-skill_recom_coin" class="col-sm-2 control-label">技能价格比重</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-skill_recom_coin" name="options[skill_recom_coin]" value="<?php echo (isset($config['skill_recom_coin']) && ($config['skill_recom_coin'] !== '')?$config['skill_recom_coin']:''); ?>">  最多4位小数
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="F">
                        <div class="form-group">
                            <label for="input-dynamic_switch" class="col-sm-2 control-label">审核开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[dynamic_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['dynamic_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-dynamic_recom_com" class="col-sm-2 control-label">评论权重</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-dynamic_recom_com" name="options[dynamic_recom_com]" value="<?php echo (isset($config['dynamic_recom_com']) && ($config['dynamic_recom_com'] !== '')?$config['dynamic_recom_com']:'0'); ?>"> 
                                最多4位小数
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-dynamic_recom_like" class="col-sm-2 control-label">点赞权重</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-dynamic_recom_like" name="options[dynamic_recom_like]" value="<?php echo (isset($config['dynamic_recom_like']) && ($config['dynamic_recom_like'] !== '')?$config['dynamic_recom_like']:'0'); ?>"> 最多4位小数
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="G">
                        <div class="form-group">
                            <label for="input-agent_switch" class="col-sm-2 control-label">邀请开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[agent_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['agent_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-agent_must" class="col-sm-2 control-label">邀请码必填</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[agent_must]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['agent_must'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select> 开启后,必须填写邀请码才能进入APP
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-agent_one" class="col-sm-2 control-label">一级分成</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-agent_one" name="options[agent_one]" value="<?php echo (isset($config['agent_one']) && ($config['agent_one'] !== '')?$config['agent_one']:'0'); ?>" > %  支持 0.01%  比例不能大于40%
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-agent_reward" class="col-sm-2 control-label">每邀请一个新人的奖励</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-agent_reward" name="options[agent_reward]" value="<?php echo (isset($config['agent_reward']) && ($config['agent_reward'] !== '')?$config['agent_reward']:'0'); ?>" > <?php echo $configpub['name_votes']; ?> 支持 0.01
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-agent_skill_one" class="col-sm-2 control-label">第一个技能认证成功-一级奖励</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-agent_skill_one" name="options[agent_skill_one]" value="<?php echo (isset($config['agent_skill_one']) && ($config['agent_skill_one'] !== '')?$config['agent_skill_one']:'0'); ?>"> <?php echo $configpub['name_votes']; ?>  支持 0.01
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="H">
                        <!-- <div class="form-group">
                            <label for="input-drip_switch" class="col-sm-2 control-label">短信验证码IP限制开关</label>
                            <div class="col-md-6 col-sm-10">
								<select class="form-control" name="options[drip_switch]">
                                    <option value="0">关闭</option>
                                    <option value="1" <?php if($config['drip_switch'] == '1'): ?>selected<?php endif; ?>>开启</option>
                                </select>
                            </div>
                        </div> -->
                        
                        <!-- <div class="form-group">
                            <label for="input-drip_times" class="col-sm-2 control-label">滴滴订单：取消限制次数</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-drip_times" name="options[drip_times]" value="<?php echo (isset($config['drip_times']) && ($config['drip_times'] !== '')?$config['drip_times']:''); ?>"> 每天可取消滴滴订单的次数 0不限制
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label for="input-order_times" class="col-sm-2 control-label">订单：取消限制次数</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-order_times" name="options[order_times]" value="<?php echo (isset($config['order_times']) && ($config['order_times'] !== '')?$config['order_times']:''); ?>"> 每天可取消订单的次数 0不限制
                            </div>
                        </div>
						
						<div class="form-group">
                            <label for="input-ban_orderlong" class="col-sm-2 control-label">禁止下单时长（小时）</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-ban_orderlong" name="options[ban_orderlong]" value="<?php echo (isset($config['ban_orderlong']) && ($config['ban_orderlong'] !== '')?$config['ban_orderlong']:''); ?>" onkeyup ="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" > 取消（包括普通订单、滴滴订单）订单次数超过限制，禁止设置时长内不能下单(正整数);0不限制
                            </div>
                        </div>
						<div class="form-group">
                            <label for="input-sys_oklong" class="col-sm-2 control-label">确认订单时长（小时）</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-sys_oklong" name="options[sys_oklong]" value="<?php echo (isset($config['sys_oklong']) && ($config['sys_oklong'] !== '')?$config['sys_oklong']:''); ?>" onkeyup ="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" > 陪玩后设置的时长（小时）后，没有手动确认订单的系统自动确认订单(正整数);0不自动完成订单
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="I">
						<div class="form-group">
                            <label for="input-liveapply_tips" class="col-sm-2 control-label">聊天室申请公告</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-liveapply_tips" name="options[liveapply_tips]" ><?php echo (isset($config['liveapply_tips']) && ($config['liveapply_tips'] !== '')?$config['liveapply_tips']:''); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-chatserver" class="col-sm-2 control-label">聊天服务器带端口</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-chatserver" name="options[chatserver]" value="<?php echo (isset($config['chatserver']) && ($config['chatserver'] !== '')?$config['chatserver']:''); ?>"> 格式：http://域名(:端口) 或者 http://IP(:端口)
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-trtc_appid" class="col-sm-2 control-label">腾讯实时音视频SDKAppID</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-trtc_appid" name="options[trtc_appid]" value="<?php echo (isset($config['trtc_appid']) && ($config['trtc_appid'] !== '')?$config['trtc_appid']:''); ?>"> 
                            </div>
                        </div>
						
						<div class="form-group">
                            <label for="input-tx_appid" class="col-sm-2 control-label">腾讯直播appid</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-tx_appid" name="options[tx_appid]" value="<?php echo (isset($config['tx_appid']) && ($config['tx_appid'] !== '')?$config['tx_appid']:''); ?>"> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-tx_bizid" class="col-sm-2 control-label">腾讯直播bizid</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-tx_bizid" name="options[tx_bizid]" value="<?php echo (isset($config['tx_bizid']) && ($config['tx_bizid'] !== '')?$config['tx_bizid']:''); ?>"> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-tx_api_key" class="col-sm-2 control-label">腾讯直播API鉴权key</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-tx_api_key" name="options[tx_api_key]" value="<?php echo (isset($config['tx_api_key']) && ($config['tx_api_key'] !== '')?$config['tx_api_key']:''); ?>"> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-tx_pull" class="col-sm-2 control-label">腾讯直播播流域名</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-tx_pull" name="options[tx_pull]" value="<?php echo (isset($config['tx_pull']) && ($config['tx_pull'] !== '')?$config['tx_pull']:''); ?>"> 不带 http:// ,最后无 /
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary js-ajax-submit" data-refresh="0">
                                    <?php echo lang('SAVE'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
    
                </div>
            </div>
        </fieldset>
    </form>

</div>
<script type="text/javascript" src="/static/js/admin.js"></script>
<script>
(function(){

	
    $("#cdn label").on('click',function(){
        var v_d=$("input",this).attr('disabled');
        if(v_d=='disabled'){
            return !1;
        }
        var v=$("input",this).val();
        var b=$("#code_switch_"+v);
        $(".cdn_bd").hide();
        b.show();
    })
    
})()
</script>
</body>
</html>
