<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.config.php');
include_once ('../includes/inc.db.connect.php');
include_once ('../includes/inc.db.tables.php');
include_once ('../includes/lib.database.php');
include_once ('../includes/lib.files.php');
include_once ('../includes/lib.misc.php');
include_once ('../includes/lib.sessions.php');
include_once ('../includes/lib.login.php');
include_once ('../includes/lib.vars.php');
include_once ('../includes/lib.abp.php');
include_once ('../vendor/autoload.php');
include_once ('../includes/autoload.php');

# session
session_secure_start();

# load settings
load_settings();

# return
$stop = false;
$return = "login.php";

# process
switch(aglobal('cmd', 10)) {

	case 'login':
	
		# vars
		$error = false;
		$token_post = apost('token', 50);
		$token_sess = session_get_data("token");
		$user = apost('user', 100);
		$password = apost('password', 128);

		# secure token
		if( $token_sess!=$token_post) {
			$error = true;
			set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
		}

		# verify user
		if( !var_is_email($user) && !var_is_valid_rfc($user) && !var_is_valid_nif($user)) {
			$error = true;
			set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
		}

		# verify aviso check
		$aviso = ( isset($_POST['aviso']) ) ? true : false;
		
		# validate user credentials with login function
		if($error==false) {

			$logged = login($user, $password, $aviso);

			if( $logged == true ) {
				system_log(0, TABLE_USERS, "Login", $user);
				$return = ( session_get_data("roleId")==ROLE_VENDOR ) ? "vendors.pos.php" : "index.php";
			}

		}

		session_unset_data("token");

	break;

	case 'logout':
	
		# check if user is logged in
		if( login_check($mysqli) == true ) {

			# logout
			logout();

		}

	break;

	case 'register':

		# vars
		$error = false;
		$updated = 0;
		$token_post = apost('token',32);
		$token_sess = session_get_data("token");
		$values['rfc'] = apost('rfc', 15);
		$values['razonSocial'] = apost('razonSocial');
		$values['email'] = apost('email');
		$values['editar'] = 1;
		$password = apost('password');
		$passwordConfirm = apost('passwordConfirm');

		# secure token
		if( $token_sess!=$token_post) {
			$error = true;
			set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
		}

		# verify
		if(!var_is_valid_rfc($values['rfc']) && !var_is_valid_nif($values['rfc'])) {
			$error = true;
			set_alert("error", "El RFC es inválido, favor de verificarlo.");
		}
		if(var_is_empty($values['razonSocial'])) {
			$error = true;
			set_alert("error", "La Razón Social es inválida, favor de verificarla.");
		}
		if(!var_is_email($values['email'])) {
			$error = true;
			set_alert("error", "El Email es inválido, favor de verificarlo.");
		}
		if($password!==$passwordConfirm) {
			$error = true;
			set_alert("error", "Las contraseñas no coinciden, favor de verificarlas.");
		}
		$vendor = sql_select("SELECT proveedorId FROM ".TABLE_VENDORS." WHERE rfc = '".$values['rfc']."' OR email = '".$values['email']."'");
		if($vendor) {
			$error = true;
			set_alert("error", "Esta cuenta ya se encuentra registrada.");
			$return = "login.register.php";
		}

		# create new record
		if($error==false) {

			$values['token'] = sec_generate_random_token();
			$values['password'] = sec_hash_password($password );

			$updated = query_insert(TABLE_VENDORS, $values);

			if($updated>0) {

				system_log($updated, TABLE_VENDORS, "New", json_encode($values));

				$content['search'] = array('SITE_URL', 'TOKEN');
				$content['replace'] = array(SITE_URL, $values['token']);

				$mail = new NEWMailer();
				$mail_sent = $mail->send_mail_login($values['email'], "Registro Como Proveedor", PATH_MAILS."mail.register.html", $content);

				if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY) {
					$stop = true;
				}

				if($mail_sent) {
					set_alert("success", "Tus datos han sido registrados, por favor confirma tu correo para poder iniciar sesión.");
				} else {
					set_alert("error", "Hubo un problema al enviar tu información, favor de intentar nuevamente.");
				}
		
			}

		}

	break;

	case 'confirm':

		# vars
		$token = aget('token',40);

		# process
		$query = sql_select_row("SELECT proveedorId FROM ".TABLE_VENDORS." WHERE token = '$token'");

		if($query) {

			$values['token'] = "";

			$updated = query_update(TABLE_VENDORS, $values, "proveedorId = ".$query['proveedorId']);

			if($updated>0) {
				set_alert("success", "Tu correo ha sido confirmado y tu cuenta activada. Ya puedes iniciar sesión");
			} else {
				set_alert("error", "Hubo un problema al activar tu cuenta, favor de intentar nuevamente.");
			}

		} else {

			set_alert("error", "Hubo un problema al encontrar tu registro, favor de intentar nuevamente.");
			redirect("login.register.php");

		}

	break;

	case 'recover':

		# vars
		$rfcemail = apost('rfcemail', 200);
		$token_post = apost('token', 50);
		$token_sess = session_get_data("token");

		# process
		if(var_is_valid_rfc($rfcemail)) {
			$query = sql_select_row("SELECT proveedorId, email FROM ".TABLE_VENDORS." WHERE rfc = '$rfcemail'");
		} elseif(var_is_email($rfcemail)) {
			$query = sql_select_row("SELECT proveedorId, email FROM ".TABLE_VENDORS." WHERE email = '$rfcemail'");
		} else {
			$query = false;
		}
		
		if($query) {

			$values['token'] = sec_generate_random_token();

			$updated = query_update(TABLE_VENDORS, $values, "proveedorId = ".$query['proveedorId']);

			if($updated>0) {

				$vendor_email = var_mask_email($query['email']);

				$content['search'] = array('SITE_URL', 'TOKEN');
				$content['replace'] = array(SITE_URL, $values['token']);

				$mail = new NEWMailer();
				$mail_sent = $mail->send_mail_login($query['email'], "Recuperar Acceso", PATH_MAILS."mail.recover.html", $content);

				if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY) {
					$stop = true;
				}

				if($mail_sent) {
					set_alert("success", "Se ha enviado un correo a $vendor_email para que puedas recuperar tu contraseña.");
				} else {
					set_alert("error", "Hubo un problema al enviar tu información, favor de intentar nuevamente.");
				}
	
			}

		} else {

			set_alert("error", "Hubo un problema al encontrar tu información, favor de intentar nuevamente.");
			$return = "login.recover.php";

		}

	break;

	case 'reset':

		# vars
		$password = apost('password');
		$passwordConfirm = apost('passwordConfirm');
		$code = apost('code', '40');
		$token_post = apost('token', 50);
		$token_sess = session_get_data("token");

		# process
		$query = sql_select_row("SELECT proveedorId FROM ".TABLE_VENDORS." WHERE token = '$code'");

		if($query) {

			$values['token'] = "";

			if($password==$passwordConfirm) {

				$values['password'] = sec_hash_password($password);

				$updated = query_update(TABLE_VENDORS, $values, "proveedorId = ".$query['proveedorId']);

				if($updated>0) {
	
					set_alert("success", "Su contraseña se ha modificado correctamente.");
		
				}
	
			} else {

				$error = true;
				set_alert("error", "Las contraseñas no coinciden, favor de verificarlas.");

			}
	
		} else {

			set_alert("error", "Hubo un problema al encontrar tu información, favor de intentar nuevamente.");
			$return = "login.recover.php";

		}

	break;

	default:

		# set error & error message on session
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
	
	break;
	
}

# redirect
redirect($return, $stop);

?>