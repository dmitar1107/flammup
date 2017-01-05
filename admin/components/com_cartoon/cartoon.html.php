<?php
/**
* @version $Id: cartoon.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Cartoon
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
* @subpackage Cartoon
*/
class HTML_cartoon {

	function showCartoon( &$rows, $pageNav, $search, $option, $lists ) {
		global $Config_live_site, $type, $parent;
		?>
		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit">
			Cartoon Manager
			</th>
			<td>
			Filter:
			</td>
			<td>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
			<!--td width="right">
			<?php echo $lists['topic'];?>
			</td-->
			<td width="right">
			<?php echo $lists['classification'];?>
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="3%" class="title">
			#
			</th>
			<th width="2%" class="title">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title">
			Name
			</th>
			<th width="5%" nowrap="nowrap">
			Chapter
			</th>
			<th colspan="2" align="center" width="5%">
			Reorder
			</th>
			<th width="2%">
			Order
			</th>
			<th width="3%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )"><img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a>
			</th>
			<!--th width="10%" class="title" >
			Topic
			</th-->
			<th class="title" width="10%" nowrap="nowrap">
			Classification
			</th>
			<th nowrap="nowrap" width="10%">
			Recommended
			</th>
			<th width="10%" class="title" >
			Author
			</th>
			<th class="title" width="10%" nowrap="nowrap">
			Public Date
			</th>
			<th class="title" width="5%">
			Score
			</th>
			<th class="title" width="5%">
			View
			</th>
			<th class="title" width="5%">
			Download
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];

