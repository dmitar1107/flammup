<?php
/**
* @version $Id: classification.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Classification
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
		editClassification( 0, $option);
		break;

	case 'edit':
		editClassification( intval( $cid[0] ), $option );
		break;

	case 'editA':
		editClassification( $id, $option );
		break;

	case 'save':
	case 'apply':
		saveClassification( $task );
		break;

	case 'remove':
		removeClassification( $cid, $option );
		break;

	case 'cancel':
		cancelClassification( $option );
		break;
		
	default:
		showClassification( $option );
		break;
}

function showClassification( $option ) {
	global $database, $mainframe, $Config_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$where 			= array();

	if (isset( $search ) && $search!= "") {
		$searchEscaped = $database->getEscaped( trim( strtolower( $search ) ) );
		$where[] = "name LIKE '%$searchEscaped%'";
	}

	$query = "SELECT COUNT(id) FROM #__classification"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT * FROM #__classification"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY id"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	HTML_classification::showClassification( $rows, $pageNav, $search, $option, $lists );
}

/**
 * Edit the Classification
 * @param int The classification ID
 * @param string The URL option
 */
function editClassification( $uid='0', $option='classification' ) {
	global $database, $mainframe;

	$row = new mosClassification( $database );
	// load the row from the db table
	$row->load( (int)$uid );

	HTML_classification::editClassification( $row, $lists, $option, $uid );
}

function saveClassification( $task ) {
	global $database;
	global $Config_live_site, $Config_sitename, $Config_classification_thumbnail_path;
	
	$row = new mosClassification( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if( $_FILES['thumbnail']['name'] ) {
		if ( strstr($_FILES['thumbnail']['type'], 'image') == FALSE ) {
			echo "<script> alert('File format is not image!'); window.history.go(-1); </script>\n";
			exit();
		}
		$file_path = $_FILES['thumbnail']['tmp_name'];
		$file_type = substr($_FILES['thumbnail']['type'], strpos($_FILES['thumbnail']['type'], '/')+1);
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$row_tmp = new mosClassification( $database );
	$row_tmp->id = $row->id;
	// thumbnail
	if( $_FILES['thumbnail']['name'] ) {
		$row_tmp->thumbnail_name = $row->id .".". $file_type;
		copy($file_path, $Config_classification_thumbnail_path ."/". $row_tmp->thumbnail_name);
		$row_tmp->store();
	}

	switch ( $task ) {
		case 'apply':
			$msg = 'Successfully Saved changes to Classification: '. $row->name;
			mosRedirect( 'index.php?option=com_classification&task=editA&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Successfully Saved Classification: '. $row->name;
			mosRedirect( 'index.php?option=com_classification', $msg );
			break;
	}
}

/**
* Cancels an edit operation
* @param option component option to call
*/
function cancelClassification( $option ) {
	mosRedirect( 'index.php?option='. $option .'&task=view' );
}

function removeClassification( $cid, $option ) {
	global $database;
	
	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	$obj = new mosClassification( $database );
	foreach ($cid as $id) {
		// delete classification
		$obj->delete( $id );
		$msg = $obj->getError();
	}

	mosRedirect( 'index.php?option='. $option, $msg );
}
?>
