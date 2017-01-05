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
	case 'browseauctions':
		/* for ajax */
		$content 	= mosGetParam( $_REQUEST, 'content', '2' );
		switch($content) {
			case "1":
				$cpage = mosGetParam( $_REQUEST, 'cpage', '1' );
				getAuctionDetail($cpage);
			break;
			case "2":
				browseauctions();
			break;
		}
		break;
}

/**
* menu for seller
*/

function myauction() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $_user;
	$query = "SELECT * FROM #__menu order by ordering ASC";
	$database->setQuery($query);
	$allmenus = $database->loadObjectList();
	HTML_seller::seller_menu($allmenus);
}
function browseauctions() {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $_user,$Config_live_site;
	$date = date("Y-m-d H:i:s");
	$_SESSION["notify"] = "Welcome, you just earned 700 flames";
	$query = "SELECT * FROM #__auction where end_time > '".$date."' order by start_time ASC";
	$database->setQuery($query);
	$auctions = $database->loadObjectList();

	$pages = ceil(count($auctions)/3);

	$cpage = isset($_GET['cpage']) ? $_GET['cpage'] : 1;
	$cpage = $cpage < 1 ? 1 : $cpage;
	$cpage = $cpage > $pages ? $pages : $cpage;

	HTML_buyer::browseauctions($cpage, $pages);
}
function getAuctionDetail($cpage) {
	global $database, $mainframe, $Config_list_limit, $Config_ad_path_site, $_user,$Config_live_site;
	$pages = mosGetParam( $_REQUEST, 'pages', '' );
	$aaa = round($pages/3);
	$ccc = round(($cpage-1)/3);

	if ($aaa == $ccc){
		
		$start = 0;
	}
	else {
		$start = ($cpage-1)*3;
	}
	
	$date = date("Y-m-d H:i:s");
	$query = "select * from #__auction where end_time > '".$date."' order by start_time limit $start,3";

	$database->setQuery($query);
	$auctions = $database->loadObjectList();
	$st_dt = new DateTime($date);

		echo "<ul>";
		for ($i = 0; $i < count($auctions); $i++) {
			$ed_dt = new DateTime($auctions[$i]->end_time);
			$interval = date_diff($st_dt, $ed_dt);
			$time = $interval->format('%y-%m-%d %h:%i:%s');
			$time = getTimeType($time);
			echo "<li class='b_li'><a class='title'>".$auctions[$i]->title.",".$auctions[$i]->location."</a><div class='list_content'></div><a class='timer' id='timer' href='#'>".$time['hour'].":".$time['minute'].":".$time['second']."</a></li>";					
		}
		echo "</ul>";
?>
		<script type="text/javascript">
//		$(document).ready(function(){
//			var Timer  = '';
//			var selecter = 0;
//			var Main =0;
//			bring(selecter);
//			$("ul a.timer").each( function (index) {
//				
//			});
//		});
//		function bring ( selecter )
//		{	
//			$('ul:eq(' + selecter + ')').stop().animate({
//				opacity  : '1.0',
//				height: '200px'
//				
//			},300,function(){
//				
//				if(selecter < 1)
//				{
//					clearTimeout(Timer); 
//				}
//			});
//			selecter++;
//			var Func = function(){ bring(selecter); };
//			Timer = setTimeout(Func, 10);
//		}

		</script>
<?php
}
		function getTimeType ($time) {
			$date = explode(" ",$time);
			$date_Val = explode("-", $date[0]);
			$hour = $date_Val[0]*365*24 + $date_Val[1]*30*24 + $date_Val[2]*24;
			$time_val = explode(":", $date[1]);
			$hour = $time_val[0]+$hour;
			$min = $time_val[1];
			$sec = $time_val[2];
			$time = array();
			$time["hour"] = $hour < 10 ? "0".$hour : $hour;
			$time["minute"] = $min < 10 ? "0".$min : $min;
			$time["second"] = $sec < 10 ? "0".$sec : $sec;
			return $time;
		}
	?>


