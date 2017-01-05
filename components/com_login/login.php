<!DOCTYPE html> 
<html>
<head>
	<title><?php echo $Config_sitename; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="Generator" content="MoGu! Content Management System" />
	<link rel="stylesheet" href="templates/flammup/css/style.css"/>
	<script src="<?php echo $Config_live_site; ?>/includes/js/jquery-1.8.2.min.js"></script>
</head>

<body>
<div align="center" id="dragon_area">
	<img src="<?php echo $Config_live_site; ?>/images/dragon.png" class="dragon"/>
</div>
<div id="loginform" name="loginform">
<form name='content' method='post' action="index.php" target="_self">
	<div class="login_area">
		<div class="label_name" style="float:left;">
			<img src="<?php echo $Config_live_site; ?>/images/mail.png" class="dragon"/>
		</div>
		<div class="label_element" style="">
			<input type="text" name="usrname" id="login_id" class="login_input" value="" style=""/>
		</div>
	</div>
	<div class="login_area" style="margin-top:20px;">
		<div class="label_name">
			<img src="<?php echo $Config_live_site; ?>/images/password.png" class="dragon"/>
		</div>
		<div class="label_element">
			<input type="password" name="pass" id="login_paswd" class="login_input" value=""/>
		</div>
		<input type="hidden" name="task" id="task" value="login">
	</div>
<div class="login_bottom" align="right">
	<input type="button" id="confirm" name="confirm" value=""/>

</div>
</form>
</div>
<script>
	$("#confirm").click( function () {
		document.content.submit();
	});
</script>

</body>
</html>
