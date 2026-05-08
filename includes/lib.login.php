<?php

/** RZM PHP Framework **/

// Login function
function login($user, $password, $aviso) {

	$admin = sql_select_row("SELECT usuarioId, companyId, rolId, nombre, password FROM ".TABLE_USERS." WHERE email = '$user' AND deleted = 0 LIMIT 1");
	$vendor = sql_select_row("SELECT proveedorId, razonSocial, password, tmp FROM ".TABLE_VENDORS." WHERE (email = '$user' OR rfc = '$user') AND token = '' AND deleted = 0 LIMIT 1");

	if($admin && $vendor) {

		// Error/Duplicate Account
		set_alert("error", "Hubo un error en la información, favor de notificar al administrador");

	} elseif($admin || $vendor) {

		if($admin) {

			$user_id = (int)$admin['usuarioId'];
			$company_access = (int)$admin['companyId'];
			$company_id = ((int)$admin['companyId']==0) ? 1 : (int)$admin['companyId'];
			$role_id = (int)$admin['rolId'];
			$is_admin = 1;
			$name = $admin['nombre'];
			$db_password = $admin['password'];
			$tmp_password = "";

		} else {

			$user_id = (int)$vendor['proveedorId'];
			$company_access = 0;
			$company_id = 0;
			$role_id = (int)ROLE_VENDOR;
			$is_admin = 0;
			$name = $vendor['razonSocial'];
			$db_password = $vendor['password'];
			$tmp_password = $vendor['tmp'];

			if($aviso==false) {
				set_alert("error", "Es necesario que lea y verifique la casilla del Aviso de Privacidad.");
				return false;
			}

		}

		if (login_checkbrute($user_id, $is_admin) == true) {
				
			set_alert("error", "Tu usuario ha sido bloqueado, por favor contacta al administrador del sitio.");

		} else {

			// Verify password
			if (login_password_verify($password, $db_password, $tmp_password) == true) {

				// Password is correct!
				$user_browser = $_SERVER['HTTP_USER_AGENT'];
				session_set_data( array("userId" => (int)$user_id, "companyAccess" => (int)$company_access, "companyId" => (int)$company_id, "roleId" => (int)$role_id, "name" => $name, "login_string" => hash('sha512', $db_password . $user_browser) ) );
				
				// Login successful.
				return true;
				
			} else {

				// Incorrect password (record attempt in database)
				login_record_failed_attempt($user_id, $is_admin);
				set_alert("error", "1-Tu usuario o contraseña son inválidos.");
				
			}

		}

	} else {

		set_alert("error", "2-Tu usuario o contraseña son inválidos.");

	}

	return false;

}

/**
* Function to check if password submitted is the password
* stored in the database
* This function needs to be substituted with password_verify
* PHP 7.0 function
*/
function login_password_verify($password, $db_password, $tmp_password="") {

	// Check with md5
	if( sec_hash_password($password) == $db_password || ($tmp_password!="" && sec_hash_password($password) == $tmp_password)) {
		return true;
	} else {
		return false;
	}

}


// Check login attempts to prevent bruteforce attacks
function login_checkbrute($user_id, $is_admin) {

	// All login attempts are counted from the past hour.
	$time = time() - (1 * 60 * 60);	// now - (hours * minuts * seconds)

	$attempts = (int)query_select_single_value("COUNT(usuarioId)", TABLE_USERS_ATTEMPTS, "usuarioId = $user_id AND admin = $is_admin AND time > '$time'");
	
	if ($attempts > 3) {

		return true;

	}

	return false;

}

function login_record_failed_attempt($user_id, $is_admin) {
	
	query_insert(TABLE_USERS_ATTEMPTS, array("usuarioId" => $user_id, "admin" => $is_admin, "time" => time()));

}
	

// Check logged in status function
function login_check($mysqli) {
	
	// Check if all session variables are set 
	if (isset($_SESSION['userId'], $_SESSION['roleId'], $_SESSION['name'], $_SESSION['login_string'])) {

		// Get the session vars
		$user_id = (int)session_get_data("userId");
		$role_id = (int)session_get_data("roleId");
		$name = session_get_data("name");
		$login_string = session_get_data("login_string");
	
		// Get the user-agent string of the user.
		$user_browser = $_SERVER['HTTP_USER_AGENT'];
	
		// Prepare stmt
		if($role_id==ROLE_VENDOR) {
			$stmt = $mysqli->prepare("SELECT password FROM ".TABLE_VENDORS." WHERE proveedorId = ? LIMIT 1");
		} else {
			$stmt = $mysqli->prepare("SELECT password FROM ".TABLE_USERS." WHERE usuarioId = ? LIMIT 1");
		}

		if ($stmt) {

			// Bind "$user_id" to parameter. 
			$stmt->bind_param('i', $user_id);
			$stmt->execute();   // Execute the prepared query.
			$stmt->store_result();
	
			// If user exists (only and user exists)
			if ($stmt->num_rows == 1) {

				// If the user exists get variables from result.
				$stmt->bind_result($password);
				$stmt->fetch();
				$login_check = hash('sha512', $password . $user_browser);
	
				if (login_hash_equals($login_check, $login_string) ){

					// User is logged In!!!! 
					return true;
					
				} else {

					// User is not logged in 
					return false;
					
				}
				
			} else {

				// User is not logged in 
				return false;
				
			}
			
		} else {

			// User is not logged in 
			return false;
			
		}
		
	} else {

		// User is not logged in 
		return false;
		
	}
	
}


// Redirect if user is not logged in
function redirect_if_logged_off($logged, $ref="") {
	if($logged==false) {
		header("Location: login.php?ref=$ref");
		exit;
	}
}

/**
* Compare login strings
* This function needs to be substituted with hash_equals
* PHP 7.0 function
*/
function login_hash_equals($login_check, $login_string) {

	if($login_check === $login_string) {
		return true;
	} else {
		return false;
	}

}


// Logout user / destroy session
function logout() {

	# unset session data
	session_unset_data();

	# gets cookies params
	$cookieParams = session_get_cookie_params();

	# set cooki with params
	setcookie(session_name(), '', time() - 84600, $cookieParams["path"], $cookieParams["domain"], SECURE_HTTP, HTTP_ONLY);

	# destroy session
	session_destroy();

}


?>