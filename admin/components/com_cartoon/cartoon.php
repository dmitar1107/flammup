<?php
/**
* @version $Id: cartoon.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage cartoon
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

$type 	= mosGetParam($_REQUEST, 'type', 'book');
if($type == 'chapter')
	$parent = mosGetParam($_REQUEST, 'parent', 0);
$id 	= mosGetParam($_REQUEST, 'id', 0);
$cid 	= josGetArrayInts( 'cid' );

switch( $type ) {
	case 'chapter':
		switch ($task) {
			case 'new':
				editChapter( 0, $option);
				break;

			case 'edit':
				editChapter( intval( $cid[0] ), $option );
				break;

			case 'editA':
				editChapter( $id, $option );
				break;

			case 'save':
			case 'apply':
				saveChapter( $task );
				break;

			case 'remove':
				removeCartoon( $cid, $option );
				break;

			case 'cancel':
				cancelCartoon( $option );
				break;

			case 'orderup':
			case 'orderdown':
				orderCartoon( intval( $cid[0] ), ($task == 'orderup' ? -1 : 1), $option );
				break;
			case 'saveorder':
				saveOrder( $cid );
				break;

			default:
				showChapter( $option );
				break;
		}
		break;
	
	case 'book':
	default:
		switch ($task) {
			case 'new':
				editCartoon( 0, $option);
				break;

			case 'edit':
				editCartoon( intval( $cid[0] ), $option );
				break;

			case 'editA':
				editCartoon( $id, $option );
				break;

			case 'save':
			case 'apply':
				saveCartoon( $task );
				break;

			case 'remove':
				removeCartoon( $cid, $option );
				break;

			case 'cancel':
				cancelCartoon( $option );
				break;

			case 'recommend':
			case 'unrecommend':
				recommendCartoon( $cid, ($task == 'recommend'), $option );
				break;

			case 'orderup':
			case 'orderdown':
				orderCartoon( intval( $cid[0] ), ($task == 'orderup' ? -1 : 1), $option );
				break;
			case 'saveorder':
				saveOrder( $cid );
				break;

			default:
				showCartoon( $option );
				break;
		}
		break;
}

function showCartoon( $option ) {
	global $database, $mainframe, $Config_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$filter_topic	= $mainframe->getUserStateFromRequest( "filter_topic{$option}", 'filter_topic', 0 );
	$filter_class	= $mainframe->getUserStateFromRequest( "filter_class{$option}", 'filter_class', 0 );
	$where 			= array();
	$lists			= array();

	$where[] = 'c.parent = 0';
	if ( $filter_topic ) {
		$where[] = "c.topic_id = '$filter_topic'";
	}
	if ( $filter_class ) {
		$where[] = "c.class_id = '$filter_class'";
	}
	if (isset( $search ) && $search!= "") {
		$searchEscaped = $database->getEscaped( trim( strtolower( $search ) ) );
		$where[] = "c.name LIKE '%$searchEscaped%'";
	}

	$query = "SELECT COUNT(c.id) FROM #__cartoon AS c"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	;
	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT c.*, a.name authorname, t.name topicname, cf.name classname FROM #__cartoon AS c"
	. "\n LEFT JOIN #__author AS a ON a.id = c.author_id"
	. "\n LEFT JOIN #__topic AS t ON t.id = c.topic_id"
	. "\n LEFT JOIN #__classification AS cf ON cf.id = c.class_id"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY c.class_id, c.ordering"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	$query = "SELECT id, name FROM #__topic";
	$database->setQuery($query);
	$rows_topic = $database->loadObjectList();
	$tmp_topic[] = mosHTML::makeOption(0, '- select topic -', 'id', 'name');
	$rows_topic = array_merge($tmp_topic, $rows_topic);
	$lists['topic'] = mosHTML::selectList($rows_topic, 'filter_topic', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $filter_topic);

	$query = "SELECT id, name FROM #__classification";
	$database->setQuery($query);
	$rows_class = $database->loadObjectList();
	$tmp_class[] = mosHTML::makeOption(0, '- select classification -', 'id', 'name');
	$rows_class = array_merge($tmp_class, $rows_class);
	$lists['classification'] = mosHTML::selectList($rows_class, 'filter_class', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'name', $filter_class);
	
	HTML_cartoon::showCartoon( $rows, $pageNav, $search, $option, $lists );
}

function showChapter( $option ) {
	global $database, $mainframe, $Config_list_limit, $parent;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search_chapter = $mainframe->getUserStateFromRequest( "search_chapter{$option}", 'search_chapter', '' );
	$where 			= array();
	$lists			= array();

	$where[] = "c.parent = $parent";
	if (isset( $search_chapter ) && $search_chapter!= "") {
		$searchEscaped = $database->getEscaped( trim( strtolower( $search_chapter ) ) );
		$where[] = "c.name LIKE '%$searchEscaped%'";
	}

	$query = "SELECT COUNT(c.id) FROM #__cartoon AS c"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	;
	$database->setQuery( $query );
	$total = $database->loadResult();
	
	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "SELECT c.*, a.name authorname, t.name topicname, cf.name classname FROM #__cartoon AS c"
	. "\n LEFT JOIN #__author AS a ON a.id = c.author_id"
	. "\n LEFT JOIN #__topic AS t ON t.id = c.topic_id"
	. "\n LEFT JOIN #__classification AS cf ON cf.id = c.class_id"
	. (count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "")
	. "\n ORDER BY c.class_id, c.ordering"
	. "\n LIMIT {$pageNav->limitstart}, {$pageNav->limit}"
	;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	$row_parent = new mosCartoon($database);
	$row_parent->load($parent);
	
	HTML_cartoon::showChapter( $rows, $row_parent, $pageNav, $search_chapter, $option, $lists );
}

/**
 * Edit the Cartoon
 * @param int The cartoon ID
 * @param string The URL option
 */
