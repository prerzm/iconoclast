<?php

/** RZM PHP Framework **/

// Generate token
function sec_generate_token() {
	
	# generate token
	$token = md5( SEC_SALT . $_SERVER['HTTP_USER_AGENT'] );
	
	return $token;
	
}
	
// Generate random token
function sec_generate_random_token() {

	# generate token
	$token = md5( SEC_SALT . uniqid() );
	
	return $token;
	
}


// Set alerts in session
function set_alert($type, $message) {
	
	# get alerts array
	$alerts = session_get_data("alerts");

	# set new alert
	if(!is_array($alerts)) {
		$alerts = array();
	}
	$alerts["alerts"][] = array("type" => $type, "message" => $message);

	session_set_data( array("alerts" => $alerts) );

}

// get alerts
function get_alerts() {
	
	$alerts = session_get_data("alerts");

	if( is_array($alerts) && count($alerts)>0 ) {
		session_unset_data("alerts");
		return $alerts["alerts"];
	} else {
		return false;
	}

}

// display alerts
function display_alerts() {

	$alerts = get_alerts();

	if( $alerts!=false ) {
        foreach($alerts as $alert) {
			print '<div class="alert alert-'.$alert["type"].'">';
			print '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			print '<h4>'.$alert["message"].'</h4>';
			#print $alert["message"];
			print '</div>';
        }
	}

}


// Generates a 10 char password
function sec_generate_password() {

	$password = "";
	
	for($i=0; $i<10; $i++) {

		# random upper, lower, or number
		$type = rand(1, 3);

		# generate random char
		switch($type) {

			# generate random upper  65-90
			case 1:
				$password .= chr(rand(65, 90));
			break;

			# generate random lower  97-122
			case 2:
				$password .= chr(rand(97, 122));
			break;

			# generate random number
			case 3:
				$password .= rand(0, 9);
			break;

		}

	}

	# return generated password
	return $password;
	
}

// Validates a password
function sec_is_secure_password($password) {
	
	if( strlen(trim($password))<8 || !preg_match("#[0-9]+#", $password) ) {
		return false;
	}

	return true;

}

// Generate hash for password
function sec_hash_password($password) {
	
	return md5( SEC_SALT . $password );
	
}


// Add url param to global array
function params_add($key, $value) {

    global $global_params;

    $global_params[$key] = $value;

}

// Remove url param from global array
function params_remove($key) {

    global $global_params;

    if(isset($global_params[$key])) {
        unset($global_params[$key]);
    }

}

// Build url params from global array
function params_get() {

	global $global_params;
    $params_str = "";

	if(isset($global_params) && is_array($global_params)) {
		foreach($global_params as $key => $value) {
			$params_str .= $key."=".$value."&";
		}
	}
	
	return $params_str;

}

// Add params to url if any and redirect to url
function redirect($url, $debug=false) {

	$params = params_get();

    $location = ($params=="") ? SITE_URL.$url : SITE_URL.$url."?".$params;
    
    if($debug==true) {
		print $location;
		print "<br>Alerts:";
		var_dump(get_alerts());
    } else {
		header("Location: $location");
		exit;
    }
    
}

// mask email
function var_mask_email($email) {

	$mask = "";

	if(var_is_email($email)) {
	
		list($user, $domain) = explode("@", $email);
		$fill = strlen($user) - 2;
		$user = substr($user, 0, 1).str_repeat("*", $fill-1).substr($user, strlen($user)-2, strlen($user));

		$mask = $user."@".$domain;
	
	}

	return $mask;

}

// Log system event
function system_log($recordId, $module, $action, $data, $debug=false) {

	$userId = session_get_data("userId");
	$date = date("Y-m-d H:i:s");

	query_insert(TABLE_SYSTEM_LOG, array("usuarioId" => $userId, "registroId" => $recordId, "fecha" => $date, "modulo" => $module, "accion" => $action, "info" => $data), $debug);

}

// array to json and base64 encode
function array_to_db($values) {
	if(is_array($values) && count($values)>0) {
		return base64_encode(json_encode($values, JSON_UNESCAPED_UNICODE));
	}
	return "";
}

function array_from_db($str) {
	if($str!="") {
		return json_decode(base64_decode($str), true);
	}
	return false;
}

// replace [] to <b> tags
function text_to_html($text) {
	$text = str_replace( array("[", "]"), array("<b>", "</b>"), $text);
	return nl2br($text);
}

?>