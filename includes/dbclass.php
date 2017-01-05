<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* Users Table Class
*
* Provides access to the jos_user table
* @package Joomla
*/
class mosAdminUser extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string The users real name (or nickname)*/
	var $name			= null;
	/** @var string The login name*/
	var $username		= null;
	/** @var string email*/
	var $email			= null;
	/** @var string MD5 encrypted password*/
	var $password		= null;
	/** @var datetime logintime */
	var $login_date		= null;

	/**
	* @param database A database connector object
	*/
	function mosAdminUser( &$database ) {
		$this->mosDBTable( '#__admin', 'id', $database );
	}

	/**
	 * Validation and filtering
	 * @return boolean True is satisfactory
	 */
	function check() {
		// Validate user information
		if (trim( $this->name ) == '') {
			$this->_error = addslashes( _REGWARN_NAME );
			return false;
		}

		if (trim( $this->username ) == '') {
			$this->_error = addslashes( _REGWARN_UNAME );
			return false;
		}

		// check that username is not greater than 25 characters
		$username = $this->username;
		if ( strlen($username) > 25 ) {
			$this->username = substr( $username, 0, 25 );
		}

		// check that password is not greater than 50 characters
		$password = $this->password;
		if ( strlen($password) > 50 ) {
			$this->password = substr( $password, 0, 50 );
		}

		if (eregi( "[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", $this->username) || strlen( $this->username ) < 3) {
			$this->_error = sprintf( addslashes( _VALID_AZ09 ), addslashes( _PROMPT_UNAME ), 2 );
			return false;
		}

		if ((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email )==false)) {
			$this->_error = addslashes( _REGWARN_MAIL );
			return false;
		}

		// check for existing username
		$query = "SELECT id"
		. "\n FROM #__admin"
		. "\n WHERE username = " . $this->_db->Quote( $this->username )
		. "\n AND id != " . (int)$this->id
		;
		$this->_db->setQuery( $query );
		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->_error = addslashes( _REGWARN_INUSE );
			return false;
		}

		if ($Config_uniquemail) {
			// check for existing email
			$query = "SELECT id"
			. "\n FROM #__admin"
			. "\n WHERE email = " . $this->_db->Quote( $this->email )
			. "\n AND id != " . (int) $this->id
			;
			$this->_db->setQuery( $query );
			$xid = intval( $this->_db->loadResult() );
			if ($xid && $xid != intval( $this->id )) {
				$this->_error = addslashes( _REGWARN_EMAIL_INUSE );
				return false;
			}
		}

		return true;
	}
}
class mosUser extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string The users real name (or nickname)*/
	var $mail			= null;
	/** @var string The login name*/
	var $username		= null;
	/** @var string email*/
	var $password		= null;
	/** @var description */
	var $login_date		= null;
	var $user_role 	= null;
	var $register_type 	= null;

	/**
	* @param database A database connector object
	*/
	function mosUser( &$database ) {
		$this->mosDBTable( '#__user', 'id', $database );
	}

	/**
	 * Validation and filtering
	 * @return boolean True is satisfactory
	 */
	function check() {
		// Validate user information		
		if (trim( $this->name ) == '') {
			$this->_error = addslashes( _REGWARN_NAME );
			return false;
		}

		if (trim( $this->username ) == '') {
			$this->_error = addslashes( _REGWARN_UNAME );
			return false;
		}

		// check that username is not greater than 25 characters
		$username = $this->username;
		if ( strlen($username) > 25 ) {
			$this->username = substr( $username, 0, 25 );
		}

		// check that password is not greater than 50 characters
		$password = $this->password;
		if ( strlen($password) > 50 ) {
			$this->password = substr( $password, 0, 50 );
		}

		if (eregi( "[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", $this->username) || strlen( $this->username ) < 3) {
			$this->_error = sprintf( addslashes( _VALID_AZ09 ), addslashes( _PROMPT_UNAME ), 2 );
			return false;
		}

		if ((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email )==false)) {
			$this->_error = addslashes( _REGWARN_MAIL );
			return false;
		}

		// check for existing username
		$query = "SELECT id"
		. "\n FROM #__user"
		. "\n WHERE username = " . $this->_db->Quote( $this->username )
		. "\n AND id != " . (int)$this->id
		;
		$this->_db->setQuery( $query );
		$xid = intval( $this->_db->loadResult() );
		if ($xid && $xid != intval( $this->id )) {
			$this->_error = addslashes( _REGWARN_INUSE );
			return false;
		}

		if ($Config_uniquemail) {
			// check for existing email
			$query = "SELECT id"
			. "\n FROM #__user"
			. "\n WHERE email = " . $this->_db->Quote( $this->email )
			. "\n AND id != " . (int) $this->id
			;
			$this->_db->setQuery( $query );
			$xid = intval( $this->_db->loadResult() );
			if ($xid && $xid != intval( $this->id )) {
				$this->_error = addslashes( _REGWARN_EMAIL_INUSE );
				return false;
			}
		}

		return true;
	}
}