function editCartoon( $uid='0', $option='cartoon' ) {
	global $database, $mainframe, $Config_state, $Config_region;
	$lists = array();

	$row = new mosCartoon( $database );
	// load the row from the db table
	$row->load( (int)$uid );

	$query = "SELECT id, CONCAT(name, ' : ', country) as name FROM #__author ORDER BY id";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	$tmp = array();
	$tmp[] = mosHTML::makeOption(0, '- select author -', 'id', 'name');
	$rows = array_merge($tmp, $rows);
	$lists['author'] = mosHTML::selectList( $rows, 'author_id', 'class="inputbox" size="1"', 'id', 'name', $row->author_id );
	
//	$query = "SELECT * FROM #__topic ORDER BY id";
//	$database->setQuery($query);
//	$rows = $database->loadObjectList();
//	$tmp = array();
//	$tmp[] = mosHTML::makeOption(0, '- select topic -', 'id', 'name');
//	$rows = array_merge($tmp, $rows);
//	$lists['topic'] = mosHTML::selectList( $rows, 'topic_id', 'class="inputbox" size="1"', 'id', 'name', $row->topic_id );
	
	$query = "SELECT * FROM #__classification ORDER BY id";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	$tmp = array();
	$tmp[] = mosHTML::makeOption(0, '- select classification -', 'id', 'name');
	$rows = array_merge($tmp, $rows);
	$lists['class'] = mosHTML::selectList( $rows, 'class_id', 'class="inputbox" size="1"', 'id', 'name', $row->class_id );
	
	$rows_state = array();
	foreach($Config_state as $k => $v)
		$rows_state[] = mosHTML::makeOption($k, $v, 'id', 'name');
	$lists['state'] = mosHTML::selectList( $rows_state, 'state', 'class="inputbox" size="1"', 'id', 'name', $row->state );
	
	$rows_region = array();
	foreach($Config_region as $k => $v)
		$rows_region[] = mosHTML::makeOption($k, $v, 'id', 'name');
	$lists['region'] = mosHTML::selectList( $rows_region, 'region', 'class="inputbox" size="1"', 'id', 'name', $row->region );

	if($uid) {
		(!$row->parent) ? 0 : $row->parent;
		$query = "SELECT ordering value, name text FROM #__cartoon WHERE parent = '{$row->parent}' AND class_id = '{$row->class_id}' ORDER BY ordering";
		$order = mosGetOrderingList( $query );
		$lists['ordering'] = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
	} else {
		$_CMN_NEW_ITEM_LAST = 'New Items default to the first place. Ordering can be changed after this Item is saved.';
		$lists['ordering'] = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. $_CMN_NEW_ITEM_LAST;
	}

	HTML_cartoon::editCartoon( $row, $lists, $option, $uid );
}

/**
 * Edit the Chapter of Cartoon
 * @param int The cartoon ID
 * @param string The URL option
 */
