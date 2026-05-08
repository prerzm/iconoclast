<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "perms.php";

# process
switch(aglobal('cmd', 20)) {

	case 'perm_add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $perm['moduloId'] = (int)apost('moduloId');
            $perm['permisoKey'] = apost('permisoKey', 20);
            $perm['permiso'] = apost('name', 30);

            # query
            $updated = query_insert(TABLE_MODULES_PERMS, $perm);
                
            if($updated>0) {
                system_log($updated, TABLE_MODULES_PERMS, "Add", json_encode($perm));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'perm_update':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $permId = (int)apost('id');
            $perm['moduloId'] = (int)apost('moduloId');
            $perm['permisoKey'] = apost('permisoKey', 20);
            $perm['permiso'] = apost('name', 30);

            $updated = query_update(TABLE_MODULES_PERMS, $perm, "permisoId = $permId");
                
            if($updated>0) {
                system_log($permId, TABLE_MODULES_PERMS, "Update", json_encode($perm));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }
        
        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

    break;

	case 'perm_del':
    
        if($global_perms['DELETE']) {

            # vars
            $permId = (int)aget('id');
            
            # del from roles_permissions
            query_delete(TABLE_ROLES_PERMS, "permisoId = $permId");

            # del from permissions
            $updated = query_delete(TABLE_MODULES_PERMS, "permisoId = $permId");
                
            if($updated>0) {
                system_log($permId, TABLE_MODULES_PERMS, "Delete", json_encode($perm));
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