<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$database = new database( $Config_host, $Config_user, $Config_password, $Config_db, $Config_dbprefix );

class MainFrame {
	/** @var database Internal database class pointer */
	var $_db						= null;
	var $_userstate					= null;

	/**
	* Class constructor
	* @param database A database connection object
	*/
	function MainFrame( &$db ) {
		$this->_db =& $db;

		if (isset( $_SESSION['session_userstate'] )) {
			$this->_userstate =& $_SESSION['session_userstate'];
		} else {
			$this->_userstate = null;
		}
	}
	
	/**
	* Initialises the user session
	*/
	function initSession() {
		global $Config_lifetime;
		
		if( !isset($_SESSION['admin_logintime']) )
			$this->logout();
		
		if( time() - $_SESSION['admin_logintime'] < $Config_lifetime ) {
			$_SESSION['admin_logintime'] = time();
		} else {
			$msg = "Admin Session Expired ";
			$this->logout($msg);
		}
	}

	/**
	* User login
	*/
	function login() {
		global $database;
		$username = mosGetParam($_REQUEST, 'usrname', '');
		$password = mosGetParam($_REQUEST, 'pass', '');
		
		$query = "SELECT * FROM #__admin WHERE username='$username' AND password=md5('$password')";
		$database->setQuery($query);
		$row = null;
		$database->loadObject($row);
		if( $row == null ) {
			mosRedirect("index.php?mosmsg=Incorrect Username or Password. Please try again");
		}
		
		$obj = new mosAdminUser($database);
		$obj->load($row->id);
		$obj->login_date = date("Y-m-d H:i:s");
		$obj->store();
		
		$_SESSION['admin_logintime'] 		= time();
		$_SESSION['admin_name'] 			= $row->name;
		$_SESSION['admin_username'] 		= $row->username;
		$_SESSION['session_userstate'] 		= array();
		
		mosRedirect("index.php");
	}
	
	/**
	* User logout
	*/
	function logout($msg='') {
		$_SESSION['admin_logintime'] 		= null;
		$_SESSION['admin_name'] 			= null;
		$_SESSION['admin_username'] 		= null;
		$_SESSION['session_userstate'] 		= null;
		
		$url = 'index.php';
		if($msg)
			$url .= '?mosmsg='. $msg;
		mosRedirect($url);
	}
	
	/**
	* Determines the paths for including engine and menu files
	* @param string The current option used in the url
	* @param string The base path from which to load the configuration file
	*/
	function getPath( $option, $type='php' ) {
		global $Config_absolute_path;
		$option 		= strtolower( $option );

		// security check to disable use of `/`, `\\` and `:` in $options variable
		if (strpos($option, '/') !== false || strpos($option, '\\') !== false || strpos($option, ':') !== false) {
			mosErrorAlert( 'Restricted access' );
			return;
		}

		$prefix = substr( $option, 0, 4 );
		if ($prefix != 'com_' && $prefix != 'mod_') {
			// ensure backward compatibility with existing links
			$name 	= $option;
			$option = "com_$option";
		} else {
			$name 	= substr( $option, 4 );
		}
		
		$url = "";

		// components
		if( $type == "php" ) {
			if (file_exists( "$Config_absolute_path/components/com_$name/$name.php" ))
				$url = "$Config_absolute_path/components/com_$name/$name.php";
		} else if ($type == "html") {
			if (file_exists( "$Config_absolute_path/components/com_$name/$name.html.php" ))
				$url = "$Config_absolute_path/components/com_$name/$name.html.php";
		} else if ($type == "class") {
			if (file_exists( "$Config_absolute_path/components/com_$name/$name.class.php" ))
				$url = "$Config_absolute_path/components/com_$name/$name.class.php";
		} else if ($type == "toolbar") {
			if (file_exists( "$Config_absolute_path/components/com_$name/toolbar.$name.php" ))
				$url = "$Config_absolute_path/components/com_$name/toolbar.$name.php";
		} else if ($type == "toolbar_html") {
			if (file_exists( "$Config_absolute_path/components/com_$name/toolbar.$name.html.php" ))
				$url = "$Config_absolute_path/components/com_$name/toolbar.$name.html.php";
		}
		
		return $url;
	}
	
	/**
	* Gets the value of a user state variable
	* @param string The name of the variable
	*/
	function getUserState( $var_name ) {
		if (is_array( $this->_userstate )) {
			return mosGetParam( $this->_userstate, $var_name, null );
		} else {
			return null;
		}
	}
	/**
	* Gets the value of a user state variable
	* @param string The name of the user state variable
	* @param string The name of the variable passed in a request
	* @param string The default value for the variable if not found
	*/
	function getUserStateFromRequest( $var_name, $req_name, $var_default=null ) {
		if (is_array( $this->_userstate )) {
			if (isset( $_REQUEST[$req_name] )) {
				$this->setUserState( $var_name, $_REQUEST[$req_name] );
			} else if (!isset( $this->_userstate[$var_name] )) {
				$this->setUserState( $var_name, $var_default );
			}

			// filter input
			$iFilter = new InputFilter();
			$this->_userstate[$var_name] = $iFilter->process( $this->_userstate[$var_name] );

			return $this->_userstate[$var_name];
		} else {
			return null;
		}
	}
	/**
	* Sets the value of a user state variable
	* @param string The name of the variable
	* @param string The value of the variable
	*/
	function setUserState( $var_name, $var_value ) {
		if (is_array( $this->_userstate )) {
			$this->_userstate[$var_name] = $var_value;
		}
	}
}

/**
* Loads an admin module
*/
function mosLoadAdminModule( $name, $params=NULL ) {
	global $Config_absolute_path, $Config_live_site, $task;
	global $database, $mainframe, $option;

	$name = str_replace( '/', '', $name );
	$name = str_replace( '\\', '', $name );
	$path = "$Config_absolute_path/modules/mod_$name.php";
	if (file_exists( $path )) {
		require $path;
	}
}

define( "_MOS_NOTRIM", 0x0001 );
define( "_MOS_ALLOWHTML", 0x0002 );
define( "_MOS_ALLOWRAW", 0x0004 );

function mosGetParam( &$arr, $name, $def=null, $mask=0 )
{
	static $noHtmlFilter 	= null;
	static $safeHtmlFilter 	= null;

	$return = null;
	if (isset( $arr[$name] )) {
		$return = $arr[$name];
		
		if (is_string( $return )) {
			// trim data
			if (!($mask&_MOS_NOTRIM)) {
				$return = trim( $return );
			}
			
			if ($mask&_MOS_ALLOWRAW) {
				// do nothing
			} else if ($mask&_MOS_ALLOWHTML) {
				// do nothing - compatibility mode
				/*
				if (is_null( $safeHtmlFilter )) {
					$safeHtmlFilter = new InputFilter( null, null, 1, 1 );
				}
				$arr[$name] = $safeHtmlFilter->process( $arr[$name] );
				*/
			} 
			// account for magic quotes setting
			if (!get_magic_quotes_gpc()) {
				$return = addslashes( $return );
			}
		}
		
		return $return;
	} else {
		return $def;
	}
}

function mosRedirect( $url, $msg='' ) {

   global $mainframe;

    // specific filters
	$iFilter = new InputFilter();
	$url = $iFilter->process( $url );
	if (!empty($msg)) {
		$msg = $iFilter->process( $msg );
	}

	// Strip out any line breaks and throw away the rest
	$url = preg_split("/[\r\n]/", $url);
	$url = $url[0];

	if ($iFilter->badAttributeValue( array( 'href', $url ))) {
		$url = $GLOBALS['mosConfig_live_site'];
	}

	if (trim( $msg )) {
	 	if (strpos( $url, '?' )) {
			$url .= '&mosmsg=' . urlencode( $msg );
		} else {
			$url .= '?mosmsg=' . urlencode( $msg );
		}
	}

	if (headers_sent()) {
		echo "<script>document.location.href='$url';</script>\n";
	} else {
		@ob_end_clean(); // clear output buffer
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( "Location: ". $url );
	}
	exit();
}

/**
*	해당한 배렬만을 얻어내기
*/
function get_Need_arr($arr){


	$return_arr=array();
	$flag = 0;
	for($i=0; $i<count($arr); $i++){
		$temp_arr = $arr[$i];
		if($temp_arr['type']=='complete')
			$flag = 1; else $flag = 0;
		if($flag == 1)
		{
			array_push($return_arr, $temp_arr);
		}
	}
	return $return_arr;
}