function editChapter( $uid='0', $option='cartoon' ) {
	global $database, $mainframe, $Config_state, $Config_region, $parent;
	$lists = array();

	$row = new mosCartoon( $database );
	// load the row from the db table
	$row->load( (int)$uid );
	
	$query = "SELECT id, CONCAT(name, ' : ', country) as name FROM #__author ORDER BY id";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	$tmp = array();
	$tmp[] = mosHTML::makeOption(0, '- select author -', 'id', 'name');
	$rows = array_merge($tmp, $rows);
	$lists['author'] = mosHTML::selectList( $rows, 'author_id', 'class="inputbox" size="1"', 'id', 'name', $row->author_id );
	
//	$query = "SELECT * FROM #__topic ORDER BY id";
//	$database->setQuery($query);
//	$rows = $database->loadObjectList();
//	$tmp = array();
//	$tmp[] = mosHTML::makeOption(0, '- select topic -', 'id', 'name');
//	$rows = array_merge($tmp, $rows);
//	$lists['topic'] = mosHTML::selectList( $rows, 'topic_id', 'class="inputbox" size="1"', 'id', 'name', $row->topic_id );
	
	$query = "SELECT * FROM #__classification ORDER BY id";
	$database->setQuery($query);
	$rows = $database->loadObjectList();
	$tmp = array();
	$tmp[] = mosHTML::makeOption(0, '- select classification -', 'id', 'name');
	$rows = array_merge($tmp, $rows);
	$lists['class'] = mosHTML::selectList( $rows, 'class_id', 'class="inputbox" size="1"', 'id', 'name', $row->class_id );
	
	$rows_state = array();
	foreach($Config_state as $k => $v)
		$rows_state[] = mosHTML::makeOption($k, $v, 'id', 'name');
	$lists['state'] = mosHTML::selectList( $rows_state, 'state', 'class="inputbox" size="1"', 'id', 'name', $row->state );
	
	$rows_region = array();
	foreach($Config_region as $k => $v)
		$rows_region[] = mosHTML::makeOption($k, $v, 'id', 'name');
	$lists['region'] = mosHTML::selectList( $rows_region, 'region', 'class="inputbox" size="1"', 'id', 'name', $row->region );

	if($uid) {
		$query = "SELECT ordering value, name text FROM #__cartoon WHERE parent = '{$row->parent}' ORDER BY ordering";
		$order = mosGetOrderingList( $query );
		$lists['ordering'] = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
	} else {
		$_CMN_NEW_ITEM_LAST = 'New Items default to the last place. Ordering can be changed after this Item is saved.';
		$lists['ordering'] = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. $_CMN_NEW_ITEM_LAST;
	}
	
	$file_info = unserialize($row->file_info);
	$row->file_name = $file_info['name'];

	$row_parent = new mosCartoon($database);
	$row_parent->load($parent);
	
	HTML_cartoon::editChapter( $row, $row_parent, $lists, $option, $uid );
}

