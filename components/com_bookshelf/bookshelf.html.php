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
class HTML_bookshelf {
	function cartoon_list_li( &$rows, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		
		foreach($rows as $row) {
			$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
			?>
			<li>
				<a href="<?php echo $link_path; ?>">
					<img src='<?php echo "{$Config_cartoon_thumbnail_path_site}/{$row->thumbnail_name}"; ?>' class='book_std_img' />
					<h3 class='book_std_h'><?php echo $row->name; ?>(<?php echo $row->description; ?>)</h3>
					<p class='book_std_p'>看到<?php echo $row->lastreadchapter; ?></p>
					<p class='book_std_p'>更新至<?php echo $row->lastupdatechapter; ?></p>
				</a>
			</li>
			<?php
		}
		
		if( $ajaxcall )
			exit;
	}

	function cartoon_list( &$rows, &$pageNav, $ajaxcall ) {
		global $option, $type, $subtype, $task, $cid;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site;
		?>
		<div data-role="page" id="bookshelf_pagediv">
		<form name='bookshelfform' id='bookshelfform' action="index.php?option=<?php echo $option; ?>" method="post">
		<div data-role="header" id='booo' class='header' data-theme="j" align='center'>
			<?php
			if( $type == 'my_bookshelf' )
				echo '<a href="index.php?option=bookshelf&type=my_bookshelf&task=type" data-role="button" data-theme="e" class="top_header_button ui-btn-active" id="my_bookshelf">我的书架</a>';
			else
				echo '<a href="index.php?option=bookshelf&type=my_bookshelf&task=type" data-role="button" data-theme="e" class="top_header_button" id="my_bookshelf">我的书架</a>';
			?>
			<div class='space_header'></div>
			<img src='<?php echo $Config_live_site; ?>/templates/<?php echo $Config_cur_template; ?>/images/logo.png' />
			<?php
			if( $type == 'recently_read' )
				echo '<a href="index.php?option=bookshelf&type=recently_read&task=type" data-role="button" data-theme="e" class="top_header_button ui-btn-active" id="recently_read">最近阅读</a>';
			else
				echo '<a href="index.php?option=bookshelf&type=recently_read&task=type" data-role="button" data-theme="e" class="top_header_button" id="recently_read">最近阅读</a>';
			?>
			
		</div><!-- /header -->
		<div data-role="content" >
			<div class="content-primary">	
				<?php
				if (empty($rows)) {
					echo "<div style='height:300px;' align='center'><p style='padding:120px;'>没有资料!</p></div>";
				} else {
					echo '<ul data-role="listview" class="vertical_list" id="bookshelflist_ul">';
					HTML_bookshelf::cartoon_list_li($rows, $ajaxcall);
					echo '</ul>';
				}
				if( $pageNav->total > $pageNav->limitstart + $pageNav->limit ) {
				?>
					<div id='viewmore' style="margin: 20px 0px 0px 0px;">
						<center><a href="javascript:ShowMore_bookshelf();" class="btnl bor-sdw-crv" data-role="button" data-theme="e" data-inline="true" style="font-size:24px">点击查看更多</a></center>
					</div>
				<?php
				}
				?>
			</div>
		</div>	
		
		<script type="text/javascript">
		<?php
		echo "total = parseInt({$pageNav->total});";
		echo "limitstart = parseInt({$pageNav->limitstart});";
		echo "limit = parseInt({$pageNav->limit});";
		?>
		function ShowMore_bookshelf() {
			$.ajax({
				beforeSend: function() { $.mobile.showPageLoadingMsg(); }, 
				complete: function() { $.mobile.hidePageLoadingMsg() }, 
				type: 'get', 
				url: '<?php echo "$Config_live_site/index.php?option=bookshelf&type={$type}&task=list"; ?>', 
				data: "&limitstart="+ (limitstart + limit) +"&limit="+ limit +"&ajaxcall=1", 
				dataType: 'html', 
				success: function(data) {
					$('#bookshelf_pagediv #bookshelflist_ul').append(data);
					$('#bookshelf_pagediv #bookshelflist_ul').listview('refresh');
					limitstart = (limitstart + limit);
					if( limitstart + limit >= total )
						$('#bookshelf_pagediv #viewmore').css('display', 'none');
				},
				error: function(data) {
					new Messi('loading error', {title: 'Error', titleClass: 'anim error', buttons: [{id: 0, label: 'Close', val: 'X'}], width: '250px', modal: true});
				}
			});
		}
		$('#my_bookshelf').bind('click', function() {
			
		});
		</script>

		<input type="hidden" name="option" id="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="subtype" id="subtype" value="<?php echo $subtype; ?>" />
		<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
		</form>
		
		<?php module_footer(); ?>
		</div>
		<?php
	}
}
?>
