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
$page = mosGetParam( $_REQUEST, 'page', '' );


switch ($page) {
	case 'myauction':
		myauction();
		break;

	case 'classification':
		$subtype 	= strval( strtolower( mosGetParam( $_REQUEST, 'subtype', 'class' ) ) );
		switch($task) {
			case "class_cartoon_list":
				$class_id = strval( strtolower( mosGetParam( $_REQUEST, 'class_id', 0 ) ) );
				class_cartoon_list($class_id);
				break;
			default:
				classification_part_list();
				break;
		}
		break;

	case 'search':
		$search 	= strval( strtolower( mosGetParam( $_REQUEST, 'search', '' ) ) );
		switch( $task ) {
			case "search":
				search_cartoon();
				break;
			case "view":
			default:
				view_search( '' );
				break;
		}
		break;

		
	case 'approve':
	default:
		switch($task) {

		}
		break;
}

/**
* menu for seller
*/

function myauction() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $_user, $_device_type;
	$query = "SELECT * FROM #__menu order by ordering ASC";
	$database->setQuery($query);
	$allmenus = $database->loadObjectList();
	HTML_seller::seller_menu($allmenus);
}
function cartoon_seenuser() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $_user, $_device_type;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$userid			= mosGetParam($_REQUEST, 'userid', 0);
	$where 			= array();
	
	// user info
	$row_user = new mosUser($database);
	$row_user->load($userid);
	
	// visit guest
	$query = "SELECT u.* FROM #__visitguestofuser v"
	. "\n LEFT JOIN #__user u ON u.id = v.guest_id"
	. "\n WHERE v.user_id='$userid' AND v.guest_id!='{$_user->id}'"
	. "\n ORDER BY v.visit_date DESC"
	. "\n LIMIT 0, ". (($_device_type == "desktop") ? '15' : '5')
	;
	$database->setQuery($query);
	$rows_guest = $database->loadObjectList();
	
	// bookshelf of user
	$query = "SELECT c.* FROM #__shelf s"
	. "\n LEFT JOIN #__cartoon c ON c.id = s.cartoon_id"
	. "\n WHERE s.user_id='$userid' ORDER BY s.add_date DESC"
	. "\n LIMIT 0, 15"
	;
	$database->setQuery($query);
	$rows_bookshelf = $database->loadObjectList();
	
	// estimate of user
	$query = "SELECT e.content, c.id, c.name, c.thumbnail_name FROM #__estimate e"
	. "\n LEFT JOIN #__cartoon c ON c.id = e.cartoon_id"
	. "\n WHERE e.user_id='{$userid}' ORDER BY e.add_date DESC"
	. "\n LIMIT 0, 15"
	;
	$database->setQuery($query);
	$rows_estimate = $database->loadObjectList();
	
	// guest register
	$query = "SELECT v.id FROM #__visitguestofuser v"
	. "\n WHERE v.user_id='$userid' AND v.guest_id='{$_user->id}'"
	;
	$database->setQuery($query);
	$visitId = $database->loadResult();
	$visit_obj = new mosVisitGuestOfUser($database);
	if($visitId)
		$visit_obj->load($visitId);
	else {
		$visit_obj->user_id = $userid;
		$visit_obj->guest_id = $_user->id;
	}
	$visit_obj->visit_date = date("Y-m-d H:i:s");
	$visit_obj->store();
	
	HTML_library::cartoon_seenuser($row_user, $rows_guest, $rows_bookshelf, $rows_estimate);
}

