<?php
//a collection of general functions to be used with anything


/**
*
* shortens a message to the desired number of characters
*
*/
function shortenMsg($msg, $length, $end = '...'){

	$msgLength = strlen($msg);
	$message = $msg;
	if($msgLength > $length){
		$message = substr($msg, 0, $length);
		$message .= $end;
	}

	return $message;
}

/**
* formats dates to specified format
*
*/
function formatDate($date)
{
	return date(DATE_FORMAT, strtotime($date));

}


/**
 *  takes a float and scientific notation and returns a more readable version
 * 
 * */
function convertFloat($float)
{
	if($float === 0){
		return 0;
	}
	$str = rtrim(sprintf('%.8F', $float), '0');
	$checkLast = substr($str, -1);
	if($checkLast == '.'){
		$str = str_replace('.', '', $str);
	}
	return $str;
	
}


/**
 *  takes a string, and a count of items (usually an array)
 *  decides if string should be a plural or not
 *  just add an s to the end... make it smarter later
 * 
 * */
function pluralize($str, $itemCount, $andZero = false)
{
	if($itemCount > 1 OR ($andZero AND $itemCount == 0)){
		$str = $str.'s';
	}
	
	return $str;
	
}


/**
 *  just check if a post has been made
 * 
 * */
function posted()
{
	if(isset($_POST) AND count($_POST) > 0){
		return true;
	}
	else{
		return false;
	}
	
}

/**
 *  Turn a string into a URL friendly string
 * 
 * */
function genURL($str)
{
    $url = strtolower(trim($str));
    $url = preg_replace("/[^a-zA-Z0-9[:space:]\/s-]/", "", $url);
    $url = preg_replace("/(-| |\/)+/","-",$url);
    return $url;
}


/**
 *  Convert plain links to real links, email addresses to mailtos
 * 
 * 
 * */
function autolink($text) {

  $pattern = '/(((http[s]?:\/\/(.+(:.+)?@)?)|(www\.))[a-z0-9](([-a-z0-9]+\.)*\.[a-z]{2,})?\/?[a-z0-9.,_\/~#&=:;%+!?-]+)/is';
  $text = preg_replace($pattern, ' <a href="$1" rel="nofollow" target="_blank">$1</a>', $text);
  // fix URLs without protocols
  $text = preg_replace('/href="www/', 'href="http://www', $text);
  return $text;
}

/**
 *  generate a random salt
 * 
 * */
function createSalt()
{
	$length = mt_rand(15, 50);
	$salt = openssl_random_pseudo_bytes($length);
	$salt = bin2hex($salt);
	
	return $salt;
	
}

/**
 *  Generate a salt/pass hash combo.
 * 
 * */
function genPassSalt($str)
{
	$salt = createSalt();
	$pass = hash('sha256', $salt.$str);
	
	return array('hash' => $pass, 'salt' => $salt);
}


function decode_sessionData($data) {
    if(  strlen( $data) == 0)
    {
        return array();
    }
    
    // match all the session keys and offsets
    preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $matchesarray, PREG_OFFSET_CAPTURE);

    $returnArray = array();

    $lastOffset = null;
    $currentKey = '';
    foreach ( $matchesarray[2] as $value )
    {
        $offset = $value[1];
        if(!is_null( $lastOffset))
        {
            $valueText = substr($data, $lastOffset, $offset - $lastOffset );
            $returnArray[$currentKey] = unserialize($valueText);
        }
        $currentKey = $value[0];

        $lastOffset = $offset + strlen( $currentKey )+1;
    }

    $valueText = substr($data, $lastOffset );
    $returnArray[$currentKey] = unserialize($valueText);
    
    return $returnArray;
}

function get_mime($filepath) {
	$finfo = new finfo(FILEINFO_MIME);
	$output = $finfo->file($filepath);
    $output = explode("; ",$output);
    if ( is_array($output) ) {
        $output = strtolower($output[0]);
    }
    return $output;
}

function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

