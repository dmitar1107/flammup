<?php

/**
* @version $Id: users.html.php 10002 2008-02-08 10:56:57Z willebil $
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

/**
* @package Joomla
* @subpackage Users
*/
class HTML_seller {
	function seller_menu ($menus) {
		
	}
	function apporve_recommend_list_class( &$rows, $class_id, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid, $_device_type;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;

		if( $class_id == "editor" ) {
			foreach ($rows as $row) {
				$a_star_num = floor($row->score/2);
				$b_star_num = 5 - $a_star_num;
				$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
				?>
				<li class='frieds'>
					<a href="<?php echo $link_path; ?>" class='comlist'>
						<img class='vertical_image'  src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' />
						<h3 style='width:50%;'><?php echo $row->name; ?></h3>
						<div class="ui-grid-a" style='width:100%;margin-top:7px;'>
							<div class="ui-block-a"><p class='full_width' style='margin:0px;'><?php echo $row->description; ?></p></div>
						</div><!-- /grid-b -->
						<p class="ui-li-aside" style='width:50%;margin:0px;'>
						<?php
						for($a = 1; $a <= $a_star_num; $a++)
							echo "<img src='$Config_live_site/templates/$Config_cur_template/images/a_star.png' alt='Go' />";
						for($b = 1; $b <= $b_star_num; $b++)
							echo "<img src='$Config_live_site/templates/$Config_cur_template/images/b_star.png' alt='Go' />";
						?>
						</p>
					</a>
				</li>
				<?php
			}
		}
		else if( $class_id == "lastupdate" ) {
			foreach ($rows as $row) {
				$a_star_num = floor($row->score/2);
				$b_star_num = 5 - $a_star_num;
				$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
				?>
				<li class='frieds'>
					<a href="<?php echo $link_path; ?>" class='comlist'>
						<img class='vertical_image'  src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' />
						<h3 style='width:50%;'><?php echo $row->name; ?></h3>
						<div class="ui-grid-a" style='width:100%;margin-top:7px;'>
							<div class="ui-block-a"><p class='full_width' style='margin:0px;'>更新至第<?php echo $row->count_child; ?>话</p></div>
							<div class="ui-block-b" align='right' style='color:#292829;' >
								<p><?php echo $row->hour; ?></p>
							</div>
						</div><!-- /grid-b -->
						<p class="ui-li-aside" style='width:50%;margin:0px;'>
						<?php
						for($a = 1; $a <= $a_star_num; $a++)
							echo "<img src='$Config_live_site/templates/$Config_cur_template/images/a_star.png' alt='Go' />";
						for($b = 1; $b <= $b_star_num; $b++)
							echo "<img src='$Config_live_site/templates/$Config_cur_template/images/b_star.png' alt='Go' />";
						?>
						</p>
					</a>
				</li>
				<?php
			}
		}
		else {
			foreach( $rows as $row ) {
				$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
				?>
				<li class='recommend_li' >
				<!--a href="<?php echo $link_path; ?>" class='comlist'-->
					<h3 class='recommend_h3'><?php echo $row->user_name; ?>(<?php echo $row->user_id; ?>)</h3>
					<p class='recommend_p1'><font>推荐</font><font class='p1_font' style=''><?php echo $row->name; ?></font></p>
					<p class='recommend_p2' style=''><font class='p2_font'><?php echo $row->content; ?></font></p>
					<p class="ui-li-aside" style='width:80px;'>
						<img class='user_recommen_img' src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' alt='recommend' link='<?php echo $link_path; ?>' onclick='go_link("<?php echo $link_path; ?>");' />
					</p>

					<div align='left'>
						<div class='approve_recommen'>
							<input type="button" data-inline="true" id='<?php echo $row->id; ?>' data-theme='n' data-icon='finger' data-corners="false"  ajaxinput="true" class='approve_recommen' value='顶' />
						</div>
						<p class='approve_recommen_data'>已有<?php echo $row->count; ?>人支持</p>
					</div>
				<!--/a-->
				</li>
				<?php
			}
		}
		
		if( $ajaxcall )
			exit;
	}
	