			$link 	= "index.php?option=com_cartoon&type=book&task=editA&id={$row->id}&hidemainmenu=1";
			$linkA 	= "index.php?option=com_cartoon&type=chapter&parent={$row->id}";
			$recommended 	= mosCommonHTML::RecommendedProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $i+1+$pageNav->limitstart;?>
				</td>
				<td>
				<?php echo mosHTML::idBox( $i, $row->id ); ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
				</td>
				<td align="center">
				<a href="<?php echo $linkA; ?>" title="Edit Chapters of Cartoon">
				<img src="<?php echo $Config_live_site; ?>/images/chapter.png" border="0"/>
				</a>
				</td>
				<td>
				<?php echo $pageNav->orderUpIcon( $i, ($row->class_id == @$rows[$i-1]->class_id) ); ?>
				</td>
				<td>
				<?php echo $pageNav->orderDownIcon( $i, $n, ($row->class_id == @$rows[$i+1]->class_id) ); ?>
				</td>
				<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td>
				<?php echo $row->classname; ?>
				</td>
				<td align="center">
				<?php echo $recommended;?>
				</td>
				<td>
				<?php echo $row->authorname; ?>
				</td>
				<!--td>
				<?php echo $row->topicname; ?>
				</td-->
				<td>
				<?php echo $row->public_date; ?>
				</td>
				<td>
				<?php echo $row->score; ?>
				</td>
				<td>
				<?php echo $row->view_num; ?>
				</td>
				<td>
				<?php echo $row->down_num; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="type" value="<?php echo $type;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	function showChapter( &$rows, &$row_parent, $pageNav, $search_chapter, $option, $lists ) {
		global $Config_live_site, $type, $parent;
		?>
		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit">
			Chapters of <?php echo $row_parent->name; ?>
			</th>
			<td>
			Filter:
			</td>
			<td>
			<input type="text" name="search_chapter" value="<?php echo htmlspecialchars( $search_chapter );?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="3%" class="title">
			#
			</th>
			<th width="2%" class="title">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title">
			Name
			</th>
			<th colspan="2" align="center" width="5%">
			Reorder
			</th>
			<th width="2%">
			Order
			</th>
			<th width="3%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )"><img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a>
			</th>
			<!--th width="10%" class="title" >
			Topic
			</th-->
			<th class="title" width="10%" nowrap="nowrap">
			Classification
			</th>
			<th width="10%" class="title" >
			Author
			</th>
			<th class="title" width="10%" nowrap="nowrap">
			Public Date
			</th>
			<th class="title" width="5%">
			Consumption<br />Point
			</th>
			<th class="title" width="5%">
			Score
			</th>
			<th class="title" width="5%">
			View
			</th>
			<th class="title" width="5%">
			Download
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];

			$link 	= "index.php?option=com_cartoon&type=chapter&task=editA&id={$row->id}&hidemainmenu=1&parent={$parent}";
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $i+1+$pageNav->limitstart;?>
				</td>
				<td>
				<?php echo mosHTML::idBox( $i, $row->id ); ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
				</td>
				<td>
				<?php echo $pageNav->orderUpIcon( $i, ($row->class_id == @$rows[$i-1]->class_id) ); ?>
				</td>
				<td>
				<?php echo $pageNav->orderDownIcon( $i, $n, ($row->class_id == @$rows[$i+1]->class_id) ); ?>
				</td>
				<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td>
				<?php echo $row->classname; ?>
				</td>
				<td>
				<?php echo $row->authorname; ?>
				</td>
				<!--td>
				<?php echo $row->topicname; ?>
				</td-->
				<td>
				<?php echo $row->public_date; ?>
				</td>
				<td>
				<?php echo $row->viewpoint; ?>
				</td>
				<td>
				<?php echo $row->score; ?>
				</td>
				<td>
				<?php echo $row->view_num; ?>
				</td>
				<td>
				<?php echo $row->down_num; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="type" value="<?php echo $type;?>" />
		<input type="hidden" name="parent" value="<?php echo $parent;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}
	
	function editCartoon( &$row, &$lists, $option, $uid ) {
		global $Config_live_site, $Config_cartoon_thumbnail_path_site, $type, $parent;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (trim(form.name.value) == "") {
				alert( "You must provide a name." );
			} else if (form.author_id.value == 0) {
				alert( "You must provide a author." );
			} else if (form.region.value == 0) {
				alert( "You must provide region." );
//			} else if (form.topic_id.value == 0) {
//				alert( "You must provide topic." );
			} else if (form.class_id.value == 0) {
				alert( "You must provide classification." );
			} else if (form.state.value == 0) {
				alert( "You must provide state." );
			} else if (form.thumbnail.value == 0 && form.thumbnail_img == null) {
				alert( "You must provide thumbnail." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">

		<table class="adminheading">
		<tr>
			<th class="edit">
			Cartoon: <small><?php echo $row->id ? 'Edit' : 'Add';?></small>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
			Cartoon Details
			</th>
		</tr>
		<tr>
			<td width="160">
			Name:
			</td>
			<td>
			<input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Desciption:
			</td>
			<td>
			<textarea class="inputbox" cols="50" rows="5" name="description"><?php echo $row->description; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
			Ordering:
			</td>
			<td>
			<?php echo $lists['ordering']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Author:
			</td>
			<td>
			<?php echo $lists['author']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Region:
			</td>
			<td>
			<?php echo $lists['region']; ?>
			</td>
		</tr>
		<!--tr>
			<td>
			Topic:
			</td>
			<td>
			<?php echo $lists['topic']; ?>
			</td>
		</tr-->
		<tr>
			<td>
			Classification:
			</td>
			<td>
			<?php echo $lists['class']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Public Date:
			</td>
			<td>
			<input type="text" name="public_date" class="inputbox" size="40" value="<?php echo $row->public_date; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			State:
			</td>
			<td>
			<?php echo $lists['state']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Score:
			</td>
			<td>
			<input type="text" name="score" class="inputbox" size="40" value="<?php echo $row->score; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			View Number:
			</td>
			<td>
			<input type="text" name="view_num" class="inputbox" size="40" value="<?php echo $row->view_num; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Down Number:
			</td>
			<td>
			<input type="text" name="down_num" class="inputbox" size="40" value="<?php echo $row->down_num; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Thumbnail:
			</td>
			<td>
			<input type="file" name="thumbnail" class="inputbox" size="40" />
			</td>
		</tr>
		<?php
		if($row->thumbnail_name) {
			$image_url = "$Config_cartoon_thumbnail_path_site/{$row->thumbnail_name}";
		?>
		<tr>
			<td>&nbsp;</td>
			<td>
				<img id='thumbnail_img' src='<?php echo $image_url; ?>' />
			</td>
		</tr>
		<?php
		}
		?>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="type" value="<?php echo $type;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	function editChapter( &$row, &$row_parent, &$lists, $option, $uid ) {
		global $Config_live_site, $Config_cartoon_thumbnail_path_site, $type, $parent;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if( trim(form.imagezip.value) != "" ) {
				var ext = getFileExtension(trim(form.imagezip.value));
				ext = ext.toLowerCase();
			}

			// do field validation
			if (trim(form.name.value) == "") {
				alert( "You must provide a name." );
			} else if ( trim(form.imagezip.value) != "" && ext != 'zip' ) {
				alert('Sorry! Only Support zip format. Please reattach zip file.');
			} else {
				submitform( pressbutton );
			}
		}
		function getFileExtension(filename)
		{
			var ext = /^.+\.([^.]+)$/.exec(filename);
			return ext == null ? "" : ext[1];
		}
		</script>
		<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">

		<table class="adminheading">
		<tr>
			<th class="edit">
			Chapter of <?php echo $row_parent->name; ?>: <small><?php echo $row->id ? 'Edit' : 'Add';?></small>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
			Chapter Details
			</th>
		</tr>
		<tr>
			<td width="160">
			Name:
			</td>
			<td>
			<input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Ordering:
			</td>
			<td>
			<?php echo $lists['ordering']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Public Date:
			</td>
			<td>
			<input type="text" name="public_date" class="inputbox" size="40" value="<?php echo $row->public_date; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Consumption Point:
			</td>
			<td>
			<input type="text" name="viewpoint" class="inputbox" size="40" value="<?php echo $row->viewpoint; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Score:
			</td>
			<td>
			<input type="text" name="score" class="inputbox" size="40" value="<?php echo $row->score; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			View Number:
			</td>
			<td>
			<input type="text" name="view_num" class="inputbox" size="40" value="<?php echo $row->view_num; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Down Number:
			</td>
			<td>
			<input type="text" name="down_num" class="inputbox" size="40" value="<?php echo $row->down_num; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Upload Cartoon Image ZIP File:
			</td>
			<td>
			<input type="file" name="imagezip" class="inputbox" size="40" />
			</td>
		</tr>
		<tr>
			<td>
			dat File Name:
			</td>
			<td>
			<?php echo $row->file_name; ?>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="type" value="<?php echo $type;?>" />
		<input type="hidden" name="parent" value="<?php echo $parent;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>