function apporve_recommend_list( $class_id ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$where 			= array();

	$where[] = "c.parent = 0";
	
	if( $class_id == "editor" ) {
		$query = "SELECT COUNT(id) FROM #__cartoon c"
		. "\n WHERE c.recommended=1 AND c.parent=0"
		;
		$database->setQuery($query);
		$total = $database->loadResult();
		require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );

		$query = "SELECT c.* FROM #__cartoon c"
		. "\n WHERE c.recommended=1 AND c.parent=0"
		. "\n ORDER BY c.public_date DESC"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
		$database->setQuery($query);
		$rows = $database->loadObjectList();
	} else if( $class_id == "user" ) {
		$rows = array();

 		$query = "SELECT a.cartoon_id FROM #__estimate a"
		. "\n GROUP BY a.cartoon_id"
		;
		$database->setQuery($query);
		$total = count($database->loadResultArray());
		require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		
 		$query = "SELECT a.id, a.cartoon_id, c.name cartoon_name, c.thumbnail_name, (SELECT COUNT(*) AS cnt FROM #__estimate a2 WHERE a2.cartoon_id = a.cartoon_id) AS order_col"
 		. "\n FROM #__estimate a"
 		. "\n LEFT JOIN #__cartoon c ON c.id = a.cartoon_id"
		. "\n GROUP BY a.cartoon_id"
		. "\n ORDER BY order_col DESC"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
		$database->setQuery($query);
		$rows_cartoon = $database->loadObjectList();
		
		foreach( $rows_cartoon as $row_cartoon ) {
			$obj 					= new stdClass;
			$obj->id 				= $row_cartoon->cartoon_id;
			$obj->name 				= $row_cartoon->cartoon_name;
			$obj->count 			= $row_cartoon->order_col;
			$obj->thumbnail_name 	= $row_cartoon->thumbnail_name;
			
			$query = "SELECT e1.content, u.id user_id, u.name user_name FROM #__estimate e1"
			. "\n LEFT JOIN #__user u ON u.id = e1.user_id"
			. "\n WHERE e1.cartoon_id = {$row_cartoon->cartoon_id}"
			. "\n ORDER BY e1.add_date DESC LIMIT 0, 1";
			$database->setQuery($query);
			$row_estimate = null;
			$database->loadObject($row_estimate);
			$obj->content 	= $row_estimate->content ? $row_estimate->content : '&nbsp;';
			$obj->user_id 	= $row_estimate->user_id;
			$obj->user_name = $row_estimate->user_name;
			
			$rows[] = $obj;
		}
	} else {
		$query = "SELECT COUNT(c.id) FROM #__cartoon AS c"
		. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
		;
		$database->setQuery($query);
		$total = $database->loadResult();
		require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );
		
		$query = "SELECT c.* FROM #__cartoon AS c"
		. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
		. "\n ORDER BY c.public_date DESC"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		for( $i = 0; $i < count($rows); $i++ ) {
			$row = &$rows[$i];

			$date = new DateTime($row->public_date);
			$public_time = $date->format('U');
			$hour = ( time() - $public_time ) / 3600;
			$row->hour = ceil($hour);
			if( $row->hour >= 24 )
				$row->hour = ceil( $row->hour / 24 ) ."天前";
			else
				$row->hour = $row->hour ."小时前";
			
			$query = "SELECT COUNT(id) FROM #__cartoon WHERE parent = '{$row->id}'";
			$database->setQuery($query);
			$row->count_child = $database->loadResult();
		}		
	}
	
	if( $ajaxcall == 0 ) {
		$query = "SELECT a.*, c1.name c1_name, c1.description c1_description, c2.name c2_name, c2.description c2_description, c3.name c3_name, c3.description c3_description"
		. "\n FROM #__advertisement a"
		. "\n LEFT JOIN #__cartoon c1 ON c1.id = a.cartoon_id1"
		. "\n LEFT JOIN #__cartoon c2 ON c2.id = a.cartoon_id2"
		. "\n LEFT JOIN #__cartoon c3 ON c3.id = a.cartoon_id3"
		;
		$database->setQuery( $query );
		$row_ad = null;
		$database->loadObject($row_ad);
	}
	
	if( $ajaxcall == 0 )
		HTML_library::apporve_recommend_list( $rows, $pageNav, $row_ad, $ajaxcall );
	else
		HTML_library::apporve_recommend_list_class( $rows, $class_id, $ajaxcall );
}

