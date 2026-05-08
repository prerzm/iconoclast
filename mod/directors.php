<?php

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "directors.php";

# process
switch(aglobal('cmd', 20)) {

	case 'add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $director['directorNombre'] = apost('directorNombre', 150);
            $director['email'] = apost('email', 150);

            # query
            $id = query_insert(TABLE_DIRECTORS, $director);

            if($id>0) {
                system_log($id, TABLE_DIRECTORS, "Add", json_encode($director));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'update':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $directorId = (int)apost('id');
            $director['directorNombre'] = apost('directorNombre', 150);
            $director['email'] = apost('email', 150);

            # update
            $updated = query_update(TABLE_DIRECTORS, $director, "directorId = $directorId");
                
            if($updated>0) {
                system_log($directorId, TABLE_DIRECTORS, "Update", json_encode($director));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'on':
    
        if($global_perms['EDIT']) {

            # vars
            $directorId = (int)aget('id');
            $director['activo'] = 1;

            # query
            $updated = query_update(TABLE_DIRECTORS, $director, "directorId = $directorId");
                
            if($updated>0) {
                system_log($directorId, TABLE_DIRECTORS, "Show", json_encode($director));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'off':
    
        if($global_perms['EDIT']) {

            # vars
            $directorId = (int)aget('id');
            $director['activo'] = 0;

            # query
            $updated = query_update(TABLE_DIRECTORS, $director, "directorId = $directorId");
                
            if($updated>0) {
                system_log($directorId, TABLE_DIRECTORS, "Hide", json_encode($director));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'del':
    
        if($global_perms['DELETE']) {

            # vars
            $directorId = (int)aget('id');
            $director['deleted'] = 1;

            # query
            $updated = query_update(TABLE_DIRECTORS, $director, "directorId = $directorId");
                
            if($updated>0) {
                system_log($directorId, TABLE_DIRECTORS, "Delete", json_encode($director));
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
redirect($return);

?>