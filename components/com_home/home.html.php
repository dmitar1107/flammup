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
class HTML_fm_home {

	function cartoon_list( &$rows ) {
		global $option, $type, $subtype, $task, $cid, $class_id;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		?>
		<div data-role="page" id="home_pagediv">
		<?php HTML_fm_home::type_button_menu();	?>
		<form name='myform' id='myform' action="index.php?option=<?php echo $option; ?>" method="post" target="_self" data-ajax="true">
		<div class="ui-grid-a">
			<div class="ui-block-a" align='right' id='home_block_a'>
				<div data-role="fieldcontain">
					<label for="name"></label>
					<input type="search" name="friend_name" id="friend_name" value=""  />
				</div>	
			</div>
			<div class="ui-block-b" id='home_block_b'>
				<a data-role="button" data-inline="true" class='home_search' id='bubble'>冒个泡</a>
			</div>
		</div><!-- /grid-b -->

		<div data-role="content" data-theme = 't'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list'>
				<?php
				foreach ($rows as $row) {
					$a_star_num = floor($row->score/500);
					$b_star_num = 5 - $a_star_num;
					$link_path = $Config_live_site ."/index.php?option={$option}&task=view&cid={$row->id}";
					?>
					<li class='frieds'>
						<img class='vertical_image'  src='<?php echo $Config_cartoon_thumbnail_path_site ."/". $row->thumbnail_name; ?>' />
						<p style='width:50%;' class='friend_p'><?php echo $row->name ? $row->name : "&nbsp;"; ?></p>
						<div class="ui-grid-a">
							<div class="ui-block-a"><p class='full_width' ><?php echo $row->description; ?></p></div>
							<div class="ui-block-b" align='right' style='color:red;'><p>1</p></div>
						</div><!-- /grid-b -->
						<p class="ui-li-aside" style='width:50%;'>1小时前</p>
					</li>
					<?php
				}
				?>
				</ul>
			</div>
			<input type='hidden' name='type' id="type" value=""/>
			</form>
			<script>
			$("#bubble").live("click",function() {
				var val = $("#friend_name").val();
				$("#type").val("search_friend");
				$('#myform').submit();
			});
			</script>
		</div>
		<script type="text/javascript">
		$("#xiaobian_recommend").click(function(){
			$(".bh_pages").css("display","none");
			$("#xiaobian_recommend_pages").css("display","");
		});
		$("#user_recommend").click(function(){
			$(".bh_pages").css("display","none");
			$("#user_recommend_pages").css("display","");
		});
		$("#last_update").click(function(){
			$(".bh_pages").css("display","none");
			$("#last_update_pages").css("display","");

		});

		$('a.button_order').each(function(index) {
			if (index == 0)
				{
				$(this).css("border-right","3px solid black");
				$(this).css("border-right-style","outset");
				$(this).css('background-image','url(pages/images/amount_button.png)');
			}
			else if (index == 1)
				{
				$(this).css("border-right","3px solid black");
				$(this).css("border-right-style","outset");
				$(this).css("border-left-style","hidden");
			}
			else if ( index == 2)
				{
				$(this).css("border-left-style","hidden");
			}
			//   alert(index + ': ' + $(this).text());
		});
		$('a.button_order').click(function() {
			$("a.button_order").css("background-image",'url("pages/images/ranking_order_buttons.png")');
			$(this).css("background-image",'url("pages/images/amount_button.png")');
		});
		</script>
		
		<input type="hidden" name="type" id="type" value="" />
		<input type="hidden" name="subtype" id="subtype" value="" />
		<input type="hidden" name="cid" id="cid" value="" />
		<input type="hidden" name="task" id="task" value="" />
		</form>
		
		<?php module_footer(); ?>
		</div>
		<?php
	}

