<?php /*a:2:{s:88:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/admin\setting\site.html";i:1603704624;s:83:"D:\phpstudy_pro\WWW\yuejie.yunbaozb.com/themes/admin_simpleboot3/public\header.html";i:1604631154;}*/ ?>
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


    <link href="/public/themes/admin_simpleboot3/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="/public/themes/admin_simpleboot3/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
            ROOT: "/public/",
            WEB_ROOT: "/public/",
            JS_ROOT: "static/js/",
            APP: '<?php echo app('request')->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/public/themes/admin_simpleboot3/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="/public/static/js/wind.js"></script>
    <script src="/public/themes/admin_simpleboot3/public/assets/js/bootstrap.min.js"></script>
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
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#A" data-toggle="tab"><?php echo lang('WEB_SITE_INFOS'); ?></a></li>
        <li><a href="#B" data-toggle="tab"><?php echo lang('SEO_SETTING'); ?></a></li>
        <li><a href="#C" data-toggle="tab">APP版本管理</a></li>
        <li><a href="#D" data-toggle="tab">分享管理</a></li>
        <!--<li><a href="#C" data-toggle="tab"><?php echo lang('URL_SETTING'); ?></a></li>-->
        <!--<li><a href="#E" data-toggle="tab"><?php echo lang('COMMENT_SETTING'); ?></a></li>-->
        <!-- <li><a href="#F" data-toggle="tab">用户注册设置</a></li> -->
        <!-- <li><a href="#G" data-toggle="tab">CDN设置</a></li> -->
		<li><a href="#H" data-toggle="tab">登录协议弹窗</a></li>
    </ul>
    <form class="form-horizontal js-ajax-form margin-top-20" role="form" action="<?php echo url('setting/sitePost'); ?>"
          method="post">
        <fieldset>
            <div class="tabbable">
                <div class="tab-content">
                    <div class="tab-pane active" id="A">
                        <div class="form-group">
                            <label for="input-site-name" class="col-sm-2 control-label"><span
                                    class="form-required">*</span><?php echo lang('WEBSITE_NAME'); ?></label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-site-name" name="options[site_name]"
                                       value="<?php echo (isset($site_info['site_name']) && ($site_info['site_name'] !== '')?$site_info['site_name']:''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-site_url" class="col-sm-2 control-label"><span
                                    class="form-required">*</span>站点域名</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-site_url" name="options[site_url]" value="<?php echo (isset($site_info['site_url']) && ($site_info['site_url'] !== '')?$site_info['site_url']:''); ?>">
                                格式： http(s)://xxxx.com(:端口号)
                            </div> 
                        </div>
                        <div class="form-group" style="display:none;">
                            <label for="input-admin_url_password" class="col-sm-2 control-label">
                                后台加密码
                                <a href="http://www.thinkcmf.com/faq.html?url=https://www.kancloud.cn/thinkcmf/faq/493509"
                                   title="查看帮助手册"
                                   data-toggle="tooltip"
                                   target="_blank"><i class="fa fa-question-circle"></i></a>
                            </label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-admin_url_password"
                                       name="admin_settings[admin_password]"
                                       value="<?php echo (isset($admin_settings['admin_password']) && ($admin_settings['admin_password'] !== '')?$admin_settings['admin_password']:''); ?>"
                                       id="js-site-admin-url-password">
                                <p class="help-block">英文字母数字，不能为纯数字</p>
                                <p class="help-block" style="color: red;">
                                    设置加密码后必须通过以下地址访问后台,请劳记此地址，为了安全，您也可以定期更换此加密码!</p>
                                <?php 
                                    $root=cmf_get_root();
                                    $root=empty($root)?'':'/'.$root;
                                    $site_domain = cmf_get_domain().$root;
                                 ?>
                                <p class="help-block">后台登录地址：<span id="js-site-admin-url"><?php echo $site_domain; ?>/<?php echo (isset($admin_settings['admin_password']) && ($admin_settings['admin_password'] !== '')?$admin_settings['admin_password']:'admin'); ?></span>
                                </p>
                            </div>
                        </div>

                        <div class="form-group" style="display:none;">
                            <label for="input-site_admin_theme" class="col-sm-2 control-label">后台模板</label>
                            <div class="col-md-6 col-sm-10">
                                <?php 
                                    $site_admin_theme=empty($admin_settings['admin_theme'])?'':$admin_settings['admin_theme'];
                                 ?>
                                <select class="form-control" name="admin_settings[admin_theme]"
                                        id="input-site_admin_theme">
                                    <?php if(is_array($admin_themes) || $admin_themes instanceof \think\Collection || $admin_themes instanceof \think\Paginator): if( count($admin_themes)==0 ) : echo "" ;else: foreach($admin_themes as $key=>$vo): $admin_theme_selected = $site_admin_theme == $vo ? "selected" : ""; ?>
                                        <option value="<?php echo $vo; ?>" <?php echo $admin_theme_selected; ?>><?php echo $vo; ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="display:none;">
                            <label for="input-site_adminstyle" class="col-sm-2 control-label"><?php echo lang('WEBSITE_ADMIN_THEME'); ?></label>
                            <div class="col-md-6 col-sm-10">
                                <?php 
                                    $site_admin_style=empty($admin_settings['admin_style'])?cmf_get_admin_style():$admin_settings['admin_style'];
                                 ?>
                                <select class="form-control" name="admin_settings[admin_style]"
                                        id="input-site_adminstyle">
                                    <?php if(is_array($admin_styles) || $admin_styles instanceof \think\Collection || $admin_styles instanceof \think\Paginator): if( count($admin_styles)==0 ) : echo "" ;else: foreach($admin_styles as $key=>$vo): $admin_style_selected = $site_admin_style == $vo ? "selected" : ""; ?>
                                        <option value="<?php echo $vo; ?>" <?php echo $admin_style_selected; ?>><?php echo $vo; ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php if(APP_DEBUG && false): ?>
                            <div class="form-group">
                                <label for="input-default_app" class="col-sm-2 control-label">默认应用</label>
                                <div class="col-md-6 col-sm-10">
                                    <?php 
                                        $site_default_app=empty($cmf_settings['default_app'])?'demo':$cmf_settings['default_app'];
                                     ?>
                                    <select class="form-control" name="cmf_settings[default_app]"
                                            id="input-default_app">
                                        <?php if(is_array($apps) || $apps instanceof \think\Collection || $apps instanceof \think\Paginator): if( count($apps)==0 ) : echo "" ;else: foreach($apps as $key=>$vo): $default_app_selected = $site_default_app == $vo ? "selected" : "";
                                             ?>
                                            <option value="<?php echo $vo; ?>" <?php echo $default_app_selected; ?>><?php echo $vo; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="input-name_coin" class="col-sm-2 control-label"><span
                                    class="form-required">*</span>虚拟币</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-name_coin" name="options[name_coin]" value="<?php echo (isset($site_info['name_coin']) && ($site_info['name_coin'] !== '')?$site_info['name_coin']:''); ?>">
                            </div> 
                        </div>
                        <div class="form-group"  style="display:none;">
                            <label for="input-name_coin_en" class="col-sm-2 control-label"><span
                                    class="form-required">*</span>虚拟币-英文</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-name_coin_en" name="options[name_coin_en]" value="<?php echo (isset($site_info['name_coin_en']) && ($site_info['name_coin_en'] !== '')?$site_info['name_coin_en']:''); ?>">
                            </div> 
                        </div>
                        
                        <div class="form-group">
                            <label for="input-name_votes" class="col-sm-2 control-label"><span
                                    class="form-required">*</span>虚拟收益币</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-name_votes" name="options[name_votes]" value="<?php echo (isset($site_info['name_votes']) && ($site_info['name_votes'] !== '')?$site_info['name_votes']:''); ?>">
                            </div> 
                        </div>
                        
                        <div class="form-group">
                            <label for="input-copyright" class="col-sm-2 control-label">版权信息</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-copyright" name="options[copyright]" maxlength="200"><?php echo (isset($site_info['copyright']) && ($site_info['copyright'] !== '')?$site_info['copyright']:''); ?></textarea> 版权信息（200字以内）
                            </div>
                        </div>
						
						<div class="form-group">
                            <label for="input-company_name" class="col-sm-2 control-label"><span
                                    class="form-required">*</span>公司名称</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-company_name" name="options[company_name]" value="<?php echo (isset($site_info['company_name']) && ($site_info['company_name'] !== '')?$site_info['company_name']:''); ?>">公司名称(网站首页关于我们使用)
                            </div> 
                        </div>
						<div class="form-group">
                            <label for="input-company_desc" class="col-sm-2 control-label">公司简介</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-company_desc" name="options[company_desc]" maxlength="200"><?php echo (isset($site_info['company_desc']) && ($site_info['company_desc'] !== '')?$site_info['company_desc']:''); ?></textarea> 公司简介（网站首页关于我们使用,字数在120字以内）
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
                            <label for="input-site_seo_title" class="col-sm-2 control-label"><?php echo lang('WEBSITE_SEO_TITLE'); ?></label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-site_seo_title"
                                       name="options[site_seo_title]" value="<?php echo (isset($site_info['site_seo_title']) && ($site_info['site_seo_title'] !== '')?$site_info['site_seo_title']:''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-site_seo_keywords" class="col-sm-2 control-label"><?php echo lang('WEBSITE_SEO_KEYWORDS'); ?></label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-site_seo_keywords"
                                       name="options[site_seo_keywords]"
                                       value="<?php echo (isset($site_info['site_seo_keywords']) && ($site_info['site_seo_keywords'] !== '')?$site_info['site_seo_keywords']:''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-site_seo_description" class="col-sm-2 control-label"><?php echo lang('WEBSITE_SEO_DESCRIPTION'); ?></label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-site_seo_description"
                                          name="options[site_seo_description]"><?php echo (isset($site_info['site_seo_description']) && ($site_info['site_seo_description'] !== '')?$site_info['site_seo_description']:''); ?></textarea>
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
                    <div class="tab-pane" id="C">
                        <div class="form-group">
                            <label for="input-apk_ver" class="col-sm-2 control-label">APK版本号</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-apk_ver" name="options[apk_ver]" value="<?php echo (isset($site_info['apk_ver']) && ($site_info['apk_ver'] !== '')?$site_info['apk_ver']:''); ?>"> 安卓APP版本号，请勿随意修改
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-apk_url" class="col-sm-2 control-label">APK下载链接</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-apk_url" name="options[apk_url]" value="<?php echo (isset($site_info['apk_url']) && ($site_info['apk_url'] !== '')?$site_info['apk_url']:''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-apk_des" class="col-sm-2 control-label">APK更新说明</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-apk_des" name="options[apk_des]" maxlength="100"><?php echo (isset($site_info['apk_des']) && ($site_info['apk_des'] !== '')?$site_info['apk_des']:''); ?></textarea> 100字以内
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-ipa_ver" class="col-sm-2 control-label">IPA版本号</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-ipa_ver" name="options[ipa_ver]" value="<?php echo (isset($site_info['ipa_ver']) && ($site_info['ipa_ver'] !== '')?$site_info['ipa_ver']:''); ?>"> IOS APP版本号，请勿随意修改
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-ios_shelves" class="col-sm-2 control-label">IPA上架版本号</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-ios_shelves" name="options[ios_shelves]" value="<?php echo (isset($site_info['ios_shelves']) && ($site_info['ios_shelves'] !== '')?$site_info['ios_shelves']:''); ?>"> IOS上架审核中版本的版本号(用于上架期间隐藏上架版本部分功能,不要和IPA版本号相同),上架成功后要修改
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-ipa_url" class="col-sm-2 control-label">IPA下载链接</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-ipa_url" name="options[ipa_url]" value="<?php echo (isset($site_info['ipa_url']) && ($site_info['ipa_url'] !== '')?$site_info['ipa_url']:''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-ipa_des" class="col-sm-2 control-label">IPA更新说明</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-ipa_des" name="options[ipa_des]" maxlength="100"><?php echo (isset($site_info['ipa_des']) && ($site_info['ipa_des'] !== '')?$site_info['ipa_des']:''); ?></textarea> 100字以内
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-qr_url" class="col-sm-2 control-label">二维码</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="hidden" name="options[qr_url]" id="thumbnail" value="<?php echo $site_info['qr_url']; ?>">
                                <a href="javascript:uploadOneImage('图片上传','#thumbnail');">
                                    <?php if(empty($site_info['qr_url'])): ?>
                                    <img src="/public/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png"
                                             id="thumbnail-preview"
                                             style="cursor: pointer;max-width:150px;max-height:150px;"/>
                                    <?php else: ?>
                                    <img src="<?php echo cmf_get_image_preview_url($site_info['qr_url']); ?>"
                                         id="thumbnail-preview"
                                         style="cursor: pointer;max-width:150px;max-height:150px;"/>
                                    <?php endif; ?>
                                </a>
                                <input type="button" class="btn btn-sm btn-cancel-thumbnail" onclick="$('#thumbnail-preview').attr('src','/public/themes/admin_simpleboot3/public/assets/images/default-thumbnail.png');$('#thumbnail').val('');return false;" value="取消图片"> 首页使用(APP共用下载二维码) 建议尺寸  200 X 200
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
                    
                    <div class="tab-pane" id="D">
                        <div class="form-group">
                            <label for="input-share_agent_title" class="col-sm-2 control-label">全民赚钱分享标题</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-share_agent_title"
                                       name="options[share_agent_title]" value="<?php echo (isset($site_info['share_agent_title']) && ($site_info['share_agent_title'] !== '')?$site_info['share_agent_title']:''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-share_agent_des" class="col-sm-2 control-label">全民赚钱分享话术</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-share_agent_des"
                                       name="options[share_agent_des]"
                                       value="<?php echo (isset($site_info['share_agent_des']) && ($site_info['share_agent_des'] !== '')?$site_info['share_agent_des']:''); ?>">
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
                            <label for="input-banned_usernames" class="col-sm-2 control-label">用户注册验证</label>
                            <div class="col-md-6 col-sm-10">
                                <select class="form-control" name="cmf_settings[open_registration]">
                                    <option value="0">是</option>
                                    <?php 
                                        $open_registration_selected = empty($cmf_settings['open_registration'])?'':'selected';
                                     ?>
                                    <option value="1" <?php echo $open_registration_selected; ?>>否</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label for="input-banned_usernames" class="col-sm-2 control-label"><?php echo lang('SPECAIL_USERNAME'); ?></label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-banned_usernames"
                                          name="cmf_settings[banned_usernames]"><?php echo $cmf_settings['banned_usernames']; ?></textarea>
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
                            <label for="input-cdn_static_root" class="col-sm-2 control-label">静态资源cdn地址</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-cdn_static_root"
                                       name="cdn_settings[cdn_static_root]"
                                       value="<?php echo (isset($cdn_settings['cdn_static_root']) && ($cdn_settings['cdn_static_root'] !== '')?$cdn_settings['cdn_static_root']:''); ?>">
                                <p class="help-block">
                                    不能以/结尾；设置这个地址后，请将ThinkCMF下的静态资源文件放在其下面；<br>
                                    ThinkCMF下的静态资源文件大致包含以下(如果你自定义后，请自行增加)：<br>
                                    themes/admin_simplebootx/public/assets<br>
                                    static<br>
                                    themes/simplebootx/public/assets<br>
                                    例如未设置cdn前：jquery的访问地址是/static/js/jquery.js, <br>
                                    设置cdn是后它的访问地址就是：静态资源cdn地址/static/js/jquery.js
                                </p>
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
                        

                        <div class="form-group">
                            <label for="input-login_alert_title" class="col-sm-2 control-label"><span
                                    class="form-required"></span>弹框标题</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-login_alert_title" name="options[login_alert_title]" value="<?php echo (isset($site_info['login_alert_title']) && ($site_info['login_alert_title'] !== '')?$site_info['login_alert_title']:''); ?>">
                            </div> 
                        </div>
                        
                        <div class="form-group">
                            <label for="input-login_alert_content" class="col-sm-2 control-label">弹框内容</label>
                            <div class="col-md-6 col-sm-10">
                                <textarea class="form-control" id="input-login_alert_content" name="options[login_alert_content]" ><?php echo (isset($site_info['login_alert_content']) && ($site_info['login_alert_content'] !== '')?$site_info['login_alert_content']:''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="input-login_clause_title" class="col-sm-2 control-label"><span
                                    class="form-required"></span>APP登录界面底部协议标题</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-login_clause_title" name="options[login_clause_title]" value="<?php echo (isset($site_info['login_clause_title']) && ($site_info['login_clause_title'] !== '')?$site_info['login_clause_title']:''); ?>">
                            </div> 
                        </div>

                        <div class="form-group">
                            <label for="input-login_private_title" class="col-sm-2 control-label"><span
                                    class="form-required"></span>隐私政策名称</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-login_private_title" name="options[login_private_title]" value="<?php echo (isset($site_info['login_private_title']) && ($site_info['login_private_title'] !== '')?$site_info['login_private_title']:''); ?>">
                                <p class="help-block">填写的名称必须与弹框内容和登录界面底部协议标题中填写的名称相符,必须包含书名号《》</p>
                            </div> 
                        </div>

                        <div class="form-group">
                            <label for="input-login_private_url" class="col-sm-2 control-label"><span
                                    class="form-required"></span>隐私政策跳转链接</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-login_private_url" name="options[login_private_url]" value="<?php echo (isset($site_info['login_private_url']) && ($site_info['login_private_url'] !== '')?$site_info['login_private_url']:''); ?>">
                                <p class="help-block">本站链接请以/开头，如：/portal/page/index?id=3 外链请以http://或https://开头</p>
                            </div> 
                        </div>

                        <div class="form-group">
                            <label for="input-login_service_title" class="col-sm-2 control-label"><span
                                    class="form-required"></span>服务协议名称</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-login_service_title" name="options[login_service_title]" value="<?php echo (isset($site_info['login_service_title']) && ($site_info['login_service_title'] !== '')?$site_info['login_service_title']:''); ?>">
                                <p class="help-block">填写的名称必须与弹框内容和登录界面底部协议标题中填写的名称相符,必须包含书名号《》</p>
                            </div> 


                        </div>

                        <div class="form-group">
                            <label for="input-login_service_url" class="col-sm-2 control-label"><span
                                    class="form-required"></span>服务协议跳转链接</label>
                            <div class="col-md-6 col-sm-10">
                                <input type="text" class="form-control" id="input-login_service_url" name="options[login_service_url]" value="<?php echo (isset($site_info['login_service_url']) && ($site_info['login_service_url'] !== '')?$site_info['login_service_url']:''); ?>">
                                <p class="help-block">本站链接请以/开头，如：/portal/page/index?id=4 外链请以http://或https://开头</p>
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
                </div>
            </div>
        </fieldset>
    </form>

</div>
<script type="text/javascript" src="/public/static/js/admin.js"></script>
</body>
</html>
