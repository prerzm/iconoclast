<?php

/** RZM PHP Framework **/

// secure sessions function
function session_secure_start() {
	
	// Forces sessions to only use cookies.
	if (ini_set('session.use_only_cookies', 1) === FALSE) {
		die("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
		exit();
	}

	// Gets and sets current cookies params.
	$cookieParams = session_get_cookie_params();
	session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], SECURE_HTTP, HTTP_ONLY);

	// Sets the session name to the defined
	session_name(SEC_SESSION_ID);

	// Start the PHP session
	session_start();

	// regenerated the session, delete the old one.
	session_regenerate_id();

}
	

/**
* Set data in the user's active session.
* receives array for the key => value _SEESION array
*/
function session_set_data($data) {

	# verify info
	if(!is_array($data) || count($data)==0) {
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			error_log("function session_set_data: data is empty");
			
		}

		return false;
	
	}
	
	# set data in user's active session
	foreach($data as $key => $value) {
	
		# set session vars
		$_SESSION[$key]	= $value;
		
	}
	
	return true;
	
}


/**
* Get data stored in the user's active session.
*
*/
function session_get_data($key="") {

	# verify if session is active
	if(isset($_SESSION) && is_array($_SESSION) && count($_SESSION)>0) {

		# if key is blank return all data stored in SESSION array
		if($key=="") {
		
			return $_SESSION;
			
		} else {

			# verify key in SESSION array
			if(isset($_SESSION[$key])) {
			
				return $_SESSION[$key];
				
			} else {
			
				return false;
				
			}
			
		}
	
	} else {
	
		return false;
		
	}
	
}


/**
	* Unset data stored in the user's active session.
	* unset all the keys in the given data array
	*
	*/

function session_unset_data($key="") {

	# verify if session is active
	if(isset($_SESSION) && is_array($_SESSION) && count($_SESSION)>0) {
		
		# if key is blank unset all data stored in SESSION array
		if($key=="") {
		
			unset($_SESSION);
			$_SESSION = array();
			
		} else {

			# verify key in SESSION array
			if(isset($_SESSION[$key])) {
			
				unset($_SESSION[$key]);
				
			} else {
			
				if(ENVIRONMENT=="DEVELOPMENT") {

					error_log("function session_unset_data: key $key not found");
					
				}

			}
			
		}
	
	}
			
}

	
?>