<?php
/**
* @version $Id: mod_pathway.php 87 2005-09-15 23:12:03Z eddieajau $
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

global $Config_sitename;

if ($option != '') {
	$html = '';

	$html .= '<div class="pathway">';
	$html .= '<a href="'. $Config_live_site .'/administrator/index.php">';
	$html .= '<strong>' . $Config_sitename . '</strong>';
	$html .= "</a>";

	if ($option != '') {
		$html .= " / ";
		// try to miss edit functions
		if ($task != '' && strpos( $task, 'edit' ) === false) {
			$html .= "<a href=\"index.php?option=$option\">$option</a>";
		} else {
			$html .= $option;
		}
	}

	if ($task != '') {
		$html .= " / $task";
	}
	$html .= '</div>';
	echo $html;
}
?>