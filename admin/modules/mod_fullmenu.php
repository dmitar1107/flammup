<?php
/**
* @version $Id: mod_fullmenu.php 9998 2008-02-07 11:36:41Z eddieajau $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined( '_JOS_FULLMENU_MODULE' )) {
	/** ensure that functions are declared only once */
	define( '_JOS_FULLMENU_MODULE', 1 );

	/**
	* Full DHTML Admnistrator Menus
	* @package Joomla
	*/
	class mosFullAdminMenu {
		/**
		* Show the menu
		* @param string The current user type
		*/
		function show( $usertype='' ) {
			global $database;
			global $Config_live_site;
			?>
			<div id="myMenuID"></div>
			<script language="JavaScript" type="text/javascript">
			var myMenu =
			[
				[null,'Home','index.php',null,'Home'],
				_cmSplit,
				[null,'User','index.php?option=users',null,'Manage Users'],
				_cmSplit,
				[null,'Author','index.php?option=author',null,'Manage Author'],
				//_cmSplit,
				//[null,'Topic','index.php?option=topic',null,'Manage Topic'],
				_cmSplit,
				[null,'Classification','index.php?option=classification',null,'Manage Classification'],
				_cmSplit,
				[null,'Cartoon','index.php?option=cartoon',null,'Manage Cartoon'],
				_cmSplit,
				[null,'Advertisement','index.php?option=advertisement',null,'Manage Advertisements'],
				_cmSplit,
				[null,'Admin User','index.php?option=admin_users',null,'Manage Admin Users']
			];
			cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
			</script>
	<?php
		}


		/**
		* Show an disbaled version of the menu, used in edit pages
		* @param string The current user type
		*/
		function showDisabled( $usertype='' ) {
			$text = 'Menu inactive for this Page';
			?>
			<div id="myMenuID" class="inactive"></div>
			<script language="JavaScript" type="text/javascript">
			var myMenu =
			[
				[null,'<?php echo 'Home'; ?>',null,null,'<?php echo $text; ?>'],
				_cmSplit,
				[null,'<?php echo 'User'; ?>',null,null,'<?php echo $text; ?>'],
				_cmSplit,
				[null,'<?php echo 'Author'; ?>',null,null,'<?php echo $text; ?>'],
				//_cmSplit,
				//[null,'<?php echo 'Topic'; ?>',null,null,'<?php echo $text; ?>'],
				_cmSplit,
				[null,'<?php echo 'Classification'; ?>',null,null,'<?php echo $text; ?>'],
				_cmSplit,
				[null,'<?php echo 'Cartoon'; ?>',null,null,'<?php echo $text; ?>'],
				_cmSplit,
				[null,'<?php echo 'Advertisement'; ?>',null,null,'<?php echo $text; ?>'],
				_cmSplit,
				[null,'<?php echo 'Admin User'; ?>',null,null,'<?php echo $text; ?>']
			];
			cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
			</script>
			<?php
		}
	}
}

$hide = intval( mosGetParam( $_REQUEST, 'hidemainmenu', 0 ) );

if ( $hide ) {
	mosFullAdminMenu::showDisabled();
} else {
	mosFullAdminMenu::show();
}
?>