function class_cartoon_list( $class_id ) {
	global $option, $type, $subtype, $task, $cid;
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$where 			= array();

	$where[] = "c.parent = 0";
	if($subtype == 'class') {
		$where[] = "c.class_id = '$class_id'";
	} else if($subtype == 'alphabetic') {
		$class_id = strtolower($class_id);
		$where[] = "c.alphabet = '$class_id'";
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
	
	if( $ajaxcall == 0 )
		HTML_library::class_cartoon_list( $rows, $pageNav, $ajaxcall );
	else
		HTML_library::class_cartoon_list_li( $rows, $class_id, $ajaxcall );
}

function ranking_part_list( ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$where 			= array();

	$where[] = "c.parent = 0";
	$query = "SELECT COUNT(c.id) FROM #__cartoon AS c"
	. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : '' )
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$order = "";
	if($subtype == "read")
		$order = "\n ORDER BY c.view_num DESC";
	else if($subtype == "download")
		$order = "\n ORDER BY c.down_num DESC";
	else if($subtype == "score")
		$order = "\n ORDER BY c.score DESC";
	$query = "SELECT c.* FROM #__cartoon AS c"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. $order
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	if( $ajaxcall == 0 )
		HTML_library::ranking_part_list( $rows, $pageNav, $ajaxcall );
	else
		HTML_library::ranking_part_list_subtype($rows, $subtype, $ajaxcall);
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

function read_cartoon_interface( $cid ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $_user, $_device_type;
	$pid = mosGetParam($_REQUEST, "pid", 0);
	$where 			= array();
	
	// view parent cartoon info & update view date
	$query = "SELECT id FROM #__viewcartoon WHERE cartoon_id='$pid' AND user_id={$_user->id}";
	$database->setQuery($query);
	$view_id = $database->loadResult();
	$obj_view = new mosViewCartoon($database);
	if( $view_id )
		$obj_view->load($view_id);
	else {
		$obj_view->cartoon_id = $pid;
		$obj_view->user_id = $_user->id;
	}
	$obj_view->view_date = date("Y-m-d H:i:s");
	$obj_view->store();

	// view cartoon info & update view date
	$query = "SELECT id FROM #__viewcartoon WHERE cartoon_id='$cid' AND user_id={$_user->id}";
	$database->setQuery($query);
	$view_id = $database->loadResult();
	$obj_view = new mosViewCartoon($database);
	if( $view_id ) {
		$obj_view->load($view_id);
	} else {
		$obj_view->cartoon_id = $cid;
		$obj_view->cartoon_parent = $pid;
		$obj_view->user_id = $_user->id;
	}
	$obj_view->view_date = date("Y-m-d H:i:s");
	$obj_view->store();
	
	// parent cartoon info
	$row_parent = new mosCartoon($database);
	$row_parent->load($pid);
	$row_parent->view_num++;
	if( $row_parent->name ) {
		$row_parent->store();
	}

	// cartoon info
	$row = new mosCartoon($database);
	$row->load($cid);
	$row->view_num++;
	if( $row->name ) {
		$row->store();
	}
	
	// subtraction view point of user
	if( $row->viewpoint ) {
		$row_user = new mosUser($database);
		$row_user->load($_user->id);
		$row_user->point -= $row->viewpoint;
		$row_user->store();
	}
	
	$file_info = unserialize($row->file_info);
	
	if( $_device_type == "desktop" ) {
		HTML_library::read_cartoon_interface_mobile( $row_parent, $row, $file_info, $pid );
	}
	else {
		HTML_library::read_cartoon_interface_mobile( $row_parent, $row, $file_info, $pid );
	}
}

function read_cartoon_image( $cid ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $Config_cartoon_path;
	$where 			= array();
	$order = mosGetParam($_REQUEST, 'order', 0);

	if( is_file("{$Config_cartoon_path}/{$cid}.dat") ) {
		$fp = fopen("{$Config_cartoon_path}/{$cid}.dat", "r");
		$file_content = fread($fp, filesize("{$Config_cartoon_path}/{$cid}.dat"));
		
		$b_pos = strpos($file_content, sprintf("FILE-BEGIN:%03d", $order)) + 14;
		$e_pos = strpos($file_content, sprintf("FILE-END:%03d", $order));
		$file_content = substr($file_content, $b_pos, $e_pos-$b_pos);
		fclose($fp);		
	}
	
	switch($file_type) {
		case "gif": $ctype="image/gif"; break; 
		case "png": $ctype="image/png"; break; 
		case "bmp": $ctype="image/bmp"; break; 
		case "jpeg": 
		case "jpg":
		case "default": $ctype="image/jpg"; break; 
	}
	header("Content-Type: $ctype");
	echo base64_decode($file_content); exit;
}

function classification_part_list() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype;
	global $Config_region, $Config_state;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	$where 			= array();

	if( $subtype == 'class' ) {
		$query = "SELECT * FROM #__classification ORDER BY id";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		for( $i = 0; $i < count($rows); $i++ ) {
			$row = &$rows[$i];
			$query = "SELECT name FROM #__cartoon WHERE class_id='{$row->id}' AND parent=0 ORDER BY ordering DESC LIMIT 0, 10";
			$database->setQuery($query);
			$name_array = $database->loadResultArray();
			$row->cartoon_name = implode(", ", $name_array);
		}
	} else if( $subtype == 'alphabetic' ) {
		$rows = array();
		$alphabetic = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		foreach($alphabetic as $k => $alpha) {
			$query = "SELECT name FROM #__cartoon WHERE alphabet='$alpha' ORDER BY public_date DESC LIMIT 0, 10";
			$database->setQuery($query);
			$rows_alpha = $database->loadResultArray();
			$cartoon_names = implode(', ', $rows_alpha);
			
			$obj = new stdClass();
			$obj->id = $k;
			$obj->name = $alpha;
			$obj->cartoon_name = $cartoon_names;
			$rows[] = $obj;
		}
	} else if( $subtype == 'region' ) {
		$rows = array();
		foreach($Config_region as $k => $v) {
			if( $k == 0 ) continue;
			$obj = new stdClass();
			$obj->id = $k;
			$obj->name = $v;
			$query = "SELECT name FROM #__cartoon WHERE region='{$k}' AND parent=0 ORDER BY ordering DESC LIMIT 0, 10";
			$database->setQuery($query);
			$name_array = $database->loadResultArray();
			$obj->cartoon_name = implode(", ", $name_array);
			$rows[] = $obj;
		}
	} else if( $subtype == 'state' ) {
		$rows = array();
		foreach($Config_state as $k => $v) {
			if( $k == 0 ) continue;
			$obj = new stdClass();
			$obj->id = $k;
			$obj->name = $v;
			$query = "SELECT name FROM #__cartoon WHERE state='{$k}' AND parent=0 ORDER BY ordering DESC LIMIT 0, 10";
			$database->setQuery($query);
			$name_array = $database->loadResultArray();
			$obj->cartoon_name = implode(", ", $name_array);
			$rows[] = $obj;
		}
	}
	
	HTML_library::classification_part_list( $rows );
}

