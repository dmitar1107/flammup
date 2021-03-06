<?php
/**
* @version $Id: admin.users.php 10002 2008-02-08 10:56:57Z willebil $
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

$id 	= mosGetParam($_REQUEST, 'id', 0);
$cid 	= josGetArrayInts( 'cid' );

switch ($task) {
	case 'new':
		editUser( 0, $option);
		break;

	case 'edit':
		editUser( intval( $cid[0] ), $option );
		break;

	case 'editA':
		editUser( $id, $option );
		break;

	case 'save':
	case 'apply':
		saveUser( $task );
		break;

	case 'remove':
		removeUsers( $cid, $option );
		break;

	case 'cancel':
		cancelUser( $option );
		break;
		
	default:
		showUsers( $option );
		break;
}

function showUsers( $option ) {
	global $database, $mainframe, $Config_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$where 			= array();

	if (isset( $search ) && $search!= "") {
		$searchEscaped = $database->getEscaped( trim( strtolower( $search ) ) );
		$where[] = "(a.username LIKE '%$searchEscaped%' OR a.email LIKE '%$searchEscaped%' OR a.name LIKE '%$searchEscaped%')";
	}

	$query = "SELECT COUNT(a.id) FROM #__admin AS a"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT * FROM #__admin AS a"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY a.name"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	HTML_users::showUsers( $rows, $pageNav, $search, $option, $lists );
}

/**
 * Edit the user
 * @param int The user ID
 * @param string The URL option
 */
function editUser( $uid='0', $option='admin_users' ) {
	global $database, $mainframe;

	$row = new mosAdminUser( $database );
	// load the row from the db table
	$row->load( (int)$uid );
	if($row->email == '')
		$row->email = ' ';

	HTML_users::editUser( $row, $lists, $option, $uid );
}

function saveUser( $task ) {
	global $database;
	global $Config_live_site, $Config_sitename;
	
	$row = new mosAdminUser( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->name = trim( $row->name );
	$row->email = trim( $row->email );
	$row->username = trim( $row->username );

	// sanitise fields
	$row->id 	= (int) $row->id;

	$isNew 	= !$row->id;
	$pwd 	= '';

	// MD5 hash convert passwords
	if ($isNew) {
		$pwd = trim( $row->password );
		$crypt = md5($pwd);
		$row->password = $crypt;
		//$row->registerDate 	= date( 'Y-m-d H:i:s' );
	} else {
		$original = new mosAdminUser( $database );
		$original->load( (int)$row->id );

		// existing user stuff
		if ($row->password == '') {
			// password set to null if empty
			$row->password = null;
		} else {
			$row->password = trim($row->password);
			$crypt = md5($row->password);
			$row->password = $crypt;
		}
	}

//	if (!$row->check()) {
//		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
//		exit();
//	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	switch ( $task ) {
		case 'apply':
			$msg = 'Successfully Saved changes to Admin User: '. $row->name;
			mosRedirect( 'index.php?option=com_admin_users&task=editA&hidemainmenu=1&id='. $row->id, $msg );
			break;

		case 'save':
		default:
			$msg = 'Successfully Saved Admin User: '. $row->name;
			mosRedirect( 'index.php?option=com_admin_users', $msg );
			break;
	}
}

/**
* Cancels an edit operation
* @param option component option to call
*/
function cancelUser( $option ) {
	mosRedirect( 'index.php?option='. $option .'&task=view' );
}

function removeUsers( $cid, $option ) {
	global $database;
	
	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	$obj = new mosAdminUser( $database );
	foreach ($cid as $id) {
		$obj->load( $id );
		if ( $obj->username == "admin" ) {
		// cannot delete Super Admin where it is the only one that exists
			$msg = "You cannot delete this user for your site";
		} else {
			// delete user
			$obj->delete( $id );
			$msg = $obj->getError();
		}
	}

	mosRedirect( 'index.php?option='. $option, $msg );
}
?>
