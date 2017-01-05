<?php
define('_JEXEC', 1);

session_start();

include ("configuration.php");
include ("includes/database.php");
include ("includes/common.php");
include ("includes/dbclass.php");
include ("includes/class.inputfilter.php");

// retrieve some expected url (or form) arguments
$option = strval( strtolower( mosGetParam( $_REQUEST, 'option' ) ) );
$Itemid = intval( mosGetParam( $_REQUEST, 'Itemid', null ) );
$task = mosGetParam( $_REQUEST, 'task', '' );

$mainframe = new MainFrame( $database );
$path = $mainframe->getPath($option);

if( $task == "login" ) {
	$mainframe->login();
}

if( $task == "logout" ) {
	$mainframe->logout();
}

if( !isset($_SESSION['admin_logintime']) || $_SESSION['admin_logintime'] == "" ) {
	require_once( "$Config_absolute_path/components/com_login/login.php" );
	exit;
}

$mainframe->initSession();

ob_start();

if (file_exists($path)) {
	$task 	= strval( mosGetParam( $_REQUEST, 'task', '' ) );
	require_once( $path );
}

$_MOS_BODY_BUFFER = ob_get_contents();

ob_end_clean();

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// loads template file
if ( !file_exists( $Config_absolute_path .'/templates/'. $Config_cur_template .'/index.php' ) ) {
	echo "Not found template: " . $Config_cur_template;
} else {
	require_once( $Config_absolute_path .'/templates/'. $Config_cur_template .'/index.php' );
	echo '<!-- '. time() .' -->';
}
?>
