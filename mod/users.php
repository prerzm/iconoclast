<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "users.php";

# process
switch(aglobal('cmd', 20)) {

    case 'user_add':
    
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $user['rolId'] = (int)apost('roleId');
            $user['nombre'] = apost('name', 50);
            $user['email'] = apost('email', 100);
            $password = apost('password', 50);

            # filer data
            if(!var_is_valid_password($password)) {
                $error = true;
                set_alert("error", "La contraseña debe ser de al menos 8 carácteres y contenter mayúsculas, minúsuclas y un número.");
            } else {
                $user['password'] = sec_hash_password($password);
            }

            # query
            if($error==false) {
                
                $userId = query_insert(TABLE_USERS, $user);
                
                if($userId>0) {

                    # companies
                    $companies = $_POST['companies'];
                    if(var_is_valid_array($companies)) {
                        $company_id = $companies[0];
                        foreach($companies as $c) {
                            query_insert(TABLE_USERS_COMPANIES, array("usuarioId" => $userId, "companyId" => $c));
                        }
                    } else {
                        $company_id = session_get_data("companyId");
                        query_insert(TABLE_USERS_COMPANIES, array("usuarioId" => $userId, "companyId" => session_get_data("companyId")));
                    }
                    query_update(TABLE_USERS, array("companyId" => $company_id), "usuarioId = $userId");

                    system_log($updated, TABLE_USERS, "Add", json_encode($user));
                    set_alert("success", "La información ha sido actualizada.");
                    
                } else {
                    set_alert("error", "Hubo un problema, favor de intentar nuevamente");
                }
        
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	case 'user_update':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $userId = (int)apost('id');
            $user['rolId'] = (int)apost('roleId');
            $user['nombre'] = apost('name', 50);
            $user['email'] = apost('email', 100);
            $password = apost('password', 50);

            # password
            if(!var_is_empty($password)) {
                if(!var_is_valid_password($password)) {
                    $error = true;
                    set_alert("error", "La contraseña debe ser de al menos 8 carácteres y contenter mayúsculas, minúsuclas y un número.");
                } else {
                    $user['password'] = sec_hash_password($password);
                }
            }

            # update companies
            $companies = $_POST['companies'];
            query_delete(TABLE_USERS_COMPANIES, "usuarioId = $userId");
            if(var_is_valid_array($companies)) {
                $user['companyId'] = $companies[0];
                foreach($companies as $c) {
                    query_insert(TABLE_USERS_COMPANIES, array("usuarioId" => $userId, "companyId" => $c));
                }
            } else {
                $error = true;
                set_alert("error", "El usuario debe pertenecer al menos a una compañía.");
            }

            # update record
            if($error==false) {
                query_update(TABLE_USERS, $user, "usuarioId = $userId");
            }

            # query
            if($error==false) {
                system_log($userId, TABLE_USERS, "Update", json_encode($user));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema al guardar la información, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	case 'user_del':
    
        if($global_perms['DELETE']) {

            # vars
            $userId = (int)aget('id');
            $user['deleted'] = 1;

            # query
            $updated = query_update(TABLE_USERS, $user, "usuarioId = $userId");
                
            if($updated>0) {
                system_log($userId, TABLE_USERS, "Delete", json_encode($user));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	default:

        # set error & error message on session
		set_alert("error", "Hubo un problema en la información, por favor intenta nuevamente.");
	
	break;
	
}

# redirect
header("Location: ../$return");

?>