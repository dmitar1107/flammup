<?php
/**
* @version $Id: index.php 9908 2008-01-06 22:57:38Z eddieajau $
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $Config_sitename; ?></title>
<link rel="stylesheet" href="templates/mogu_default/css/template_css.css" type="text/css" />
<link rel="stylesheet" href="templates/mogu_default/css/theme.css" type="text/css" />
<script language="JavaScript" src="<?php echo $Config_live_site; ?>/includes/js/cartoon.javascript.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo $Config_live_site; ?>/includes/js/JSCookMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo $Config_live_site; ?>/includes/js/ThemeOffice/theme.js" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Generator" content="MoGu! Content Management System" />
</head>
<body>

<?php
if( $option == "advertisement" && $task == "cartoonList" ) {
	mosMainBody();
} else {
?>

<div id="wrapper">
	<div id="header">
			<div id="joomla"><img src="templates/mogu_default/images/header_text.png" alt="MoGu! Logo" /></div>
	</div>
</div>
<table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td class="menubackgr" style="padding-left:5px;">
		<?php mosLoadAdminModule( 'fullmenu' );?>
	</td>
	<td class="menubackgr" align="right">&nbsp;</td>
	<td class="menubackgr" align="right">&nbsp;</td>
	<td class="menubackgr" align="right" style="padding-right:5px;">
		<a href="index.php?task=logout" style="color: #333333; font-weight: bold">
			Logout</a>
		<strong><?php echo $_SESSION['admin_username'];?></strong>
	</td>
</tr>
</table>

<table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td class="menudottedline" width="40%">
		<?php mosLoadAdminModule( 'pathway' );?>
	</td>
	<td class="menudottedline" align="right">
		<?php mosLoadAdminModule( 'toolbar' );?>
	</td>
</tr>
</table>

<br />
<?php mosLoadAdminModule( 'mosmsg' );?>

<div align="center" class="centermain">
	<div class="main">
		<?php mosMainBody(); ?>
	</div>
</div>

<div align="center" class="footer">
	<table width="99%" border="0">
	<tr>
		<td align="center">
			<div align="center">
				MoGu Fun site is the cartoon site!
			</div>
		</td>
	</tr>
	</table>
</div>

<?php
}
?>

</body>
</html>