function saveCartoon( $task ) {
	global $database, $type;
	global $Config_live_site, $Config_sitename, $Config_cartoon_path, $Config_cartoon_thumbnail_path;
	
	$row = new mosCartoon( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	// pinyin alphabet
	require_once( $GLOBALS['Config_absolute_path'] . '/includes/pinyin.php' );
	$row->alphabet = substr( convertChineseToPinyin(mb_substr(trim($row->name), 0, 3)), 0, 1 );
	
	if(!$row->id)
		$row->public_date = date("Y-m-d H:i:s");
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
	$row->updateOrder( "parent='0' AND class_id='{$row->class_id}'" );
	
	$row_tmp = new mosCartoon( $database );
	$row_tmp->id = $row->id;
	// thumbnail
	if( $_FILES['thumbnail']['name'] ) {
		$row_tmp->thumbnail_name = $row->id .".". $file_type;
		copy($file_path, $Config_cartoon_thumbnail_path ."/". $row_tmp->thumbnail_name);
	}
	
	if( $_FILES['imagezip']['name'] || $_FILES['thumbnail']['name'] )
		$row_tmp->store();

	switch ( $task ) {
		case 'apply':
			$msg = 'Successfully Saved changes to Cartoon: '. $row->name;
			mosRedirect( "index.php?option=com_cartoon&type={$type}&task=editA&hidemainmenu=1&id={$row->id}", $msg );
			break;

		case 'save':
		default:
			$msg = 'Successfully Saved Cartoon: '. $row->name;
			mosRedirect( "index.php?option=com_cartoon&type={$type}", $msg );
			break;
	}
}


function saveChapter( $task ) {
	global $database, $type, $parent;
	global $Config_live_site, $Config_sitename, $Config_cartoon_path, $Config_cartoon_thumbnail_path;

	$row_parent = new mosCartoon( $database );
	if (!$row_parent->load( $parent )) {
		echo "<script> alert('".$row_parent->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row_parent->public_date 	= date("Y-m-d H:i:s");
	$row_parent->store();
	
	$row = new mosCartoon( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->author_id 	= $row_parent->author_id;
	$row->class_id 		= $row_parent->class_id;
	$row->region 		= $row_parent->region;
	$row->state 		= $row_parent->state;
	$row->recommended 	= $row_parent->recommended;
	$row->public_date 	= date("Y-m-d H:i:s");
	
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->updateOrder( "parent='$parent' AND class_id='{$row->class_id}'" );
	
	$row_tmp = new mosCartoon( $database );
	$row_tmp->id = $row->id;
	// cartoon zip file
	if( $_FILES['imagezip']['name'] ) {
		$extension = substr(strrchr($_FILES['imagezip']['name'], '.'), 1);
		if( strtolower($extension) != "zip" ) {
			echo "<script> alert('Sorry! Only Support zip format. Please reattach zip file.'); window.history.go(-1); </script>\n";
			exit();
		}

		$dat_file_name = $row->id . ".dat";
		$dat_file = $Config_cartoon_path ."/". $dat_file_name;
		$fp = fopen($dat_file, 'w');
		$des_path = $Config_cartoon_path ."/ziptmp/". time() .'_'. rand() . '/';
		mkdir($des_path);
		$zip = new ZipArchive;
		$res = $zip->open($_FILES['imagezip']['tmp_name']);
		if ($res === TRUE) {
			$zip->extractTo($des_path);
			$zip->close();
		}
		$all = opendir($des_path);
		$file_num = 0;
		$arr_file = array();
		while ($file = readdir($all)) {
			if( isImageFile($file) == false )
				continue;
			$arr_file[] = $file;
		}
		sort($arr_file);
		foreach($arr_file as $file) {
			$path = $des_path . $file;
			$file_num++;
			fwrite($fp, sprintf("FILE-BEGIN:%03d", $file_num));
			$file_content = base64_encode(file_get_contents($path));
			$file_size = strlen($file_content);
			fwrite($fp, $file_content, $file_size);
			fwrite($fp, sprintf("FILE-END:%03d", $file_num));
			echo "$file, $filetype, <br />";
		}
		closedir($all);
		rrmdir($des_path);
		fclose($fp);

		$file_info = array();
		$file_info['name'] = $dat_file_name;
		$file_info['size'] = filesize($dat_file);
		$file_info['num'] = $file_num;
		$row_tmp->file_info = serialize($file_info);
	}
	
	if( $_FILES['imagezip']['name'] || $_FILES['thumbnail']['name'] )
		$row_tmp->store();

	switch ( $task ) {
		case 'apply':
			$msg = 'Successfully Saved changes to Chapter: '. $row->name;
			mosRedirect( "index.php?option=com_cartoon&type={$type}&task=editA&hidemainmenu=1&id={$row->id}&parent={$parent}", $msg );
			break;

		case 'save':
		default:
			$msg = 'Successfully Saved Chapter: '. $row->name;
			mosRedirect( "index.php?option=com_cartoon&type={$type}&parent={$parent}", $msg );
			break;
	}
}

/**
* Cancels an edit operation
* @param option component option to call
*/
function cancelCartoon( $option ) {
	global $type, $parent;
	
	if($type == 'book')
		$path = "index.php?option={$option}&type={$type}&task=view";
	else
		$path = "index.php?option={$option}&type={$type}&task=view&parent={$parent}";
	mosRedirect( $path );
}

function removeCartoon( $cid, $option ) {
	global $database, $type, $parent;
	
	if (!is_array( $cid ) || count( $cid ) < 1) {
		echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
		exit;
	}

	$obj = new mosCartoon( $database );
	foreach ($cid as $id) {
		// delete cartoon
		$obj->delete( $id );
		$msg = $obj->getError();
	}

	if($type == 'book')
		$path = "index.php?option={$option}&type={$type}";
	else
		$path = "index.php?option={$option}&type={$type}&parent={$parent}";
	mosRedirect( $path );
}

function isImageFile($file) {
	$ext = strtolower( substr( strrchr($file, '.'), 1 ) );
	$ext_array = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
	if( in_array($ext, $ext_array) )
		return true;
	else
		return false;
}

function viewThumnailImage( $option='cartoon', $uid ) {
	global $database;
	global $Config_live_site, $Config_sitename;
	
	$row = new mosCartoon( $database );
	// load the row from the db table
	$row->load($uid);
	$file_info = unserialize($row->thumbnail_info);
	$file_type = $file_info['type'];
	$file_type = strtolower(substr($file_type, strpos($file_type, '/')));
//	$im = imagecreatefromstring(base64_decode($row->thumbnail));
	switch($file_type) {
		case "gif": $ctype="image/gif"; break; 
		case "png": $ctype="image/png"; break; 
		case "jpeg": 
		case "jpg": $ctype="image/jpg"; break; 
		case "bmp": $ctype="image/bmp"; break; 
	}
	header("Content-Type: $ctype");
	echo base64_decode($row->thumbnail); exit;
//	switch($file_type) {
//		case "gif": imagegif($im); break; 
//		case "png": imagepng($im); break; 
//		case "jpeg": 
//		case "jpg": imagejpeg($im); break; 
//		case "bmp": image2wbmp($im); break; 
//	}
//	imagedestroy($im);
//	exit;
}

function rrmdir($dir) { 
	if (is_dir($dir)) { 
		$objects = scandir($dir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
			} 
		} 
		reset($objects); 
		rmdir($dir); 
	} 
}

/**
* Moves the order of a record
* @param integer The unique id of record
* @param integer The increment to reorder by
*/
function orderCartoon( $uid, $inc, $option ) {
	global $database, $type;
	$parent = mosGetParam($_REQUEST, 'parent', 0);
	
	$row = new mosCartoon( $database );
	$row->load( (int)$uid );
	if ($type == 'book') {
		$where = "parent = 0 AND class_id = " . $database->Quote( $row->class_id );
	} else {
		$where = "parent = $parent AND class_id = " . $database->Quote( $row->class_id );
	}

	$row->move( $inc, $where );
	
	if($type == 'book')
		$path = "index.php?option={$option}&type={$type}&task=view";
	else
		$path = "index.php?option={$option}&type={$type}&task=view&parent={$parent}";
	mosRedirect( $path );
}

function saveOrder( &$cid ) {
	global $database, $option, $type, $parent;

	$total		= count( $cid );
	$order 		= josGetArrayInts( 'order' );
	
	$row 		= new mosCartoon( $database );
	$conditions = array();

	// update ordering values
	for( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			$condition = "class_id = " . $database->Quote( $row->class_id ) . ($type == 'book') ? " AND parent = 0" : " AND parent = $parent";
			$found = false;
			foreach ( $conditions as $cond )
				if ($cond[1]==$condition) {
					$found = true;
					break;
				} // if
			if (!$found) $conditions[] = array($row->id, $condition);
		} // if
	} // for

	// execute updateOrder for each group
	foreach ( $conditions as $cond ) {
		$row->load( $cond[0] );
		$row->updateOrder( $cond[1] );
	} // foreach
	
	$msg 	= 'New ordering saved';
	if($type == 'book')
		$path = "index.php?option={$option}&type={$type}&task=view";
	else
		$path = "index.php?option={$option}&type={$type}&task=view&parent={$parent}";
	mosRedirect( $path, $msg );
} // saveOrder

function recommendCartoon( $cid=null, $recommend=1, $option ) {
	global $database, $my, $type;

	if (count( $cid ) < 1) {
		$action = $recommend ? 'recommend' : 'unrecommend';
		echo "<script> alert('Select a cartoon to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );

	$query = "UPDATE #__cartoon"
	. "\n SET recommended = " . (int) $recommend
	. "\n WHERE ( $cids )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row = new mosCartoon( $database );
		$row->checkin( $cid[0] );
	}

	if($type == 'book')
		$path = "index.php?option={$option}&type={$type}&task=view";
	else
		$path = "index.php?option={$option}&type={$type}&task=view&parent={$parent}";
	mosRedirect( $path, $msg );
}
?>