	function earn_point() {
		global $option, $type, $subtype, $task, $cid, $class_id;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		?>
		<div data-role="page" id="home_pagediv">
		<?php HTML_fm_home::type_button_menu();	?>
		<form name='myform' id='myform' action="index.php?option=<?php echo $option; ?>" method="post" target="_self" data-ajax="true">
		<div class="ui-grid-a" style='border-bottom:1px solid green;padding:10px;'>
			<div class="ui-block-a" align='left'><a href="index.html" data-role="button" data-inline="true">赚积分</a></div>
			<div class="ui-block-b" align='right'><a href="index.html" data-role="button" data-inline="true">积分规则</a></div>
		</div>		
		<div class="ui-grid-a">
			<div class="ui-block-a" align='right' id='home_earn_a'>
	
			<div data-role="fieldcontain" id='home_textarea'>
				<label for="textarea"></label>
					<textarea name="textarea" id="textarea"></textarea>	
				</div>
			</div>
			<div class="ui-block-b" align='left' id='home_earn_b'><a data-role="button" class='announ' data-inline="true" id="posted">发表心情</a></div>
		</div>
		<div data-role="content" data-theme='t'>
			<div class="content-primary">
				<ul data-role="listview">
					<li>
						<?php
						echo "<img src='$Config_live_site/templates/$Config_cur_template/images/book3.png'  class = 'vertical_image'/>";
						?>						
						<p class="down_content_p">Hey Stephen, if you're available at 10am tomorrow, we've got a meeting with the jQuery team.</p>
						<div align = 'right' class='down_divs_button'>
							<a href="index.php?option=library&task=read_cartoon&cid=<?php echo $row_child->id; ?>&pid=<?php echo $cid; ?>" data-role="button" data-inline="true" data-theme='e' data-corners="false" data-ajax="true">加为好友</a>
							<a href="" id="download_cartoon<?php echo $row_child->id; ?>" data-role="button" data-inline="true" data-theme='e' data-corners="false">取消</a>
						</div>
					</li>
				</ul>
			</div><!--/content-primary -->	
		</div>
		<input type='hidden' name='type' id="type" value=""/>
		</form>
		<script>
			$("#bubble").live("click",function() {
					var val = $("#friend_name").val();
					$("#type").val("search_friend");
					$('#myform').submit();
			});
		</script>
		<?php module_footer(); ?>
		</div>
		<?php
	}
	
	function friend_field() {
		global $option, $type, $subtype, $task, $cid, $class_id;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		?>
		<div data-role="page" id="friend_pagediv">
		<?php HTML_fm_home::type_button_menu();	?>
		<form name='myform' id='myform' action="index.php?option=<?php echo $option; ?>" method="post" target="_self" data-ajax="true">
		<div class="ui-grid-a" style='border-bottom:1px solid green;margin:10px;'>
			<div class="ui-block-a" align='right' id='friend_a'>
				<div data-role="fieldcontain">
					<label for="textarea"></label>
					<input type="search" name="password" id="search" value="" />
				</div>
			</div>
			<div class="ui-block-b" align='left' id='friend_b'><a data-role="button" class='announ' data-inline="true">找朋友</a></div>
		</div>

		<div data-role="content" data-theme = 't'>
			<div class="content-primary">	
				<ul data-role="listview" class='vertical_list'>
					<li class='frieds'>
							<?php
                                echo "<img src='$Config_live_site/templates/$Config_cur_template/images/book3.png'  class = 'vertical_image'/>";
								?>
							<p class='friend_p' style='width:50%;'>123</p>
							<div class="ui-grid-a">
								<div class="ui-block-a"><p class='full_width'>213123123</p></div>
								<div class="ui-block-b" align='right' style='color:green;'><p>留言</p></div>
							</div><!-- /grid-b -->
							<p class="ui-li-aside" style='width:50%;'>1小时前</p>
					</li>
				</ul>
			</div>
		</div>	

		<input type='hidden' name='type' id="type" value=""/>
		</form>
		<script>
			$("#bubble").live("click",function() {
					var val = $("#friend_name").val();
					$("#type").val("search_friend");
					$('#myform').submit();
			});
		</script>
		<?php module_footer(); ?>
		</div>
		<?php
	}
		