class mosAdvertisement extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string ad name*/
	var $name			= null;
//	/** @var string ad description*/
//	var $description	= null;
	/** @var string ad file name1 */
	var $file_name1		= null;
	/** @var int cartoon1 id */
	var $cartoon_id1	= null;
	/** @var string ad file name2 */
	var $file_name2		= null;
	/** @var int cartoon2 id */
	var $cartoon_id2	= null;
	/** @var string ad file name3 */
	var $file_name3		= null;
	/** @var int cartoon3 id */
	var $cartoon_id3	= null;

	/**
	* @param database A database connector object
	*/
	function mosAdvertisement( &$database ) {
		$this->mosDBTable( '#__advertisement', 'id', $database );
	}
}

class mosCartoon extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string cartoon name*/
	var $name			= null;
	/** @var string cartoon description*/
	var $description	= null;
	/** @var int parent*/
	var $parent 		= null;
	/** @var int author id*/
	var $author_id		= null;
	/** @var int region*/
	var $region			= null;
	/** @var int classification id*/
	var $class_id		= null;
	/** @var int topic id*/
	var $topic_id		= null;
	/** @var datetime public date*/
	var $public_date	= null;
	/** @var int ordering*/
	var $ordering		= null;
	/** @var int cartoon state*/
	var $state			= null;
	/** @var int cartoon score*/
	var $score			= null;
	/** @var int view number*/
	var $view_num		= null;
	/** @var int download number*/
	var $down_num		= null;
	/** @var string file info (name, size, total page)*/
	var $file_info		= null;
	/** @var string thumbnail file name*/
	var $thumbnail_name	= null;
	/** @var int recommened */
	var $recommended	= null;
	/** @var int view point */
	var $viewpoint		= null;
	/** @var string alphabet */
	var $alphabet		= null;

	/**
	* @param database A database connector object
	*/
	function mosCartoon( &$database ) {
		$this->mosDBTable( '#__cartoon', 'id', $database );
	}
}

class mosAuthor extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string author name*/
	var $name			= null;
	/** @var string Author Nationality*/
	var $country		= null;
	/** @var date Birthday */
	var $birthday		= null;
	/** @var string thumbnail file name*/
	var $thumbnail_name	= null;

	/**
	* @param database A database connector object
	*/
	function mosAuthor( &$database ) {
		$this->mosDBTable( '#__author', 'id', $database );
	}
}

class mosTopic extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string name*/
	var $name			= null;
	/** @var string content*/
	var $content		= null;

	/**
	* @param database A database connector object
	*/
	function mosTopic( &$database ) {
		$this->mosDBTable( '#__topic', 'id', $database );
	}
}

