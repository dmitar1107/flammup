<?php
define('_JEXEC', 1);

session_start();

include ("configuration.php");
include ("includes/database.php");
include ("includes/common.php");
include ("includes/dbclass.php");
include ("includes/class.inputfilter.php");
include ("includes/Mobile_Detect.php");

// check mobile
//$detect = new Mobile_Detect();
//$_device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');


$task = mosGetParam( $_REQUEST, 'task', '' );
if( $task == "option" ) {
	unset($_REQUEST['type']); unset($_POST['type']); unset($_GET['type']);
}
$option = strval( strtolower( mosGetParam( $_REQUEST, 'option', 'library' ) ) );

$page = strval( strtolower( mosGetParam( $_REQUEST, 'page', '' ) ) );

$mainframe = new MainFrame( $database );
$path = $mainframe->getPath($option);



if( $task == "login" ) {
	$mainframe->login();
}

if( $task == "logout" ) {
	$mainframe->logout($option,$page);
}
if($task == "register") {
	$mainframe->register();
}

//if( !isset($_SESSION['logintime']) || $_SESSION['logintime'] == "" ) {
//	require_once( "$Config_absolute_path/components/com_login/login.php" );
//	exit;
//}

//$mainframe->initSession();


// user info
$_user = new mosUser($database);
$_user->load($_SESSION['user_id']);
$_user->load(3);

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