/**
* Copy the named array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
*/
function mosBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
	if (!is_array( $array ) || !is_object( $obj )) {
		return (false);
	}

	foreach (get_object_vars($obj) as $k => $v) {
		if( substr( $k, 0, 1 ) != '_' ) {			// internal attributes of an object are ignored
			if (strpos( $ignore, $k) === false) {
				if ($prefix) {
					$ak = $prefix . $k;
				} else {
					$ak = $k;
				}
				if (isset($array[$ak])) {
					$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? mosStripslashes( $array[$ak] ) : $array[$ak];
				}
			}
		}
	}
	return true;
}

/**
 * Strip slashes from strings or arrays of strings
 * @param mixed The input string or array
 * @return mixed String or array stripped of slashes
 */
function mosStripslashes( &$value ) {
	$ret = '';
	if (is_string( $value )) {
		$ret = stripslashes( $value );
	} else {
		if (is_array( $value )) {
			$ret = array();
			foreach ($value as $key => $val) {
				$ret[$key] = mosStripslashes( $val );
			}
		} else {
			$ret = $value;
		}
	}
	return $ret;
}

function mosTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 ) {

	if (@$children[$id] && $level <= $maxlevel) {
		foreach ($children[$id] as $v) {
			$id = $v->id;

			if ( $type ) {
				$pre 	= '<sup>L</sup>&nbsp;';
				$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			} else {
				$pre 	= '- ';
				$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;';
			}

			if ( $v->parent == 0 ) {
				$txt 	= $v->name;
			} else {
				$txt 	= $pre . $v->name;
			}
			$pt = $v->parent;
			$list[$id] = $v;
			$list[$id]->treename = "$indent$txt";
			$list[$id]->children = count( @$children[$id] );

			$list = mosTreeRecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
		}
	}
	return $list;
}

function mosErrorAlert( $text, $action='window.history.go(-1);', $mode=1 ) {
	$text = nl2br( $text );
	$text = addslashes( $text );
	$text = strip_tags( $text );

	switch ( $mode ) {
		case 2:
			echo "<script>$action</script> \n";
			break;

		case 1:
		default:
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; "._ISO."\" />";
			echo "<script>alert('$text'); $action</script> \n";
			//echo '<noscript>';
			//mosRedirect( @$_SERVER['HTTP_REFERER'], $text );
			//echo '</noscript>';
			break;
	}

	exit;
}

function mosMainBody() {
	echo $GLOBALS['_MOS_BODY_BUFFER'];
}

