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
class HTML_download {
	function complete_list( &$rows ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="complete_pagediv">
		<form name='completeform' id='completeform' action="index.php?option=<?php echo $option; ?>&type=<?php echo $type; ?>" method="post">
		<?php HTML_download::header(); ?>
		<div data-role="content" data-theme = 't'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list'>
				<?php
				if (empty($rows)) {
					echo "<div style='height:300px;' align='center'><p style='padding:120px;'>没有资料!</p></div>";
				}

				foreach( $rows as $row ) {
					$a_star_num = floor($row->score/500);
					$b_star_num = 5 - $a_star_num;
					$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
					?>
					<li>
						<img src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' class='vertical_image' />
						<h3><?php echo $row->name; ?></h3>
						<p class="down_content_hp" style='width:60%;'><?php echo $row->description; ?></p>
						<p class="ui-li-aside" style='width:50%;'>
							<?php
							for($b = 1; $b <= $b_star_num; $b++)
								echo "<img src='$Config_live_site/templates/$Config_cur_template/images/b_star.png' class='down_approve_image' alt='Go' />";
							for($a = 1; $a <= $a_star_num; $a++)
								echo "<img src='$Config_live_site/templates/$Config_cur_template/images/a_star.png' class='down_approve_image' alt='Go' />";
							?>
						</p>
						<div align='right' class='down_contents_div'><button data-inline="true" data-theme='e' class='content_button'>加入书架</button></div>
					</li>
					<?php
					}
				?>
				</ul>
			</div>
		</div>
		
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
	
	function wait_list( &$rows ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="wait_pagediv">
		<link rel="stylesheet" href="templates/mogu_default/css/checkbox.css" />
		<script src="<?php echo $Config_live_site; ?>/includes/js/checkbox.js"></script>

		<form name='waitform' id='waitform' action="index.php?option=<?php echo $option; ?>&type=<?php echo $type; ?>" method="post">
		<?php HTML_download::header(); ?>
		<div data-role="content">
			<a href="index.html" data-role="button" data-inline="true" data-theme="s" class='down_all'>全部下载</a>
			<a href="index.html" data-role="button" data-inline="true" style='float:right' data-theme="s" class='down_all'>全部下载</a>
			<a href="index.html" data-role="button" data-inline="true" style='float:right' data-theme="s" class='down_all'>全部暂停</a>
		</div>

		<div data-role="content" data-theme = 't'>
			<div class="content-primary">
				<ul data-role="listview">
					<li>
						<?php
							echo "<img src='$Config_live_site/templates/$Config_cur_template/images/book3.png' class='vertical_image'/>";
						?>						
						<p class="down_content_p">Hey Stephen, if you're available at 10am tomorrow, we've got a meeting with the jQuery team.</p>
						<div class='down_confirm_button'>
							<a data-role="button" data-inline="true" data-theme='e' data-corners="false" data-ajax="true" style='float:left;height:30px;'>开始下载</a>
							<div class='checkbox_area' style='margin-left:7px;'><input type="checkbox" name="a" class="styled" /></div>
						</div>
					</li>
				</ul>
			</div><!--/content-primary -->	
		</div>
		
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
	
	function bookmark_list( &$rows ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="complete_pagediv">
		<form name='completeform' id='completeform' action="index.php?option=<?php echo $option; ?>&type=<?php echo $type; ?>" method="post">
		<?php HTML_download::header(); ?>
		<div data-role="content" data-theme='t'>
			<div class="content-primary">
				<ul data-role="listview">
					<li>
						<?php
							echo "<img src='$Config_live_site/templates/$Config_cur_template/images/book3.png' class='vertical_image'/>";
						?>						
						<p class="down_content_p">Hey Stephen, if you're available at 10am tomorrow, we've got a meeting with the jQuery team.</p>
						<div align = 'right' class='down_divs_button'>
							<a href="index.php?option=library&task=read_cartoon&cid=<?php echo $row_child->id; ?>&pid=<?php echo $cid; ?>" data-role="button" data-inline="true" data-theme='e' data-corners="false" data-ajax="true">删除</a>
							<a href="" id="download_cartoon<?php echo $row_child->id; ?>" data-role="button" data-inline="true" data-theme='e' data-corners="false">添加</a>
							<a href="" id="download_cartoon<?php echo $row_child->id; ?>" data-role="button" data-inline="true" data-theme='e' data-corners="false">管理</a>

						</div>
					</li>
				</ul>
			</div><!--/content-primary -->	
		</div>
		
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
	
	function header() {
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
			<fieldset class="ui-grid-b" id='top_down_button' style='padding:10px;'>
			<?php
			if( $type == 'download_complete' )
				echo "<div class='ui-block-a'><a href='index.php?option=download&type=download_complete' data-role='button' data-theme='g' id='download_complete' class='ui-btn-active'>下载完成</a></div>";
			else
				echo "<div class='ui-block-a'><a href='index.php?option=download&type=download_complete' data-role='button' data-theme='g' id='download_complete'>下载完成</a></div>";
			if( $type == 'download_wait' )
				echo "<div class='ui-block-b'><a href='index.php?option=download&type=download_wait' data-role='button' data-theme='g' id='download_wait' class='ui-btn-active' data-ajax='false'>等待下载</a></div>";
			else
				echo "<div class='ui-block-b'><a href='index.php?option=download&type=download_wait' data-role='button' data-theme='g' id='download_wait' data-ajax='false'>等待下载</a></div>";
			if( $type == 'manage_bookshelf' )
				echo "<div class='ui-block-c'><a href='index.php?option=download&type=manage_bookshelf' data-role='button' data-theme='g' id='manage_bookshelf' class='ui-btn-active'>管理书架</a></div>";
			else
				echo "<div class='ui-block-c'><a href='index.php?option=download&type=manage_bookshelf' data-role='button' data-theme='g' id='manage_bookshelf'>管理书架</a></div>";
			?>
			</fieldset>
		</div>
		<?php
	}
}
?>
