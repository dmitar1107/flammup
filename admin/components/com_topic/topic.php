<?php
/**
* @version $Id: topic.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Topic
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

$id 	= mosGetParam($_REQUEST, 'id', 0);
$cid 	= josGetArrayInts( 'cid' );

switch ($task) {
	case 'new':
		editTopic( 0, $option);
		break;

	case 'edit':
		editTopic( intval( $cid[0] ), $option );
		break;

	case 'editA':
		editTopic( $id, $option );
		break;

	case 'save':
	case 'apply':
		saveTopic( $task );
		break;

	case 'remove':
		removeTopic( $cid, $option );
		break;

	case 'cancel':
		cancelTopic( $option );
		break;
		
	default:
		showTopic( $option );
		break;
}

function showTopic( $option ) {
	global $database, $mainframe, $Config_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$where 			= array();

	if (isset( $search ) && $search!= "") {
		$searchEscaped = $database->getEscaped( trim( strtolower( $search ) ) );
		$where[] = "name LIKE '%$searchEscaped%'";
	}

	$query = "SELECT COUNT(id) FROM #__topic"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT * FROM #__topic"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY id"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	HTML_topic::showTopic( $rows, $pageNav, $search, $option, $lists );
}

/**
 * Edit the Topic
 * @param int The topic ID
 * @param string The URL option
 */
function editTopic( $uid='0', $option='topic' ) {
	global $database, $mainframe;

	$row = new mosTopic( $database );
	// load the row from the db table
	$row->load( (int)$uid );

	HTML_topic::editTopic( $row, $lists, $option, $uid );
}

function saveTopic( $task ) {
	global $database;
	global $Config_live_site, $Config_sitename;
	
	$row = new mosTopic( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	switch ( $task ) {
		case 'apply':
			$msg = 'Successfully Saved changes to Topic: '. $row->name;
			mosRedirect( 'index.php?option=com_topic&task=editA&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Successfully Saved Topic: '. $row->name;
			mosRedirect( 'index.php?option=com_topic', $msg );
			break;
	}
}

/**
* Cancels an edit operation
* @param option component option to call
*/
function cancelTopic( $option ) {
	mosRedirect( 'index.php?option='. $option .'&task=view' );
}

function removeTopic( $cid, $option ) {
	global $database;
	
	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	$obj = new mosTopic( $database );
	foreach ($cid as $id) {
		// delete topic
		$obj->delete( $id );
		$msg = $obj->getError();
	}

	mosRedirect( 'index.php?option='. $option, $msg );
}
?>
