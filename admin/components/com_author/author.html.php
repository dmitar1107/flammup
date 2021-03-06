<?php
/**
* @version $Id: author.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Author
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
* @subpackage Author
*/
class HTML_author {

	function showAuthor( &$rows, $pageNav, $search, $option, $lists ) {
		?>
		<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="user">
			Author Manager
			</th>
			<td>
			Filter:
			</td>
			<td>
			<input type="text" name="search" value="<?php echo htmlspecialchars( $search );?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="5%" class="title">
			#
			</th>
			<th width="5%" class="title">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th width="10%" class="title">
			ID
			</th>
			<th width="15%" class="title">
			Name
			</th>
			<th class="title" >
			Region
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row 	=& $rows[$i];

			$link 	= 'index.php?option=com_author&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $i+1+$pageNav->limitstart;?>
				</td>
				<td>
				<?php echo mosHTML::idBox( $i, $row->id ); ?>
				</td>
				<td>
				<?php echo $row->id; ?>
				</td>
				<td>
				<a href="<?php echo $link; ?>">
				<?php echo $row->name; ?>
				</a>
				<td>
				<?php echo $row->country; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	function editAuthor( &$row, &$lists, $option, $uid ) {
		global $Config_live_site, $Config_author_thumbnail_path_site;
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
			} else if (form.country.value == "") {
				alert( "You must provide a author region." );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">

		<table class="adminheading">
		<tr>
			<th class="user">
			Author: <small><?php echo $row->id ? 'Edit' : 'Add';?></small>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
			Author Details
			</th>
		</tr>
		<tr>
			<td width="130">
			Name:
			</td>
			<td>
			<input type="text" name="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td>
			Origin:
			</td>
			<td>
			<input type="text" name="country" class="inputbox" size="40" value="<?php echo $row->country; ?>" maxlength="25" />
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
			$image_url = "$Config_author_thumbnail_path_site/{$row->thumbnail_name}";
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
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>
