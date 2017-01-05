<?php
/**
* @version $Id: author.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Author
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
		editAuthor( 0, $option);
		break;

	case 'edit':
		editAuthor( intval( $cid[0] ), $option );
		break;

	case 'editA':
		editAuthor( $id, $option );
		break;

	case 'save':
	case 'apply':
		saveAuthor( $task );
		break;

	case 'remove':
		removeAuthor( $cid, $option );
		break;

	case 'cancel':
		cancelAuthor( $option );
		break;
		
	default:
		showAuthor( $option );
		break;
}

function showAuthor( $option ) {
	global $database, $mainframe, $Config_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$where 			= array();

	if (isset( $search ) && $search!= "") {
		$searchEscaped = $database->getEscaped( trim( strtolower( $search ) ) );
		$where[] = "name LIKE '%$searchEscaped%'";
	}

	$query = "SELECT COUNT(id) FROM #__author"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT * FROM #__author"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY id"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	HTML_author::showAuthor( $rows, $pageNav, $search, $option, $lists );
}

/**
 * Edit the Author
 * @param int The author ID
 * @param string The URL option
 */
function editAuthor( $uid='0', $option='author' ) {
	global $database, $mainframe;

	$row = new mosAuthor( $database );
	// load the row from the db table
	$row->load( (int)$uid );

	HTML_author::editAuthor( $row, $lists, $option, $uid );
}

function saveAuthor( $task ) {
	global $database;
	global $Config_live_site, $Config_sitename, $Config_author_thumbnail_path;
	
	$row = new mosAuthor( $database );
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
	
	$row_tmp = new mosAuthor( $database );
	$row_tmp->id = $row->id;
	// thumbnail
	if( $_FILES['thumbnail']['name'] ) {
		$row_tmp->thumbnail_name = $row->id .".". $file_type;
		copy($file_path, $Config_author_thumbnail_path ."/". $row_tmp->thumbnail_name);
		$row_tmp->store();
	}

	switch ( $task ) {
		case 'apply':
			$msg = 'Successfully Saved changes to Author: '. $row->name;
			mosRedirect( 'index.php?option=com_author&task=editA&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Successfully Saved Author: '. $row->name;
			mosRedirect( 'index.php?option=com_author', $msg );
			break;
	}
}

/**
* Cancels an edit operation
* @param option component option to call
*/
function cancelAuthor( $option ) {
	mosRedirect( 'index.php?option='. $option .'&task=view' );
}

function removeAuthor( $cid, $option ) {
	global $database;
	
	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	$obj = new mosAuthor( $database );
	foreach ($cid as $id) {
		// delete author
		$obj->delete( $id );
		$msg = $obj->getError();
	}

	mosRedirect( 'index.php?option='. $option, $msg );
}
?>
