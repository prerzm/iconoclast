<?php

/** RZM PHP Framework **/

// find & return var from all arrays
function aglobal($name="", $max_input=5000){

	# vars
	$output = "";

	# search in _GET
	if(isset($_GET) && isset($_GET[$name])) {
		$output = $_GET[$name];
	}
	
	# search in _POST
	if(isset($_POST) && isset($_POST[$name])) {
		$output = $_POST[$name];
	}
	
	# return
	return substr(trim($output), 0, $max_input);

}


// find & return var from post array
function apost($name, $max_input=2048) {

	# get & sanitize info
	$output = isset($_POST[$name]) ? $_POST[$name] : "";
	
	# return value or empty string
	if($output!=null && $output!=false) {
		return substr($output, 0, $max_input);
	} else {
		return "";
	}

}

// find & return var from get array
function aget($name, $max_input=1024){

	# get & sanitize info
	$output = isset($_GET[$name]) ? $_GET[$name] : "";
	
	# return value or empty string
	if($output!=null && $output!=false) {
		return substr($output, 0, $max_input);
	} else {
		return "";
	}

}

// set value to $_COOKIE array
function cookie_set($name, $value, $expire_days=7, $path="/", $domain=""){

	# verify info
	if(var_is_empty($name)) {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function cookie_set: cookie name not set, params: $name, $value, $expire_days, $path, $domain");
			
		}
	
		return false;
	
	}
	
	# encode value
	$output		= base64_encode($value);
	
	# expires
	$expires	= time() + (3600 * 24 * $expire_days);
	
	# set cookie
	setcookie($name, $output, $expires, $path, $domain);
	
}


// get from $_COOKIE array for specified key.
function cookie_get($name, $max_input=4093){

	# verify info
	if(!isset($_COOKIE) || var_is_empty($name) || !isset($_COOKIE[$name])) {
		return "";
	}
	
	# decode value
	$output	= base64_decode($_COOKIE[$name]);
	
	# return value
	return substr(trim($output), 0, $max_input);

}


// Validates a var not being null or empty
function var_is_empty($var) {

	if( is_null($var) || strlen(trim($var))==0 || empty($var)) {

		return true;
	
	} else {
	
		return false;
		
	}

}


// Validates a var being a min length
function var_too_short($var, $length) {

	if( is_null($var) || strlen(trim($var))==0 || empty($var) || strlen(trim($var))<$length) {

		return true;
	
	} else {
	
		return false;
		
	}

}


// Validates an email address
function var_is_email($email) {

	# validate email with php filter
	return filter_var($email, FILTER_VALIDATE_EMAIL);

}

// Validates a non-empty array
function var_is_valid_array($var) {
	return ( is_array($var) && count($var)>0 );
}


// Validates a password
function var_is_valid_password($password) {
	
	# validate restrictions
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$number    = preg_match('@[0-9]@', $password);
	$length = (strlen($password) >= 8) ? true : false;
	
	if(!$uppercase || !$lowercase || !$number || !$length ) {
		return false;
	} else {
		return true;
	}
	
}

function uniord($u) {
    $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
    $k1 = ord(substr($k, 0, 1));
    $k2 = ord(substr($k, 1, 1));
    return $k2 * 256 + $k1;
}

// Sanitizes rfc (only valid chars)
function var_sanitize_rfc($rfc) {

	$sanitized = "";

	for($i=0; $i<strlen($rfc); $i++) {
		$char = uniord($rfc[$i]);
		if( ($char>=48 && $char<=57) || ($char>=65 && $char<=90) || ($char>=97 && $char<=122) ) {
			$sanitized .= $rfc[$i];
		}
	}

	return $sanitized;

}

// Validates an rfc
function var_is_valid_rfc($rfc) {

	preg_match('/[A-Z&]{4}[0-9]{6}.{3}/', $rfc, $person);
	if(strlen(trim($rfc))==13 && is_array($person) && count($person)>0) {
		return true;
	}

	preg_match('/[A-Z&]{3}[0-9]{6}.{3}/', $rfc, $company);
	if(strlen(trim($rfc))==12 && is_array($company) && count($company)>0) {
		return true;
	}

	return false;

}

// Validates a nif
function var_is_valid_nif($nif) {

	if(strlen(trim($nif))>3 && strlen(trim($nif))<16) {
		return true;
	}

	return false;

}

?>