function view_search( $search_keyword='', $rows_search=array(), $pageNav=null, $ajaxcall=0 ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype;
	$query = "SELECT * FROM #__searchkeyword ORDER BY search_count DESC LIMIT 0, 10";
	$database->setQuery($query);
	$rows_hotkey = $database->loadObjectList();
	HTML_library::view_search( $search_keyword, $rows_search, $rows_hotkey, $pageNav, $ajaxcall );
}

function search_cartoon() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task;
	$limit 			= intval( mosGetParam( $_REQUEST, 'limit', $Config_list_limit ) );
	$limitstart 	= intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );
	$search_keyword = mosGetParam($_REQUEST, "search_text", '');
	$ajaxcall		= mosGetParam($_REQUEST, 'ajaxcall', 0);
	
	if( $search_keyword ) {
		$query = "SELECT COUNT(id) FROM #__cartoon WHERE name LIKE '%{$search_keyword}%' AND parent=0";
		$database->setQuery($query);
		$total = $database->loadResult();
		
		require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
		$pageNav = new mosPageNav( $total, $limitstart, $limit  );

		$query = "SELECT * FROM #__cartoon"
		. "\n WHERE name LIKE '%{$search_keyword}%' AND parent=0 ORDER BY ordering DESC"
		. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
		;
		$database->setQuery($query);
		$rows_search = $database->loadObjectList();
	} else {
		$rows_search = array();
	}
	
	if($search_keyword) {
		$obj = new mosSearchKeyword($database);

		$query = "SELECT id FROM #__searchkeyword WHERE keyword='$search_keyword'";
		$database->setQuery($query);
		$id = $database->loadResult();
		if($id) {
			$obj->load($id);
		} else {
			$obj->keyword = $search_keyword;
		}
		$obj->search_count++;
		if( !$obj->store() ) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	
	if( $ajaxcall == 0 )
		view_search($search_keyword, $rows_search, $pageNav, $ajaxcall);
	else
		HTML_library::view_search_li($rows_search, $ajaxcall);
}

