<?php
/**
* @version $Id: login.php 5975 2006-12-11 01:26:33Z robs $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php echo "<?xml version=\"1.0\"?>\r\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $Config_sitename; ?> - Administration</title>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<style type="text/css">
@import url(<?php echo $Config_live_site; ?>/templates/mogu_default/css/admin_login.css);
</style>
<script language="javascript" type="text/javascript">
	function setFocus() {
		document.loginForm.usrname.select();
		document.loginForm.usrname.focus();
	}
</script>
</head>
<body onload="setFocus();">
<div id="wrapper">
	<div id="header">
			<div id="joomla"><img src="templates/mogu_default/images/header_text.png" alt="Joomla! Logo" /></div>
	</div>
</div>
<div id="ctr" align="center">
	<?php
	// handling of mosmsg text in url
	include_once( $Config_absolute_path .'/modules/mod_mosmsg.php' );
	?>
	<div class="login">
		<div class="login-form">
			<img src="templates/mogu_default/images/login.gif" alt="Login" />
			<form action="index.php" method="post" name="loginForm" id="loginForm">
			<div class="form-block">
				<div class="inputlabel">Username</div>
				<div><input name="usrname" type="text" class="inputbox" size="15" /></div>
				<div class="inputlabel">Password</div>
				<div><input name="pass" type="password" class="inputbox" size="15" /></div>
				<div align="left"><input type="submit" name="submit" class="button" value="Login" /></div>
			</div>
			<input type="hidden" name="option" value="" />
			<input type="hidden" name="task" value="login" />
			</form>
		</div>
		<div class="login-text">
			<div class="ctr"><img src="templates/mogu_default/images/security.png" width="64" height="64" alt="security" /></div>
			<p>Welcome to MoGu!</p>
			<p>Use a valid username and password to gain access to the administration console.</p>
		</div>
		<div class="clr"></div>
	</div>
</div>
<div id="break"></div>
</body>
</html>