function debug($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

function timestamp()
{
	return date('Y-m-d H:i:s');
}

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        @$sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

function boolToText($num, $yes = 'yes', $no = 'no')
{
	if($num === true || intval($num) === 1){
		return $yes;
	}
	else{
		return $no;
	}
}

function shorten($text, $length = 200, $url = '', $more = '', $moreClass = ''){
$short = mb_substr($text, 0, $length);

if($short != $text) {
    $lastspace = strrpos($short, ' ');
    $short = substr($short , 0, $lastspace);
	
	$short .= ' ...';
    if($more != ''){
         $short .= ' <a href="'.$url.'" class="'.$moreClass.'">'.$more.'</a> ';
    } // end if more is blank

   
} // end if content != short

$short = str_replace("’","'", $short);
$short = stripslashes($short);
$short = nl2br($short);

return $short;

} // end short function


function get_object_vars_all($obj) {
  $objArr = substr(str_replace(get_class($obj)."::__set_state(","",var_export($obj,true)),0,-1);
  eval("\$values = $objArr;");
  return $values;
}

function mention($str, $message, $userId, $itemId = 0, $type = '')
{
	$match = preg_match_all('/\B\@([\w\-]+)/', $str, $matches);
	$model = new Slick_Core_Model;
	

	$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
	$thisUser = $model->get('users', $userId, array('userId', 'username', 'slug'));
	
	$success = false;
	foreach($matches[1] as $user){
		$user = strtolower(trim($user));

		$getUser = $model->get('users', $user, array('userId', 'username'), 'slug');
		if($getUser AND $getUser['userId'] != $userId){
			$message = str_replace('%username%', '<a href="'.$getSite['url'].'/profile/user/'.$thisUser['slug'].'">'.$thisUser['username'].'</a>', $message);
			$notify = Slick_App_Meta_Model::notifyUser($getUser['userId'], $message, $itemId, $type);
			if($notify){
			
				$success = true;
			}
		}
	}

	if($success){
		return true;
	}
	
	return false;
	
	
}

function markdown($str)
{
	require_once(SITE_PATH.'/resources/Parsedown.php');
	$parsedown = new Parsedown();
	$parse =  $parsedown->parse($str);
	
	/*** @mention mod ***/
	$match = preg_match_all('/\B\@([\w\-]+)/', $str, $matches);
	$model = new Slick_Core_Model;
	
	$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
	
	$success = false;
	foreach($matches[1] as $user){
		$orig = '@'.$user;
		$user = strtolower(trim($user));
		//$user = substr($user, 1);
		
		$getUser = $model->get('users', $user, array('userId', 'username', 'slug'), 'slug');
		if($getUser){
			$parse = str_replace($orig, '<a href="'.$getSite['url'].'/profile/user/'.$getUser['slug'].'">'.$orig.'</a>', $parse);
		}
	}
	
	/*** end @mention mod ***/
	
	return $parse;
}


function parseRawInput()
{
	// Fetch content and determine boundary
	$raw_data = file_get_contents('php://input');
	
	$boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
	if(trim($boundary) == ''){
		return array();
	}

	// Fetch each part

	$parts = array_slice(explode($boundary, $raw_data), 1);
	$data = array();

	foreach ($parts as $part) {
		// If this is the last part, break
		if ($part == "--\r\n") break; 

		// Separate content from headers
		$part = ltrim($part, "\r\n");
		
		list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);
		
		// Parse the headers list
		$raw_headers = explode("\r\n", $raw_headers);
		$headers = array();
		foreach ($raw_headers as $header) {
			list($name, $value) = explode(':', $header);
			$headers[strtolower($name)] = ltrim($value, ' '); 
		} 

		// Parse the Content-Disposition to get the field name, etc.
		if (isset($headers['content-disposition'])) {
			$filename = null;
			preg_match(
				'/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', 
				$headers['content-disposition'], 
				$matches
			);
			list(, $type, $name) = $matches;
			isset($matches[4]) and $filename = $matches[4]; 

			// handle your fields here
			switch ($name) {
				// this is a file upload
				case 'userfile':
					 file_put_contents($filename, $body);
					 break;

				// default for all other files is to populate $data
				default: 
					 $data[$name] = substr($body, 0, strlen($body) - 2);
					 break;
			} 
		}

	}
	
	return $data;
		
}

function checkRequiredFields($data, $fields = array())
{
	$req = array();
	foreach($data as $k => $v){
		$req[$k] = false;
		foreach($fields as $fieldK => $field){
			if($k == $fieldK){
				$req[$k] = true;
			}
		}
	}
	$useData = array();
	foreach($req as $key => $required){
		if(!isset($data[$key]) OR trim($data[$key]) == ''){
			if($required){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = '';
			}
		}
		else{
			$useData[$key] = $data[$key];
		}
	}
	return $useData;
}

