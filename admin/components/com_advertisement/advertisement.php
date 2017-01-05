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

$id 	= mosGetParam($_REQUEST, 'id', 0);

switch ($task) {
	case 'save':
		saveAd( $task );
		break;
		
	case "cartoonList":
		showCartoon($option);
		break;

	default:
		editAd( $option );
		break;
}

function editAd( $option='advertisement' ) {
	global $database, $mainframe;
	$lists = array();
	
	$query = "SELECT * FROM #__advertisement LIMIT 0, 1";
	$database->setQuery($query);
	$row = null;
	$database->loadObject($row);
	
	$query = "SELECT id, name FROM #__cartoon WHERE id='{$row->cartoon_id1}' OR id='{$row->cartoon_id2}' OR id='{$row->cartoon_id3}'";
	$database->setQuery($query);
	$rows_tmp = $database->loadObjectList();
	$rows_cartoon = array();
	foreach( $rows_tmp as $row_tmp ) {
		$rows_cartoon[$row_tmp->id] = $row_tmp;
	}
	
	HTML_Ad::editAd( $row, $rows_cartoon, $option );
}

function saveAd( $task ) {
	global $database, $Config_ad_path;

	$row = new mosAdvertisement( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->name = trim( $row->name );
	if( $_FILES['ad_file1']['name'] && $_FILES['ad_file2']['name'] && $_FILES['ad_file3']['name'] ) {
		if ( strstr($_FILES['ad_file1']['type'], 'image') == FALSE || strstr($_FILES['ad_file2']['type'], 'image') == FALSE || strstr($_FILES['ad_file3']['type'], 'image') == FALSE ) {
			echo "<script> alert('File format is not image!'); window.history.go(-1); </script>\n";
			exit();
		}
		$file_path1 = $_FILES['ad_file1']['tmp_name'];
		$file_type1 = substr($_FILES['ad_file1']['type'], strpos($_FILES['ad_file1']['type'], '/')+1);
		$file_path2 = $_FILES['ad_file2']['tmp_name'];
		$file_type2 = substr($_FILES['ad_file2']['type'], strpos($_FILES['ad_file2']['type'], '/')+1);
		$file_path3 = $_FILES['ad_file3']['tmp_name'];
		$file_type3 = substr($_FILES['ad_file3']['type'], strpos($_FILES['ad_file3']['type'], '/')+1);
	}

	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if($file_type1) {
		$row_tmp = new mosAdvertisement( $database );
		$row_tmp->id = $row->id;
		$row_tmp->file_name1 = "1.". $file_type1;
		$row_tmp->file_name2 = "2.". $file_type2;
		$row_tmp->file_name3 = "3.". $file_type3;
		$row_tmp->store();
		if(is_file($Config_ad_path ."/". $row_tmp->file_name1))
			unlink($Config_ad_path ."/". $row_tmp->file_name1);
		copy($file_path1, $Config_ad_path ."/". $row_tmp->file_name1);
		if(is_file($Config_ad_path ."/". $row_tmp->file_name2))
			unlink($Config_ad_path ."/". $row_tmp->file_name2);
		copy($file_path2, $Config_ad_path ."/". $row_tmp->file_name2);
		if(is_file($Config_ad_path ."/". $row_tmp->file_name3))
			unlink($Config_ad_path ."/". $row_tmp->file_name3);
		copy($file_path3, $Config_ad_path ."/". $row_tmp->file_name3);
	}

	switch ( $task ) {
		case 'save':
		default:
			$msg = 'Successfully Saved Advertisement: '. $row->name;
			mosRedirect( 'index.php?option=com_advertisement', $msg );
			break;
	}
}

function showCartoon( $option ) {
	global $database, $mainframe, $Config_list_limit;

	$limit 			= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $Config_list_limit ) );
	$limitstart 	= intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search 		= $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$filter_topic	= $mainframe->getUserStateFromRequest( "filter_topic{$option}", 'filter_topic', 0 );
	$filter_class	= $mainframe->getUserStateFromRequest( "filter_class{$option}", 'filter_class', 0 );
	$where 			= array();
	$lists 			= array();
	$index			= mosGetParam($_REQUEST, 'index', 0);

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
	
	HTML_ad::showCartoon( $rows, $pageNav, $search, $option, $lists, $index );
}
?>
