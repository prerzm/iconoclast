<?php

/** RZM PHP Framework **/

# include configuration file
include_once ('../includes/inc.init.php');

# return
$return = "roles.php";

# process
switch(aglobal('cmd', 20)) {

	case 'role_add':
	
        if($global_perms['ADD']) {

            # vars
            $error = false;
            $role['rol'] = apost('name', 20);

            # query
            $updated = query_insert(TABLE_ROLES, $role);
                
            if($updated>0) {
                system_log($updated, TABLE_ROLES, "Add", json_encode($role));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }
    
	break;

	case 'role_update':
	
        if($global_perms['EDIT']) {

            # vars
            $error = false;
            $roleId = (int)apost('id');
            $role['rol'] = apost('name', 20);

            $updated = query_update(TABLE_ROLES, $role, "rolId = $roleId");

            $perms = $_POST['permisos'];

            if(is_array($perms) && count($perms)>0) {

                $updated += query_delete(TABLE_ROLES_PERMS, "rolId = $roleId");

                foreach($perms as $permisoId => $value) {
                    $updated += query_insert(TABLE_ROLES_PERMS, array("rolId" => $roleId, "permisoId" => $permisoId));
                }

            }
            
            if($updated>0) {
                system_log($roleId, TABLE_ROLES, "Update", json_encode($role));
                set_alert("success", "La información ha sido actualizada.");
            } else {
                set_alert("error", "Hubo un problema, favor de intentar nuevamente");
            }

        } else {
            set_alert("error", "No cuenta con los permisos para acceder a este módulo");
        }

	break;

	case 'role_del':
    
        if($global_perms['DELETE']) {

            # vars
            $roleId = (int)aget('id');
            $role['deleted'] = 1;

            # query
            $updated = query_update(TABLE_ROLES, $role, "rolId = $roleId");
                
            if($updated>0) {
                system_log($roleId, TABLE_ROLES, "Delete", json_encode($role));
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