function arrayToCSV($data)
{
	$output = array();
	foreach($data as $row){
		if(is_array($row)){
			foreach($row as $rk => $rv){
				$row[$rk] = '"'.str_replace('"','',$rv).'"';
			}
		}
		else{
			$row = array($row);
		}
		$row = join(',',$row);
		$output[] = $row;
	}
	$output = join("\n", $output);
	return trim($output);
}

function isExternalLink($link)
{
	$link = trim($link);
	if(substr($link, 0, 7) == 'http://' OR substr($link, 0, 8) == 'https://'){
		return true;
	}
	return false;
}

function extract_row($data, $vals, $returnEmpty = false)
{
	$output = array();
	foreach($data as $row){
		$found = true;
		foreach($vals as $key => $val){
			if(!isset($row[$key]) OR $row[$key] != $val){
				$found = false;
			}
		}
		if($found){
			$output[] = $row;
		}
	}
	if(!$returnEmpty AND count($output) == 0){
		return false;
	}
	return $output;
}

function encrypt_string($string)
{
	if(!defined('ENCRYPT_KEY')){
		return false;
	}
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(ENCRYPT_KEY), $string, MCRYPT_MODE_CBC, md5(md5(ENCRYPT_KEY))));
}

function decrypt_string($string)
{
	if(!defined('ENCRYPT_KEY')){
		return false;
	}
	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(ENCRYPT_KEY), base64_decode($string), MCRYPT_MODE_CBC, md5(md5(ENCRYPT_KEY))), "\0");
	
}

function replaceNonSGML($string)
{
	$entities = array(
		"\x80" => '&euro;',    # 128 -> euro sign, U+20AC NEW
		"\x82" => '&sbquo;',   # 130 -> single low-9 quotation mark, U+201A NEW
		"\x83" => '&fnof;',    # 131 -> latin small f with hook = function = florin, U+0192 ISOtech
		"\x84" => '&bdquo;',   # 132 -> double low-9 quotation mark, U+201E NEW
		"\x85" => '&hellip;',  # 133 -> horizontal ellipsis = three dot leader, U+2026 ISOpub
		"\x86" => '&dagger;',  # 134 -> dagger, U+2020 ISOpub
		"\x87" => '&Dagger;',  # 135 -> double dagger, U+2021 ISOpub
		"\x88" => '&circ;',    # 136 -> modifier letter circumflex accent, U+02C6 ISOpub
		"\x89" => '&permil;',  # 137 -> per mille sign, U+2030 ISOtech
		"\x8A" => '&Scaron;',  # 138 -> latin capital letter S with caron, U+0160 ISOlat2
		"\x8B" => '&lsaquo;',  # 139 -> single left-pointing angle quotation mark, U+2039 ISO proposed
		"\x8C" => '&OElig;',   # 140 -> latin capital ligature OE, U+0152 ISOlat2
		"\x8E" => '&#381;',    # 142 -> U+017D
		"\x91" => '&lsquo;',   # 145 -> left single quotation mark, U+2018 ISOnum
		"\x92" => '&rsquo;',   # 146 -> right single quotation mark, U+2019 ISOnum
		"\x93" => '&ldquo;',   # 147 -> left double quotation mark, U+201C ISOnum
		"\x94" => '&rdquo;',   # 148 -> right double quotation mark, U+201D ISOnum
		"\x95" => '&bull;',    # 149 -> bullet = black small circle, U+2022 ISOpub
		"\x96" => '&ndash;',   # 150 -> en dash, U+2013 ISOpub
		"\x97" => '&mdash;',   # 151 -> em dash, U+2014 ISOpub
		"\x98" => '&tilde;',   # 152 -> small tilde, U+02DC ISOdia
		"\x99" => '&trade;',   # 153 -> trade mark sign, U+2122 ISOnum
		"\x9A" => '&scaron;',  # 154 -> latin small letter s with caron, U+0161 ISOlat2
		"\x9B" => '&rsaquo;',  # 155 -> single right-pointing angle quotation mark, U+203A ISO proposed
		"\x9C" => '&oelig;',   # 156 -> latin small ligature oe, U+0153 ISOlat2
		"\x9E" => '&#382;',    # 158 -> U+017E
		"\x9F" => '&Yuml;',    # 159 -> latin capital letter Y with diaeresis, U+0178 ISOlat2
	);
	
	foreach($entities as $k => $v){
		$string = str_replace($k, $v, $string);
	}
	
	$string =  trim(mb_convert_encoding($string, 'UTF-8'));
	$string = preg_replace('/[^(\x20-\x7F)]*/','', $string);
	
	return $string;
}

?>