function add_bookshelf() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;

	$query = "SELECT id FROM #__shelf WHERE cartoon_id=$cid AND user_id={$_user->id}";
	$database->setQuery($query);
	$id = $database->loadResult();
	
	$obj = new mosShelf($database);
	if( $id ) {
		$obj->load($id);
	} else {
		$obj->user_id = $_user->id;
		$obj->cartoon_id = $cid;
	}
	$obj->add_date = date("Y-m-d H:i:s");
	if( $obj->store() )
		echo "success";
	else
		echo "failure";
	exit;
}

function cartoon_download( ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;
	global $Config_cartoon_path, $_device_type;
	
	// download info
	$query = "SELECT id FROM #__download WHERE cartoon_id='$cid' AND user_id={$_user->id} AND flag=1";
	$database->setQuery($query);
	$download_id = $database->loadResult();
	
	$obj_download = new mosDownload($database);
	if( $download_id )
		$obj_download->load($download_id);
	else {
		$obj_download->cartoon_id = $cid;
		$obj_download->user_id = $_user->id;
		$obj_download->flag = 1;
	}
	$obj_download->download_date = date("Y-m-d H:i:s");
	$obj_download->store();

	// cartoon info
	$obj = new mosCartoon($database);
	$obj->load($cid);
	$obj->down_num++;
	if( $obj->name ) {
		$obj->store();
	}
	
	// parent cartoon info
	$obj_parent = new mosCartoon($database);
	$obj_parent->load($obj->parent);
	$obj_parent->down_num++;
	if( $obj_parent->name ) {
		$obj_parent->store();
	}
	
	$fileinfo = unserialize($obj->file_info);
	
	// 실제 파일명 또는 경로 
	$fullPath = $Config_cartoon_path ."/". $fileinfo['name'];

	// Must be fresh start
	if( headers_sent() )
		die('Headers Sent');

	// Required for some browsers
	if(ini_get('zlib.output_compression'))
		ini_set('zlib.output_compression', 'Off');

	// File Exists?
	if( file_exists($fullPath) ){
		// Parse Info / Get Extension
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);

		// Determine Content Type
		switch ($ext) {
			case "pdf": $ctype="application/pdf"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "zip": $ctype="application/zip"; break;
			case "doc": $ctype="application/msword"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			default:
				if( $_device_type == "desktop" )
					$ctype="application/force-download";
				else
					$ctype="application/vnd.android.package-archive";
					//$ctype="application/octet-stream";
				break;
		}
//echo "ctype = $ctype, $fullPath, $_device_type";exit;
		
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$fsize);
		ob_clean();
		flush();
		echo readfile( $fullPath );
	} else {
		die('File Not Found');
	}
	exit;
}

