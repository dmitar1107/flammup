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
<!DOCTYPE html> 
<html>
<head>
<title><?php echo $Config_sitename; ?></title>

<meta name="viewport" content="width=device-width, initial-scale=1"> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Generator" content="Flammup! Content Management System" />
<link rel="stylesheet" href="templates/flammup/css/style.css"/>
<script src="<?php echo $Config_live_site; ?>/includes/js/jquery-1.8.2.min.js"></script>
</head>

<body>
<?php
	if (!isset($_SESSION["notify"])) {
//		echo "<img src=".$Config_live_site."/images/top_logo.png>";
	}
	else{
//		echo "<span class='notify' style=''>".$_SESSION["notify"]."</span></br>";
//		echo "<span class='notify' style=''>Know more</span>";
	}
?>
<?php
	mosMainBody();
?>

</body>
</html>