class mosClassification extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string name*/
	var $name			= null;
	/** @var string content*/
	var $content		= null;
	/** @var string thumbnail file name*/
	var $thumbnail_name	= null;

	/**
	* @param database A database connector object
	*/
	function mosClassification( &$database ) {
		$this->mosDBTable( '#__classification', 'id', $database );
	}
}

class mosEstimate extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var int Cartoon id*/
	var $cartoon_id		= null;
	/** @var int User id*/
	var $user_id		= null;
	/** @var String Content*/
	var $content		= null;
	/** @var int Mark*/
	var $mark			= null;
	/** @var DateTime register Date*/
	var $add_date		= null;

	/**
	* @param database A database connector object
	*/
	function mosEstimate( &$database ) {
		$this->mosDBTable( '#__estimate', 'id', $database );
	}
}

class mosSharing extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var int Cartoon id*/
	var $cartoon_id		= null;
	/** @var int User id*/
	var $user_id		= null;
	/** @var String Content*/
	var $content		= null;
	/** @var DateTime register Date*/
	var $add_date		= null;

	/**
	* @param database A database connector object
	*/
	function mosSharing( &$database ) {
		$this->mosDBTable( '#__sharing', 'id', $database );
	}
}

class mosShelf extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var int Cartoon id*/
	var $cartoon_id		= null;
	/** @var int User id*/
	var $user_id		= null;
	/** @var DateTime register Date*/
	var $add_date		= null;

	/**
	* @param database A database connector object
	*/
	function mosShelf( &$database ) {
		$this->mosDBTable( '#__shelf', 'id', $database );
	}
}

class mosDownload extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var int Cartoon id*/
	var $cartoon_id		= null;
	/** @var int User id*/
	var $user_id		= null;
	/** @var DateTime download Date*/
	var $download_date	= null;
	/** @var int Flag(1: downloaded, 2: canceled)*/
	var $flag			= null;

	/**
	* @param database A database connector object
	*/
	function mosDownload( &$database ) {
		$this->mosDBTable( '#__download', 'id', $database );
	}
}

class mosViewCartoon extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var int Cartoon id*/
	var $cartoon_id		= null;
	/** @var int Cartoon parent id*/
	var $cartoon_parent	= null;
	/** @var int User id*/
	var $user_id		= null;
	/** @var DateTime view Date*/
	var $view_date		= null;

	/**
	* @param database A database connector object
	*/
	function mosViewCartoon( &$database ) {
		$this->mosDBTable( '#__viewcartoon', 'id', $database );
	}
}

class mosSearchKeyword extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string keyword */
	var $keyword		= null;
	/** @var int search count */
	var $search_count	= null;

	/**
	* @param database A database connector object
	*/
	function mosSearchKeyword( &$database ) {
		$this->mosDBTable( '#__searchkeyword', 'id', $database );
	}
}

/**
* visit guest of any user
*/
class mosVisitGuestOfUser extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var int user id */
	var $user_id		= null;
	/** @var int guest id */
	var $guest_id		= null;
	/** @var datetime visit date */
	var $visit_date		= null;

	/**
	* @param database A database connector object
	*/
	function mosVisitGuestOfUser( &$database ) {
		$this->mosDBTable( '#__visitguestofuser', 'id', $database );
	}
}

class mosUni2Pinyin extends mosDBTable {
	/** @var int Unique id*/
	var $id				= null;
	/** @var string hex */
	var $hexaUnicode	= null;
	/** @var string pinyin1 */
	var $pinyin1		= null;
	/** @var string pinyin2 */
	var $pinyin2		= null;
	/** @var string pinyin3 */
	var $pinyin3		= null;
	/** @var string pinyin4 */
	var $pinyin4		= null;
	/** @var string pinyin5 */
	var $pinyin5		= null;

	/**
	* @param database A database connector object
	*/
	function mosUni2Pinyin( &$database ) {
		$this->mosDBTable( '#__uni2pinyin', 'id', $database );
	}
}
?>