	function apporve_recommend_list( &$rows, $pageNav, $row_ad, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid, $class_id, $_device_type;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		?>
		<div data-role="page" id="list_<?php echo $class_id; ?>_pagediv">
		<link rel='stylesheet' href='<?php echo $Config_live_site; ?>/templates/mogu_default/css/wowslider_style.css' type='text/css' media='all'>
		<form name='listform' id='listform' action="index.php?option=<?php echo $option; ?>" method="post">
		<?php HTML_library::type_button_menu(); ?>
		<?php
		if($row_ad->file_name1) {
			$ad_path1 = $Config_ad_path_site ."/". $row_ad->file_name1;
			$link_path1 = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row_ad->cartoon_id1}";
		}
		if($row_ad->file_name2) {
			$ad_path2 = $Config_ad_path_site ."/". $row_ad->file_name2;
			$link_path2 = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row_ad->cartoon_id2}";
		}
		if($row_ad->file_name3) {
			$ad_path3 = $Config_ad_path_site ."/". $row_ad->file_name3;
			$link_path3 = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row_ad->cartoon_id3}";
		}
		if( $ad_path1 ) {
			?>
			<div id="wowslider_container_<?php echo $class_id; ?>" class="wowslider_container">
				<div class="ws_images">
					<ul>
						<li><a href="<?php echo $link_path1; ?>"><img src="<?php echo $ad_path1; ?>" alt="<?php echo $row_ad->c1_name; ?>" id="wows1_0"/></a><?php echo $row_ad->c1_description; ?></li>
						<li><a href="<?php echo $link_path2; ?>"><img src="<?php echo $ad_path2; ?>" alt="<?php echo $row_ad->c2_name; ?>" id="wows1_0"/></a><?php echo $row_ad->c2_description; ?></li>
						<li><a href="<?php echo $link_path3; ?>"><img src="<?php echo $ad_path3; ?>" alt="<?php echo $row_ad->c3_name; ?>" id="wows1_0"/></a><?php echo $row_ad->c3_description; ?></li>
					</ul>
				</div>
				<div class="ws_bullets">
					<div>
						<a href="#" title="<?php echo $row_ad->c1_name; ?>">1</a>
						<a href="#" title="<?php echo $row_ad->c2_name; ?>">2</a>
						<a href="#" title="<?php echo $row_ad->c3_name; ?>">3</a>
					</div>
				</div>
				<a class="wsl" href="http://wowslider.com">Javascript Slideshow Generator by WOWSlider.com v2.2.3</a>
				<div class="ws_shadow"></div>
			</div>
			<?php
		}
		?>

		<div data-theme='l' rel="style.css" class='middle_button_area' align='center'>
			<div data-role="controlgroup" data-type="horizontal" align='center' id='middle_button'>
			<?php
			if( $class_id == 'editor' )
				echo "<a href='{$Config_live_site}/index.php?option={$option}&task=recommend_list&class_id=editor' data-role='button' data-theme='s' class='ui-btn-active' data-ajax='false'>小编推荐</a>";
			else
				echo "<a href='{$Config_live_site}/index.php?option={$option}&task=recommend_list&class_id=editor' data-role='button' data-theme='s' data-ajax='false'>小编推荐</a>";
			if( $class_id == "user" )
				echo "<a href='{$Config_live_site}/index.php?option={$option}&task=recommend_list&class_id=user' data-role='button' data-theme='s' class='ui-btn-active' data-ajax='false'>网友推荐</a>";
			else
				echo "<a href='{$Config_live_site}/index.php?option={$option}&task=recommend_list&class_id=user' data-role='button' data-theme='s' data-ajax='false'>网友推荐</a>";
			if( $class_id == 'lastupdate' )
				echo "<a href='{$Config_live_site}/index.php?option={$option}&task=recommend_list&class_id=lastupdate' data-role='button' data-theme='s' class='ui-btn-active' data-ajax='false'>最近更新</a>";
			else
				echo "<a href='{$Config_live_site}/index.php?option={$option}&task=recommend_list&class_id=lastupdate' data-role='button' data-theme='s' data-ajax='false'>最近更新</a>";
			?>
			</div>
		</div>

		<div data-role="content" data-theme='t'>
			<div class="content-primary">	
			<?php
			if( $class_id == "editor" ) {
				?>
				<ul data-role="listview" class='vertical_list' ajaxul="true">
				<?php
				HTML_library::apporve_recommend_list_class($rows, $class_id, $ajaxcall);
				?>
				</ul>
			<?php
			}
			else if( $class_id == "lastupdate" ) {
				?>
				<ul data-role="listview" class='vertical_list' ajaxul="true">
				<?php
				HTML_library::apporve_recommend_list_class($rows, $class_id, $ajaxcall);
				?>
				</ul>
			<?php
			}
			else {
				?>
				<ul data-role="listview" data-inset="true" ajaxul="true">
				<?php
				HTML_library::apporve_recommend_list_class($rows, $class_id, $ajaxcall);
				?>
				</ul>
				<?php
			}
			if( $pageNav->total > $pageNav->limitstart + $pageNav->limit ) {
			?>
				<div id='viewmore' style="margin: 20px 0px 0px 0px;">
					<center><a href="javascript:ShowMore();" class="btnl bor-sdw-crv" data-role="button" data-theme="e" data-inline="true" style="font-size:24px">点击查看更多</a></center>
				</div>
			<?php
			}
			?>
			</div>
		</div>
		<script src="<?php echo $Config_live_site; ?>/includes/js/wowslider.js"></script>
		<script src="<?php echo $Config_live_site; ?>/includes/js/wowslider_script.js"></script>
		<script type="text/javascript">
		<?php
		echo "total = parseInt({$pageNav->total});";
		echo "limitstart = parseInt({$pageNav->limitstart});";
		echo "limit = parseInt({$pageNav->limit});";
		?>
		function ShowMore() {
			$.ajax({
				beforeSend: function() { $.mobile.showPageLoadingMsg(); }, 
				complete: function() { $.mobile.hidePageLoadingMsg() }, 
				type: 'get', 
				url: '<?php echo "$Config_live_site/index.php?option=library&task=recommend_list&class_id={$class_id}"; ?>', 
				data: "&limitstart="+ (limitstart + limit) +"&limit="+ limit +"&ajaxcall=1", 
				dataType: 'html', 
				success: function(data) {
					$('#list_<?php echo $class_id; ?>_pagediv ul[ajaxul|="true"]').append(data);
					$('#list_<?php echo $class_id; ?>_pagediv ul[ajaxul|="true"]').listview('refresh');
					$("input[type='button']").button();

					limitstart = (limitstart + limit);
					if( limitstart + limit >= total )
						$('#list_<?php echo $class_id; ?>_pagediv #viewmore').css('display', 'none');
				},
				error: function(data) {
					new Messi('loading error', {title: 'Error', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
				}
			});
		}

		function go_link(val) {
			document.location.href = val;
		}
		$( document ).delegate("#list_<?php echo $class_id; ?>_pagediv", "pagecreate", function() {
			// wowslider
			_wowslider('<?php echo $class_id; ?>');
		});
		</script>

		<input type="hidden" name="option" id="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" id="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="subtype" id="subtype" value="<?php echo $subtype; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
		</form>
		<?php module_footer(); ?>
		</div>
		<?php
	}

	function class_cartoon_list_li( &$rows, $class_id, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		
		foreach ($rows as $row) {
			$a_star_num = floor($row->score/2);
			$b_star_num = 5 - $a_star_num;
			$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
			?>
			<li class='frieds'>
				<a href="<?php echo $link_path; ?>" class='comlist'>
				<img class='vertical_image'  src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' />
					<h3 style='width:50%;'><?php echo $row->name; ?></h3>
					<div class="ui-grid-a" style='width:100%;margin-top:7px;'>
						<div class="ui-block-a"><p class='full_width' style='margin:0px;'><?php echo $row->description; ?></p></div>
					</div><!-- /grid-b -->
					<p class="ui-li-aside" style='width:50%;margin:0px;'>
					<?php
					for($a = 1; $a <= $a_star_num; $a++)
						echo "<img src='$Config_live_site/templates/$Config_cur_template/images/a_star.png' alt='Go' />";
					for($b = 1; $b <= $b_star_num; $b++)
						echo "<img src='$Config_live_site/templates/$Config_cur_template/images/b_star.png' alt='Go' />";
					?>
					</p>
				</a>
			</li>
			<?php
		}
		
		if( $ajaxcall )
			exit;
	}
	
	function class_cartoon_list( &$rows, $pageNav, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid, $class_id;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		?>
		<div data-role="page" id="class_pagediv">
		<form name='classform' id='classform' action="index.php?option=<?php echo $option; ?>" method="post">
		<?php HTML_library::type_button_menu(); ?>
		<?php HTML_library::classification_part_button_menu(); ?>

		<div data-role="content" data-theme='t'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list' ajaxul="true">
				<?php
				HTML_library::class_cartoon_list_li($rows, $class_id, $ajaxcall);
				?>
				</ul>
			</div>
			<?php
			if( $pageNav->total > $pageNav->limitstart + $pageNav->limit ) {
			?>
				<div id='viewmore' style="margin: 20px 0px 0px 0px;">
					<center><a href="javascript:ShowMore();" class="btnl bor-sdw-crv" data-role="button" data-theme="e" data-inline="true" style="font-size:24px">点击查看更多</a></center>
				</div>
			<?php
			}
			?>
		</div>	

		<input type="hidden" name="option" id="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" id="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="subtype" id="subtype" value="<?php echo $subtype; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
		<input type="hidden" name="class_id" id="class_id" value="<?php echo $class_id; ?>" />
		</form>
		
		<script type="text/javascript">
		<?php
		echo "total = parseInt({$pageNav->total});";
		echo "limitstart = parseInt({$pageNav->limitstart});";
		echo "limit = parseInt({$pageNav->limit});";
		?>
		function ShowMore() {
			$.ajax({
				beforeSend: function() { $.mobile.showPageLoadingMsg(); }, 
				complete: function() { $.mobile.hidePageLoadingMsg() }, 
				type: 'get', 
				url: '<?php echo "$Config_live_site/index.php?option=library&type=classification&subtype={$subtype}&task=class_cartoon_list&class_id={$class_id}"; ?>', 
				data: "&limitstart="+ (limitstart + limit) +"&limit="+ limit +"&ajaxcall=1", 
				dataType: 'html', 
				success: function(data) {
					$('#class_pagediv ul[ajaxul|="true"]').append(data);
					$('#class_pagediv ul[ajaxul|="true"]').listview('refresh');

					limitstart = (limitstart + limit);
					if( limitstart + limit >= total )
						$('#class_pagediv #viewmore').css('display', 'none');
				},
				error: function(data) {
					new Messi('loading error', {title: 'Error', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
				}
			});
		}
		</script>
		<?php module_footer(); ?>
		</div>
		<?php
	}

	function ranking_part_list_subtype( &$rows, $subtype, $ajaxcall ) {
		global $option, $type, $task, $cid, $_device_type;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		
		foreach ($rows as $row) {
		?>
		<li>
			<a href="index.php?option=library&task=view&cid=<?php echo $row->id; ?>">
				<img class='vertical_image'  src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' />
				<h3 style='width:50%;'><?php echo $row->name; ?></h3>
				<p class='library_list'><?php echo $row->description; ?></p>
				<p class="ui-li-aside" style='width:50%;'>
				<?php 
				if( $subtype == "read" ) {
					echo "阅读：{$row->view_num}次";
				} else if ($subtype == "download") {
						echo "下载：{$row->down_num}次";
				} else {
					$str = "";

					$a_star_num = floor($row->score/2);
					$b_star_num = 5 - $a_star_num;
					for($a = 1; $a <= $a_star_num; $a++)
						$str .= "<img src='$Config_live_site/templates/$Config_cur_template/images/a_star.png' alt='Go' class='right_buttons_image'/>";
					for($b = 1; $b <= $b_star_num; $b++)
						$str .= "<img src='$Config_live_site/templates/$Config_cur_template/images/b_star.png' alt='Go' class='right_buttons_image'/>";
					echo $str;
				}
				?>
				</p>	
			</a>
		</li>
		<?php
		}
		
		if( $ajaxcall == 1 )
			exit;
	}

	function ranking_part_list( &$rows, $pageNav, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid, $_device_type;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="ranking_pagediv">
		<form name='rankingform' id='rankingform' action="index.php?option=<?php echo $option; ?>" method="post">
		<?php HTML_library::type_button_menu();	?>
		<center>
			<div data-role="controlgroup" data-type="horizontal">
			<?php
			if( $subtype == 'read' )
				echo "<a href='index.php?option=library&type=ranking&subtype=read' data-role='button' class='ui-btn-active' id='subtype_read' data-theme='s'><font>阅读量</font></a>";
			else
				echo "<a href='index.php?option=library&type=ranking&subtype=read' data-role='button' id='subtype_read' data-theme='s'><font>阅读量</font></a>";
			if( $_device_type != 'desktop' ) {
				if( $subtype == 'download' )
					echo "<a href='index.php?option=library&type=ranking&subtype=download' data-role='button' class='ui-btn-active' id='subtype_download' data-theme='s'><font>下载量</font></a>";
				else
					echo "<a href='index.php?option=library&type=ranking&subtype=download' data-role='button' id='subtype_download' data-theme='s'><font>下载量</font></a>";
			}
			if( $subtype == 'score' )
				echo "<a href='index.php?option=library&type=ranking&subtype=score' data-role='button' class='ui-btn-active' id='subtype_score' data-theme='s'><font>评分</font></a>";
			else
				echo "<a href='index.php?option=library&type=ranking&subtype=score' data-role='button' id='subtype_score' data-theme='s'><font>评分</font></a>";
			?>
			</div>
		</center>
		<div data-role="content" data-theme='t'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list' ajaxul="true">
				<?php
				HTML_library::ranking_part_list_subtype($rows, $subtype, $ajaxcall);
				?>
				</ul>
			</div>
			<?php
			if( $pageNav->total > $pageNav->limitstart + $pageNav->limit ) {
			?>
				<div id='viewmore' style="margin: 20px 0px 0px 0px;">
					<center><a href="javascript:ShowMore();" class="btnl bor-sdw-crv" data-role="button" data-theme="e" data-inline="true" style="font-size:24px">点击查看更多</a></center>
				</div>
			<?php
			}
			?>
		</div>
		
		<script type="text/javascript">
		<?php
		echo "total = parseInt({$pageNav->total});";
		echo "limitstart = parseInt({$pageNav->limitstart});";
		echo "limit = parseInt({$pageNav->limit});";
		?>
		function ShowMore() {
			$.ajax({
				beforeSend: function() { $.mobile.showPageLoadingMsg(); }, 
				complete: function() { $.mobile.hidePageLoadingMsg() }, 
				type: 'get', 
				url: '<?php echo "$Config_live_site/index.php?option=library&type=ranking&subtype={$subtype}"; ?>', 
				data: "&limitstart="+ (limitstart + limit) +"&limit="+ limit +"&ajaxcall=1", 
				dataType: 'html', 
				success: function(data) {
					$('#ranking_pagediv ul[ajaxul|="true"]').append(data);
					$('#ranking_pagediv ul[ajaxul|="true"]').listview('refresh');
					limitstart = (limitstart + limit);
					if( limitstart + limit >= total )
						$('#ranking_pagediv #viewmore').css('display', 'none');
				},
				error: function(data) {
					new Messi('loading error', {title: 'Error', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
				}
			});
		}
		</script>

		<input type="hidden" name="option" id="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" id="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="subtype" id="subtype" value="<?php echo $subtype; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
		</form>
		<?php module_footer(); ?>
		</div>
		<?php
	}

	function read_cartoon_interface( &$file_info, $pid ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="read_pagediv">
		<link rel="stylesheet" href="<?php echo $Config_live_site; ?>/templates/mogu_default/css/pagecurl.css" />
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<div id="magazine" class="centerStart">
			<?php
			$k = 0;
			for($i = 1; $i <= $file_info['num']; $i++) {
				$class = "img". $i;
				?>
				<div id="page<?php echo $i; ?>" class="page">
					<div class="<?php echo $class; ?>">
						<span class="pageNum <?php echo ($k==0) ? 'right' : 'left'?>"><?php echo $i; ?> / <?php echo $file_info['num']; ?></span>
						<img src='<?php echo "$Config_live_site/index.php?option=library&task=read_cartoon_image&cid=$cid&order=$i"; ?>' alt="" />
					</div>
				</div>
				<?php
				$k = 1 - $k;
			}
			?>
		</div>

		<footer>
			<!--center><a href="index.php?option=library&task=view&cid=<?php echo $pid; ?>">Go Back</a></center-->
			<!--center><a data-rel='back' data-role='button' data-inline='true' data-ajax='true'>Go Back</a></center-->
		</footer>

		<!-- JavaScript includes - jQuery, turn.js and our own script.js -->
		<script src="<?php echo $Config_live_site; ?>/includes/js/jquery.js"></script>
		<script src="<?php echo $Config_live_site; ?>/includes/js/pageturn.js"></script>
		<script src="<?php echo $Config_live_site; ?>/includes/js/pageturn_script.js"></script>
		</div>
		<?php
	}

	function read_cartoon_interface_mobile( &$row_parent, &$row, &$file_info, $pid ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $_device_type;
		?>
		<div data-role="page" id="read_pagediv">
		<link rel="stylesheet" href="<?php echo $Config_live_site; ?>/templates/mogu_default/css/wowslider_read_style.css" />

		<div id="wowslider-container1" >
			<div id='top_control' style='background:black;opacity:0;width:100%;padding:5px 0 5px 0;z-index:1000;position:absolute;border-radius:10px;height:85px;'>

			<div class="ui-grid-b">
				<div class="ui-block-a" style='width:25%;'><a data-rel='back' data-role='button' data-inline='true' data-ajax='true' data-theme='a' style='border-radius:20px;font-size:12px;'>Go Back</a></div>
				<div class="ui-block-b" style='width:50%;' align='center'><p class='read_titles' style='text-shadow:none;font-size:14px;text-align:center;'><?php echo $row_parent->name. ' - '. $row->name; ?></p></div>
				<div class="ui-block-c" style='width:25%;' align='center'></div>
				</div><!-- /grid-a -->

				<div data-role="fieldcontain" style='opacity:1;margin:0;padding:0px 0 0 0px;' id='myslider' data-theme='a'>
				  <label for="slider-2"  style='width:30px;margin-left:31px;'></label>
				  <input type='text' name='slide_value' id='slide_value' disabled value='1' style='width:40px;text-align:center;margin-left:25px;display:inline-block;' data-theme='a' />
				  <input type="range" name="sldGilda" id="sldGilda" value="1" min="1" max="<?php echo $file_info['num'];?>" style= 'text-align:center;display:inline-block;' data-theme='a' data-track-theme="a"/>
				</div>
			</div>

			<div class="ws_images" style='z-index:-10;' id='mmm'>
				<ul>
					<!--li><img src="<?php echo $Config_live_site; ?>/images/images/blue_hills.jpg" alt="Blue hills" title="Blue hills" id="wows1_0"/></li>
					<li><img src="<?php echo $Config_live_site; ?>/images/images/sunset.jpg" alt="Sunset" title="Sunset" id="wows1_1"/></li>
					<li><img src="<?php echo $Config_live_site; ?>/images/images/water_lilies.jpg" alt="Water lilies" title="Water lilies" id="wows1_2"/></li>
					<li><img src="<?php echo $Config_live_site; ?>/images/images/winter.jpg" alt="Winter" title="Winter" id="wows1_3"/></li-->
				<?php
				$k = 0;
				for($i = 1; $i <= $file_info['num']; $i++) {
					?>
					<li><img src="<?php echo "$Config_live_site/index.php?option=library&task=read_cartoon_image&cid=$cid&order=$i"; ?>" title="<?php echo $i; ?> / <?php echo $file_info['num']; ?>" id="wows1_<?php $i-1; ?>"/></li>
					<?php
					$k = 1 - $k;
				}
				?>
				</ul>

			</div>

			<!--a class="wsl" href="http://wowslider.com">Javascript Slideshow Generator by WOWSlider.com v2.2.3</a-->
			<div class="ws_shadow"></div>
			<div id='bottom_control' style='position:absolute;background:black;opacity:0;width:100%;padding:5px 0 5px 0;z-index:1000;border-radius:10px;bottom:0px;overflow:hidden;display:block;'>
				<div class="ui-grid-d">
					<div class="ui-block-a" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/hidden.png"; ?>' class='read_images' id='hide_pagenum'/>
						<p class='read_functions' id='hide_pagenum'>隐藏页码</p>
					</div>
					<div class="ui-block-b" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/chapter_list.png"; ?>' class='read_images' id='chapter_list'/>
						<p class='read_functions' id='chapter_list'>章节列表</p>
					</div>
					<div class="ui-block-b" align='center'>
						<img id='estimate_read' src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/estimate.png"; ?>' class='read_images' id=''/>
						<p class='read_functions' id='estimate_read'>平分评论</p>
					</div>
					<div class="ui-block-d" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/sharing_read.png"; ?>' class='read_images' id='friend_sharing'/>
						<p class='read_functions' id='friend_sharing'>好友分享</p>
					</div>
					<div class="ui-block-e" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/download_read.png"; ?>' class='read_images' id='image_download'/>
						<p class='read_functions' id='image_download'>保存图片</p>
					</div>
					<!--div class="ui-block-c" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/rotate.png"; ?>' class='read_images'/>
						<p class='read_functions'>隐藏页码</p>
					</div-->
					<!--div class="ui-block-d" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/nomove.png"; ?>' class='read_images'/>
						<p class='read_functions'>隐藏页码</p>
					</div-->
				</div>
				<div class="ui-grid-c" >
					<!--div class="ui-block-a" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/brightness.png"; ?>' class='read_images'/>
						<p class='read_functions'>隐藏页码</p>
					</div-->
					<!--div class="ui-block-b" align='center'>
						<img id='estimate_read' src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/estimate.png"; ?>' class='read_images'/>
						<p class='read_functions' id='estimate_read'>隐藏页码</p>
					</div>
					<div class="ui-block-c" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/sharing_read.png"; ?>' class='read_images'/>
						<p class='read_functions'>隐藏页码</p>
					</div>
					<div class="ui-block-d" align='center'>
						<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/download_read.png"; ?>' class='read_images'/>
						<p class='read_functions'>隐藏页码</p>
					</div-->
				</div>
			</div>
		</div>

		<!--center><a data-rel='back' data-role='button' data-inline='true' data-ajax='true'>Go Back</a></center-->
		<script src="<?php echo $Config_live_site; ?>/includes/js/wowslider.js"></script>
		<script src="<?php echo $Config_live_site; ?>/includes/js/wowslider_read_script.js"></script>
		<script>
			$("input#sldGilda").css("display","none");
			$("#myslider div.ui-slider").css("margin","0px");
			$("#wowslider-container1 a.ws_next").live("click", function() {
				var pageVal = $(".ws-title span").html().split("/");	
				var value = pageVal[0];
				value++;
				if (value > $("#sldGilda").attr("max"))
				{
					value = $("#sldGilda").attr("min")
				}
				$('#page').page();
				$("#sldGilda").val(value);
				$('#sldGilda').slider('refresh');

			});
			$("#wowslider-container1 a.ws_prev").live("click", function() {
				var pageVal = $(".ws-title span").html().split("/");	
				var value = pageVal[0];
				value--;
				if (value < $("#sldGilda").attr("min"))
				{
					value = $("#sldGilda").attr("max")
				}
				$("#sldGilda").val(value);
				$('sldGilda').slider('refresh');
				$('#sldGilda').slider('refresh');
			});
			var a=$(".ws_images");
			var H=a.find("ul");		 // H : div ul tags

			$("p#estimate_read, img#estimate_read").bind("click",function () {
				$.mobile.changePage( "index.php?option=library&task=estimate_dialog&cid=<?php echo $pid; ?>", {
					transition: "pop",
					reverse: false,
					changeHash: true
				});
			});
			$("p#chapter_list, img#chapter_list").bind("click",function () {
				alert("aaa");
			}); 
			
			$("p#friend_sharing, img#friend_sharing").bind("click", function () {
				alert("bbb");
			});
			$("p#image_download, img#image_download").bind("click",function() {
				alert("ccc");
			});
			$("p#hide_pagenum, img#hide_pagenum").bind("click", function() {
			var hide_state = $("#slide_value").css("display");
			
			if (hide_state == "none")
			{
				$("#slide_value").css("display","");
				$("#slide_value").css("display","inline-block");
				$("div.ui-slider").css("margin-left","");
				$("div.ui-slider").css("width","37%");
			}
			else {
				$("#slide_value").css("display","none");
				$("div.ui-slider").css("margin-left","57px");
				$("div.ui-slider").css("margin-top","5px");
				$("div.ui-slider").css("width","45%");
			
			}
		});	

		$(document).keypress(function (event) {
			if (event.charCode == "32"){
				var value = $('#top_control').css('opacity');
				if (value == 0.8)
				{
					$('#top_control').animate({
						opacity: 0,
					  }, 500, function() {
						// Animation complete.
					  });
					$('#bottom_control').animate({
						opacity: 0,
					  }, 500, function() {
						// Animation complete.
					  });

				}
				else if(value == 0){
					$('#top_control').animate({
						opacity: 0.8,
					  }, 500, function() {
						// Animation complete.
					});
					$('#bottom_control').animate({
						opacity: 0.8,
					  }, 500, function() {
						// Animation complete.
					  });
				}
			}
		});
		</script>
		<?php 			
		if ($_device_type != 'desktop') {
			?>
			<script>
				$("#read_pagediv").bind('taphold',function(event){
				event.preventDefault();
				var value = $('#top_control').css('opacity');
				if (value == 0.8)
				{
					$('#top_control').animate({
						opacity: 0,
					  }, 500, function() {
						// Animation complete.
					  });
					$('#bottom_control').animate({
						opacity: 0,
					  }, 500, function() {
						// Animation complete.
					  });

				}
				else if(value == 0){
					$('#top_control').animate({
						opacity: 0.8,
					  }, 500, function() {
						// Animation complete.
					});
					$('#bottom_control').animate({
						opacity: 0.8,
					  }, 500, function() {
						// Animation complete.
					  });
				}
			});
			</script>
			<?php
		}
		?>
		</div>
		<?php
	}

	
	function read_cartoon_interface_bak( &$file_info, $pid ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8" />
			<title>Reading Cartoon</title>

			<!-- Our Stylesheet -->
			<link rel="stylesheet" href="<?php echo $Config_live_site; ?>/templates/mogu_default/css/pagecurl.css" />

			<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
		</head>

		<body>

		<div id="magazine" class="centerStart">
			<?php
			$k = 0;
			for($i = 1; $i <= $file_info['num']; $i++) {
	//			if( $i == 1) $class = "img1";
	//			else $class = "img2";
				$class = "img". $i;
				?>
				<div id="page<?php echo $i; ?>" class="page">
					<div class="<?php echo $class; ?>">
						<span class="pageNum <?php echo ($k==0) ? 'right' : 'left'?>"><?php echo $i; ?> / <?php echo $file_info['num']; ?></span>
						<img src='<?php echo "$Config_live_site/index.php?option=library&task=read_cartoon_image&cid=$cid&order=$i"; ?>' alt="" />
					</div>
				</div>
				<?php
				$k = 1 - $k;
			}
			?>
		</div>

		<footer>
			<center><a href="index.php?option=library&task=view&cid=<?php echo $pid; ?>">Go Back</a></center>
		</footer>

		<!-- JavaScript includes - jQuery, turn.js and our own script.js -->
		<script src="<?php echo $Config_live_site; ?>/includes/js/jquery.js"></script>
		<script src="<?php echo $Config_live_site; ?>/includes/js/pageturn.js"></script>
		<script src="<?php echo $Config_live_site; ?>/includes/js/pageturn_script.js"></script>

		</body>
		</html>
		<?php
	}

	function type_button_menu() {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<!--div data-role="header" class='header' data-theme="p" align='center' data-position="fixed"-->
		<div data-role="header" class='header' data-theme="p" align='center'>
			<a data-icon="back" class="ui-btn-left" data-shadow="true" data-iconpos="notext" data-rel='back'>Back</a>
			<div>
				<img src='<?php echo "{$Config_live_site}/templates/{$Config_cur_template}/images/logo.png"; ?>' class='logo_image' />
			</div>
			<a href="index.php?option=library&type=search" data-icon="search" class="ui-btn-right" data-shadow="true" data-iconpos="notext">Search</a>
			<div class='header_line'></div>
			<fieldset class="ui-grid-c" id='top_button'>
			<?php
			if( $type == 'approve' )
				echo "<div class='ui-block-a'><a href='index.php?option=library&type=approve' data-role='button' data-theme='g' id='type_approve' class='ui-btn-active' data-ajax='false'>推荐</a></div>";
			else
				echo "<div class='ui-block-a'><a href='index.php?option=library&type=approve' data-role='button' data-theme='g' id='type_approve' data-ajax='false'>推荐</a></div>";
			if( $type == 'ranking' )
				echo "<div class='ui-block-b'><a href='index.php?option=library&type=ranking' data-role='button' data-theme='g' id='type_ranking' class='ui-btn-active'>排行</a></div>";
			else
				echo "<div class='ui-block-b'><a href='index.php?option=library&type=ranking' data-role='button' data-theme='g' id='type_ranking'>排行</a></div>";
			if( $type == 'classification' )
				echo "<div class='ui-block-c'><a href='index.php?option=library&type=classification' data-role='button' data-theme='g' id='type_classification' class='ui-btn-active'>分类</a></div>";
			else
				echo "<div class='ui-block-c'><a href='index.php?option=library&type=classification' data-role='button' data-theme='g' id='type_classification'>分类</a></div>";
			if( $type == 'search' )
				echo "<div class='ui-block-d'><a href='index.php?option=library&type=search' data-role='button' data-theme='g' id='type_search' class='ui-btn-active'>搜索</a></div>";
			else
				echo "<div class='ui-block-d'><a href='index.php?option=library&type=search' data-role='button' data-theme='g' id='type_search'>搜索</a></div>";
			?>
			</fieldset>
		</div>
		<?php
	}

	function classification_part_list( &$rows ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_classification_thumbnail_path_site;
		?>
		<div data-role="page" id="classb_pagediv">
		<form name='classBform' id='classBform' action="index.php?option=<?php echo $option; ?>" method="post">
		<?php HTML_library::type_button_menu();	?>
		<?php HTML_library::classification_part_button_menu(); ?>

		<div data-role="content" data-theme='t' >
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list'>
					<?php
					if($subtype == 'class') {
						foreach($rows as $row) {
						?>
						<li>
							<a href="index.php?option=library&type=classification&subtype=class&task=class_cartoon_list&class_id=<?php echo $row->id; ?>">
							<img class='vertical_image'  src='<?php echo $Config_classification_thumbnail_path_site ."/". $row->thumbnail_name; ?>' />
								<h3><?php echo $row->name; ?></h3>
								<p class='class_p_list'><?php echo $row->cartoon_name; ?></p>
							</a>
						</li>
						<?php
						}
					}
					else if ($subtype == 'alphabetic') {
						foreach($rows as $row) {
						?>
						<li>
							<a href="index.php?option=library&type=classification&subtype=alphabetic&task=class_cartoon_list&class_id=<?php echo $row->name; ?>">
								<h3><?php echo $row->name; ?></h3>
								<p class='class_p_list'><?php echo $row->cartoon_name; ?></p>
							</a>
						</li>

						<?php
						}					
					}
					else if ($subtype == 'region') {
						foreach($rows as $row) {
						?>
						<li>
							<a href="index.php?option=library&type=classification&subtype=region&task=class_cartoon_list&class_id=<?php echo $row->id; ?>">
								<h3><?php echo $row->name; ?></h3>
								<p class='class_p_list'><?php echo $row->cartoon_name; ?></p>
							</a>
						</li>
						<?php
						}					
					}
					else if ($subtype == 'state') {
						foreach($rows as $row) {
						?>
						<li class_id="<?php echo $row->id; ?>">
							<a href="index.php?option=library&type=classification&subtype=state&task=class_cartoon_list&class_id=<?php echo $row->id; ?>">
								<h3><?php echo $row->name; ?></h3>
								<p class='class_p_list'><?php echo $row->cartoon_name; ?></p>
							</a>
						</li>
						<?php
						}					
					}
				?>
				</ul>
			</div>
		</div>		

		<input type="hidden" name="option" id="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" id="task" value="<?php echo $task; ?>" />
		<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="subtype" id="subtype" value="<?php echo $subtype; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
		<input type="hidden" name="class_id" id="class_id" value="" />
		</form>
		<?php module_footer(); ?>
		</div>
		<?php
	}

	function classification_part_button_menu() {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<center>
			<div data-role="controlgroup" data-type="horizontal">
				<?php
				if( $subtype == 'class' )
					echo "<a href='index.php?option=library&type=classification&subtype=class' data-role='button' class='ui-btn-active' id='subtype_class' data-theme='s'><font>按内容</font></a>";
				else
					echo "<a href='index.php?option=library&type=classification&subtype=class' data-role='button' id='subtype_class' data-theme='s'><font>按内容</font></a>";
				if( $subtype == 'alphabetic' )
					echo "<a href='index.php?option=library&type=classification&subtype=alphabetic' data-role='button' class='ui-btn-active' id='subtype_alphabetic' data-theme='s'><font>按字母</font></a>";
				else
					echo "<a href='index.php?option=library&type=classification&subtype=alphabetic' data-role='button' id='subtype_alphabetic' data-theme='s'><font>按字母</font></a>";
				if( $subtype == 'region' )
					echo "<a href='index.php?option=library&type=classification&subtype=region' data-role='button' class='ui-btn-active' id='subtype_region' data-theme='s'><font>按地区</font></a>";
				else
					echo "<a href='index.php?option=library&type=classification&subtype=region' data-role='button' id='subtype_region' data-theme='s'><font>按地区</font></a>";
				if( $subtype == 'state' )
					echo "<a href='index.php?option=library&type=classification&subtype=state' data-role='button' class='ui-btn-active' id='subtype_state' data-theme='s'><font>按状态</font></a>";
				else
					echo "<a href='index.php?option=library&type=classification&subtype=state' data-role='button' id='subtype_state' data-theme='s'><font>按状态</font></a>";
				?>
			</div>
		</center>
		<?php
	}
	
	function cartoon_seenuser( &$row_user, &$rows_guest, &$rows_bookshelf, &$rows_estimate ) {
		global $option, $type, $subtype, $task, $cid, $_device_type;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_user_thumbnail_path_site;
		global $Config_state, $Config_region;
		?>
		<div data-role="page" id="user_pagediv">
		<form name='detail_cartoon_log' id='detail_cartoon_log' action="index.php?option=<?php echo $option; ?>&type=search" method="post">
		<div data-role="header" class='header' data-theme="p" align='center' data-position="fixed">
			<a data-icon="back" class="ui-btn-left" data-shadow="true" data-iconpos="notext" data-rel='back'>Back</a>
			<h1 style='color:#652617'><?php echo $row_user->username; ?>的空间</h1>
			<a href="index.php?option=library&type=search" data-icon="search" class="ui-btn-right" data-iconshadow="true" data-shadow="true" data-iconpos="notext">Search</a>
		</div><!-- /header -->
		<div data-role="content" data-theme='t'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list'>
					<li style='height:60px;border-bottom:0px;'>
						<img src='<?php echo $Config_user_thumbnail_path_site ."/".$row_user->thumbnail_name; ?>' class='vertical_image' />
						<div style='display:inline-block;width:100%;'>
							<h3 style='color:#662716;'>
							<span style='float:left;color:#739d18;'><?php echo $row_user->username; ?>(<?php echo $row_user->id; ?>)</span>
							<span style='float:right;color:#739d18;'>积分 : <?php echo $row_user->point; ?></span>
							</h3>
						</div>
						<div style='float:left;width:220px;' align='left'>
							<div>
								<a href="http://www.mogufun.com/" data-role="button" data-mini="true" data-inline="true" style='height:30px;margin-left:0px;'>去ta的主页</a>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<p style='background:#CCCCCC; margin-top:30px;'><?php echo $row_user->description; ?></p>
			<div class='visitors'>
				<p class='visitor_title'>访客</p>
				<?php
				foreach( $rows_guest as $row_guest ) {
					echo "<span class='vistor_span'><a href='index.php?option=library&task=seenuser&userid={$row_guest->id}'><img src='{$Config_user_thumbnail_path_site}/{$row_guest->thumbnail_name}' class='visitor_img' /></a></span>";
				}
				?>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style='border-bottom:2px solid #ffc200;height:39px;border-radius:0px;'>
				<div class='ta_area_a_down' id="tab_bookshelf"><p style='margin:8px;'>Ta的收藏</p></div>
				<div class='ta_area_a_up' id="tab_estimate"><p style='margin:8px;'>Ta的评论</p></div>
			</div>
			<center>
			<?php
			$grid_array = array("a", "b", "c", "d", "e");
			if( $_device_type == "desktop" ) {
				$grid_class = "ui-grid-d";
				$grid_column = 5;
			} else {
				$grid_class = "ui-grid-b";
				$grid_column = 3;
			}
			?>
			<div id='bookshelf_div' class="<?php echo $grid_class; ?>" style='margin-bottom:10px;'>
				<?php
				for($i = 0; $i < ceil(count($rows_bookshelf)/$grid_column); $i++) {
					for($j = 0; $j < $grid_column; $j++) {
						$index = ($i * $grid_column) + $j;
						$row_bookshelf = &$rows_bookshelf[$index];
						if(!$row_bookshelf->name) {
							echo "<div class='ui-block-{$grid_array[$j]}'>&nbsp;</div>";
							continue;
						}
						echo "
						<div class='ui-block-{$grid_array[$j]}'>
							<a href='index.php?option={$option}&task=view&cid={$row_bookshelf->id}'>
							<img src='{$Config_cartoon_thumbnail_path_site}/{$row_bookshelf->thumbnail_name}' class='ta_imgs' />
							</a>
							<p style='margin:0px;'>{$row_bookshelf->name}</p>
						</div>
						";
					}
				}
				?>
			</div><!-- /grid-b -->
			<div id='estimate_div' style="display: none;">
				<ul data-role="listview" data-theme="d" class='vertical_list' data-inset="true">
				<?php
				foreach($rows_estimate as $row_estimate) {
					?>
					<li class='frieds'>
					<a href="index.php?option=<?php echo $option; ?>&task=view&cid=<?php echo $row_estimate->id; ?>" data-ajax='true'>
						<img class='vertical_image' src="<?php echo $Config_cartoon_thumbnail_path_site ."/". $row_estimate->thumbnail_name; ?>" />
						<h3><?php echo $row_estimate->name; ?></h3>
						<p><?php echo $row_estimate->content; ?></p>
					</a>
					</li>
					<?php
				}
				?>
				</ul>
			</div>
			</center>
		</div>
		<script>
		$("#user_pagediv #tab_bookshelf").mousedown(function () {
			$("#user_pagediv #tab_bookshelf").attr("class","ta_area_a_down");
			$('#user_pagediv #tab_estimate').attr("class","ta_area_a_up");
			$('#user_pagediv #bookshelf_div').css("display", "");
			$('#user_pagediv #estimate_div').css("display", "none");
		});
		$("#user_pagediv #tab_estimate").mousedown(function () {
			$("#user_pagediv #tab_bookshelf").attr("class","ta_area_a_up");
			$('#user_pagediv #tab_estimate').attr("class","ta_area_a_down");
			$('#user_pagediv #bookshelf_div').css("display", "none");
			$('#user_pagediv #estimate_div').css("display", "");
		});
		</script>
		<?php module_footer(); ?>
		</div>
		<?php	
	}
	
	function view_search_li( &$rows_search, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid, $_device_type;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;

		foreach ($rows_search as $row_search) {
			$a_star_num = floor($row_search->score/2);
			$b_star_num = 5 - $a_star_num;
			?>
			<li class='frieds'>
				<a href="index.php?option=library&task=view&cid=<?php echo $row_search->id; ?>" class='comlist'>
				<img class='vertical_image'  src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row_search->thumbnail_name; ?>' />						<h3 style='width:50%;'><?php echo $row_search->name; ?></h3>
					<div class="ui-grid-a" style='width:100%;margin-top:7px;'>
						<div class="ui-block-a"><p class='full_width' style='margin:0px;'><?php echo $row_search->description; ?></p></div>
					</div><!-- /grid-b -->
					<p class="ui-li-aside" style='width:50%;margin:0px;'>
					<?php
					for($a = 1; $a <= $a_star_num; $a++)
						echo "<img src='$Config_live_site/templates/$Config_cur_template/images/a_star.png' alt='Go' />";
					for($b = 1; $b <= $b_star_num; $b++)
						echo "<img src='$Config_live_site/templates/$Config_cur_template/images/b_star.png' alt='Go' />";
					?>
					</p>
				</a>
			</li>
			<?php
		}
		
		if( $ajaxcall )
			exit;
	}
	
	function view_search( $search_keyword, &$rows_search, &$rows_hotkey, &$pageNav, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="search_pagediv">
		<form name='searchform' id='searchform' action="index.php?option=<?php echo $option; ?>&type=search" method="post">
		<?php HTML_library::type_button_menu();	?>

		<div data-role="content">
			<label for="search" class="ui-hidden-accessible">Search Input:</label>
			<input type="search" name="search_text" id="search_text" value="<?php echo $search_keyword; ?>" data-inline="true" />
		</div>
		<?php
		if( count($rows_search) == 0 && $search_keyword ) {
			echo "<div data-role='content' data-theme='d' class='sorry_text' >
			<div class='space'></div>
			对不起，还没有您要看的作品！</div>";
		}
		?>
		<?php
		if( count($rows_search) == 0 ) {
		?>
		<div data-role="content" data-theme='d'>
			<div class='hot_keywords'>	
				<p style="padding-left: 15px;">热门关键词</p>
			</div>
			<div class="header_line">&nbsp;</div>
			<p style="height:20px; margin: 0px;">&nbsp;</p>
			<fieldset class="ui-grid-a">
				<?php
				for( $i = 0; $i < count($rows_hotkey); $i++ ) {
					?>
					<div class="ui-block-a" style="height: 35px;"><a href="#" onclick="search_cartoon('<?php echo $rows_hotkey[$i]->keyword; ?>');"><?php echo $rows_hotkey[$i]->keyword; $i++; ?></a></div>
					<div class="ui-block-b" style="height: 35px;"><a href="#" onclick="search_cartoon('<?php echo $rows_hotkey[$i]->keyword; ?>');"><?php echo $rows_hotkey[$i]->keyword; ?></a></div>
					<?php
				}
				?>
			</fieldset>
		</div><!-- /page -->
		<?php
		}
		?>

		<script type="text/javascript">
		function search_cartoon(val) {
			$("#searchform #search_text").val(val);
			$("#searchform #task").val('search');
			$('#searchform').attr('action', 'index.php?option=library&type=search');
			$('#searchform').submit();
		}
		</script>
		<div data-role="content" data-theme='t'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list' ajaxul="true">
				<?php
				HTML_library::view_search_li($rows_search, $ajaxcall);
				?>
				</ul>
			</div>
			<?php
			if( $pageNav->total > $pageNav->limitstart + $pageNav->limit ) {
			?>
				<div id='viewmore' style="margin: 20px 0px 0px 0px;">
					<center><a href="javascript:ShowMore();" class="btnl bor-sdw-crv" data-role="button" data-theme="e" data-inline="true" style="font-size:24px">点击查看更多</a></center>
				</div>
			<?php
			}
			?>
		</div>
		
		<script type="text/javascript">
		<?php
		echo "total = parseInt({$pageNav->total});";
		echo "limitstart = parseInt({$pageNav->limitstart});";
		echo "limit = parseInt({$pageNav->limit});";
		?>
		function ShowMore() {
			$.ajax({
				beforeSend: function() { $.mobile.showPageLoadingMsg(); }, 
				complete: function() { $.mobile.hidePageLoadingMsg() }, 
				type: 'get', 
				url: '<?php echo "$Config_live_site/index.php?option=library&type=search&task=search&search_text="; ?>'+encodeURI($('#search_text').val()), 
				data: "&limitstart="+ (limitstart + limit) +"&limit="+ limit +"&ajaxcall=1", 
				dataType: 'html', 
				success: function(data) {
					$('ul[ajaxul|="true"]').append(data);
					$('ul[ajaxul|="true"]').listview('refresh');
					limitstart = (limitstart + limit);
					if( limitstart + limit >= total )
						$('#search_pagediv #viewmore').css('display', 'none');
				},
				error: function(data) {
					new Messi('loading error', {title: 'Error', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
				}
			});
		}
		</script>

		<input type="hidden" name="option" id="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" id="task" value="search" />
		<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="subtype" id="subtype" value="<?php echo $subtype; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
		</form>
		<?php module_footer(); ?>
		</div>
		<?php
	}

	function estimate_dialog( &$row ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
	?>
	<!DOCTYPE html> 
	<html> 
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>estimate dialog</title> 
	</head> 
	<body> 

	<div data-role="page">
		<form name="estimateform" id="estimateform" method="post" action="index.php?option=library">
			<div data-role="header" data-theme="d">
				<h1>评分评论</h1>
			</div>

			<div data-role="content" data-theme="c">
				<p>
					<div id='mark' class="rateit bigstars" data-rateit-starwidth="32" data-rateit-starheight="32" data-rateit-max='5' data-rateit-step='0.5' data-rateit-value='<?php echo $row->mark/2; ?>'></div>
				</p>
				<p>
					<textarea name="estimate_content" id="estimate_content"><?php echo $row->content; ?></textarea>
				</p>
				<a href="#" id="estimate_save" data-role='button' data-theme="e" data-inline='true'>Save</a>       
				<a href="#" data-role="button" data-rel='back' data-theme="c" data-inline='true'>Cancel</a>    
			</div>
		</form>
		<script type="text/javascript">
			$('#estimate_save').bind('click', function() {
				if( $('#mark').rateit('value') == 0 ) {
					new Messi('Please give esitimate mark', {title: 'Warming', titleClass: 'anim warming', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
					return;
				}
				if( $("#estimateform #estimate_content").val() == "" ) {
					new Messi('Please enter esitimate content', {title: 'Warming', titleClass: 'anim warming', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
					return;
				}
				
				$.ajax({
					beforeSend: function() { $.mobile.showPageLoadingMsg(); }, 
					complete: function() { $.mobile.hidePageLoadingMsg() }, 
					type: 'post', 
					url: '<?php echo "$Config_live_site/index.php?option=library&task=save_estimate"; ?>', 
					data: $('#estimateform').serialize() + "&cid=<?php echo $cid; ?>&mark=" + ($('#mark').rateit('value')*2), 
					dataType: 'text', 
					success: function(data) {
						if( data == "success") {
							new Messi('save estimate success', {title: 'Success', titleClass: 'success', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
							$('.ui-dialog').dialog('close');
						} else {
							new Messi('save estimate failure', {title: 'Failure', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
						}
					},
					error: function(data) {
						new Messi('save estimate failure', {title: 'Failure', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
					}
				});
			});
			$('#mark').rateit();
			$("#mark").bind('over', function (event,value) { $(this).attr('title', value*2); });
		</script>
		<style type="text/css">
			div.bigstars div.rateit-range
			{
				background: url(templates/mogu_default/images/rating/star-white32.png);
				height: 32px;
			}

			div.bigstars div.rateit-hover
			{
				background: url(templates/mogu_default/images/rating/star-gold32.png);
			}

			div.bigstars div.rateit-selected
			{
				background: url(templates/mogu_default/images/rating/star-red32.png);
			}

			div.bigstars div.rateit-reset
			{
				background: url(templates/mogu_default/images/rating/star-black32.png);
				width: 32px;
				height: 32px;
			}

			div.bigstars div.rateit-reset:hover
			{
				background: url(templates/mogu_default/images/rating/star-white32.png);
			}
		</style>
	</div>
	
	</body>
	</html>		
	<?php
	}

	function amount_download_dialog( &$rows ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
	?>
	<!DOCTYPE html> 
	<html> 
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>内容批量下载</title> 
	</head> 
	<body> 

	<div data-role="page">
		<form name="amountdownloadform" id="amountdownloadform" method="post" action="index.php?option=library&task=chapters_download">
			<div data-role="header" data-theme="d">
				<!--img class="logo_image" src="http://175.160.107.3/cartoon/templates/mogu_default/images/logo.png" /-->
				<h1>内容批量下载</h1>
			</div>

			<div data-role="content" data-theme="c">
			 	<fieldset data-role="controlgroup">
			 	<?php
			 	$i = 0; $chapterArray = array();
			 	foreach($rows as $row) {
					echo "
					<input type='checkbox' name='checkbox_d[]' id='checkbox_d{$i}' class='custom' value='{$row->id}' />
					<label for='checkbox_d{$i}' id='label_d{$i}'>{$row->name}</label>
					";
					$chapterArray[] = '"'.$row->name.'"';
			 		$i++;
			 	}
			 	$chapterArray = implode(",", $chapterArray);
				?>
			    </fieldset>
				<div>
					<div class="ui-grid-b">
						<div class="ui-block-a">
							<a href="javascript: checkAll(<?php echo count($rows); ?>);" data-role="button" data-theme="c">全选</a>
						</div>
						<div class="ui-block-b"><a href="javascript:downloadChapters();" data-role="button" data-theme="c">确定</a></div>
						<div class="ui-block-c"><a data-role="button" data-theme="c" data-rel="back">取消</a></div>
					</div><!-- /grid-b -->
				</div>
			</div>
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<script type="text/javascript">
		toggle_check = "on";
		chapterArray = <?php echo '['. $chapterArray .']'; ?>;
		function checkAll( n, fldName ) {
		  if (!fldName) {
		     fldName = 'checkbox_d';
		  }
			var f = document.amountdownloadform;
			var c = toggle_check;
			toggle_check = toggle_check ? "" : "on";
			var n2 = 0;
			for (i=0; i < n; i++) {
				cb = document.getElementById(fldName + i);
				lb = document.getElementById('label_d' + i);
				//cb = eval( 'f.' + fldName + '' + i );
				if (cb) {
					if( c ) {
						checkStr = " ui-checkbox-on"
						checkStr2 = " ui-icon-checkbox-on"
					} else {
						checkStr = " ui-checkbox-off"
						checkStr2 = " ui-icon-checkbox-off"
					}
					if( i == 0 )
						checkStr1 = ' ui-corner-top';
					else if( i == n - 1 )
						checkStr1 = ' ui-corner-bottom ui-controlgroup-last';
					else
						checkStr1 = '';
					lb.className = "ui-btn ui-btn-icon-left ui-btn-up-c"+checkStr+checkStr1;
					lb.innerHTML = '<span class="ui-btn-inner'+checkStr+checkStr1+'"><span class="ui-btn-text">'+chapterArray[i]+'</span><span class="ui-icon ui-icon-shadow'+ checkStr2 +'"></span></span>';
					cb.checked = c;
					n2++;
				}
			}
			if (c) {
				document.amountdownloadform.boxchecked.value = n2;
			} else {
				document.amountdownloadform.boxchecked.value = 0;
			}
		}
		
		function downloadChapters() {
			checked_flag = 0;
			for(i = 0; i < <?php echo count($rows); ?>; i++) {
				if($('#checkbox_d'+i).attr("checked") == "checked")
					checked_flag = 1;
			}
			if(checked_flag == 0) {
				new Messi('Please select chapter', {title: 'Warming', titleClass: 'anim warming', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
				return;
			}
			document.amountdownloadform.submit();
		}
		</script>
	</div>
	
	</body>
	</html>		
	<?php
	}
}
?>
