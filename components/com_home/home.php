<?php
/**
* @version $Id: users.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Users
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( $option, 'html' ) );
if( $task == 'type' ) {
	unset($_REQUEST['subtype']); unset($_POST['subtype']); unset($_GET['subtype']);
}
$type 	= strval( strtolower( mosGetParam( $_REQUEST, 'type', '' ) ) );
$cid 	= strval( strtolower( mosGetParam( $_REQUEST, 'cid', 0 ) ) );

if( $_REQUEST['type'] ) {
	$_SESSION['type'] = $_REQUEST['type'];
	unset($_SESSION['subtype']);
	unset($_SESSION['search']);
}
$type 	= strval( strtolower( mosGetParam( $_SESSION, 'type', 'type_approve' ) ) );
if( $_REQUEST['task'] )
	$_SESSION['task'] = $_REQUEST['task'];
$task 	= strval( strtolower( mosGetParam( $_SESSION, 'task', '' ) ) );

if( $_REQUEST['cid'] )
	$_SESSION['cid'] = $_REQUEST['cid'];
$cid 	= strval( strtolower( mosGetParam( $_SESSION, 'cid', 0 ) ) );

switch ($type) {
	default:
		cartoon_list();
		break;
	case "search_friend":
		search_friend();
		break;
	case "earn_point":
		earn_point();
		break;
	case "friend_field":
		friend_field();
		break;
	case "about_me":
		about_me();
		break;
}
function friend_field() {
	HTML_fm_home::friend_field( );
}
function earn_point() {
	HTML_fm_home::earn_point( );

}
function about_me() {
	HTML_fm_home::about_me( );

}
function search_friend() {
	$name 	= mosGetParam( $_REQUEST, 'friend_name', "" ) ;

	global $option, $type, $subtype, $task, $cid;
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$where 			= array();
	$where[] = "c.parent = 0";
	if($subtype == 'class') {
		$where[] = "c.class_id = '$class_id'";
	} else if($subtype == 'alphabetic') {
		$where[] = "c.class_id = '$class_id'";
	} else if($subtype == 'region') {
		$where[] = "c.region = '$class_id'";
	} else if($subtype == 'state') {
		$where[] = "c.state = '$class_id'";
	}
		
	$query = "SELECT COUNT(c.id) FROM #__cartoon AS c"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT c.* FROM #__cartoon AS c"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY c.id DESC"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	HTML_fm_home::cartoon_list( $rows );
}
function cartoon_list( ) {
	global $option, $type, $subtype, $task, $cid;
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$where 			= array();

	$where[] = "c.parent = 0";
	if($subtype == 'class') {
		$where[] = "c.class_id = '$class_id'";
	} else if($subtype == 'alphabetic') {
		$where[] = "c.class_id = '$class_id'";
	} else if($subtype == 'region') {
		$where[] = "c.region = '$class_id'";
	} else if($subtype == 'state') {
		$where[] = "c.state = '$class_id'";
	}
		
	$query = "SELECT COUNT(c.id) FROM #__cartoon AS c"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT c.* FROM #__cartoon AS c"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY c.id DESC"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	HTML_fm_home::cartoon_list( $rows );
}
?>
