<?php
/*
* Decimal representation of $c
* function found there: http://www.cantonese.sheik.co.uk/phorum/read.php?2,19594
*/
function uniord($c)
{
	$ud = 0;
	if (ord($c{0})>=0 && ord($c{0})<=127)
		$ud = $c{0};
	if (ord($c{0})>=192 && ord($c{0})<=223)
		$ud = (ord($c{0})-192)*64 + (ord($c{1})-128);
	if (ord($c{0})>=224 && ord($c{0})<=239)
		$ud = (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
	if (ord($c{0})>=240 && ord($c{0})<=247)
		$ud = (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
	if (ord($c{0})>=248 && ord($c{0})<=251)
		$ud = (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
	if (ord($c{0})>=252 && ord($c{0})<=253)
		$ud = (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
	if (ord($c{0})>=254 && ord($c{0})<=255) //error
		$ud = false;
	return $ud;
}
/*
* Translate the $string string of a single chinese charactere to unicode
*/
function chineseToHexaUnicode($string) {
	return strtoupper(dechex(uniord($string)));
}
/*
* 
*/
function convertChineseToPinyin($string) {
	global $database;
	
	$pinyinValue = '';
	for ($i = 0; $i < mb_strlen($string);$i=$i+3) {
		$unicode = chineseToHexaUnicode(mb_substr($string, $i, 3));
		$query = "SELECT * FROM #__uni2pinyin WHERE hexaUnicode='$unicode'";
		$database->setQuery($query);
		$row = null;
		$database->loadObject($row);
		$pinyinValue .= $row->pinyin1;
	}
	return $pinyinValue;
}

function utf8_to_unicode( $str ) {

	$unicode = array();        
	$values = array();
	$lookingFor = 1;

	for ($i = 0; $i < mb_strlen( $str ); $i=$i+3 ) {
		$str_piece = mb_substr($str, $i, 3);
		$thisValue = ord( $str_piece );
		if ( $thisValue < ord('A') ) {
			// exclude 0-9
			if ($thisValue >= ord('0') && $thisValue <= ord('9')) {
				// number
				$unicode[] = chr($thisValue);
			}
			else {
				$unicode[] = '%'.dechex($thisValue);
			}
		} else {
			if ( $thisValue < 128) 
				$unicode[] = $str_piece;
			else {
				if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;                
				$values[] = $thisValue;                
				if ( count( $values ) == $lookingFor ) {
					$number = ( $lookingFor == 3 ) ?
					( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
					( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
					$number = dechex($number);
					$unicode[] = (strlen($number)==3)?"%u0".$number:"%u".$number;
					$values = array();
					$lookingFor = 1;
				} // if
			} // if
		}
	} // for
	return implode("",$unicode);

} // utf8_to_unicode

?>