function mosTreeRecurseSpace( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 ) {

	if (@$children[$id] && $level <= $maxlevel) {
		foreach ($children[$id] as $v) {
			$id = $v->id;

			if ( $type ) {
				$pre 	= '<sup>L</sup>　';
				$spacer = '.　　　　　';
			} else {
				$pre 	= '- ';
				$spacer = '.　　';
			}

			if ( $v->parent == 0 ) {
				$txt 	= $v->name;
			} else {
				$txt 	= $pre . $v->name;
			}
			$pt = $v->parent;
			$list[$id] = $v;
			$list[$id]->treename = "$indent$txt";
			$list[$id]->children = count( @$children[$id] );

			$list = mosTreeRecurseSpace( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
		}
	}
	return $list;
}

/**
* Utility class for all HTML drawing classes
* @package Joomla
*/
class mosHTML {
	function quickiconButton($text,$icon_name,$attrib=null){		
		echo "<div id='cpanel' ".$attrib.">";
		echo "<div style='float:left'>";
		echo "<div class='icon'>";
		echo "<a href='#' >";
		echo "<img src='images/header/".$icon_name."' alt='".$text."' style='border:0px'>";
		echo "<span>".$text."</span>";
		echo "</a>";
		echo "</div>";
		echo "</div>";
		echo "</div>";		
	}

	function makeOption( $value, $text='', $value_name='value', $text_name='text' ) {
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim( $text ) ? $text : $value;
		return $obj;	
	}
	function writableCell( $folder ) {

		echo '<tr>';
		echo '<td class="item">' . $folder . '/</td>';
		echo '<td align="left">';
		echo is_writable( "../$folder" ) ? '<b><font color="green">Writeable</font></b>' : '<b><font color="red">Unwriteable</font></b>' . '</td>';
		echo '</tr>';
	}

	/**
	* Generates an HTML select list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	function selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL ) {
		reset( $arr );
		$html = "\n<select name=\"$tag_name\" id=\"$tag_name\" $tag_attribs>";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"".$k."\"$extra>" . $t . "</option>";
		}
		$html .= "\n</select>\n";
		return $html;
	}

	/**
	* Writes a select list of integers
	* @param int The start integer
	* @param int The end integer
	* @param int The increment
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The printf format to be applied to the number
	* @returns string HTML for the select list
	*/
	function integerSelectList( $start, $end, $inc, $tag_name, $tag_attribs, $selected=null, $format="" ) {
		$start 	= intval( $start );
		$end 	= intval( $end );
		$inc 	= intval( $inc );
		$arr 	= array();

		for ($i=$start; $i <= $end; $i+=$inc) {
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = mosHTML::makeOption( $fi, $fi );
		}

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Writes a select list of month names based on Language settings
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	function monthSelectList( $tag_name, $tag_attribs, $selected=null ) {
		$arr = array(
			mosHTML::makeOption( '01', _JAN ),
			mosHTML::makeOption( '02', _FEB ),
			mosHTML::makeOption( '03', _MAR ),
			mosHTML::makeOption( '04', _APR ),
			mosHTML::makeOption( '05', _MAY ),
			mosHTML::makeOption( '06', _JUN ),
			mosHTML::makeOption( '07', _JUL ),
			mosHTML::makeOption( '08', _AUG ),
			mosHTML::makeOption( '09', _SEP ),
			mosHTML::makeOption( '10', _OCT ),
			mosHTML::makeOption( '11', _NOV ),
			mosHTML::makeOption( '12', _DEC )
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML select list from a tree based query list
	* @param array Source array with id and parent fields
	* @param array The id of the current list item
	* @param array Target array.  May be an empty array.
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	function treeSelectList( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected ) {

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($src_list as $v ) {
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items

		$ilist = mosTreeRecurse( $src_id, '', array(), $children );
		// assemble menu items to the array
		$this_treename = '';
		foreach ($ilist as $item) {
			if ($this_treename) {
				if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
					$tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
				}
			} else {
				if ($item->id != $src_id) {
					$tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
				} else {
					$this_treename = "$item->treename/";
				}
			}
		}
		// build the html select list
		return mosHTML::selectList( $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
	}

	/**
	* Writes a yes/no select list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	function yesnoSelectList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		$arr = array(
		mosHTML::makeOption( '0', $no ),
		mosHTML::makeOption( '1', $yes ),
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML radio list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @returns string HTML for the select list
	*/
	function radioList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"".$k."\"$extra $tag_attribs />";
			$html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
		}
		$html .= "\n";
		return $html;
	}

	/**
	* Writes a yes/no radio list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	function yesnoRadioList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
		$arr = array(
		mosHTML::makeOption( '0', $no ),
		mosHTML::makeOption( '1', $yes )
		);
		return mosHTML::radioList( $arr, $tag_name, $tag_attribs, $selected );
	}

	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	* @return string
	*/
	function idBox( $rowNum, $recId, $checkedOut=false, $name='cid' ) {
		if ( $checkedOut ) {
			return '';
		} else {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />';
		}
	}


	function sortIcon( $base_href, $field, $state='none' ) {
		global $Config_live_site;

		$alts = array(
			'none' 	=> _CMN_SORT_NONE,
			'asc' 	=> _CMN_SORT_ASC,
			'desc' 	=> _CMN_SORT_DESC,
		);
		$next_state = 'asc';
		if ($state == 'asc') {
			$next_state = 'desc';
		} else if ($state == 'desc') {
			$next_state = 'none';
		}

		$html = "<a href=\"$base_href&field=$field&order=$next_state\">"
		. "<img src=\"$Config_live_site/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
		. "</a>";
		return $html;
	}

	/**
	* Writes Close Button
	*/
	function CloseButton ( &$params, $hide_js=NULL ) {
		// displays close button in Pop-up window
		if ( $params->get( 'popup' ) && !$hide_js ) {
			?>
			<div align="center" style="margin-top: 30px; margin-bottom: 30px;">
			<a href='javascript:window.close();'>
			<span class="small">
			<?php echo _PROMPT_CLOSE;?>
			</span>
			</a>
			</div>
			<?php
		}
	}

	/**
	* Writes Back Button
	*/
	function BackButton ( &$params, $hide_js=NULL ) {
		// Back Button
		if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
			?>
			<div class="back_button">
			<a href='javascript:history.go(-1)'>
			<?php echo _BACK; ?>
			</a>
			</div>
			<?php
		}
	}

	/**
	* Cleans text of all formating and scripting code
	*/
	function cleanText ( &$text ) {
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		$text = htmlspecialchars( $text );
		return $text;
	}

	/**
	* Writes Print icon
	*/
	function PrintIcon( &$row, &$params, $hide_js, $link, $status=NULL ) {
		global $Config_live_site, $Config_absolute_path, $cur_template, $Itemid;
		if ( $params->get( 'print' )  && !$hide_js ) {
			// use default settings if none declared
			if ( !$status ) {
				$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			}

			// checks template image directory for image, if non found default are loaded
			if ( $params->get( 'icons' ) ) {
				$image = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT, _CMN_PRINT );
			} else {
				$image = _ICON_SEP .'&nbsp;'. _CMN_PRINT. '&nbsp;'. _ICON_SEP;
			}

			if ( $params->get( 'popup' ) && !$hide_js ) {
				// Print Preview button - used when viewing page
				?>
				<td align="right" width="100%" class="buttonheading">
				<a href="javascript:void(0)" onclick="javascript:window.print(); return false" title="<?php echo _CMN_PRINT;?>">
				<?php echo $image;?>
				</a>
				</td>
				<?php
			} else {
				// Print Button - used in pop-up window
				?>
				<td align="right" width="100%" class="buttonheading">
				<a href="javascript:void(0)" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>');" title="<?php echo _CMN_PRINT;?>">
				<?php echo $image;?>
				</a>
				</td>
				<?php
			}
		}
	}

	/**
	* simple Javascript Cloaking
	* email cloacking
 	* by default replaces an email with a mailto link with email cloacked
	*/
	function emailCloaking( $mail, $mailto=1, $text='', $email=1 ) {
		// convert text
		$mail 		= mosHTML::encoding_converter( $mail );
		// split email by @ symbol
		$mail		= explode( '@', $mail );
		$mail_parts	= explode( '.', $mail[1] );
		// random number
		$rand	= rand( 1, 100000 );

		$replacement 	= "\n<script language='JavaScript' type='text/javascript'> \n";
		$replacement 	.= "<!-- \n";
		$replacement 	.= "var prefix = '&#109;a' + 'i&#108;' + '&#116;o'; \n";
		$replacement 	.= "var path = 'hr' + 'ef' + '='; \n";
		$replacement 	.= "var addy". $rand ." = '". @$mail[0] ."' + '&#64;'; \n";
		$replacement 	.= "addy". $rand ." = addy". $rand ." + '". implode( "' + '&#46;' + '", $mail_parts ) ."'; \n";
		if ( $mailto ) {
			// special handling when mail text is different from mail addy
			if ( $text ) {
				if ( $email ) {
					// convert text
					$text 	= mosHTML::encoding_converter( $text );
					// split email by @ symbol
					$text 	= explode( '@', $text );
					$text_parts	= explode( '.', $text[1] );
					$replacement 	.= "var addy_text". $rand ." = '". @$text[0] ."' + '&#64;' + '". implode( "' + '&#46;' + '", @$text_parts ) ."'; \n";
				} else {
					//$text 	= mosHTML::encoding_converter( $text );
					$replacement 	.= "var addy_text". $rand ." = '". $text ."';\n";
				}
				$replacement 	.= "document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' ); \n";
				$replacement 	.= "document.write( addy_text". $rand ." ); \n";
				$replacement 	.= "document.write( '<\/a>' ); \n";
			} else {
				$replacement 	.= "document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' ); \n";
				$replacement 	.= "document.write( addy". $rand ." ); \n";
				$replacement 	.= "document.write( '<\/a>' ); \n";
			}
		} else {
			$replacement 	.= "document.write( addy". $rand ." ); \n";
		}
		$replacement 	.= "//--> \n";
		$replacement 	.= "</script>";
		$replacement 	.= "<noscript> \n";
		$replacement 	.= _CLOAKING;
		$replacement 	.= "\n</noscript>";

		return $replacement;
	}

	function encoding_converter( $text ) {
		// replace vowels with character encoding
		$text 	= str_replace( 'a', '&#97;', $text );
		$text 	= str_replace( 'e', '&#101;', $text );
		$text 	= str_replace( 'i', '&#105;', $text );
		$text 	= str_replace( 'o', '&#111;', $text );
		$text	= str_replace( 'u', '&#117;', $text );

		return $text;
	}
}

/**
 * 날자클라스
 * @version 1.0
 * @created 2009-07-22 11:50:36 AM
 */
class DateClass {
	var $cur_year 		= null;
	var $cur_month 		= null;
	var $cur_week 		= null;
	var $cur_day 		= null;
	var $cur_weekday 	= null;
	var $cur_lastday 	= null;

	function DateClass() {
		$this->cur_year 		= date("Y");
		$this->cur_month 		= date("n");
		$this->cur_day 			= date("j");
		$this->cur_weekday 		= date("w");
		$this->cur_lastday 		= date("t");
		
		// 현재주얻기
		$first_weekday	= date("w", mktime(0, 0, 0, $this->cur_month, 1, $this->cur_year));	// 현재 달의 1일의 요일
		$cur_week_tmp 	= $this->cur_day - (7 - $first_weekday);
		$this->cur_week = ceil($cur_week_tmp / 7);
		if ($first_weekday <= 3)
			$this->cur_week++;
	}
	
	/**
	* 월의 마지막날자를 얻기
	*/
	function getLastdayOfMonth($year, $month) {
		return date("t", mktime(0, 0, 0, $month, 1, $year));
	}

	/**
	* 월의 주수를 얻기
	*/
	function getWeeksOfMonth($year, $month) {
		$lastday 		= date("t", mktime(0, 0, 0, $month, 1, $year));				// 현재 달의 총날자수
		$first_weekday 	= date("w", mktime(0, 0, 0, $month, 1, $year));				// 현재 달의 1일의 요일
		$end_weekday	= date("w", mktime(0, 0, 0, $month, $lastday, $year)); 		// 현재 달의 마지막날자의 요일
		
		// 월의 옹근주를 구하기
		$weeks_tmp = $lastday - (6 - $first_weekday) - ($end_weekday + 1);
		$weeks = floor($weeks_tmp / 7);
		// 월이 모두 몇주인가를 구하기
		if ($first_weekday <= 3)
			$weeks++;
		if ($end_weekday >= 4)
			$weeks++;
		
		return $weeks;
	}
	
	/**
	* 주의 첫날자와 마지막날자 얻기
	*/
	function getDateOfWeek($year, $month, $week, &$startDate, &$endDate) {
		$lastday 		= date("t", mktime(0, 0, 0, $month, 1, $year));				// 현재 달의 총날자수
		$first_weekday 	= date("w", mktime(0, 0, 0, $month, 1, $year));				// 현재 달의 1일의 요일

		// 첫주의 첫날자구하기
		if($first_weekday <= 3){
			$first_wDay		= date("j", mktime(0, 0,0, $month, 2 - $first_weekday, $year));
			$first_wMonth	= date("m", mktime(0, 0,0, $month, 2 - $first_weekday, $year));
			$first_wYear	= date("Y", mktime(0, 0,0, $month, 2 - $first_weekday, $year));
		}
		else{
			$first_wDay		= date("j", mktime(0, 0, 0, $month, 9 - $first_weekday, $year));
			$first_wMonth	= date("m", mktime(0, 0, 0, $month, 9 - $first_weekday, $year));
			$first_wYear	= date("Y", mktime(0, 0, 0, $month, 9 - $first_weekday, $year));
		}
	
		// 선택된 주의 첫날자 구하기
		$timestamp 	= mktime(0, 0, 0, $first_wMonth, $first_wDay + 7 * ($week - 1), $first_wYear);
		$fYear 		= date("Y", $timestamp);
		$fMonth 	= date("n", $timestamp);
		$fDay 		= date("j", $timestamp);
		$startDate 	= sprintf("%d-%02d-%02d", $fYear, $fMonth, $fDay);
		$endDate 	= date('Y-m-d', mktime(0, 0, 0, $fMonth, $fDay + 6, $fYear));		

		return;
	}
	
	/**
	* 해당한 날자가 몇년, 몇월의 몇주인가를 얻기
	*/
	function getWeekOfDate($date, &$year, &$month) {
		// 년, 월
		$date_array 	= explode("-", $date);
		$year 			= $date_array[0];
		$month 			= intval($date_array[1]);
		$day 			= intval($date_array[2]);
		$weekday 		= date("w", mktime(0, 0, 0, $month, $day, $year));
		$weekday_diff 	= 6 - $weekday - 1;	// 토요일과 현재날자의 요일과의 차
		$sat_year 		= date("Y", mktime(0, 0, 0, $month, $day, $year) + $weekday_diff * 86400);
		$sat_month 		= date("n", mktime(0, 0, 0, $month, $day, $year) + $weekday_diff * 86400);
		$sat_weekday 	= date("w", mktime(0, 0, 0, $month, $day, $year) + $weekday_diff * 86400);
		if ($month != $sat_month && $sat_weekday <= 3)
		{
			$year 	= $sat_year;
			$month 	= $sat_month;
			$day 	= date("j", mktime(0, 0, 0, $month, $day, $year) + $weekday_diff * 86400);
		}
			
		// 주
		$first_weekday	= date("w", mktime(0, 0, 0, $month, 1, $year));	// 현재 달의 1일의 요일
		$week_tmp 		= $day - (7 - $first_weekday);
		$week 			= ceil($week_tmp / 7);
		if ($first_weekday <= 3)
			$week++;
		return $week;
	}
	/**
	* 해당한 날자가 몇년, 몇월의 몇주인가를 얻기
	*/
	function getComboDateOfMonth($year, $month, $defaultDay=0,$other="") {
		// 년, 월
		$return = "<SELECT ".$other.">";
		for ($i=1;$i<32;$i++)
		{
			if (!checkdate($month,$i,$year))
				break;
			if ($defaultDay == $i)
				$return .= "<option value='".$i."' selected='true'>".$i."</option>";
			else
				$return .= "<option value='".$i."'>".$i."</option>";
		}
		$return .= "</SELECT>";
		return $return;
	}
}

function os_info()
{
	$os = strpos(getenv('HTTP_USER_AGENT'), 'Win');
	if ($os) $sys = 'Win';
	else $sys = 'Linux';
	return $sys;
}


class securityModule {

	function encrypt($planText)
	{
		global $encryptKey;
		if (strlen($planText) == 0)
			return "";
		$planText1 = str_replace("\0","",$planText);
		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $encryptKey, $iv);
		$encrypted_data = mcrypt_generic($td, $planText1);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return base64_encode($encrypted_data);
		
	}
	function decrypt($cryptText)
	{
		global $encryptKey;
		if (strlen($cryptText) == 0)
			return "";
		$cryptText1 = str_replace("\0","",$cryptText);
		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $encryptKey, $iv);
		$mdecrypt_generic = mdecrypt_generic($td, base64_decode($cryptText1));
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return trim($mdecrypt_generic);
	}
}


