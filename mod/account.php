<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# vars
$cmd = aglobal('cmd', 20);
$return	= "account.php";
$params	= "";

# process
switch($cmd) {

	case 'update':
	
		# vars
		$error = false;
		$user['email'] = apost('email', 100);
        $password = apost('password', 128);
        $password_confirm = apost('password_confirm', 128);

		# filer data
		if(!var_is_email($user['email'])) {
			$error = true;
			set_alert("error", "Por favor ingresa un correo electrónico válido.");
        }
        
        # check password change
        if(!var_is_empty($password)) {
			if(var_is_valid_password($password)) {
	            if($password===$password_confirm) {
					$old_password = query_select_single_value("userId", TABLE_USERS, "userId = ".(int)session_get_data("userId")." AND password = '".sec_hash_password($password)."'");
					if($old_password==false) {
						$user['password'] = sec_hash_password($password);
					}
        	    } else {
            	    $error = true;
                	set_alert("error", "Las contraseñas no coinciden");
				}
			} else {
				$error = true;
				set_alert("error", "La contraseña no es válida");
			}
        }

		# update
		if($error==false) {
			
            $updated = query_update(TABLE_USERS, $user, "userId = ".(int)session_get_data("userId"));
			
            if($updated>0) {
                system_log($updated, TABLE_USERS, "Update", json_encode($user));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
    
        }

	break;
	
	default:

		# set error & error message on session
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
	
	break;
	
}

# redirect
redirect($return);

?>