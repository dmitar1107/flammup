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
$type 	= strval( strtolower( mosGetParam( $_REQUEST, 'type', 'my_bookshelf' ) ) );
$cid 	= strval( strtolower( mosGetParam( $_REQUEST, 'cid', 0 ) ) );

switch ($type) {
	default:
		switch( $task ) {
			case "chapterlist":
			case "view":
				detail_cartoon($cid);
				break;
			case "list":
			default:
				cartoon_list();
				break;
		}
		break;
}

function cartoon_list( ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site;
	global $type, $_user;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$order			= mosGetParam($_REQUEST, 'order', 0);
	$where 			= array();

	if( $type == "my_bookshelf" ) {
		$query = "SELECT COUNT(c.id) FROM #__shelf s"
		. "\n LEFT JOIN #__cartoon c ON c.id = s.cartoon_id"
		. "\n WHERE s.user_id='{$_user->id}'"
		;
	} else if( $type == 'recently_read' ) {
		$query = "SELECT COUNT(c.id) FROM #__viewcartoon s"
		. "\n LEFT JOIN #__cartoon c ON c.id = s.cartoon_id"
		. "\n WHERE s.user_id='{$_user->id}' AND s.cartoon_parent='0'"
		;
	}
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	if( $type == "my_bookshelf" ) {
		$query = "SELECT c.* FROM #__shelf s"
		. "\n LEFT JOIN #__cartoon c ON c.id = s.cartoon_id"
		. "\n WHERE s.user_id='{$_user->id}' ORDER BY s.add_date DESC"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
	} else if( $type == 'recently_read' ) {
		$query = "SELECT c.* FROM #__viewcartoon s"
		. "\n LEFT JOIN #__cartoon c ON c.id = s.cartoon_id"
		. "\n WHERE s.user_id='{$_user->id}' AND s.cartoon_parent='0' ORDER BY s.view_date DESC"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
	}
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	for($i = 0; $i < count($rows); $i++) {
		$row = &$rows[$i];
		// last read cartoon chapter
		$query = "SELECT c.* FROM #__viewcartoon s"
		. "\n LEFT JOIN #__cartoon c ON c.id = s.cartoon_id"
		. "\n WHERE s.user_id='{$_user->id}' AND s.cartoon_parent='$row->id' ORDER BY s.view_date DESC"
		. "\n LIMIT 0, 1"
		;
		$database->setQuery($query);
		$row_viewchapter = $database->loadObjectList();
		$row->lastreadchapter = $row_viewchapter[0]->name;
		// last update cartoon chapter
		$query = "SELECT c.* FROM #__cartoon c"
		. "\n WHERE c.parent='$row->id' ORDER BY c.name, c.public_date DESC"
		. "\n LIMIT 0, 1"
		;
		$database->setQuery($query);
		$row_updatechapter = $database->loadObjectList();
		$row->lastupdatechapter = $row_updatechapter[0]->name;		
	}
	
	if( $ajaxcall == 0 )
		HTML_bookshelf::cartoon_list( $rows, $pageNav, $ajaxcall );
	else
		HTML_bookshelf::cartoon_list_li( $rows, $ajaxcall );
}

function detail_cartoon( $cid ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $_user, $_device_type, $task;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$order			= mosGetParam($_REQUEST, 'order', 0);
	
	if( $task == "chapterlist" && $ajaxcall == 1 ) {
		$query = "SELECT COUNT(id) FROM #__cartoon WHERE parent='{$cid}' ORDER BY ordering $ordering"
		;
		$database->setQuery($query);
		$total = $database->loadResult();
		require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		
		$ordering = ($order == "down") ? "DESC" : "ASC";
		$query = "SELECT * FROM #__cartoon WHERE parent='{$cid}' ORDER BY ordering $ordering"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
		$database->setQuery($query);
		$rows_child = $database->loadObjectList();
		
		module_chapterlistOfCartoon($rows_child, $ajaxcall);
	} else {
		module_detail_cartoon($cid);
	}
}
?>