/**
* Common HTML Output Files
* @package Joomla
*/
class mosAdminMenus {
	/**
	* build the select list for Menu Ordering
	*/
	function Ordering( &$row, $id ) {
		global $database;

		if ( $id ) {
			$query = "SELECT ordering AS value, name AS text"
			. "\n FROM #__menu"
			. "\n WHERE menutype = " . $database->Quote ( $row->menutype )
			. "\n AND parent = " . (int) $row->parent
			. "\n AND published != -2"
			. "\n ORDER BY ordering"
			;
			$order = mosGetOrderingList( $query );
			$ordering = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
		} else {
			$ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. _CMN_NEW_ITEM_LAST;
		}
		return $ordering;
	}

	/**
	* build the select list for access level
	*/
	function Access( &$row ) {
		global $database;

		$query = "SELECT id AS value, name AS text"
		. "\n FROM #__groups"
		. "\n ORDER BY id"
		;
		$database->setQuery( $query );
		$groups = $database->loadObjectList();
		$access = mosHTML::selectList( $groups, 'access', 'class="inputbox" size="3"', 'value', 'text', intval( $row->access ) );

		return $access;
	}

	/**
	* build the select list for parent item
	*/
	function Parent( &$row ) {
		global $database;

		$id = '';
		if ( $row->id ) {
			$id = "\n AND id != " . (int) $row->id;
		}

		// get a list of the menu items
		// excluding the current menu item and its child elements
		$query = "SELECT m.*"
		. "\n FROM #__menu m"
		. "\n WHERE menutype = " . $database->Quote( $row->menutype )
		. "\n AND published != -2"
		. $id
		. "\n ORDER BY parent, ordering"
		;
		$database->setQuery( $query );
		$mitems = $database->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();

		if ( $mitems ) {
			// first pass - collect children
			foreach ( $mitems as $v ) {
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = mosTreeRecurse( 0, '', array(), $children, 20, 0, 0 );

		// assemble menu items to the array
		$mitems 	= array();
		$mitems[] 	= mosHTML::makeOption( '0', 'Top' );

		foreach ( $list as $item ) {
			$mitems[] = mosHTML::makeOption( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename );
		}

		$output = mosHTML::selectList( $mitems, 'parent', 'class="inputbox" size="10"', 'value', 'text', $row->parent );

		return $output;
	}

	/**
	* build a radio button option for published state
	*/
	function Published( &$row ) {
		$published = mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );
		return $published;
	}

	/**
	* build the link/url of a menu item
	*/
	function Link( &$row, $id, $link=NULL ) {
		global $mainframe;

		if ( $id ) {
			switch ($row->type) {
				case 'content_item_link':
				case 'content_typed':
					// load menu params
					$params = new mosParameters( $row->params, $mainframe->getPath( 'menu_xml', $row->type ), 'menu' );

					if ( $params->get( 'unique_itemid' ) ) {
						$row->link .= '&Itemid='. $row->id;
					} else {
						$temp = split( '&task=view&id=', $row->link);
						$row->link .= '&Itemid='. $mainframe->getItemid($temp[1], 0, 0);
					}

					$link = $row->link;
					break;

				default:
					if ( $link ) {
						$link = $row->link;
					} else {
						$link = $row->link .'&amp;Itemid='. $row->id;
					}
					break;
			}
		} else {
			$link = NULL;
		}

		return $link;
	}

	/**
	* build the select list for target window
	*/
	function Target( &$row ) {
		$click[] = mosHTML::makeOption( '0', 'Parent Window With Browser Navigation' );
		$click[] = mosHTML::makeOption( '1', 'New Window With Browser Navigation' );
		$click[] = mosHTML::makeOption( '2', 'New Window Without Browser Navigation' );
		$target = mosHTML::selectList( $click, 'browserNav', 'class="inputbox" size="4"', 'value', 'text', intval( $row->browserNav ) );
		return $target;
	}

	/**
	* build the multiple select list for Menu Links/Pages
	*/
	function MenuLinks( &$lookup, $all=NULL, $none=NULL, $unassigned=1 ) {
		global $database;

		// get a list of the menu items
		$query = "SELECT m.*"
		. "\n FROM #__menu AS m"
		. "\n WHERE m.published = 1"
		//. "\n AND m.type != 'separator'"
		//. "\n AND NOT ("
		//	. "\n ( m.type = 'url' )"
		//	. "\n AND ( m.link LIKE '%index.php%' )"
		//	. "\n AND ( m.link LIKE '%Itemid=%' )"
		//. "\n )"
		. "\n ORDER BY m.menutype, m.parent, m.ordering"
		;
		$database->setQuery( $query );
		$mitems = $database->loadObjectList();
		$mitems_temp = $mitems;

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ( $mitems as $v ) {
			$id = $v->id;
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$list = mosTreeRecurse( intval( $mitems[0]->parent ), '', array(), $children, 20, 0, 0 );

		// Code that adds menu name to Display of Page(s)
		$text_count 	= 0;
		$mitems_spacer 	= $mitems_temp[0]->menutype;
		foreach ($list as $list_a) {
			foreach ($mitems_temp as $mitems_a) {
				if ($mitems_a->id == $list_a->id) {
					// Code that inserts the blank line that seperates different menus
					if ($mitems_a->menutype != $mitems_spacer) {
						$list_temp[] 	= mosHTML::makeOption( -999, '----' );
						$mitems_spacer 	= $mitems_a->menutype;
					}

					// do not display `url` menu item types that contain `index.php` and `Itemid`
					if (!($mitems_a->type == 'url' && strpos($mitems_a->link, 'index.php') !== false && strpos($mitems_a->link, 'Itemid=') !== false)) {
						$text 			= $mitems_a->menutype .' | '. $list_a->treename;
						$list_temp[] 	= mosHTML::makeOption( $list_a->id, $text );

						if ( strlen($text) > $text_count) {
							$text_count = strlen($text);
						}
					}
				}
			}
		}
		$list = $list_temp;

		$mitems = array();
		if ( $all ) {
			// prepare an array with 'all' as the first item
			$mitems[] = mosHTML::makeOption( 0, 'All' );
			// adds space, in select box which is not saved
			$mitems[] = mosHTML::makeOption( -999, '----' );
		}
		if ( $none ) {
			// prepare an array with 'all' as the first item
			$mitems[] = mosHTML::makeOption( -999, 'None' );
			// adds space, in select box which is not saved
			$mitems[] = mosHTML::makeOption( -999, '----' );
		}
		if ( $unassigned ) {
			// prepare an array with 'all' as the first item
			$mitems[] = mosHTML::makeOption( 99999999, 'Unassigned' );
			// adds space, in select box which is not saved
			$mitems[] = mosHTML::makeOption( -999, '----' );
		}

		// append the rest of the menu items to the array
		foreach ($list as $item) {
			$mitems[] = mosHTML::makeOption( $item->value, $item->text );
		}
		$pages = mosHTML::selectList( $mitems, 'selections[]', 'class="inputbox" size="26" multiple="multiple"', 'value', 'text', $lookup );
		return $pages;
	}


	/**
	* build the select list to choose a category
	*/
	function Category( &$menu, $id, $javascript='' ) {
		global $database;

		$query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`"
		. "\n FROM #__sections AS s"
		. "\n INNER JOIN #__categories AS c ON c.section = s.id"
		. "\n WHERE s.scope = 'content'"
		. "\n ORDER BY s.name, c.name"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		$category = '';
		if ( $id ) {
			foreach ( $rows as $row ) {
				if ( $row->value == $menu->componentid ) {
					$category = $row->text;
				}
			}
			$category .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
			$category .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
		} else {
			$category = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"'. $javascript, 'value', 'text' );
			$category .= '<input type="hidden" name="link" value="" />';
		}
		return $category;
	}

	/**
	* build the select list to choose a section
	*/
	function Section( &$menu, $id, $all=0 ) {
		global $database;

		$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text`"
		. "\n FROM #__sections AS s"
		. "\n WHERE s.scope = 'content'"
		. "\n ORDER BY s.name"
		;
		$database->setQuery( $query );
		if ( $all ) {
			$rows[] = mosHTML::makeOption( 0, '- All Sections -' );
			$rows = array_merge( $rows, $database->loadObjectList() );
		} else {
			$rows = $database->loadObjectList();
		}

		if ( $id ) {
			foreach ( $rows as $row ) {
				if ( $row->value == $menu->componentid ) {
					$section = $row->text;
				}
			}
			$section .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
			$section .= '<input type="hidden" name="link" value="'. $menu->link .'" />';
		} else {
			$section = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text' );
			$section .= '<input type="hidden" name="link" value="" />';
		}
		return $section;
	}

	/**
	* build the select list to choose a component
	*/
	function Component( &$menu, $id ) {
		global $database;

		$query = "SELECT c.id AS value, c.name AS text, c.link"
		. "\n FROM #__components AS c"
		. "\n WHERE c.link != ''"
		. "\n ORDER BY c.name"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList( );

		if ( $id ) {
			// existing component, just show name
			foreach ( $rows as $row ) {
				if ( $row->value == $menu->componentid ) {
					$component = $row->text;
				}
			}
			$component .= '<input type="hidden" name="componentid" value="'. $menu->componentid .'" />';
		} else {
			$component = mosHTML::selectList( $rows, 'componentid', 'class="inputbox" size="10"', 'value', 'text' );
		}
		return $component;
	}

	/**
	* build the select list to choose a component
	*/
	function ComponentName( &$menu, $id ) {
		global $database;

		$query = "SELECT c.id AS value, c.name AS text, c.link"
		. "\n FROM #__components AS c"
		. "\n WHERE c.link != ''"
		. "\n ORDER BY c.name"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList( );

		$component = 'Component';
		foreach ( $rows as $row ) {
			if ( $row->value == $menu->componentid ) {
				$component = $row->text;
			}
		}

		return $component;
	}

	/**
	* build the select list to choose an image
	*/
	function Images( $name, &$active, $javascript=NULL, $directory=NULL ) {
		global $Config_absolute_path;

		if ( !$directory ) {
			$directory = '/images/stories';
		}

		if ( !$javascript ) {
			$javascript = "onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='..$directory/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		}

		$imageFiles = mosReadDirectory( $Config_absolute_path . $directory );
		$images 	= array(  mosHTML::makeOption( '', '- Select Image -' ) );
		foreach ( $imageFiles as $file ) {
			if ( eregi( "bmp|gif|jpg|png", $file ) ) {
				$images[] = mosHTML::makeOption( $file );
			}
		}
		$images = mosHTML::selectList( $images, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $images;
	}

	/**
	* build the select list for Ordering of a specified Table
	*/
	function SpecificOrdering( &$row, $id, $query, $neworder=0 ) {
		global $database;

		if ( $neworder ) {
			$text = _CMN_NEW_ITEM_FIRST;
		} else {
			$text = _CMN_NEW_ITEM_LAST;
		}

		if ( $id ) {
			$order = mosGetOrderingList( $query );
			$ordering = mosHTML::selectList( $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
		} else {
			$ordering = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. $text;
		}
		return $ordering;
	}

	/**
	* Select list of active users
	*/
	function UserSelect( $name, $active, $nouser=0, $javascript=NULL, $order='name', $reg=1 ) {
		global $database, $my;

		$and = '';
		if ( $reg ) {
		// does not include registered users in the list
			$and = "\n AND gid > 18";
		}

		$query = "SELECT id AS value, name AS text"
		. "\n FROM #__users"
		. "\n WHERE block = 0"
		. $and
		. "\n ORDER BY $order"
		;
		$database->setQuery( $query );
		if ( $nouser ) {
			$users[] = mosHTML::makeOption( '0', '- No User -' );
			$users = array_merge( $users, $database->loadObjectList() );
		} else {
			$users = $database->loadObjectList();
		}

		$users = mosHTML::selectList( $users, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $users;
	}

	/**
	* Select list of positions - generally used for location of images
	*/
	function Positions( $name, $active=NULL, $javascript=NULL, $none=1, $center=1, $left=1, $right=1 ) {
		if ( $none ) {
			$pos[] = mosHTML::makeOption( '', _CMN_NONE );
		}
		if ( $center ) {
			$pos[] = mosHTML::makeOption( 'center', _CMN_CENTER );
		}
		if ( $left ) {
			$pos[] = mosHTML::makeOption( 'left', _CMN_LEFT );
		}
		if ( $right ) {
			$pos[] = mosHTML::makeOption( 'right', _CMN_RIGHT );
		}

		$positions = mosHTML::selectList( $pos, $name, 'class="inputbox" size="1"'. $javascript, 'value', 'text', $active );

		return $positions;
	}

	/**
	* Select list of active categories for components
	*/
	function ComponentCategory( $name, $section, $active=NULL, $javascript=NULL, $order='ordering', $size=1, $sel_cat=1 ) {
		global $database;

		$query = "SELECT id AS value, name AS text"
		. "\n FROM #__categories"
		. "\n WHERE section = " . $database->Quote( $section )
		. "\n AND published = 1"
		. "\n ORDER BY $order"
		;
		$database->setQuery( $query );
		if ( $sel_cat ) {
			$categories[] = mosHTML::makeOption( '0', _SEL_CATEGORY );
			$categories = array_merge( $categories, $database->loadObjectList() );
		} else {
			$categories = $database->loadObjectList();
		}

		if ( count( $categories ) < 1 ) {
			mosRedirect( 'index.php?option=com_categories&section='. $section, 'You must create a category first.' );
		}

		$category = mosHTML::selectList( $categories, $name, 'class="inputbox" size="'. $size .'" '. $javascript, 'value', 'text', $active );

		return $category;
	}

	/**
	* Select list of active sections
	*/
	function SelectSection( $name, $active=NULL, $javascript=NULL, $order='ordering' ) {
		global $database;

		$categories[] = mosHTML::makeOption( '0', _SEL_SECTION );
		$query = "SELECT id AS value, title AS text"
		. "\n FROM #__sections"
		. "\n WHERE published = 1"
		. "\n ORDER BY $order"
		;
		$database->setQuery( $query );
		$sections = array_merge( $categories, $database->loadObjectList() );

		$category = mosHTML::selectList( $sections, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $category;
	}

	/**
	* Select list of menu items for a specific menu
	*/
	function Links2Menu( $type, $and ) {
		global $database;

		$query = "SELECT *"
		. "\n FROM #__menu"
		. "\n WHERE type = " . $database->Quote( $type )
		. "\n AND published = 1"
		. $and
		;
		$database->setQuery( $query );
		$menus = $database->loadObjectList();

		return $menus;
	}

	/**
	 * Select list of menus
	 * @param string The control name
	 * @param string Additional javascript
	 * @return string A select list
	 */
	function MenuSelect( $name='menuselect', $javascript=NULL ) {
		global $database;

		$query = "SELECT params"
		. "\n FROM #__modules"
		. "\n WHERE module = 'mod_mainmenu'"
		;
		$database->setQuery( $query );
		$menus = $database->loadObjectList();
		$total = count( $menus );
		$menuselect = array();
		for( $i = 0; $i < $total; $i++ ) {
			$params = mosParseParams( $menus[$i]->params );
			$menuselect[$i]->value 	= $params->menutype;
			$menuselect[$i]->text 	= $params->menutype;
		}
		// sort array of objects
		SortArrayObjects( $menuselect, 'text', 1 );

		$menus = mosHTML::selectList( $menuselect, $name, 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

		return $menus;
	}

	/**
	* Internal function to recursive scan the media manager directories
	* @param string Path to scan
	* @param string root path of this folder
	* @param array  Value array of all existing folders
	* @param array  Value array of all existing images
	*/
	function ReadImages( $imagePath, $folderPath, &$folders, &$images ) {
		$imgFiles = mosReadDirectory( $imagePath );

		foreach ($imgFiles as $file) {
			$ff_ 	= $folderPath . $file .'/';
			$ff 	= $folderPath . $file;
			$i_f 	= $imagePath .'/'. $file;

			if ( is_dir( $i_f ) && $file != 'CVS' && $file != '.svn') {
				$folders[] = mosHTML::makeOption( $ff_ );
				mosAdminMenus::ReadImages( $i_f, $ff_, $folders, $images );
			} else if ( eregi( "bmp|gif|jpg|png", $file ) && is_file( $i_f ) ) {
				// leading / we don't need
				$imageFile = substr( $ff, 1 );
				$images[$folderPath][] = mosHTML::makeOption( $imageFile, $file );
			}
		}
	}

	/**
	* Internal function to recursive scan the media manager directories
	* @param string Path to scan
	* @param string root path of this folder
	* @param array  Value array of all existing folders
	* @param array  Value array of all existing images
	*/
	function ReadImagesX( &$folders, &$images ) {
		global $Config_absolute_path;

		if ( $folders[0]->value != '*0*' ) {
			foreach ( $folders as $folder ) {
				$imagePath 	= $Config_absolute_path .'/images/stories' . $folder->value;
				$imgFiles 	= mosReadDirectory( $imagePath );
				$folderPath = $folder->value .'/';

				foreach ($imgFiles as $file) {
					$ff 	= $folderPath . $file;
					$i_f 	= $imagePath .'/'. $file;

					if ( eregi( "bmp|gif|jpg|png", $file ) && is_file( $i_f ) ) {
						// leading / we don't need
						$imageFile = substr( $ff, 1 );
						$images[$folderPath][] = mosHTML::makeOption( $imageFile, $file );
					}
				}
			}
		} else {
			$folders 	= array();
			$folders[] 	= mosHTML::makeOption( 'None' );
		}
	}

	function GetImageFolders( &$temps, $path ) {
		if ( $temps[0]->value != 'None' ) {
			foreach( $temps as $temp ) {
				if ( substr( $temp->value, -1, 1 ) != '/' ) {
					$temp 		= $temp->value .'/';
					$folders[] 	= mosHTML::makeOption( $temp, $temp );
				} else {
					$temp 		= $temp->value;
					$temp 		= ampReplace( $temp );
					$folders[] 	= mosHTML::makeOption( $temp, $temp );
				}
			}
		} else {
			$folders[] 	= mosHTML::makeOption( 'None Selected' );
		}

		$javascript 	= "onchange=\"changeDynaList( 'imagefiles', folderimages, document.adminForm.folders.options[document.adminForm.folders.selectedIndex].value, 0, 0);\"";
		$getfolders 	= mosHTML::selectList( $folders, 'folders', 'class="inputbox" size="1" '. $javascript, 'value', 'text', '/' );

		return $getfolders;
	}

	function GetImages( &$images, $path, $base='/' ) {
		if ( is_array($base) && count($base) > 0 ) {
			if ( $base[0]->value != '/' ) {
				$base = $base[0]->value .'/';
			} else {
				$base = $base[0]->value;
			}
		} else {
			$base = '/';
		}

		if ( !isset($images[$base] ) ) {
			$images[$base][] = mosHTML::makeOption( '' );
		}

		$javascript	= "onchange=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\" onfocus=\"previewImage( 'imagefiles', 'view_imagefiles', '$path/' )\"";
		$getimages	= mosHTML::selectList( $images[$base], 'imagefiles', 'class="inputbox" size="10" multiple="multiple" '. $javascript , 'value', 'text', null );

		return $getimages;
	}

	function GetSavedImages( &$row, $path ) {
		$images2 = array();

		foreach( $row->images as $file ) {
			$temp = explode( '|', $file );
			if( strrchr($temp[0], '/') ) {
				$filename = substr( strrchr($temp[0], '/' ), 1 );
			} else {
				$filename = $temp[0];
			}
			$images2[] = mosHTML::makeOption( $file, $filename );
		}

		$javascript	= "onchange=\"previewImage( 'imagelist', 'view_imagelist', '$path/' ); showImageProps( '$path/' ); \"";
		$imagelist 	= mosHTML::selectList( $images2, 'imagelist', 'class="inputbox" size="10" '. $javascript, 'value', 'text' );

		return $imagelist;
	}

	/**
	* Checks to see if an image exists in the current templates image directory
 	* if it does it loads this image.  Otherwise the default image is loaded.
	* Also can be used in conjunction with the menulist param to create the chosen image
	* load the default or use no image
	*/
	function ImageCheck( $file, $directory='/images/M_images/', $param=NULL, $param_directory='/images/M_images/', $alt=NULL, $name=NULL, $type=1, $align='middle', $title=NULL, $admin=NULL ) {
		global $Config_absolute_path, $Config_live_site, $mainframe, $Config_cur_template;

		$cur_template = $Config_cur_template;

		$name 	= ( $name 	? ' name="'. $name .'"' 	: '' );
		$title 	= ( $title 	? ' title="'. $title .'"' 	: '' );
		$alt 	= ( $alt 	? ' alt="'. $alt .'"' 		: ' alt=""' );
		$align 	= ( $align 	? ' align="'. $align .'"' 	: '' );

		// change directory path from frontend or backend
		if ($admin) {
			$path 	= '/administrator/templates/'. $cur_template .'/images/';
		} else {
			$path 	= '/templates/'. $cur_template .'/images/';
		}

		if ( $param ) {
			$image = $Config_live_site. $param_directory . $param;
			if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $align .' border="0" />';
			}
		} else if ( $param == -1 ) {
			$image = '';
		} else {
			if ( file_exists( $Config_absolute_path . $path . $file ) ) {
				$image = $Config_live_site . $path . $file;
			} else {
				// outputs only path to image
				$image = $Config_live_site. $directory . $file;
			}

			// outputs actual html <img> tag
			if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $title . $align .' border="0" />';
			}
		}

		return $image;
	}

	/**
	* Checks to see if an image exists in the current templates image directory
 	* if it does it loads this image.  Otherwise the default image is loaded.
	* Also can be used in conjunction with the menulist param to create the chosen image
	* load the default or use no image
	*/
	function ImageCheckAdmin( $file, $directory='/administrator/images/', $param=NULL, $param_directory='/administrator/images/', $alt=NULL, $name=NULL, $type=1, $align='middle', $title=NULL ) {
/*
		global $Config_absolute_path, $Config_live_site, $mainframe;

		$cur_template = $mainframe->getTemplate();

		$name 	= ( $name 	? ' name="'. $name .'"' 	: '' );
		$title 	= ( $title 	? ' title="'. $title .'"' 	: '' );
		$alt 	= ( $alt 	? ' alt="'. $alt .'"' 		: ' alt=""' );
		$align 	= ( $align 	? ' align="'. $align .'"' 	: '' );

		$path 	= '/administrator/templates/'. $cur_template .'/images/';

		if ( $param ) {
			$image = $Config_live_site. $param_directory . $param;
			if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $align .' border="0" />';
			}
		} else if ( $param == -1 ) {
			$image = '';
		} else {
			if ( file_exists( $Config_absolute_path . $path . $file ) ) {
				$image = $Config_live_site . $path . $file;
			} else {
				// outputs only path to image
				$image = $Config_live_site. $directory . $file;
			}

			// outputs actual html <img> tag
			if ( $type ) {
				$image = '<img src="'. $image .'" '. $alt . $name . $title . $align .' border="0" />';
			}
		}
*/
		// functionality consolidated into ImageCheck
		$image = mosAdminMenus::ImageCheck( $file, $directory, $param, $param_directory, $alt, $name, $type, $align, $title, $admin=1 );

		return $image;
	}

	function menutypes() {
		global $database;

		$query = "SELECT params"
		. "\n FROM #__modules"
		. "\n WHERE module = 'mod_mainmenu'"
		. "\n ORDER BY title"
		;
		$database->setQuery( $query	);
		$modMenus = $database->loadObjectList();

		$query = "SELECT menutype"
		. "\n FROM #__menu"
		. "\n GROUP BY menutype"
		. "\n ORDER BY menutype"
		;
		$database->setQuery( $query	);
		$menuMenus = $database->loadObjectList();

		$menuTypes = '';
		foreach ( $modMenus as $modMenu ) {
			$check = 1;
			mosMakeHtmlSafe( $modMenu) ;
			$modParams 	= mosParseParams( $modMenu->params );
			$menuType 	= @$modParams->menutype;
			if (!$menuType) {
				$menuType = 'mainmenu';
			}

			// stop duplicate menutype being shown
			if ( !is_array( $menuTypes) ) {
				// handling to create initial entry into array
				$menuTypes[] = $menuType;
			} else {
				$check = 1;
				foreach ( $menuTypes as $a ) {
					if ( $a == $menuType ) {
						$check = 0;
					}
				}
				if ( $check ) {
					$menuTypes[] = $menuType;
				}
			}

		}
		// add menutypes from jos_menu
		foreach ( $menuMenus as $menuMenu ) {
			$check = 1;
			foreach ( $menuTypes as $a ) {
				if ( $a == $menuMenu->menutype ) {
					$check = 0;
				}
			}
			if ( $check ) {
				$menuTypes[] = $menuMenu->menutype;
			}
		}

		// sorts menutypes
		asort( $menuTypes );

		return $menuTypes;
	}

	/*
	* loads files required for menu items
	*/
	function menuItem( $item ) {
		global $Config_absolute_path;

		$path = $Config_absolute_path .'/administrator/components/com_menus/'. $item .'/';
		include_once( $path . $item .'.class.php' );
		include_once( $path . $item .'.menu.html.php' );
	}
}

/**
 * Function to convert array to integer values
 * @param array
 * @param int A default value to assign if $array is not an array
 * @return array
 */
function mosArrayToInts( &$array, $default=null ) {
	if (is_array( $array )) {
		foreach( $array as $key => $value ) {
			$array[$key] = (int) $value;
		}
	} else {
		if (is_null( $default )) {
			$array = array();
			return array(); // Kept for backwards compatibility
		} else {
			$array = array( (int) $default );
			return array( $default ); // Kept for backwards compatibility
		}
	}
}

/*
* Function to handle an array of integers
* Added 1.0.11
*/
function josGetArrayInts( $name, $type=NULL ) {
	if ( $type == NULL ) {
		$type = $_POST;
	}

	$array = mosGetParam( $type, $name, array(0) );

	mosArrayToInts( $array );

	if (!is_array( $array )) {
		$array = array(0);
	}

	return $array;
}

/**
* @param string SQL with ordering As value and 'name field' AS text
* @param integer The length of the truncated headline
*/
function mosGetOrderingList( $sql, $chop='30' ) {
	global $database;

	$order = array();
	$database->setQuery( $sql );
	if (!($orders = $database->loadObjectList())) {
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		} else {
			$order[] = mosHTML::makeOption( 1, 'first' );
			return $order;
		}
	}
	$order[] = mosHTML::makeOption( 0, '0 first' );
	for ($i=0, $n=count( $orders ); $i < $n; $i++) {

		if (strlen($orders[$i]->text) > $chop) {
			$text = substr($orders[$i]->text,0,$chop)."...";
		} else {
			$text = $orders[$i]->text;
		}

		$order[] = mosHTML::makeOption( $orders[$i]->value, $orders[$i]->value.' ('.$text.')' );
	}
	$order[] = mosHTML::makeOption( $orders[$i-1]->value+1, ($orders[$i-1]->value+1).' last' );

	return $order;
}

class mosCommonHTML {

	function ContentLegend( ) {
		?>
		<table cellspacing="0" cellpadding="4" border="0" align="center">
		<tr align="center">
			<td>
			<img src="images/publish_y.png" width="12" height="12" border="0" alt="Pending" />
			</td>
			<td>
			Published, but is <u>Pending</u> |
			</td>
			<td>
			<img src="images/publish_g.png" width="12" height="12" border="0" alt="Visible" />
			</td>
			<td>
			Published and is <u>Current</u> |
			</td>
			<td>
			<img src="images/publish_r.png" width="12" height="12" border="0" alt="Finished" />
			</td>
			<td>
			Published, but has <u>Expired</u> |
			</td>
			<td>
			<img src="images/publish_x.png" width="12" height="12" border="0" alt="Finished" />
			</td>
			<td>
			Not Published
			</td>
		</tr>
		<tr>
			<td colspan="8" align="center">
			Click on icon to toggle state.
			</td>
		</tr>
		</table>
		<?php
	}

	function menuLinksContent( &$menus ) {
		?>
		<script language="javascript" type="text/javascript">
		function go2( pressbutton, menu, id ) {
			var form = document.adminForm;

			// assemble the images back into one field
			var temp = new Array;
			for (var i=0, n=form.imagelist.options.length; i < n; i++) {
				temp[i] = form.imagelist.options[i].value;
			}
			form.images.value = temp.join( '\n' );

			if (pressbutton == 'go2menu') {
				form.menu.value = menu;
				submitform( pressbutton );
				return;
			}

			if (pressbutton == 'go2menuitem') {
				form.menu.value 	= menu;
				form.menuid.value 	= id;
				submitform( pressbutton );
				return;
			}
		}
		</script>
		<?php
		foreach( $menus as $menu ) {
			?>
			<tr>
				<td colspan="2">
				<hr />
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Menu
				</td>
				<td>
				<a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );" title="Go to Menu">
				<?php echo $menu->menutype; ?>
				</a>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Link Name
				</td>
				<td>
				<strong>
				<a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );" title="Go to Menu Item">
				<?php echo $menu->name; ?>
				</a>
				</strong>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				State
				</td>
				<td>
				<?php
				switch ( $menu->published ) {
					case -2:
						echo '<font color="red">Trashed</font>';
						break;
					case 0:
						echo 'UnPublished';
						break;
					case 1:
					default:
						echo '<font color="green">Published</font>';
						break;
				}
				?>
				</td>
			</tr>
			<?php
		}
		?>
		<input type="hidden" name="menu" value="" />
		<input type="hidden" name="menuid" value="" />
		<?php
	}

	function menuLinksSecCat( &$menus ) {
		?>
		<script language="javascript" type="text/javascript">
		function go2( pressbutton, menu, id ) {
			var form = document.adminForm;

			if (pressbutton == 'go2menu') {
				form.menu.value = menu;
				submitform( pressbutton );
				return;
			}

			if (pressbutton == 'go2menuitem') {
				form.menu.value 	= menu;
				form.menuid.value 	= id;
				submitform( pressbutton );
				return;
			}
		}
		</script>
		<?php
		foreach( $menus as $menu ) {
			?>
			<tr>
				<td colspan="2">
				<hr/>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Menu
				</td>
				<td>
				<a href="javascript:go2( 'go2menu', '<?php echo $menu->menutype; ?>' );" title="Go to Menu">
				<?php echo $menu->menutype; ?>
				</a>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Type
				</td>
				<td>
				<?php echo $menu->type; ?>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				Item Name
				</td>
				<td>
				<strong>
				<a href="javascript:go2( 'go2menuitem', '<?php echo $menu->menutype; ?>', '<?php echo $menu->id; ?>' );" title="Go to Menu Item">
				<?php echo $menu->name; ?>
				</a>
				</strong>
				</td>
			</tr>
			<tr>
				<td width="90px" valign="top">
				State
				</td>
				<td>
				<?php
				switch ( $menu->published ) {
					case -2:
						echo '<font color="red">Trashed</font>';
						break;
					case 0:
						echo 'UnPublished';
						break;
					case 1:
					default:
						echo '<font color="green">Published</font>';
						break;
				}
				?>
				</td>
			</tr>
			<?php
		}
		?>
		<input type="hidden" name="menu" value="" />
		<input type="hidden" name="menuid" value="" />
		<?php
	}

	function checkedOut( &$row, $overlib=1 ) {
		$hover = '';
		if ( $overlib ) {
			$date 				= mosFormatDate( $row->checked_out_time, '%A, %d %B %Y' );
			$time				= mosFormatDate( $row->checked_out_time, '%H:%M' );
			$editor				= addslashes( htmlspecialchars( html_entity_decode( $row->editor, ENT_QUOTES ) ) );
			$checked_out_text 	= '<table>';
			$checked_out_text 	.= '<tr><td>'. $editor .'</td></tr>';
			$checked_out_text 	.= '<tr><td>'. $date .'</td></tr>';
			$checked_out_text 	.= '<tr><td>'. $time .'</td></tr>';
			$checked_out_text 	.= '</table>';
			$hover = 'onMouseOver="return overlib(\''. $checked_out_text .'\', CAPTION, \'Checked Out\', BELOW, RIGHT);" onMouseOut="return nd();"';
		}
		$checked	 		= '<img src="images/checked_out.png" '. $hover .'/>';

		return $checked;
	}

	/*
	* Loads all necessary files for JS Overlib tooltips
	*/
	function loadOverlib() {
		global  $mosConfig_live_site, $mainframe;

		if ( !$mainframe->get( 'loadOverlib' ) ) {
		// check if this function is already loaded
			?>
			<script language="javascript" type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
			<script language="javascript" type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_hideform_mini.js"></script>
			<?php
			// change state so it isnt loaded a second time
			$mainframe->set( 'loadOverlib', true );
		}
	}


	/*
	* Loads all necessary files for JS Calendar
	*/
	function loadCalendar() {
		global  $mosConfig_live_site;
		?>
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $mosConfig_live_site;?>/includes/js/calendar/calendar-mos.css" title="green" />
		<!-- import the calendar script -->
		<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/calendar/calendar_mini.js"></script>
		<!-- import the language module -->
		<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/calendar/lang/calendar-en.js"></script>
		<?php
	}

	function AccessProcessing( &$row, $i ) {
		if ( !$row->access ) {
			$color_access = 'style="color: green;"';
			$task_access = 'accessregistered';
		} else if ( $row->access == 1 ) {
			$color_access = 'style="color: red;"';
			$task_access = 'accessspecial';
		} else {
			$color_access = 'style="color: black;"';
			$task_access = 'accesspublic';
		}

		$href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task_access .'\')" '. $color_access .'>
		'. $row->groupname .'
		</a>'
		;

		return $href;
	}

	function CheckedOutProcessing( &$row, $i ) {
		global $my;

		if ( $row->checked_out) {
			$checked = mosCommonHTML::checkedOut( $row );
		} else {
			$checked = mosHTML::idBox( $i, $row->id, ($row->checked_out && $row->checked_out != $my->id ) );
		}

		return $checked;
	}

	function PublishedProcessing( &$row, $i ) {
		$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? 'Published' : 'Unpublished';
		$action	= $row->published ? 'Unpublish Item' : 'Publish item';

		$href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}

	function RecommendedProcessing( &$row, $i ) {
		$img 	= $row->recommended ? 'publish_g.png' : 'publish_x.png';
		$task 	= $row->recommended ? 'unrecommend' : 'recommend';
		$alt 	= $row->recommended ? 'Recommended' : 'Unrecommended';
		$action	= $row->recommended ? 'Unrecommend Cartoon' : 'Recommend Cartoon';

		$href = '
		<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}

	/*
	* Special handling for newfeed encoding and possible conflicts with page encoding and PHP version
	* Added 1.0.8
	* Static Function
	*/
	function newsfeedEncoding( $rssDoc, $text ) {
		if (!defined( '_JOS_FEED_ENCODING' )) {
		// determine encoding of feed
			$feed 			= $rssDoc->toNormalizedString(true);
			$feed 			= strtolower( substr( $feed, 0, 150 ) );
			$feedEncoding 	= strpos( $feed, 'encoding=&quot;utf-8&quot;' );

			if ( $feedEncoding !== false ) {
			// utf-8 feed
				$utf8 = 1;
			} else {
			// non utf-8 page
				$utf8 = 0;
			}

			define( '_JOS_FEED_ENCODING', $utf8 );
		}

		if (!defined( '_JOS_SITE_ENCODING' )) {
		// determine encoding of page
			if ( strpos( strtolower( _ISO ), 'utf' ) !== false ) {
			// utf-8 page
				$utf8 = 1;
			} else {
			// non utf-8 page
				$utf8 = 0;
			}

			define( '_JOS_SITE_ENCODING', $utf8 );

		}

		if ( phpversion() >= 5 ) {
		// handling for PHP 5
			if ( _JOS_FEED_ENCODING ) {
			// handling for utf-8 feed
				if ( _JOS_SITE_ENCODING ) {
				// utf-8 page
					$encoding = 'html_entity_decode';
				} else {
				// non utf-8 page
					$encoding = 'utf8_decode';
				}
			} else {
			// handling for non utf-8 feed
				if ( _JOS_SITE_ENCODING ) {
					// utf-8 page
					$encoding = '';
				} else {
					// non utf-8 page
					$encoding = 'utf8_decode';
				}
			}
		} else {
		// handling for PHP 4
			if ( _JOS_FEED_ENCODING ) {
			// handling for utf-8 feed
				if ( _JOS_SITE_ENCODING ) {
				// utf-8 page
					$encoding = '';
				} else {
				// non utf-8 page
					$encoding = 'utf8_decode';
				}
			} else {
			// handling for non utf-8 feed
				if ( _JOS_SITE_ENCODING ) {
				// utf-8 page
					$encoding = 'utf8_encode';
				} else {
				// non utf-8 page
					$encoding = 'html_entity_decode';
				}
			}
		}

		if ( $encoding ) {
			$text = $encoding( $text );
		}
		$text = str_replace('&apos;', "'", $text);

		return $text;
	}
}
?>
