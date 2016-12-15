<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title>后台管理 | 登录</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="/Public/media/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/media/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/media/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/media/css/style-metro.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/media/css/style.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/media/css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/media/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="/Public/media/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link href="/Public/media/css/login.css" rel="stylesheet" type="text/css"/>
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="/Public/media/image/favicon.ico" />
	<script language="JavaScript">
		<!--
		function fleshVerify(type){ 
			//重载验证码
			var timenow = new Date().getTime();
			if (type){
				$('#verifyImg').attr("src", 'verify/adv/1/'+timenow);
			}else{
				$('#verifyImg').attr("src", 'verify/'+timenow);
			}
		}
		//-->
	</script>
	<script>
	var _hmt = _hmt || [];
	(function() {
	  var hm = document.createElement("script");
	  hm.src = "//hm.baidu.com/hm.js?e6ea67101700040a01867aa382be912d";
	  var s = document.getElementsByTagName("script")[0]; 
	  s.parentNode.insertBefore(hm, s);
	})();
	</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
	<!-- BEGIN LOGO -->
	<div class="logo">
		<img src="/Public/image/logo2.png" alt="" /> 
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content">
		<!-- BEGIN LOGIN FORM -->
		<form class="form-vertical login-form" action="<?php echo U('Public/checkLogin');?>" method="post">
			<h3 class="form-title">登录到你的账户</h3>
			<div class="alert alert-error hide">
				<button class="close" data-dismiss="alert"></button>
				<span>请输入用户名和密码</span>
			</div>
			<div class="control-group">
				<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
				<label class="control-label visible-ie8 visible-ie9">用户名</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-user"></i>
						<input class="m-wrap placeholder-no-fix" type="text" placeholder="用户名" name="account" value="<?php echo ($saveUser["account"]); ?>" />
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">密码</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-lock"></i>
						<input class="m-wrap placeholder-no-fix" type="password" placeholder="密码" name="password" value="<?php echo ($saveUser["password"]); ?>"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">验证码</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-certificate"></i>
						<input name="verify" type="text" class="m-wrap placeholder-no-fix small" placeholder="验证码" />
					</div>
				</div>
				<div class="controls" style="margin-top:10px;">
					<img id="verifyImg" src="<?php echo U('verify');?>" onClick="fleshVerify()" border="0" alt="点击刷新验证码" style="cursor:pointer" align="absmiddle">
				</div>
			</div>
			<div class="form-actions">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="1" <?php if(($remember) == "1"): ?>checked="checked"<?php endif; ?>/> 记住我
				</label>
				<button type="submit" class="btn green pull-right">
				登录 <i class="m-icon-swapright m-icon-white"></i>
				</button>            
			</div>
			<div class="forget-password">
				<h4>忘记密码 ?</h4>
				<p>
					别担心, 点击 <a href="javascript:;" class="" id="forget-password">这里</a>
					重置你的密码.
				</p>
			</div>
			<div class="create-account">
				<p>
					还没有账号 ?&nbsp; 
					<a href="javascript:;" id="register-btn" class="">注册一个账号</a>
				</p>
			</div>
		</form>
		<!-- END LOGIN FORM -->        
		<!-- BEGIN FORGOT PASSWORD FORM -->
		<form class="form-vertical forget-form" action="">
			<h3 class="">忘记密码了 ?</h3>
			<p>请输入你的Email来重置你的密码</p>
			<div class="control-group">
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-envelope"></i>
						<input class="m-wrap placeholder-no-fix" type="text" placeholder="Email" name="email" />
					</div>
				</div>
			</div>
			<div class="form-actions">
				<button type="button" id="back-btn" class="btn">
				<i class="m-icon-swapleft"></i> 返回
				</button>
				<button type="submit" class="btn green pull-right">
				提交 <i class="m-icon-swapright m-icon-white"></i>
				</button>            
			</div>
		</form>
		<!-- END FORGOT PASSWORD FORM -->
		<!-- BEGIN REGISTRATION FORM -->
		<form class="form-vertical register-form" method="post" action="<?php echo U('Public/register');?>">
			<h3 class="">注册</h3>
			<p>填写你的账号详情:</p>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">用户名</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-user"></i>
						<input class="m-wrap placeholder-no-fix" type="text" placeholder="用户名" name="account"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">昵称</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-user"></i>
						<input class="m-wrap placeholder-no-fix" type="text" placeholder="昵称" name="nickname"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">密码</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-lock"></i>
						<input class="m-wrap placeholder-no-fix" type="password" id="register_password" placeholder="密码" name="password"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">再次输入密码</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-ok"></i>
						<input class="m-wrap placeholder-no-fix" type="password" placeholder="再次输入密码" name="rpassword"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
				<label class="control-label visible-ie8 visible-ie9">电子邮件</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-envelope"></i>
						<input class="m-wrap placeholder-no-fix" type="text" placeholder="电子邮件" name="email"/>
					</div>
				</div>
			</div>
			<!-- <div class="control-group">
				<div class="controls">
					<label class="checkbox">
					<input type="checkbox" name="tnc"/> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
					</label>  
					<div id="register_tnc_error"></div>
				</div>
			</div> -->
			<div class="form-actions">
				<button id="register-back-btn" type="button" class="btn">
				<i class="m-icon-swapleft"></i>  返回
				</button>
				<button type="submit" id="register-submit-btn" class="btn green pull-right">
				注册 <i class="m-icon-swapright m-icon-white"></i>
				</button>            
			</div>
		</form>
		<!-- END REGISTRATION FORM -->
	</div>
	<!-- END LOGIN -->
	<!-- BEGIN COPYRIGHT -->
	<div class="copyright">
		2016 &copy; backend.mcgoldfish.com version <?php echo (C("SITE_VERSION")); ?>
	</div>
	<!-- END COPYRIGHT -->
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
	<script src="/Public/media/js/jquery-1.10.1.min.js" type="text/javascript"></script>
	<script src="/Public/media/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="/Public/media/js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
	<script src="/Public/media/js/bootstrap.min.js" type="text/javascript"></script>
	<!--[if lt IE 9]>
	<script src="/Public/media/js/excanvas.min.js"></script>
	<script src="/Public/media/js/respond.min.js"></script>  
	<![endif]-->   
	<script src="/Public/media/js/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src="/Public/media/js/jquery.blockui.min.js" type="text/javascript"></script>  
	<script src="/Public/media/js/jquery.cookie.min.js" type="text/javascript"></script>
	<script src="/Public/media/js/jquery.uniform.min.js" type="text/javascript" ></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script src="/Public/media/js/jquery.validate.min.js" type="text/javascript"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="/Public/media/js/app.js" type="text/javascript"></script>
	<script src="/Public/media/js/login.js" type="text/javascript"></script>      
	<!-- END PAGE LEVEL SCRIPTS --> 
	<script>
		jQuery(document).ready(function() {     
		  App.init();
		  Login.init();
		  if($('input[name="account"]').val() != ''){
		  	$('input[name="verify"]').focus();
		  }else{
		  	$('input[name="account"]').focus();
		  }
		  
		});
	</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>