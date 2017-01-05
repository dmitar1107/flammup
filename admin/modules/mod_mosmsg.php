<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mosmsg = strval( ( stripslashes( strip_tags( mosGetParam( $_REQUEST, 'mosmsg', '' ) ) ) ) );
// Browser Check
$browserCheck = 0;
if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
	$browserCheck = 1;
}

if ($mosmsg && $browserCheck ) {	
	// limit mosmsg to 200 characters
	if ( strlen( $mosmsg ) > 200 ) {
		$mosmsg = substr( $mosmsg, 0, 200 );
	}	
	?>
	<div class="message">
		<?php echo $mosmsg; ?>
	</div>
	<?php
}
?>