function chapters_download( ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;
	global $Config_cartoon_path;
	$chapterids 	= josGetArrayInts( 'checkbox_d' );
//print_r($_POST);
//print_r($chapterids);
//exit;
	
	$cid = $chapterids[0];
	
	// download info
	$query = "SELECT id FROM #__download WHERE cartoon_id='$cid' AND user_id={$_user->id} AND flag=1";
	$database->setQuery($query);
	$download_id = $database->loadResult();
	
	$obj_download = new mosDownload($database);
	if( $download_id )
		$obj_download->load($download_id);
	else {
		$obj_download->cartoon_id = $cid;
		$obj_download->user_id = $_user->id;
		$obj_download->flag = 1;
	}
	$obj_download->download_date = date("Y-m-d H:i:s");
	$obj_download->store();

	// cartoon info
	$obj = new mosCartoon($database);
	$obj->load($cid);
	$obj->down_num++;
	if( $obj->name ) {
		$obj->store();
	}
	
	// parent cartoon info
	$obj_parent = new mosCartoon($database);
	$obj_parent->load($obj->parent);
	$obj_parent->down_num++;
	if( $obj_parent->name ) {
		$obj_parent->store();
	}
	
	$fileinfo = unserialize($obj->file_info);
	
	// 실제 파일명 또는 경로 
	$file = $Config_cartoon_path ."/". $fileinfo['name']; 
	// 다운받아 하드에 저장될때의 파일이름) 
	$dnurl = $fileinfo['name'];

	$dn = $dn ? "0" : "1";		// 1 이면 다운 0 이면 브라우져가 인식하면 화면에 출력 
	$dn_yn = ($dn) ? "attachment" : "inline";

	$bin_txt = "0"; 
	$bin_txt = ($bin_txt) ? "r" : "rb"; 

	// application/octet-stream
	$ext_array 	= explode(".",$dest);
	$index 		= count($ext_array) - 1;
	$ext 		= trim($ext_array[$index]);
	$content_type = "application/octet-stream";
	switch ($ext) {
		case "zip":
			$content_type = "applicatioin/zip";
			break;
		default: 
			$content_type = "application/force-download";
			break;
	}

	header("Content-type: ".$content_type);
	header("Content-Length: ".filesize("$file"));	// 이부분을 넣어 주어야지 다운로드 진행 상태가 표시 됩니다. 
	header("Content-Disposition:$dn_yn;filename=$dnurl;"); 
	header("Pragma: cache"); 
	header("Expires: 0");

	if (is_file($file)) 
	{ 
		$fp = fopen($file, $bin_txt); 
		if (!fpassthru($fp)) // 서버부하를 줄이려면 print 나 echo 또는 while 문을 리용한 기타 보다 이방법이... 
			fclose($fp); 
	} 
	else 
	{ 
		echo "Does not search cartoon file."; 
	}
	exit;
}

function save_sharing( $id ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;
	global $Config_cartoon_path;

	$query = "SELECT id FROM #__sharing WHERE cartoon_id=$cid AND user_id={$_user->id}";
	$database->setQuery($query);
	$sharing_id = $database->loadResult();
	
	$row = new mosSharing($database);
	if( $sharing_id ) {
		$row->load($sharing_id);
	} else {
		$row->cartoon_id = $cid;
		$row->user_id = $_user->id;
	}
	$row->content = $_POST['content_sharing'];
	$row->add_date = date("Y-m-d H:i:s");
	if( $row->store() )
		echo "success";
	else
		echo "failure";
	exit;
}

function estimate_dialog( $id ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;
	global $Config_cartoon_path;
	
	$query = "SELECT * FROM #__estimate WHERE cartoon_id=$cid AND user_id={$_user->id}";
	$database->setQuery($query);
	$row = null;
	$database->loadObject($row);
	
	HTML_library::estimate_dialog($row);
}

function amount_download_dialog( $id ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;
	global $Config_cartoon_path;
	
	$query = "SELECT * FROM #__cartoon WHERE parent='$cid' ORDER BY ordering";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	
	HTML_library::amount_download_dialog($rows);
}

function save_estimate( $id ) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $subtype, $task, $cid, $_user;
	global $Config_cartoon_path;

	$result = "success";
	$score = mosGetParam($_POST, 'mark', 0);
	$content = mosGetParam($_POST, 'estimate_content', '');
	
	$query = "SELECT id FROM #__estimate WHERE cartoon_id='$cid' AND user_id='{$_user->id}'";
	$database->setQuery($query);
	$eid = $database->loadResult();
	
	$row = new mosEstimate($database);
	if( $eid ) {
		$row->load($eid);
	} else {
		$row->cartoon_id = $cid;
		$row->user_id = $_user->id;
	}
	if( $content )
		$row->content = $content;
	if( $score )
		$row->mark = $score;
	$row->add_date = date("Y-m-d H:i:s");
	if( !$row->store() )
		echo "failure";
		
	// get average mark of cartoon
	$query = "SELECT AVG(score) FROM #__estimate WHERE cartoon_id='$cid'";
	$database->setQuery($query);
	$score = $database->loadResult();
	
	$row_cartoon = new mosCartoon($database);
	$row_cartoon->load($cid);
	$row_cartoon->score = $score;
	if( $row_cartoon->name ) {
		if (!$row_cartoon->store()) {
			$result = "failure";
		}
	}

	echo $result;
	exit;
}
?>
