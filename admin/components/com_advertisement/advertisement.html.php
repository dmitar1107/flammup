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
class HTML_ad {
	function editAd( &$row, &$rows_cartoon, $option ) {
		global $Config_live_site, $Config_ad_path_site;
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
				alert( "You must provide a Ad name." );
//			} else if (trim(form.ad_file1.value) == "") {
//				alert( "You must attach ad file1." );
//			} else if (trim(form.ad_file2.value) == "") {
//				alert( "You must attach ad file2." );
//			} else if (trim(form.ad_file3.value) == "") {
//				alert( "You must attach ad file3." );
			} else {
				submitform( pressbutton );
			}
		}
		function openCartoonList(index) {
			window.open("index.php?option=advertisement&task=cartoonList&index="+index,"_blank","height=500,width=750,status=yes,toolbar=no,menubar=no,location=no");
		}
		</script>
		<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">

		<table class="adminheading">
		<tr>
			<th>
			Advertisement
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="3">
			Advertisement Details
			</th>
		</tr>
		<tr>
			<td width="130">
			Name:
			</td>
			<td colspan="2">
			<input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" />
			</td>
		</tr>
		<!--tr>
			<td>
			Desciption:
			</td>
			<td>
			<textarea class="inputbox" cols="50" rows="5" name="description"><?php echo $row->description; ?></textarea>
			</td>
		</tr-->
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
		<tr>
			<td>
			Cartoon Link1:
			</td>
			<td>
				<a href="javascript:openCartoonList(1);"><div id='cartoon_name1'><?php echo $rows_cartoon[$row->cartoon_id1]->name; ?></div></a>
				<input type="hidden" name="cartoon_id1" id="cartoon_id1" value="<?php echo $row->cartoon_id1; ?>" />
			</td>
		</tr>
		<tr>
			<td>
			Advertisement File1:
			</td>
			<td style="width:400px;">
				<input type="file" name="ad_file1" class="inputbox" size="40" />
			</td>
		</tr>
		<?php
		if($row->file_name1) {
			$image_url1 = $Config_ad_path_site ."/". $row->file_name1;
		?>
		<tr>
			<td>&nbsp;</td>
			<td>
				<img src='<?php echo $image_url1; ?>' />
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
		<tr>
			<td>
			Cartoon Link2:
			</td>
			<td>
				<a href="javascript:openCartoonList(2);"><div id='cartoon_name2'><?php echo $rows_cartoon[$row->cartoon_id2]->name; ?></div></a>
				<input type="hidden" name="cartoon_id2" id="cartoon_id2" value="<?php echo $row->cartoon_id2; ?>" />
			</td>
		</tr>
		<tr>
			<td>
			Advertisement File2:
			</td>
			<td style="width:400px;">
				<input type="file" name="ad_file2" class="inputbox" size="40" />
			</td>
		</tr>
		<?php
		if($row->file_name2) {
			$image_url2 = $Config_ad_path_site ."/". $row->file_name2;
		?>
		<tr>
			<td>&nbsp;</td>
			<td>
				<img src='<?php echo $image_url2; ?>' />
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
		<tr>
			<td>
			Cartoon Link3:
			</td>
			<td>
				<a href="javascript:openCartoonList(3);"><div id='cartoon_name3'><?php echo $rows_cartoon[$row->cartoon_id3]->name; ?></div></a>
				<input type="hidden" name="cartoon_id3" id="cartoon_id3" value="<?php echo $row->cartoon_id3; ?>" />
			</td>
		</tr>
		<tr>
			<td>
			Advertisement File3:
			</td>
			<td style="width:400px;">
				<input type="file" name="ad_file3" class="inputbox" size="40" />
			</td>
		</tr>
		<?php
		if($row->file_name3) {
			$image_url3 = $Config_ad_path_site ."/". $row->file_name3;
		?>
		<tr>
			<td>&nbsp;</td>
			<td>
				<img src='<?php echo $image_url3; ?>' />
			</td>
		</tr>
		<?php
		}
		?>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	function showCartoon( &$rows, $pageNav, $search, $option, $lists, $index ) {
		global $Config_live_site, $type, $parent, $task;
		?>
		<script type="text/javascript">
		function selectCartoon(id, name) {
			window.opener.document.getElementById('cartoon_id<?php echo $index; ?>').value = id;
			window.opener.document.getElementById('cartoon_name<?php echo $index; ?>').innerHTML = name;
			window.close();
		}
		</script>
		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="edit">
			Select Cartoon
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
			<th width="5%" class="title">
			#
			</th>
			<th class="title">
			Name
			</th>
			<th class="title" width="15%" nowrap="nowrap">
			Classification
			</th>
			<th width="15%" class="title" >
			Author
			</th>
			<th class="title" width="15%" nowrap="nowrap">
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
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $i+1+$pageNav->limitstart;?>
				</td>
				<td>
					<a href="javascript:selectCartoon(<?php echo $row->id; ?>, '<?php echo $row->name; ?>');"><?php echo $row->name; ?></a>
				</td>
				<td>
				<?php echo $row->classname; ?>
				</td>
				<td>
				<?php echo $row->authorname; ?>
				</td>
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
		<input type="hidden" name="task" value="<?php echo $task;?>" />
		<input type="hidden" name="index" value="<?php echo $index;?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}
}
?>