	function about_me() {
		global $option, $type, $subtype, $task, $cid, $class_id;
		global $Config_live_site, $Config_cur_template, $Config_cartoon_thumbnail_path_site, $Config_ad_path_site;
		?>
		<div data-role="page" id="about_pagediv">
		<?php HTML_fm_home::type_button_menu();	?>
		<div data-role='header' data-theme='h'>
			<div class="ui-grid-a">
				<div class="ui-block-a" style='width:125px;display:inline-block;'>
					<div class="ui-bar ui-bar-h" id='detail_title_image' style="height:140px;">
						<img width = '100' height = '120' src='<?php echo $Config_cartoon_thumbnail_path_site ."/".'3.png'; ?>' class='template'/>
					</div>
				</div>
				<div class="ui-block-b" id='detail_block_two'>
					<div class="ui-bar ui-bar-h" id='detail_title_content' style="height:140px">
						<p style='margin:7px;margin-top:20px;'>ID: <?php echo $Config_state[$row->state]; ?></p>
						<p style='margin:7px;'>积分: <?php echo $row_author->name; ?></p>
						<a href="#" data-role="button"  data-theme="b" data-inline="true" id='detail_button' style='font-size:15px;width:90px;' data-icon = 'detail_lib' icon-pos = 'left'>关注14</a>
						<a href="#" data-role="button" data-theme="b" data-inline="true" style='font-size:15px;width:auto;'>粉丝320</a>
					</div>
				</div>
			</div><!-- /grid-a -->	
		</div>
		<div class="ui-grid-a">
			<div class="ui-block-a" align='center'><p>昵称：淘气的猫</p></div>
			<div class="ui-block-b">

				<a href="index.html" data-role="button" data-inline="true">修改</a>
				<a href="index.html" data-role="button" data-inline="true">确定</a>	

			</div>
		</div><!-- /grid-b -->
		<div data-role="content">
			<div class="content-primary">
				<ul data-role="listview" data-inset="true">
					<li>完成的任务：2个</li>
					<li>参加的活动：5个</li>
					<li>待办的任务：2个</li>
				</ul>
			</div>
		</div>
		<?php module_footer(); ?>
		</div>
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
					if( $type == 'active_field' )
						echo "<div class='ui-block-a'><a href='index.php?option=home&type=active_field' data-role='button' data-theme='g' id='type_approve' class='ui-btn-active'>活动场</a></div>";
					else
						echo "<div class='ui-block-a'><a href='index.php?option=home&type=active_field' data-role='button' data-theme='g' id='type_approve'>活动场</a></div>";
					if( $type == 'earn_point' )
						echo "<div class='ui-block-b'><a href='index.php?option=home&type=earn_point' data-role='button' data-theme='g' id='type_ranking' class='ui-btn-active'>赚积分</a></div>";
					else
						echo "<div class='ui-block-b'><a href='index.php?option=home&type=earn_point' data-role='button' data-theme='g' id='type_ranking'>赚积分</a></div>";
					if( $type == 'friend_field' )
						echo "<div class='ui-block-c'><a href='index.php?option=home&type=friend_field' data-role='button' data-theme='g' id='type_classification' class='ui-btn-active'>朋友圈</a></div>";
					else
						echo "<div class='ui-block-c'><a href='index.php?option=home&type=friend_field' data-role='button' data-theme='g' id='type_classification'>朋友圈</a></div>";
					if( $type == 'about_me' )
						echo "<div class='ui-block-d'><a href='index.php?option=home&type=about_me' data-role='button' data-theme='g' id='type_search' class='ui-btn-active'>关于我</a></div>";
					else
						echo "<div class='ui-block-d'><a href='index.php?option=home&type=about_me' data-role='button' data-theme='g' id='type_search'>关于我</a></div>";
				?>
			</fieldset>
		</div>
		<?php
	}
}
?>
